# ENTERPRISE-GRADE TIME OFF MANAGEMENT SYSTEM

## Laravel + MySQL

### Designed with DDD + Clean Architecture + Odoo-Level Improvements

---

# 1. ARCHITECTURE STRATEGY

## 1.1 Architectural Style

We implement:

* Domain-Driven Design (DDD)
* Clean Architecture
* Service Layer Pattern
* Event-Driven Components
* Modular Monolith (Domain Modules separated)

Folder Structure:

app/
├── Domain/
│    ├── Leave/
│    ├── Employee/
│    ├── Approval/
│    ├── Accrual/
│
├── Application/
│    ├── Services/
│    ├── DTOs/
│    ├── Commands/
│
├── Infrastructure/
│    ├── Persistence/
│    ├── Repositories/
│    ├── Notifications/
│
├── Interfaces/
│    ├── Http/
│    │    ├── Controllers/
│    │    ├── Requests/
│    │    ├── Resources/

---

# 2. DOMAIN DESIGN (DDD)

## 2.1 Core Aggregates

1. LeaveRequest (Aggregate Root)
2. LeaveAllocation
3. AccrualPlan
4. ApprovalWorkflow
5. Employee
6. LeaveType

Each aggregate encapsulates business rules.

---

# 3. FULL DATABASE ERD (DETAILED)

## 3.1 USERS

* id (PK)
* name
* email (unique)
* password

## 3.2 ROLES

* id (PK)
* name (unique)

## 3.3 USER_ROLES (Pivot)

* user_id (FK users.id)
* role_id (FK roles.id)

Constraint:

* Many-to-Many

---

## 3.4 EMPLOYEES

* id (PK)
* user_id (FK users.id)
* department_id (FK departments.id)
* manager_id (self FK employees.id)
* hire_date
* status

Constraint:

* Self-referencing for hierarchy

---

## 3.5 LEAVE_TYPES

* id
* name
* unit (days/hours)
* allow_negative
* requires_document
* approval_strategy (single/sequential/parallel)

---

## 3.6 LEAVE_ALLOCATIONS

* id
* employee_id
* leave_type_id
* balance
* accrued_total
* used_total
* valid_from
* valid_to

Index:

* (employee_id, leave_type_id)

---

## 3.7 ACCRUAL_PLANS

* id
* leave_type_id
* frequency (monthly/yearly)
* rate
* cap_limit
* carry_forward
* expiry_rule

---

## 3.8 LEAVE_REQUESTS

* id
* employee_id
* leave_type_id
* start_date
* end_date
* total_units
* status (draft/pending/approved/rejected/cancelled)
* submitted_at

Index:

* (employee_id, start_date)

---

## 3.9 APPROVAL_STEPS

* id
* leave_request_id
* approver_id
* level
* status
* decided_at

Unique Constraint:

* (leave_request_id, level)

---

## 3.10 PUBLIC_HOLIDAYS

* id
* country_code
* date

---

# 4. BUSINESS RULE IMPROVEMENTS OVER ODOO

1. Strict Sequential Approval Engine
2. Smart Conflict Engine
3. Real-Time Balance Engine
4. Predictive Leave Forecasting
5. Policy Versioning
6. Minimum Staffing Rules
7. Leave Blackout Periods
8. Performance-Optimized Accrual Jobs

---

# 5. LARAVEL IMPLEMENTATION BLUEPRINT

## 5.1 Controllers

LeaveController
AllocationController
AccrualController
ApprovalController
ReportController

---

## 5.2 Services

LeaveRequestService

* submit()
* cancel()
* calculateDuration()

ApprovalService

* approveStep()
* rejectStep()
* evaluateNextStep()

AccrualService

* runMonthlyAccrual()
* applyCarryForward()

ConflictService

* checkOverlap()
* checkDepartmentQuota()

BalanceService

* calculateAvailableBalance()

---

## 5.3 Jobs (Queued)

RunMonthlyAccrualJob
SendApprovalReminderJob
ExpireCarryForwardJob
RecalculateBalanceJob

---

## 5.4 Policies

LeavePolicy

* submit
* approve
* cancel

AllocationPolicy

---

## 5.5 Middleware

RoleMiddleware
EnsureManagerMiddleware
AuditMiddleware

---

# 6. ADVANCED ACCRUAL ENGINE (Pseudo-Code)

function runAccrual(employee, leaveType):

plan = getAccrualPlan(leaveType)

if employee.status != 'active':
return

accrued = plan.rate

if allocation.balance + accrued > plan.cap_limit:
accrued = plan.cap_limit - allocation.balance

allocation.balance += accrued
allocation.accrued_total += accrued

save(allocation)

---

# 7. SEQUENTIAL APPROVAL ENGINE (Pseudo-Code)

function approveStep(request, approver):

currentStep = request.getPendingStep()

if currentStep.approver_id != approver.id:
throw Unauthorized

currentStep.status = 'approved'

if request.hasNextStep():
notify(nextApprover)
else:
request.status = 'approved'
deductBalance()

---

# 8. UI/UX DESIGN STRUCTURE

## 8.1 Dashboard Layout

Top Cards:

* Remaining Leave
* Pending Approvals
* Upcoming Leave

Main Area:

* Calendar View
* Team Availability Heatmap

Right Panel:

* Notifications

---

## 8.2 HR Admin Panel

Sidebar:

* Leave Types
* Allocations
* Accrual Plans
* Reports
* Policies

---

# 9. PERFORMANCE STRATEGY

* Use caching for leave balance
* Use DB indexes
* Use queue workers for heavy logic
* Avoid calculating balance on each request

---

# 10. SECURITY STRATEGY

* RBAC via spatie
* CSRF protection
* Input validation
* Audit logs

---

# 11. FUTURE ENTERPRISE EXTENSIONS

* Microservice extraction
* Multi-tenant support
* Payroll auto deduction
* AI leave prediction

---

END OF ENTERPRISE ARCHITECTURE DOCUMENT
