# Leave Management System

Odoo-inspired, enterprise-grade time off and leave management UI built on Laravel with Tailwind CSS.

## Highlights

- Employee, manager, and admin workspaces with role-based navigation.
- Card-driven dashboards, dense data tables, and workflow-focused layouts.
- Modern design system with tokens, glassmorphism accents, and dark mode.
- Reusable UI components for forms, badges, tables, and notifications.

## Panels

- Employee: dashboard, requests, calendar, notifications.
- Manager: approvals, team availability, calendar, reports.
- Admin: control center, leave types, allocations, users, settings.

## Tech Stack

- Laravel 12
- Blade templates + Tailwind CSS
- Alpine.js

## Local Setup

1. Install PHP 8.2+, Composer, Node.js, and a MySQL database.
2. Copy environment variables: `copy .env.example .env`
3. Generate app key: `php artisan key:generate`
4. Configure the database in `.env`.
5. Install dependencies: `composer install` and `npm install`
6. Run migrations: `php artisan migrate`
7. Build assets: `npm run dev`
8. Start the app: `php artisan serve`

## Entry Routes

- Workspace hub: `/dashboard`
- Employee panel: `/employee`
- Manager panel: `/manager`
- Admin panel: `/admin`

Authentication is required to access the panels.
