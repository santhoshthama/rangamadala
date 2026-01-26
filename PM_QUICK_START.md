# Production Manager System - Quick Start Checklist

## 🚀 Installation (5 minutes)

### Step 1: Run Database Migration
- [ ] Open phpMyAdmin
- [ ] Select `rangamandala_db` database
- [ ] Go to SQL tab
- [ ] Open `database_manager_assignment.sql`
- [ ] Copy all content and paste into SQL window
- [ ] Click "Go" to execute
- [ ] Verify success message appears

### Step 2: Verify Tables Created
- [ ] In phpMyAdmin, check that these tables exist:
  - `drama_manager_assignments`
  - `drama_manager_requests`
- [ ] Click each table and verify structure matches documentation

### Step 3: Verify Files Exist
- [ ] Check these files were created:
  ```
  ✓ app/models/M_production_manager.php
  ✓ app/views/director/assign_managers.view.php
  ✓ app/views/director/search_managers.view.php
  ✓ database_manager_assignment.sql
  ✓ PRODUCTION_MANAGER_GUIDE.md
  ✓ PM_SYSTEM_ARCHITECTURE.md
  ✓ pm_system_reference.sql
  ```

- [ ] Check these files were updated:
  ```
  ✓ app/controllers/director.php
  ✓ app/controllers/Artistdashboard.php
  ✓ app/models/M_drama.php
  ✓ app/views/artistdashboard.view.php
  ```

---

## ✅ Testing (10 minutes)

### Test 1: Director Workflow
- [ ] Login as an artist who has created a drama
- [ ] Navigate to drama dashboard
- [ ] Click "Production Manager" in sidebar
- [ ] Verify "Assign Production Manager" button appears
- [ ] Click the button
- [ ] Verify redirect to search page
- [ ] Search for an artist (try searching by name)
- [ ] Click "Send Request" on an artist card
- [ ] Add a message in the modal
- [ ] Submit the request
- [ ] Verify success message appears
- [ ] Verify request appears in "Pending Requests" section

### Test 2: Artist Workflow
- [ ] Logout current user
- [ ] Login as the artist you invited
- [ ] Navigate to Artist Dashboard
- [ ] Click "Requests" tab
- [ ] Verify PM request appears at the top
- [ ] Verify drama name, director name, and message are displayed
- [ ] Click "Accept" button
- [ ] Verify success message appears
- [ ] Click "Manager" tab
- [ ] Verify the drama now appears in manager dramas list

### Test 3: PM Management
- [ ] Logout and login as the director again
- [ ] Navigate to drama dashboard → Production Manager
- [ ] Verify assigned PM profile appears
- [ ] Verify PM name, email, and assignment date shown
- [ ] Test "Change Manager" button
  - [ ] Redirects to search page
  - [ ] Can search for different artist
  - [ ] Can send new request
- [ ] Test "Remove Manager" button
  - [ ] Shows confirmation dialog
  - [ ] Removes PM successfully
  - [ ] Returns to empty state

### Test 4: Edge Cases
- [ ] Try to search while already having a PM assigned (should exclude current PM)
- [ ] Send PM request to yourself (should fail with error)
- [ ] Send duplicate request to same artist (should fail with error)
- [ ] Accept request while another is pending (other requests auto-cancelled)
- [ ] Search with no results (shows "No Artists Found" message)

---

## 🎯 Quick Feature Tour

### For Directors

**Access Point:**
```
Drama Dashboard → Production Manager (sidebar)
```

**Key Features:**
1. **View PM Status** - See who is assigned or if position is vacant
2. **Search Artists** - Find artists to invite with real-time search
3. **Send Invitations** - Invite artists with optional personal message
4. **Track Requests** - See pending invitations and their status
5. **Manage PM** - Remove or change assigned Production Manager

### For Artists

**Access Point:**
```
Artist Dashboard → Requests Tab
```

**Key Features:**
1. **View Requests** - See all PM invitations from directors
2. **Drama Details** - Review drama info before accepting
3. **Accept/Decline** - Respond to requests with one click
4. **Track Assignments** - See all dramas where you're PM in Manager tab

---

## 📊 Expected Behavior

### When Director Assigns PM

**Before:**
```
Drama Dashboard → Production Manager
├─ Empty state
├─ "Assign Production Manager" button
└─ Info: No PM assigned
```

**After Request Sent:**
```
Drama Dashboard → Production Manager
├─ Empty state (still no PM)
├─ "Assign Production Manager" button
└─ Pending Requests section
    └─ Shows invited artist with timestamp
```

**After Artist Accepts:**
```
Drama Dashboard → Production Manager
├─ PM Profile Card
│  ├─ Photo
│  ├─ Name & Contact
│  ├─ Experience
│  └─ Assignment Date
└─ Action Buttons
    ├─ Remove Manager (red)
    └─ Change Manager (yellow)
```

### When Artist Receives Request

**Artist Dashboard → Requests Tab:**
```
┌─────────────────────────────────────┐
│ Production Manager Requests (1)     │
├─────────────────────────────────────┤
│ Drama: Maname                       │
│ Director: John Doe                  │
│ Message: "Would you like to be PM?" │
│ [Accept] [Decline]                  │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│ Role Requests (2)                   │
├─────────────────────────────────────┤
│ ... role requests ...               │
└─────────────────────────────────────┘
```

**After Accepting:**
```
Artist Dashboard → Manager Tab
├─ Drama appears in list
├─ Shows assignment date
└─ Can access PM features (future implementation)
```

---

## 🔍 Troubleshooting

### Issue: PM requests not showing in artist dashboard

**Check:**
1. Is `M_production_manager.php` in `app/models/` folder?
2. Is model loaded in `Artistdashboard.php` constructor?
3. Check PHP error log: `C:\xampp\apache\logs\error.log`

**Fix:**
```php
// In app/controllers/Artistdashboard.php
$pm_model = $this->getModel('M_production_manager');
$data['pm_requests'] = $pm_model ? $pm_model->getPendingRequestsForArtist($user_id) : [];
```

### Issue: "Assign PM" button not appearing

**Check:**
1. Is user logged in as the drama director?
2. Does drama exist in database?
3. Is `pmModel` loaded in director controller?

**Debug:**
```php
// In director.php assign_managers() method
error_log("Current PM: " . print_r($currentManager, true));
```

### Issue: Search returns no artists

**Possible Causes:**
- You're the only artist in database
- All artists are already assigned or invited
- Search term too specific

**Solution:**
1. Create more artist accounts for testing
2. Clear search to see all available artists
3. Remove current PM to make that artist available again

### Issue: Database error on accept

**Check:**
1. Are foreign keys properly set up?
2. Does artist exist in users table?
3. Does drama exist in dramas table?

**Fix:**
```sql
-- Re-run migration
SOURCE database_manager_assignment.sql;
```

---

## 📝 Usage Tips

### For Best Results

1. **Use Descriptive Messages**
   - When sending PM requests, add a personal message
   - Explain what the drama is about
   - Mention why you think they'd be a good fit

2. **Search Efficiently**
   - Start with partial names (e.g., "John" instead of "John Doe")
   - Search by email if you know it
   - Clear search to see all available artists

3. **Manage Requests**
   - Check "Pending Requests" section regularly
   - Cancel old requests by inviting someone else
   - Remove PM before reassigning to avoid confusion

4. **Artist Response**
   - Review drama details before accepting
   - Check director's message
   - Understand PM responsibilities
   - Accept promptly if interested

---

## 🎉 Success Indicators

You've successfully implemented the PM system if:

✅ Directors can navigate to PM page
✅ Search returns list of artists
✅ Requests can be sent and received
✅ Artists can accept/decline requests
✅ Accepted requests create PM assignments
✅ PMs appear in artist's manager tab
✅ Directors can remove/change PMs
✅ Only one PM per drama enforced
✅ Pending requests cancelled when new PM assigned
✅ Clean UI with proper styling

---

## 📚 Documentation Reference

- **Complete Guide:** `PRODUCTION_MANAGER_GUIDE.md`
- **Architecture:** `PM_SYSTEM_ARCHITECTURE.md`
- **SQL Reference:** `pm_system_reference.sql`
- **Database Migration:** `database_manager_assignment.sql`

---

## 🚨 Common Mistakes to Avoid

1. ❌ Forgetting to run database migration
2. ❌ Trying to assign yourself as PM
3. ❌ Not checking if model is loaded before using
4. ❌ Sending duplicate requests to same artist
5. ❌ Not handling null values in views
6. ❌ Forgetting to check session before operations
7. ❌ Not validating drama ownership

---

## ✨ Next Steps

After successful implementation:

1. **Test with Multiple Users**
   - Create 3-5 artist accounts
   - Create 2-3 dramas
   - Test cross-assignment scenarios

2. **Add Enhancements** (Optional)
   - Email notifications
   - Request expiry
   - PM dashboard features
   - Permission management

3. **Monitor Performance**
   - Check query execution times
   - Optimize indexes if needed
   - Monitor database size

4. **Document for Team**
   - Share guide with team members
   - Create training videos
   - Update user manual

---

**Setup Complete!** 🎊

Your Production Manager assignment system is ready to use. If you encounter any issues, refer to the troubleshooting section or check the comprehensive guides.
