# Leave Management System Engineering Package

## Scope
This package translates your policy-driven LMS vision into implementable artifacts aligned with the current Laravel codebase.

Current baseline already exists in the repository:
- Core tables: `leave_types`, `leave_allocations`, `leave_requests`, `audit_logs`
- Core models: `LeaveType`, `LeaveAllocation`, `LeaveRequest`, `AuditLog`
- Core services: `LeaveRequestService`, `LeaveBalanceService`, `AllocationService`
- Current workflow: employee submit -> manager/hr actions -> allocation deduction

---

## 1) ERD (Text-Based)

```text
users
  id PK
  department_id FK -> departments.id (nullable in current model depending on migration)
  manager_id FK -> users.id (nullable)
  ...

departments
  id PK
  ...

leave_types
  id PK
  code UNIQUE
  name
  validation_type (no|manager|hr|both)
  allocation_type (fixed|accrual)
  request_unit (day|half_day|hour)
  requires_allocation
  carry_forward
  carry_forward_cap
  accrual_rate
  accrual_cap
  max_days_per_request
  ...

leave_policies (NEW)
  id PK
  leave_type_id FK -> leave_types.id
  version
  min_service_months
  max_days_per_year
  max_unpaid_days_per_year
  allow_backdate
  allow_future_apply_days
  yearly_reset
  expiry_days
  carry_forward_limit
  effective_from
  effective_to nullable
  is_active

leave_allocations
  id PK
  user_id FK -> users.id
  leave_type_id FK -> leave_types.id
  year
  allocated_days
  used_days
  carried_over_days
  expires_at nullable
  notes nullable
  UNIQUE(user_id, leave_type_id, year)

leave_requests
  id PK
  user_id FK -> users.id
  leave_type_id FK -> leave_types.id
  start_date
  end_date
  request_unit (day|half_day|hour)
  requested_hours nullable
  half_day_period nullable
  days
  status
  manager_id FK -> users.id nullable
  manager_status
  hr_id FK -> users.id nullable
  hr_status
  reason
  rejection_reason nullable
  approved_at nullable
  rejected_at nullable

leave_request_states (NEW, optional for full auditable state machine)
  id PK
  leave_request_id FK -> leave_requests.id
  from_status
  to_status
  actor_id FK -> users.id nullable
  reason nullable
  metadata json nullable
  created_at

leave_history (NEW, balance ledger)
  id PK
  user_id FK -> users.id
  leave_type_id FK -> leave_types.id
  year
  action (allocate|accrue|approve_deduct|reject_restore|expire|carry_forward|override)
  delta_days
  before_days
  after_days
  reference_type
  reference_id
  actor_id FK -> users.id nullable
  metadata json nullable
  created_at

audit_logs
  id PK
  actor_id FK -> users.id nullable
  action
  auditable_type
  auditable_id
  data json nullable
  ip_address nullable
  user_agent nullable
```

---

## 2) SQL Schema Additions (Production-Oriented)

```sql
CREATE TABLE leave_policies (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  leave_type_id BIGINT UNSIGNED NOT NULL,
  version INT UNSIGNED NOT NULL DEFAULT 1,
  min_service_months SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  max_days_per_year DECIMAL(6,2) NULL,
  max_unpaid_days_per_year DECIMAL(6,2) NULL,
  allow_backdate TINYINT(1) NOT NULL DEFAULT 0,
  allow_future_apply_days SMALLINT UNSIGNED NULL,
  yearly_reset TINYINT(1) NOT NULL DEFAULT 1,
  expiry_days SMALLINT UNSIGNED NULL,
  carry_forward_limit DECIMAL(6,2) NULL,
  effective_from DATE NOT NULL,
  effective_to DATE NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_leave_policies_leave_type
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id)
    ON DELETE CASCADE,
  UNIQUE KEY uq_leave_policy_version (leave_type_id, version)
);

CREATE TABLE leave_history (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  leave_type_id BIGINT UNSIGNED NOT NULL,
  year SMALLINT UNSIGNED NOT NULL,
  action VARCHAR(50) NOT NULL,
  delta_days DECIMAL(6,2) NOT NULL,
  before_days DECIMAL(6,2) NOT NULL,
  after_days DECIMAL(6,2) NOT NULL,
  reference_type VARCHAR(100) NULL,
  reference_id BIGINT UNSIGNED NULL,
  actor_id BIGINT UNSIGNED NULL,
  metadata JSON NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  KEY idx_leave_history_user_type_year (user_id, leave_type_id, year),
  KEY idx_leave_history_action (action),
  CONSTRAINT fk_leave_history_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_leave_history_leave_type FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE CASCADE,
  CONSTRAINT fk_leave_history_actor FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE leave_request_states (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  leave_request_id BIGINT UNSIGNED NOT NULL,
  from_status VARCHAR(50) NULL,
  to_status VARCHAR(50) NOT NULL,
  actor_id BIGINT UNSIGNED NULL,
  reason VARCHAR(255) NULL,
  metadata JSON NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  KEY idx_leave_request_states_request (leave_request_id),
  CONSTRAINT fk_leave_request_states_request
    FOREIGN KEY (leave_request_id) REFERENCES leave_requests(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_leave_request_states_actor
    FOREIGN KEY (actor_id) REFERENCES users(id)
    ON DELETE SET NULL
);
```

---

## 3) Laravel Model/Service Structure (Target)

### Models
- `LeaveType` (existing)
- `LeavePolicy` (new)
- `LeaveAllocation` (existing)
- `LeaveRequest` (existing)
- `LeaveHistory` (new)
- `LeaveRequestState` (new)
- `AuditLog` (existing)

### Services (recommended split)
- `PolicyEngineService`
  - `getActivePolicy(LeaveType $type, Carbon $onDate)`
  - `validateEligibility(User $user, LeaveType $type, Carbon $start, Carbon $end)`
  - `validateLimits(...)`
- `LeaveBalanceService` (existing; extend)
  - `calculateRequestedDays(...)`
  - `getRemaining(...)`
  - `calculateAccruedDays(...)`
- `LeaveRequestService` (existing; keep orchestration)
  - `createRequest(...)`
  - `approveManager(...)`
  - `approveHr(...)`
  - `reject(...)`
  - `cancel(...)` (add)
- `LeaveLedgerService` (new)
  - `recordTransaction(...)`
  - `replayBalance(...)`
- `ComplianceService` (new)
  - `checkOverlap(...)`
  - `checkBackdatePolicy(...)`
  - `checkBiometricReturn(...)`

### Jobs / Scheduler
- `RunMonthlyAccrualJob`
- `ExpireCarryForwardJob`
- `LeaveReturnVerificationJob`
- `LeaveBalanceExhaustedNotificationJob`

---

## 4) API Design (Versioned)

```http
POST   /api/v1/leave/requests
GET    /api/v1/leave/requests/{id}
POST   /api/v1/leave/requests/{id}/cancel

POST   /api/v1/leave/requests/{id}/approve/manager
POST   /api/v1/leave/requests/{id}/approve/hr
POST   /api/v1/leave/requests/{id}/reject

GET    /api/v1/leave/balances/{employeeId}
GET    /api/v1/leave/history/{employeeId}
GET    /api/v1/leave/types

POST   /api/v1/leave/types
PUT    /api/v1/leave/types/{id}
POST   /api/v1/leave/policies
PUT    /api/v1/leave/policies/{id}/activate
```

### Request validation layers
1. Request DTO/FormRequest rules
2. Policy engine rules
3. Compliance checks
4. Transactional database checks
5. Audit + ledger write

---

## 5) Workflow Diagram (Text)

```text
EMPLOYEE SUBMIT
  -> Input validation
  -> PolicyEngine.validateEligibility
  -> BalanceEngine.checkAvailability
  -> ComplianceEngine.checkOverlap
  -> status = PENDING_SUPERVISOR

PENDING_SUPERVISOR
  -> manager approve => if HR required -> PENDING_HR
  -> manager approve => if HR not required -> APPROVED
  -> manager reject => REJECTED

PENDING_HR
  -> hr approve => APPROVED
  -> hr reject => REJECTED

APPROVED
  -> Deduct balance (transaction + ledger)
  -> Notify employee/HR
  -> Await leave completion

CANCELLED
  -> Restore balance if previously deducted
  -> ledger + audit

COMPLETED/EXPIRED
  -> close lifecycle
```

---

## 6) Policy Engine Pseudocode

```text
function validateRequest(user, leaveType, startDate, endDate, days, unit):
  policy = getActivePolicy(leaveType, startDate)

  assert startDate <= endDate
  assert not overlapWithExistingApprovedOrPending(user, startDate, endDate)

  serviceMonths = monthsBetween(user.employment_date, startDate)
  if serviceMonths < policy.min_service_months:
      reject("insufficient_service_months")

  if policy.max_days_per_year is not null:
      used = yearlyUsed(user, leaveType, year(startDate))
      assert used + days <= policy.max_days_per_year

  if leaveType.code == 'ANNUAL' and serviceMonths < 11:
      reject("annual_before_11_months")

  if unit == 'hour':
      assert leaveType.allow_hour == true
  if unit == 'half_day':
      assert leaveType.allow_half_day == true

  remaining = balanceEngine.getRemaining(user, leaveType, year(startDate))
  assert remaining >= days

  return ok
```

---

## 7) Balance Algorithms

### Base Formula
```text
remaining = opening_balance + accrued + carried_forward - used - expired
```

### Current-year allocation view (fits existing table)
```text
remaining = allocated_days + carried_over_days - used_days
```

### Deduction priority (annual leave)
```text
1) deduct from carried_over_days first
2) then deduct from allocated/accrued current-year
3) persist to leave_history and audit_logs in one transaction
```

---

## 8) State Machine Model

Recommended canonical states:
```text
DRAFT
PENDING_SUPERVISOR
PENDING_HR
APPROVED
REJECTED
CANCELLED
EXPIRED
COMPLETED
```

Current states in code:
```text
draft, submitted, manager_approved, hr_approved, approved, rejected, cancelled
```

Mapping recommendation:
- `submitted` -> `PENDING_SUPERVISOR`
- `manager_approved` -> `PENDING_HR`
- `approved` -> `APPROVED`
- keep existing DB enum in MVP; introduce canonical constants in code first.

---

## 9) Validation Logic Matrix

| Validation | Layer | Current | Action |
|---|---|---|---|
| Date range valid | request/service | yes | keep |
| Unit eligibility (hour/half_day) | service | yes | keep |
| Overlap prevention | service | yes | keep |
| Balance sufficiency | service | yes | keep |
| Max per request | service | yes | keep |
| Min service months | policy engine | no | add |
| Unpaid leave yearly cap | policy engine | no | add |
| Backdated leave block | compliance | no | add |
| Biometric return rules | integration | no | later phase |
| Immutable audit trail | audit/ledger | partial | add leave_history + state log |

---

## 10) 20-Day MVP Plan (Repo-Aligned)

### Sprint Goal
Deliver policy-driven leave lifecycle with legal baseline rules, manager/hr approvals, balance deduction integrity, and audit-ready reporting.

### Days 1-3: Domain hardening
- Add migrations: `leave_policies`, `leave_history`, `leave_request_states`
- Add models + factories
- Add enum/constants for request statuses and actions

### Days 4-6: Policy engine (minimum legal rules)
- Implement `PolicyEngineService`
- Rules: 11-month annual eligibility, max per year, unpaid cap, invalid/backdate/overlap checks
- Integrate into `LeaveRequestService::createRequest`

### Days 7-9: Ledger + transactional integrity
- Implement `LeaveLedgerService`
- Record every deduction/restore/accrual in `leave_history`
- Record state transitions in `leave_request_states`

### Days 10-12: Approval workflow hardening
- Restrict manager approvals to assigned manager/admin/hr by policy
- Add cancel flow + restore logic
- Add endpoint-level authorization + policy tests

### Days 13-15: Notifications + reports
- Event-driven notifications for submit/approve/reject/exhaustion
- Reports: leave by type, by department, employee balance summary

### Days 16-18: Compliance + audit controls
- Add admin override workflow with mandatory reason
- Log overrides to `audit_logs`
- Add reconciliation command for `leave_allocations` vs `leave_history`

### Days 19-20: Testing + release readiness
- Feature tests for full lifecycle
- Unit tests for policy edge cases
- Seed sample policies and leave types
- Final hardening + rollback scripts

---

## 11) Immediate Next Coding Tasks (Low-Risk)

1. Add `leave_policies` migration + model.
2. Add `PolicyEngineService` with 11-month annual leave check.
3. Add `leave_history` and write ledger entry on approve.
4. Add one end-to-end feature test: submit -> manager approve -> hr approve -> deduction.

These 4 tasks produce a measurable policy-engine baseline without breaking current UI routes.
