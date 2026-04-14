# Production Manager Budget Section Guide

This README explains how the **Budget section** works for the **Production Manager** part of the Rangamadala project.

---

## 1) What this section does

The Production Manager budget module lets an assigned PM:

- View budget summary for one drama
- Add budget items
- Edit budget items
- Delete budget items
- Export budget report as CSV
- Track allocated vs spent vs remaining amounts
- View category-wise totals

It also links to service management because service requests and payments affect budget planning.

---

## 2) Relevant files (source of truth)

### Controllers
- `app/controllers/Production_manager.php`
  - `manage_budget()`
  - `get_budget_items()`
  - `get_budget_item()`
  - `save_budget_item()`
  - `delete_budget_item()`
  - `export_budget_report()`
  - `authorizeDrama()` (authorization gate)

### Models
- `app/models/M_budget.php` (all budget CRUD + summaries)
- `app/models/M_production_manager.php` (checks PM assignment)
- `app/models/M_drama_services.php` (service-type budget config)

### Views
- `app/views/production_manager/manage_budget.php` (budget UI)
- `app/views/production_manager/manage_services.php` (service/payment context for PM)
- `app/views/director/view_services_budget.php` (director view-only page)

### Frontend JS
- `public/assets/JS/manage-budget.js` (budget page actions + API calls)
- `public/assets/JS/view-services-budget.js` (director-side view tabs)

### SQL / Schema
- `database_setup.sql` (table definitions)
- `add_notes_to_drama_budgets.sql` (adds `notes` column)
- `add_missing_pm_service_tables.sql` (service/payment related missing tables)

### Existing related doc
- `readme/SERVICE_PAYMENT_PROCESS.md`

---

## 3) Access and permissions

Budget page route:
- `/production_manager/manage_budget?drama_id={ID}`

Access is protected by `authorizeDrama()` in `Production_manager` controller:

1. User must be logged in
2. `drama_id` must exist
3. Drama must exist
4. User must be active PM for that drama (`M_production_manager::isManagerForDrama`)

If not authorized, user is redirected away with an error message.

---

## 4) Data model used by budget module

Main table: `drama_budgets`

Important columns:
- `id`
- `drama_id`
- `item_name`
- `category`
- `allocated_amount`
- `spent_amount`
- `status` (`pending`, `approved`, `completed`, `cancelled`)
- `notes`
- `created_by`
- `created_at`, `updated_at`

Budget categories allowed by controller (`save_budget_item`):
- `venue`
- `technical`
- `costume`
- `marketing`
- `other`

---

## 5) How budget page works (step-by-step)

## 5.1 Open budget page

When PM opens `manage_budget`:
- Controller loads all budget items for `drama_id`
- Computes:
  - total allocated
  - total spent
  - remaining
  - percent spent
  - category summary
- Renders `production_manager/manage_budget` view

## 5.2 Add budget item

From UI button **Add Budget Item**:
- Opens modal (`openAddBudgetModal()`)
- PM fills item fields
- JS calls:
  - `POST /production_manager/save_budget_item?drama_id={ID}`
- On success: reload page

Validation on backend:
- `item_name` required
- category must be allowed list
- allocated amount >= 0
- spent amount >= 0
- spent <= allocated
- status in allowed status list

## 5.3 Edit budget item

From edit icon:
- JS calls `GET /production_manager/get_budget_item?drama_id={ID}&id={item_id}`
- Fills modal with existing values
- Save sends same `save_budget_item` endpoint with `id`
- Controller updates existing row if row belongs to drama

## 5.4 Delete budget item

From trash icon:
- JS confirms action
- Calls:
  - `POST /production_manager/delete_budget_item?drama_id={ID}` with item id
- Controller checks ownership + deletes row

## 5.5 Export report

From **Export Report**:
- JS opens:
  - `/production_manager/export_budget_report?drama_id={ID}`
- Controller streams CSV with columns:
  - Item Name, Category, Allocated Amount, Spent Amount, Status, Notes, Created At

---

## 6) Budget API endpoints used by frontend

All require PM auth + valid `drama_id`.

- `GET /production_manager/get_budget_items?drama_id={ID}`
- `GET /production_manager/get_budget_item?drama_id={ID}&id={item_id}`
- `POST /production_manager/save_budget_item?drama_id={ID}`
- `POST /production_manager/delete_budget_item?drama_id={ID}`
- `GET /production_manager/export_budget_report?drama_id={ID}`

`public/assets/JS/manage-budget.js` sets base URL from:
- `window.PM_BUDGET_API_BASE` (provided by view)

---

## 7) Relationship with service management

Budget module is separate from service request lifecycle, but PM uses both together:

- `manage_services` handles service requests, provider responses, and payment actions
- `manage_budget` tracks budget items and spending totals
- `drama_services` table stores required service types and optional service-level budget values

For payment flow details (advance/full/remaining, verification), see:
- `readme/SERVICE_PAYMENT_PROCESS.md`

---

## 8) Director-side visibility

Director page:
- `/director/view_services_budget?drama_id={ID}`

This is intended as **view-only**. UI says PM manages changes.

Current implementation note:
- In `director.php`, `view_services_budget()` currently renders without injecting populated budget/service arrays.
- The view handles empty states safely.

If you want real data there, extend `Director::view_services_budget()` with data builders (similar to `renderDramaView` usage in other methods).

---

## 9) Common troubleshooting

### Problem: “You are not authorized to access this drama.”
Check:
- `drama_manager_assignments` has active row for user + drama
- `drama_id` query parameter is correct

### Problem: Add/edit fails with validation message
Check:
- Category value is one of allowed keys (`venue`, `technical`, `costume`, `marketing`, `other`)
- `spent_amount <= allocated_amount`

### Problem: Notes not saving
Run migration:
- `add_notes_to_drama_budgets.sql`

### Problem: Export CSV opens blank
Check:
- Budget rows exist for selected drama
- PM has access to that drama

---

## 10) Quick test checklist

1. Login as user assigned as PM for a drama
2. Open `manage_budget` with valid `drama_id`
3. Add item (all fields valid)
4. Edit same item (change amounts/status/notes)
5. Delete item
6. Export report and verify CSV content
7. Try same endpoints with unauthorized user (must fail/redirect)

---

## 11) Future improvements (recommended)

- Link budget items directly to service requests/payments for automatic spent updates
- Add server-side pagination/filtering for large budget tables
- Add audit trail for who changed each budget item
- Sync director view page with live PM budget/service data

---

## 12) Requested upgrade plan (Budget + Service/Payment sync)

This section explains **how to implement** the requested changes safely.

### 12.1 Link budget items directly to service requests/payments (auto spent updates)

#### Goal
When a PM/service payment is completed, corresponding budget item(s) should auto-update `spent_amount`.

#### Recommended DB changes

Add linkage fields to `drama_budgets`:

- `service_request_id` (nullable FK to `service_requests.id`)
- `source_type` enum/string (`manual`, `service_request`, `payment_sync`)

Optional useful fields:

- `service_type_snapshot` (store service type at creation time)
- `last_synced_at` timestamp

#### Sync trigger points

Run budget sync right after payment/request status updates in payment flow:

- after `Payment/return` (PayHere success)
- after `confirmBankPayment`
- after `confirmCashPayment`
- after any logic that marks request payment as `paid` / request status `completed_paid`

#### Sync logic (high-level)

1. Load `service_request` + payment totals for that request
2. Find linked budget item by `service_request_id`
3. If found: update `spent_amount`
4. If not found:
  - find or create category budget item based on service type
  - increment/update `spent_amount`
5. Keep idempotent behavior (running twice should not double-count)

---

### 12.2 Sync director view page with live PM data

#### Goal
`/director/view_services_budget?drama_id={ID}` should show real live values from PM-managed data.

#### Required controller changes

In `Director::view_services_budget()`:

- load service requests for drama (`M_service_request`)
- load budget items + totals (`M_budget`)
- load category summary (`M_budget::getBudgetSummaryByCategory`)
- optionally load theater/service schedules if needed
- pass all arrays to `director/view_services_budget`

#### Required view behavior

`app/views/director/view_services_budget.php` already has placeholders/empty-state support. Keep it view-only, but feed it populated arrays from controller.

---

### 12.3 Change Add Budget Item form fields

Requested form updates:

1. **Category** should use service types (not static budget categories)
2. **Amount** label should be **Allocated Amount**
3. **Status** should align with service provider/payment lifecycle

#### Form mapping recommendation

Use service types from `drama_services` (for selected drama) as category options.

If no `drama_services` configured, fallback to:

- Theater Production
- Lighting Design
- Sound Systems
- Video Production
- Set Design
- Costume Design
- Makeup & Hair
- Other

Rename modal field:

- `Amount (LKR)` ➜ `Allocated Amount (LKR)`

Status options (budget item side) should still store one of:

- `pending`
- `approved`
- `completed`
- `cancelled`

But auto-map from service/payment state (see next subsection).

---

### 12.4 Status mapping aligned with service_payment process

Service request status / payment state ➜ Budget status:

- `pending` / `provider_responded` ➜ `pending`
- `confirmed` / `accepted` ➜ `approved`
- `completed` + payment not fully settled (`unpaid`/`partially_paid`) ➜ `approved`
- `completed_paid` or fully settled payment (`paid`) ➜ `completed`
- `rejected` / `cancelled` ➜ `cancelled`

This keeps budget lifecycle consistent with `readme/SERVICE_PAYMENT_PROCESS.md`.

---

### 12.5 Validation rules (must align with payment process)

In `Production_manager::save_budget_item()` keep/extend checks:

- `item_name` required
- category required and must be valid service-type value for this drama
- `allocated_amount >= 0`
- `spent_amount >= 0`
- `spent_amount <= allocated_amount`
- status in allowed set (`pending|approved|completed|cancelled`)

Additional sync-time checks:

- only trusted backend updates can set `spent_amount` from payment events
- do not allow manual form to mark `completed` if linked request is not fully paid
- prevent duplicate sync writes for same payment record (idempotency key: payment id)

---

### 12.6 Implementation checklist for this upgrade

1. Add migration for `drama_budgets` linkage columns
2. Update budget model methods to support `service_request_id`
3. Update budget modal UI labels/options
4. Update `manage-budget.js` payload keys/labels
5. Add payment->budget sync service/helper
6. Inject live budget/service data in `Director::view_services_budget()`
7. Add regression tests for:
  - advance + remaining flow
  - full payment flow
  - cancelled/rejected service requests
  - unauthorized access

---

**Maintainer note:** This document reflects current implementation in repository as of 2026-04-14.
