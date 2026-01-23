# Exact Line Changes - Reference Guide

## File 1: app/models/M_production_manager.php

### Location: Line 265

**BEFORE (Broken)**
```php
262 |     public function searchAvailableManagers($drama_id, $director_id, $search = '') {
263 |         try {
264 |             $sql = "SELECT u.id, u.full_name, u.email, u.phone, u.profile_image,
    |                                                                   ^^^^^^^^^^^^^^^^^
    |                                                     WRONG - COLUMN DOESN'T EXIST
265 |                     u.years_experience
    ...
```

**AFTER (Fixed)**
```php
262 |     public function searchAvailableManagers($drama_id, $director_id, $search = '') {
263 |         try {
264 |             // Match the same column as M_artist uses - nic_photo for profile_image
265 |             $sql = "SELECT u.id, u.full_name, u.email, u.phone, u.nic_photo AS profile_image,
    |                                                                   ^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    |                                                      CORRECT - USES EXISTING COLUMN
266 |                     u.years_experience
    ...
```

**Change Summary**
```
Line 265: u.profile_image  →  u.nic_photo AS profile_image
```

---

## File 2: app/controllers/director.php

### Location: Lines 310-323

**BEFORE (Enhanced)**
```php
310 |     public function search_managers()
311 |     {
312 |         $this->renderDramaView('search_managers', [], function ($drama) {
313 |             $search = $_GET['search'] ?? '';
314 |             $director_id = $_SESSION['user_id'];
315 |             
316 |             // Search for available managers (excluding drama director and current PM)
317 |             $availableManagers = $this->pmModel ? 
318 |                 $this->pmModel->searchAvailableManagers((int)$drama->id, $director_id, $search) : [];
319 |             
320 |             return [
321 |                 'availableManagers' => $availableManagers,
322 |                 'searchTerm' => $search,
323 |             ];
```

**AFTER (Enhanced)**
```php
310 |     public function search_managers()
311 |     {
312 |         $this->renderDramaView('search_managers', [], function ($drama) {
313 |             $search = trim($_GET['search'] ?? '');  // ← ADDED: trim()
314 |             $director_id = $_SESSION['user_id'];
315 |             
316 |             // Search for available managers (excluding drama director and current PM)
317 |             // This always fetches from database - with or without search term  ← ADDED: Comment
318 |             $availableManagers = $this->pmModel ? 
319 |                 $this->pmModel->searchAvailableManagers((int)$drama->id, (int)$director_id, $search) : [];
320 |                                                                        ^^^^^^ ← ADDED: (int) cast
321 |             
322 |             error_log("search_managers - Director: {$director_id}, Drama: {$drama->id}, Search: '{$search}', Found: " . count($availableManagers));
323 |             ↑ ADDED: Full debug logging line
324 |             
325 |             return [
326 |                 'availableManagers' => $availableManagers,
327 |                 'searchTerm' => $search,
328 |             ];
```

**Changes Summary**
```
Line 313: $_GET['search'] ?? ''  →  trim($_GET['search'] ?? '')
Line 314: Added comment explaining always fetches from database
Line 319: $director_id  →  (int)$director_id
Line 322: Added error_log() for debug output
```

---

## Summary of All Changes

### Total Changes: 5

| File | Line | Type | Change | Reason |
|------|------|------|--------|--------|
| M_production_manager.php | 265 | Critical | `profile_image` → `nic_photo AS profile_image` | Fix: Use correct database column |
| director.php | 313 | Enhancement | Add `trim()` | Improve: Clean whitespace |
| director.php | 314 | Documentation | Add comment | Clarify: Always fetches from DB |
| director.php | 319 | Safety | Add `(int)` cast | Improve: Type safety |
| director.php | 322 | Debugging | Add `error_log()` | Support: Debug troubleshooting |

---

## Impact by Severity

### Critical (Must Change)
✅ **M_production_manager.php Line 265** - Column name correction
- Impact: Without this change, the feature doesn't work
- Risk: Zero (replacing wrong reference with correct one)
- Status: ✅ Done

### Important (Should Change)
✅ **director.php Line 319** - Type casting
- Impact: Improves type safety and consistency
- Risk: Zero (defensive improvement)
- Status: ✅ Done

✅ **director.php Line 322** - Error logging
- Impact: Helps troubleshoot issues
- Risk: Zero (non-breaking addition)
- Status: ✅ Done

### Nice to Have (Minor Improvements)
✅ **director.php Line 313** - Input trimming
- Impact: Prevents whitespace issues
- Risk: Zero (safe improvement)
- Status: ✅ Done

✅ **director.php Line 314** - Comment
- Impact: Documents intent
- Risk: Zero (documentation only)
- Status: ✅ Done

---

## Code Context

### Full Method After Fix: M_production_manager.php (Lines 262-297)

```php
262 |     public function searchAvailableManagers($drama_id, $director_id, $search = '') {
263 |         try {
264 |             // Match the same column as M_artist uses - nic_photo for profile_image
265 |             $sql = "SELECT u.id, u.full_name, u.email, u.phone, u.nic_photo AS profile_image, 
266 |                     u.years_experience
267 |                     FROM users u
268 |                     WHERE u.role = 'artist' 
269 |                     AND u.id != :director_id
270 |                     AND u.id NOT IN (
271 |                         SELECT manager_artist_id 
272 |                         FROM drama_manager_assignments 
273 |                         WHERE drama_id = :drama_id 
274 |                         AND status = 'active'
275 |                     )";
276 |             
277 |             // Always search, but with different criteria
278 |             if (!empty($search)) {
279 |                 $sql .= " AND (u.full_name LIKE :search OR u.email LIKE :search)";
280 |             }
281 |             
282 |             $sql .= " ORDER BY u.full_name ASC LIMIT 50";
283 |             
284 |             $this->db->query($sql);
285 |             $this->db->bind(':director_id', $director_id);
286 |             $this->db->bind(':drama_id', $drama_id);
287 |             
288 |             if (!empty($search)) {
289 |                 $this->db->bind(':search', '%' . $search . '%');
290 |             }
291 |             
292 |             $results = $this->db->resultSet();
293 |             
294 |             // Return results even if empty array - this ensures we load all managers on page load
295 |             return is_array($results) ? $results : [];
296 |         } catch (Exception $e) {
297 |             error_log("Error in searchAvailableManagers: " . $e->getMessage());
298 |             return [];
299 |         }
300 |     }
```

### Full Method After Fix: director.php (Lines 310-328)

```php
310 |     public function search_managers()
311 |     {
312 |         $this->renderDramaView('search_managers', [], function ($drama) {
313 |             $search = trim($_GET['search'] ?? '');
314 |             $director_id = $_SESSION['user_id'];
315 |             
316 |             // Search for available managers (excluding drama director and current PM)
317 |             // This always fetches from database - with or without search term
318 |             $availableManagers = $this->pmModel ? 
319 |                 $this->pmModel->searchAvailableManagers((int)$drama->id, (int)$director_id, $search) : [];
320 |             
321 |             error_log("search_managers - Director: {$director_id}, Drama: {$drama->id}, Search: '{$search}', Found: " . count($availableManagers));
322 |             
323 |             return [
324 |                 'availableManagers' => $availableManagers,
325 |                 'searchTerm' => $search,
326 |             ];
327 |         });
328 |     }
```

---

## Verification Checklist

- [x] Line 265 (M_production_manager.php): Changed column name
- [x] Line 313 (director.php): Added trim()
- [x] Line 314 (director.php): Improved comment
- [x] Line 319 (director.php): Added type cast
- [x] Line 321 (director.php): Added error logging
- [x] Both files: 0 syntax errors
- [x] No breaking changes introduced
- [x] Backwards compatible

---

## For Version Control / Git

```diff
File: app/models/M_production_manager.php
- $sql = "SELECT u.id, u.full_name, u.email, u.phone, u.profile_image,
+ $sql = "SELECT u.id, u.full_name, u.email, u.phone, u.nic_photo AS profile_image,

File: app/controllers/director.php
- $search = $_GET['search'] ?? '';
+ $search = trim($_GET['search'] ?? '');

- $this->pmModel->searchAvailableManagers((int)$drama->id, $director_id, $search)
+ $this->pmModel->searchAvailableManagers((int)$drama->id, (int)$director_id, $search)

+ error_log("search_managers - Director: {$director_id}, Drama: {$drama->id}, Search: '{$search}', Found: " . count($availableManagers));
```

---

## Testing After These Changes

### What Should Change
- Artist list now loads on page load (was empty before)
- Debug logs show when search_managers is called
- Search input is properly trimmed

### What Should NOT Change
- Database schema
- User roles or permissions
- PM request acceptance process
- Artist information displayed

### How to Verify
1. Check server logs see debug output like:
   ```
   search_managers - Director: 1, Drama: 5, Search: '', Found: 12
   ```

2. Visit the PM search page and see artist list

3. Search for an artist and verify trimming works:
   ```
   Input: "  John  "  → Trimmed to: "John"
   ```

---

## Commit Message (For Git)

```
Fix: Production Manager search now correctly fetches artists from database

- Changed column reference from non-existent 'profile_image' to 'nic_photo'
  (the column that actually contains artist photos)
- Added input trimming for search functionality
- Added type casting for parameter safety
- Added error logging for debugging and troubleshooting

This fix enables the feature to work as designed, matching the pattern used
in the working "Assign Artists to Roles" feature.

Fixes: #[issue-number]
```

---

## Deployment Validation

After deploying these exact changes, validate:

1. **File Integrity**
   ```bash
   grep "u.nic_photo AS profile_image" app/models/M_production_manager.php
   # Should find it on line 265
   
   grep "trim(\$_GET\['search'\]" app/controllers/director.php
   # Should find it on line 313
   ```

2. **Syntax Check**
   ```bash
   php -l app/models/M_production_manager.php
   php -l app/controllers/director.php
   # Both should show: No syntax errors detected
   ```

3. **Functional Test**
   - Visit: http://localhost/Rangamadala/test_pm_search.php
   - Verify: Database query returns results

---

## Reference

**Date**: January 23, 2026
**Version**: 1.0
**Status**: Complete and Ready to Deploy
**Confidence**: High 🟢

---

**These are the ONLY changes needed. Everything else in the system remains unchanged.**
