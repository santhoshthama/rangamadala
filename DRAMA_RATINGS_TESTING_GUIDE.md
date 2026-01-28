# Drama Rating System - Testing & Verification Guide

## ✅ Quick Verification Steps

### 1. Database Verification

**Check if table was created:**
```sql
SHOW TABLES LIKE 'drama_ratings';
```

Expected: Table `drama_ratings` appears

**Check table structure:**
```sql
DESCRIBE drama_ratings;
```

Expected: All columns present (id, drama_id, user_id, rating, comment, etc.)

**Verify data:**
```sql
SELECT COUNT(*) FROM drama_ratings;
```

Expected: Shows count (0 if no ratings yet)

---

### 2. File Verification

Check if all files are in place:

```
✓ app/models/M_rating.php
✓ app/controllers/BrowseDramas.php (updated)
✓ app/views/drama_details.view.php (updated)
✓ public/assets/JS/drama-ratings.js
✓ public/assets/CSS/drama_ratings.css
```

---

### 3. Manual Testing Walkthrough

#### Step 1: Login
1. Open browser
2. Navigate to application
3. Login with valid credentials
4. Verify you're in audience role

#### Step 2: Browse Dramas
1. Click "Browse Dramas"
2. Select any drama from list
3. Verify drama details page loads

#### Step 3: Open Rating Modal
1. Locate "Rate Drama" button
   - Should be next to "Book Ticket" button
   - Should have star icon
2. Click the button
3. Verify modal appears with:
   - Title: "Rate This Drama"
   - 5 interactive stars
   - Comment textarea
   - "Submit Rating" button
   - Close button (X)

#### Step 4: Test Star Picker
1. Hover over stars
   - Watch for star glow effect
   - Watch for emoji feedback below
2. Click on 3rd star
   - 3 stars should light up (selected)
   - Emoji should change
3. Press Escape
   - Modal should close
4. Click "Rate Drama" again
   - Modal reopens (not persistent)

#### Step 5: Test Comment Field
1. Click comment textarea
2. Type test comment: "This is a test rating!"
3. Watch character counter
   - Should show "21/500"
4. Type more text
   - Counter should update
5. Try typing past 500 chars
   - Should stop at 500

#### Step 6: Submit Rating
1. Select 5 stars
2. Add comment "Amazing performance!"
3. Click "Submit Rating" button
4. Watch for:
   - Button becomes disabled with "Submitting..." text
   - Success toast appears (top center)
   - Modal closes
   - Page refreshes/ratings update
5. Look for green success message:
   - "Rating submitted successfully!"

#### Step 7: View Rating Summary
1. Scroll up on drama details page
2. Look for rating summary section:
   - Should show: ★ 5.0 (1 rating)
   - Golden color scheme
3. Scroll down to "Audience Ratings" section
4. Verify your rating appears:
   - Your name
   - 5 filled stars
   - Your comment
   - Timestamp
   - "Mark as helpful" button

#### Step 8: Test Already Rated Behavior
1. Click "Rate Drama" again
2. Notice "Already Rated" notice:
   - Background color: gold tint
   - Message: "You already rated this drama with 5 stars"
   - Text: "Updating will replace your previous review"
3. Change rating to 4 stars
4. Change comment to "Very good actually"
5. Click "Submit Rating"
6. Verify:
   - Success message appears
   - Modal closes
   - Rating updated (now shows 4 stars)
   - Comment updated

#### Step 9: Test Helpful Button
1. Scroll to ratings list
2. Find your rating (or another rating)
3. Click "Mark as Helpful" button
4. Verify:
   - Button becomes disabled (lighter color)
   - Helpful count increases
   - Success message appears

#### Step 10: Test Error Handling
1. Click "Rate Drama"
2. Don't select any stars
3. Click "Submit Rating"
4. Verify error message: "Please select a star rating"

---

## 🧪 Automated Testing Scenarios

### Functional Test Suite

#### Test Case 1: New Rating Submission
```
Precondition: User logged in, drama selected
Test Steps:
1. Click "Rate Drama"
2. Select 5 stars
3. Enter comment "Test comment"
4. Click Submit
Expected: Rating saved, modal closes, summary updated
```

#### Test Case 2: Rating Update
```
Precondition: User has already rated the drama
Test Steps:
1. Click "Rate Drama"
2. Verify "Already Rated" notice appears
3. Change rating to 3 stars
4. Click Submit
Expected: Rating updated, previous rating replaced
```

#### Test Case 3: Comment Optional
```
Precondition: Rating modal open
Test Steps:
1. Select 4 stars
2. Leave comment field empty
3. Click Submit
Expected: Rating submitted without error
```

#### Test Case 4: Comment Length Validation
```
Precondition: Rating modal open
Test Steps:
1. Paste 600 characters into comment
2. Verify it stops at 500
Expected: Comment capped at 500 characters
```

#### Test Case 5: Rating Range Validation
```
Precondition: Testing via browser console
Test Steps:
1. submitRatingForm() with rating = 0
2. submitRatingForm() with rating = 6
Expected: Both fail with error message
```

#### Test Case 6: Mark as Helpful
```
Precondition: Rating visible in list
Test Steps:
1. Click "Mark as Helpful" button
2. Try clicking again
Expected: First click works, second is disabled
```

---

## 📊 Data Integrity Tests

### Rating Uniqueness
```sql
-- Should show max 1 row per user per drama
SELECT drama_id, user_id, COUNT(*) 
FROM drama_ratings 
GROUP BY drama_id, user_id 
HAVING COUNT(*) > 1;
```
Expected: No results (all unique)

### Foreign Key Integrity
```sql
-- Should show no orphaned ratings
SELECT COUNT(*) FROM drama_ratings 
WHERE drama_id NOT IN (SELECT id FROM dramas)
   OR user_id NOT IN (SELECT id FROM users);
```
Expected: Result = 0

### Rating Range Validation
```sql
-- Should show all ratings 1-5
SELECT DISTINCT rating FROM drama_ratings ORDER BY rating;
```
Expected: 1, 2, 3, 4, 5 (no 0, 6+)

---

## 🔒 Security Tests

### Test 1: Authentication Required
1. Open drama_details page
2. Logout
3. Manually navigate to: `/BrowseDramas/submitRating`
4. Expected: Redirected to login

### Test 2: XSS Prevention
1. Open rating modal
2. In comment field, try: `<script>alert('xss')</script>`
3. Submit rating
4. Check database: Comment should be escaped
5. View page: Should see literal text, not alert

### Test 3: SQL Injection
1. In comment field, try: `'; DROP TABLE drama_ratings; --`
2. Submit rating
3. Expected: Rating submitted normally, table intact

### Test 4: Drama Existence
1. Open browser console
2. Run: `submitRatingForm()` with fake drama_id
3. Expected: Error "Drama not found"

### Test 5: User ID Manipulation
1. Check that user_id is from session, not form input
2. Expected: No way to modify user_id from frontend

---

## 📱 Responsive Design Tests

### Desktop (1920x1080)
- [ ] Modal centered, 450px wide
- [ ] All stars visible
- [ ] Comment textarea full width
- [ ] Buttons properly sized
- [ ] Ratings list displays well

### Tablet (768x1024)
- [ ] Modal 95% width
- [ ] Stars smaller but clickable
- [ ] Comment field responsive
- [ ] Touch targets adequate (44px+)
- [ ] No horizontal scroll

### Mobile (375x667)
- [ ] Modal full width
- [ ] Stars still interactive
- [ ] Comment field fills screen
- [ ] Keyboard doesn't hide content
- [ ] All buttons reachable
- [ ] Toast fits on screen

---

## ⌨️ Keyboard Navigation Tests

| Key | Expected Behavior |
|-----|------------------|
| Tab | Focus star buttons, textarea, submit button |
| 1-5 | Quick select 1-5 star rating (when modal open) |
| Escape | Close modal |
| Enter | In comment, adds newline (not submit) |
| Shift+Enter | Could submit (optional) |

---

## 🎨 Visual/UI Tests

### Color & Theme Consistency
- [ ] Stars are gold (#d4af37)
- [ ] Modal background is dark brown
- [ ] Text is light (readability)
- [ ] Hover effects smooth
- [ ] Selected state clear

### Animations
- [ ] Star hover has glow effect
- [ ] Modal slides up smoothly
- [ ] Toast slides down on appear
- [ ] Button click feedback

### Icons
- [ ] Star icon in "Rate Drama" button
- [ ] Check circle in success toast
- [ ] Error icon in error toast
- [ ] Material Design Icons load correctly

---

## 📊 Performance Tests

### Load Time
```javascript
// In browser console
console.time('rating-load');
// Click Rate Drama
console.timeEnd('rating-load');
```
Expected: < 200ms

### Memory Usage
```javascript
// Open DevTools > Memory > Heap Snapshot
// Take snapshot before/after opening modal
```
Expected: < 5MB difference

### Network Requests
```
Expected for submitRating:
- 1 POST request
- Size: < 1KB
- Time: < 500ms
```

---

## 🧪 Browser Compatibility Tests

Test in each browser:

#### Chrome 90+
- [ ] All features work
- [ ] Animations smooth
- [ ] No console errors

#### Firefox 88+
- [ ] All features work
- [ ] CSS compatible
- [ ] Form validation works

#### Safari 14+
- [ ] Stars render correctly
- [ ] Modal opens/closes
- [ ] Animations smooth

#### Edge 90+
- [ ] No issues
- [ ] All features available

#### Mobile Safari (iOS)
- [ ] Touch interaction works
- [ ] Modal dismissible
- [ ] Keyboard works

#### Chrome Android
- [ ] Responsive layout
- [ ] Touch targets adequate
- [ ] Performance acceptable

---

## 🔧 Debugging Checklist

If something doesn't work:

1. **Check Browser Console**
   ```javascript
   // Open DevTools > Console
   // Look for red error messages
   ```

2. **Check Network Tab**
   ```
   DevTools > Network
   - Is submitRating POST request sent?
   - Is response success?
   - Check response data
   ```

3. **Check Database**
   ```sql
   SELECT * FROM drama_ratings 
   WHERE user_id = 2 
   ORDER BY created_at DESC;
   ```

4. **Check JavaScript**
   ```javascript
   // In console:
   console.log(selectedRatingInput.value);  // Should show 1-5
   console.log(ratingComment.value);        // Should show text
   console.log(DRAMA_ID);                   // Should show ID
   ```

5. **Check CSS**
   ```
   DevTools > Elements
   - Right-click element
   - Inspect
   - Check computed styles
   ```

---

## ✨ Edge Cases to Test

### Edge Case 1: Rapid Submission
1. Click Submit
2. Click Submit again immediately
3. Expected: Second click disabled/ignored

### Edge Case 2: Network Delay
1. Open DevTools > Network
2. Throttle to "Slow 3G"
3. Submit rating
4. Expected: Button stays disabled, shows "Submitting..."

### Edge Case 3: Very Long Comment
1. Paste 1000+ characters
2. Expected: Truncated to 500

### Edge Case 4: Special Characters
1. Comment: `"<>&'`
2. Expected: Properly escaped in display

### Edge Case 5: Multiple Tabs
1. Open drama in Tab A
2. Open same drama in Tab B
3. Rate in Tab A
4. Switch to Tab B, refresh
5. Expected: Correct rating shown in Tab B

### Edge Case 6: Concurrent Ratings
1. Have two users rate the same drama
2. Verify average updates correctly
3. Expected: 2 ratings visible

---

## 📈 Reporting Results

### Test Report Template

```
Drama Rating System - Test Report
Date: [DATE]
Tester: [NAME]
Browser: [BROWSER/VERSION]

Results:
✓ Test 1: [PASS/FAIL] [Notes]
✓ Test 2: [PASS/FAIL] [Notes]
...

Summary:
- Total Tests: [X]
- Passed: [X]
- Failed: [X]
- Success Rate: [X]%

Issues Found:
1. [Issue description]
   - Steps to reproduce
   - Expected vs actual
   - Severity: High/Medium/Low

Sign-off:
- QA Approved: [Yes/No]
- Ready for Production: [Yes/No]
```

---

## 🎯 Test Coverage Matrix

| Component | Unit Test | Integration Test | E2E Test | Security Test |
|-----------|-----------|-----------------|----------|---------------|
| Model Methods | ✓ | ✓ | - | ✓ |
| Controller Endpoints | ✓ | ✓ | ✓ | ✓ |
| Modal UI | - | ✓ | ✓ | - |
| Star Picker | - | ✓ | ✓ | - |
| Comment Field | - | ✓ | ✓ | ✓ |
| Form Submission | ✓ | ✓ | ✓ | ✓ |
| Rating Display | - | ✓ | ✓ | - |
| Database | ✓ | ✓ | - | ✓ |
| Authentication | - | ✓ | ✓ | ✓ |
| Validation | ✓ | ✓ | ✓ | ✓ |

---

**Remember**: Test thoroughly before production deployment! 🚀
