# Time Off (Leave) Management System

## Full Technical Documentation & Development Plan

---

# 1. Project Overview

## 1.1 Objective

Build a full-featured Leave/Time Off Management System similar to Odoo Time Off using:

* Backend: Laravel (Latest LTS)
* Database: MySQL
* Frontend: Blade or React (optional SPA)
* Authentication: Laravel Breeze / Jetstream
* Role & Permission: spatie/laravel-permission

The system must support leave types, allocations, accrual rules, approvals, reporting, payroll integration readiness, and automation.

---

# 2. System Architecture

## 2.1 Architecture Style

* RESTful Modular Monolith (Recommended)
* Service Layer Pattern
* Repository Pattern (Optional)

## 2.2 High-Level Modules

1. Authentication & Roles
2. Employee Management
3. Leave Types Management
4. Leave Allocation Management
5. Accrual Engine
6. Leave Request Workflow
7. Approval Workflow Engine
8. Calendar & Conflict Engine
9. Reporting & Analytics
10. Notifications
11. Settings & Policies

---

# 3. Functional Requirements

## 3.1 Roles

* Super Admin
* HR Manager
* Manager
* Employee

## 3.2 Core Features

### A. Leave Types

* Create/edit/delete leave types
* Paid / Unpaid
* Requires document
* Allow half-day/hourly
* Requires approval
* Negative balance allowed

### B. Leave Allocations

* Manual allocation
* Bulk allocation
* Accrual-based allocation
* Validity period

### C. Accrual Rules

* Accrual frequency (Monthly, Yearly)
* Accrual rate (e.g., 1.5 days/month)
* Cap limit
* Carry-forward rules
* Expiry rules

### D. Leave Request

* Employee submits request
* Attach documents
* Validate balance
* Conflict check
* Multi-level approval

### E. Approval Workflow

* Single level
* Multi level sequential
* Conditional approval

### F. Calendar View

* Company view
* Department view
* Individual view
* Overlap detection

### G. Reporting

* Leave summary per employee
* Department leave usage
* Balance sheet report
* Monthly trends

---

# 4. Database Design

## 4.1 Core Tables

users

* id
* name
* email
* password
* role_id
* manager_id

employees

* id
* user_id
* department_id
* hire_date
* status

leave_types

* id
* name
* is_paid
* allow_half_day
* allow_hourly
* require_document
* allow_negative
* approval_required

leave_allocations

* id
* employee_id
* leave_type_id
* total_days
* used_days
* remaining_days
* valid_from
* valid_to

accrual_plans

* id
* leave_type_id
* frequency
* rate
* cap_limit
* carry_forward

leave_requests

* id
* employee_id
* leave_type_id
* start_date
* end_date
* total_days
* status
* reason

leave_approvals

* id
* leave_request_id
* approver_id
* level
* status
* approved_at

public_holidays

* id
* name
* date

---

# 5. Business Logic Layer

## 5.1 Leave Balance Calculation

remaining = allocations + accruals - approved leaves

## 5.2 Accrual Engine (Scheduler)

Use Laravel Scheduler:

* Monthly job
* Compute accrual
* Update leave_allocations

## 5.3 Conflict Detection

* Check overlapping leave_requests
* Check department minimum staffing rule

---

# 6. API Design

Auth Routes
POST /login
POST /register

Leave Routes
GET /leave-types
POST /leave-request
GET /leave-balance
POST /approve/{id}

Admin Routes
POST /leave-type
POST /allocation
POST /accrual-plan

---

# 7. UI/UX Structure

Dashboard

* Leave balance cards
* Pending approvals
* Calendar widget

Employee Portal

* My Leave
* Apply Leave
* Leave History

HR Panel

* Manage Types
* Manage Allocations
* Reports
* Settings

---

# 8. Non-Functional Requirements

* Secure (RBAC)
* API Rate limiting
* Validation rules
* Audit trail
* Scalable structure
* Logging

---

# 9. Development Phases

Phase 1: Authentication & Roles
Phase 2: Leave Types & Allocation
Phase 3: Leave Request Workflow
Phase 4: Approval Engine
Phase 5: Accrual Automation
Phase 6: Reports
Phase 7: Optimization & Testing

---

# 10. Testing Strategy

* Unit Tests (PHPUnit)
* Feature Tests
* Workflow Tests
* Edge Cases (negative balance, expiry)

---

# 11. Deployment

* Nginx / Apache
* MySQL 8+
* Queue worker
* Scheduler (cron)

---

# 12. Future Enhancements

* Payroll integration
* Attendance integration
* Multi-company
* Mobile API
* Advanced analytics

---

END OF DOCUMENTATION
