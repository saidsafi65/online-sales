# Online Sale

**Online Sale** is a Laravel-based, Arabic-language (RTL) retail and repair-shop management system. It runs the full day-to-day operation of an electronics store/repair shop — public product browsing, point-of-sale, invoicing, repairs and maintenance tracking, purchasing, multi-branch operations, and role-based staff access — all in one system. Prices are handled in Israeli shekels (₪).

This is a personal, independently owned project built and maintained by **Eng. Said Mohammed Safi**. It is not affiliated with any company or third party.

---

## Table of Contents
- [Overview](#overview)
- [Public Storefront](#public-storefront)
- [Admin & Staff Panel](#admin--staff-panel)
- [Roles & Permissions](#roles--permissions)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Installation & Setup](#installation--setup)
- [Maintenance Utilities](#maintenance-utilities)
- [Branding](#branding)
- [License](#license)
- [Contact](#contact)

---

## Overview

The system serves two audiences:

1. **Guests / customers** — browse the public storefront (products, laptops, software) without logging in.
2. **Staff (admin / manager / employee)** — log in to a full back-office panel to run the business: sales, invoices, repairs, purchasing, inventory, and reporting.

The UI is fully right-to-left (RTL) Arabic, built with Blade templates and Bootstrap 5.

---

## Public Storefront

Reachable by any visitor, no account required:

- **Products** (`/products`) — general product catalog with search, category filter, price-range slider, discount/stock toggles, sorting, and a detail modal.
- **Laptops** (`/laptops`) — dedicated catalog for used/sale laptops with brand filtering, full specs (processor, RAM, storage, GPU, battery life), a multi-image gallery per laptop, and price sorting.
- **Software** (`/software`) — catalog of available software/licenses with category, platform, and license-type details.
- **Support / Privacy / Terms** (`/support`, `/privacy`, `/terms`) — static informational pages.

All three catalogs share a consistent card-grid layout with an expandable product-detail view, out-of-stock indicators, and discount badges.

---

## Admin & Staff Panel

Behind authentication (`/login`), organized by module:

- **Dashboard** — role-aware landing page with quick links to enabled sections.
- **Sales** — record sales, daily/weekly/monthly income views, and a return-sale workflow that restocks the catalog and reverses income.
- **Invoices** — create invoices, generate/print/download as PDF, and issue receipts.
- **Products / Laptops / Software (admin)** — full CRUD, image uploads (including multi-image galleries for laptops), discounts, and out-of-stock toggles.
- **Catalog** — the underlying inventory/catalog items referenced by sales and purchases.
- **Purchases** — record incoming stock purchases, including catalog-linked purchases.
- **Repairs & Maintenance** — repair job tracking, maintenance parts, and maintenance deposits.
- **Laptop Compatibility** — a parts-compatibility tool for matching laptop parts/models.
- **Customer Orders** — track customer orders end-to-end.
- **Debts & Obligations** — track amounts owed to/by the business.
- **Returned Goods** — log and manage product returns outside the sales-return flow.
- **Daily Handovers** — end-of-day cash/shift handover records.
- **Branches** — multi-branch support; data (e.g. sales) is scoped per branch for non-admin users via a global query scope.
- **Reports** — aggregated business reporting.
- **User Management** — manage staff accounts, roles, and per-section permissions.
- **Backups** — manual and scheduled (daily/weekly) database backups, with automatic cleanup of old backups.
- **Mobile Shop module** — a parallel set of sales/debts/expenses/inventory/maintenance records for a mobile-devices side of the business.

---

## Roles & Permissions

Three roles: `admin`, `manager`, `employee`.

- **Admins** bypass all permission checks and see everything, across all branches.
- **Managers/employees** are scoped to their assigned branch and only see the sections explicitly enabled for their account (`can_view_sales`, `can_view_repairs`, `can_view_invoices`, `can_view_products`, etc. — one boolean permission per module, checked via `User::canViewSection()`).

---

## Tech Stack

- **Backend:** PHP 8.4, Laravel 12
- **Database:** MySQL
- **Frontend:** Blade templates, Bootstrap 5 (RTL), vanilla JavaScript, SweetAlert2
- **PDF generation:** for invoices/receipts
- **Hosting:** shared cPanel hosting

---

## Project Structure

```
app/
  Http/Controllers/          Core business controllers (Sales, Invoices, Repairs, Purchases, ...)
  Http/Controllers/Products/ Public + admin controllers for Products, Laptops, Software
  Models/                    Eloquent models (Sale, Invoice, SaleLaptop, Software, Branch, User, ...)
  Models/Scopes/             Global scopes (e.g. BranchScope for per-branch data isolation)
resources/views/
  layout/                    Shared layouts — gust.blade.php (public), app.blade.php (admin panel)
  products/ laptops/ software/  Public catalog pages + admin CRUD views
  sales/ invoices/ repairs/ purchases/ ...  Module-specific admin views
  legal/                     Support / privacy / terms pages
routes/
  web.php                    All application routes
  console.php                Scheduled tasks (backups, cleanup)
public/
  images/                    Site logo and static images
```

---

## Installation & Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/saidsafi65/online-sales.git
   cd online-sales
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Configure your database credentials in `.env`.

4. **Run migrations**
   ```bash
   php artisan migrate
   ```

5. **Link storage** (required for product/laptop/software images)
   ```bash
   php artisan storage:link
   ```

6. **Serve the app**
   ```bash
   php artisan serve
   ```
   Visit `http://localhost:8000`.

> **Note:** if deploying to shared hosting where the document root points at the project root (not `public/`), all internal links and AJAX calls use Laravel's `url()`/`route()` helpers so they automatically resolve correctly under a `/public` path — avoid hardcoding absolute paths in Blade/JS.

---

## Maintenance Utilities

A few convenience routes exist for production troubleshooting (should be removed or protected once no longer needed):

- `/fix-config` — clears config, route, and view caches, then re-caches config. Use after deploying changes to `routes/web.php` or compiled Blade views that don't seem to take effect.
- `/run-migrate` — runs pending migrations on the live server.

---

## Branding

The site uses a red-based color scheme derived from the "Online Sale" logo (laptop + gear icon). Brand colors are centralized as CSS custom properties (`--primary-color`, `--brand`, etc.) in each layout/page, making future palette changes straightforward.

---

## License

This project is proprietary. All rights reserved by the owner, Eng. Said Mohammed Safi. No part of this codebase may be reused, redistributed, or repurposed without written permission.

---

## Contact

- **Owner:** Eng. Said Mohammed Safi
- **GitHub:** [saidsafi65](https://github.com/saidsafi65)
- **Email:** said.safi.056@gmail.com
- **Phone:** 0599971755
