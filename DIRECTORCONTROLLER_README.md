# Director Controller README

This document explains how `app/controllers/director.php` is structured, how it connects to models and views, and how to add new director features safely.

---

## 1) What this controller does

`Director` is the main controller for drama owners/directors. It handles:

- Drama dashboard
- Drama detail updates
- Role management (create, update, delete, publish/unpublish, assign/remove artists)
- Role applications (accept/reject/profile/interview scheduling)
- Production manager assignment flow
- Schedule/event management
- Notifications to artists

It follows an MVC pattern:

- **Controller**: `Director`
- **Models**: `M_drama`, `M_role`, `M_artist`, `M_production_manager`, `M_schedule`, `M_notification`
- **Views**: `app/views/director/*`

---

## 2) Model connections in `__construct()`

In `__construct()`, each dependency is loaded once:

- `$this->dramaModel = $this->getModel('M_drama')`
- `$this->roleModel = $this->getModel('M_role')`
- `$this->artistModel = $this->getModel('M_artist')`
- `$this->pmModel = $this->getModel('M_production_manager')`
- `$this->scheduleModel = $this->getModel('M_schedule')`
- `$this->notificationModel = $this->getModel('M_notification')`

This lets each action method call domain-specific logic from the correct model.

---

## 3) How controller → model → view works here

### Standard read flow

1. Authorize user and drama with `authorizeDrama()`.
2. Fetch required data from model(s).
3. Return view with payload using `renderDramaView()`.

Example pattern:

- `dashboard()` calls role + PM model methods, prepares data, then renders `director/dashboard`.

### Standard write flow (POST)

1. Confirm request method is `POST`.
2. Run `authorizeDrama()`.
3. Validate input.
4. Call model method to persist changes.
5. Set `$_SESSION['message']` and `$_SESSION['message_type']`.
6. Redirect to correct page.

Examples:

- `create_role()`, `update_role()`, `delete_role()`
- `create_schedule()`, `update_schedule()`, `delete_schedule()`

---

## 4) Core helper methods you should reuse

### `authorizeDrama()`

Use this first in any director action. It ensures:

- User is logged in
- Drama exists
- Logged-in user owns that drama

If checks fail, it redirects safely.

### `renderDramaView($view, $data, $dataBuilder)`

Use this for director pages. It automatically:

- Loads authorized drama
- Merges drama + extra data
- Calls `$this->view('director/' . $view, $payload)`

### Redirect helpers

- `redirectToManageRoles()`
- `redirectToCreateRole()`
- `redirectToRoleDetails()`
- `respondWithRedirect()` / `buildRedirectUrl()`

These reduce duplicated header logic.

---

## 5) Which methods use which model

### `M_drama`

- `authorizeDrama()` -> `getDramaById()`
- `update_drama()` -> `updateDrama()`

### `M_role`

- Role listing/stats/details
- Role CRUD
- Role requests and assignments
- Vacancy publish/unpublish
- Application handling and interview scheduling

### `M_artist`

- Search artists for roles
- Load artist profile in application view

### `M_production_manager`

- Current PM, PM search, request creation/removal

### `M_schedule`

- Event list/stats/date checks
- Event CRUD + status updates

### `M_notification`

- Sends notifications when role/event/application actions occur

---

## 6) View mapping (important)

`renderDramaView('x')` maps to:

- `app/views/director/x.php`

Examples:

- `renderDramaView('dashboard')` -> `app/views/director/dashboard.php`
- `renderDramaView('manage_roles_overview')` -> `app/views/director/manage_roles_overview.php`
- `renderDramaView('schedule_management')` -> `app/views/director/schedule_management.php`

When adding a new controller method, make sure the corresponding view file exists.

---

## 7) How to add a new Director controller feature

Use this checklist:

1. **Create action method** in `Director`.
2. **Authorize** with `authorizeDrama()`.
3. If write action, enforce `POST`.
4. **Validate input** (server-side).
5. **Call model** method(s).
6. **Set flash message** (`$_SESSION['message']`, `$_SESSION['message_type']`).
7. **Redirect or render** using existing helper methods.
8. Add/update **view file** under `app/views/director/`.

---

## 8) Example mini template

```php
public function my_new_action()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $dramaId = $this->getQueryParam('drama_id');
        if ($dramaId) {
            header('Location: ' . ROOT . '/director/some_page?drama_id=' . $dramaId);
            exit;
        }
        $this->dashboard();
        return;
    }

    $drama = $this->authorizeDrama();

    // Validate input
    // Call model
    // Set flash message

    header('Location: ' . ROOT . '/director/some_page?drama_id=' . $drama->id);
    exit;
}
```

---

## 9) Good practices already used in this file

- Defensive model checks (`if (!$this->roleModel) ...`)
- Ownership authorization by drama ID
- Explicit redirects after POST (prevents duplicate submit)
- Input normalization (`trim`, casting, whitelist checks)
- Reusable private/protected helper methods for consistency

---

## 10) Suggested cleanup (optional)

In `manage_roles()`, there are debug logs:

- `error_log("Request ID ...")`
- `error_log("Filtered pending requests ...")`

These are useful while debugging, but can be removed or guarded by an environment debug flag in production.

---

If you want, I can also generate a second document with a **method-by-method table** (method name, HTTP type, models used, view rendered, redirect target) for even faster onboarding.