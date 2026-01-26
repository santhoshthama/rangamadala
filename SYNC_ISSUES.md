# UI-Backend Synchronization Issues for manage_roles.php

## Current Status: ❌ NOT SYNCHRONIZED

### Issues Found:

1. **Hardcoded Drama Name** (Line 78)
   - Current: `<span>Sinhabahu</span>`
   - Fix: `<span><?= htmlspecialchars($drama->drama_name) ?></span>`

2. **Hardcoded Tab Counts** (Lines 96-104)
   - Current: "All Roles (15)", "Applications (8)", "Sent Requests (3)"
   - Fix: Use `<?= count($roles) ?>`, `<?= count($pendingApplications) ?>`, etc.

3. **Static Role Data** (Lines 115-240)
   - Current: Hardcoded HTML for King Sinhabahu, Princess Suppadevi, etc.
   - Fix: Loop through `$roles` array with PHP foreach

4. **Missing Backend Variables**
   - Backend provides: `$roles`, `$roleStats`, `$pendingApplications`
   - UI uses: None (all hardcoded)

5. **Incomplete Create Role Modal** (Lines 358-386)
   - Missing fields: `role_type` (dropdown), `positions_available` (number input)
   - Current fields have wrong IDs: `roleName` should be `role_name`

6. **JavaScript Not Connected** (manage-roles.js)
   - Has TODO comments instead of actual AJAX calls
   - Functions don't call backend endpoints:
     - `submitCreateRole()` - should POST to `/director/create_role`
     - `acceptApplication()` - should POST to `/director/accept_application`
     - `rejectApplication()` - should POST to `/director/reject_application`
     - `editRole()` - should GET `/director/get_role` then POST to `/director/update_role`
     - `deleteRole()` - should POST to `/director/delete_role`

7. **Missing Data Flow**
   - No initial data load from `$roles`, `$roleStats`, `$pendingApplications`
   - No dynamic rendering of roles by status (filled/vacant/pending)
   - No grouping or filtering logic

## Files That Need Updates:

1. ✅ `app/controllers/director.php` - Backend ready
2. ✅ `app/models/M_role.php` - Model ready
3. ❌ `app/views/director/manage_roles.php` - Needs complete rewrite to use backend data
4. ❌ `public/assets/JS/manage-roles.js` - Needs AJAX implementation

## Next Steps:

1. Update manage_roles.php to display dynamic data from backend
2. Implement JavaScript AJAX calls to backend endpoints
3. Test CRUD operations
4. Run database_roles_table.sql to create tables
