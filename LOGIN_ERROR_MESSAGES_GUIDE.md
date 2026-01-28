-- =============================================
-- ERROR MESSAGE DISPLAY & VERIFICATION FLOW
-- =============================================

-- The login system now shows error messages for:
-- 1. Wrong email or password
-- 2. Pending verification (awaiting admin approval)
-- 3. Rejected registration (with reason)

-- =============================================
-- ERROR MESSAGE SCENARIOS
-- =============================================

SCENARIO 1: Wrong Email or Password
=====================================
- User enters incorrect email or password
- Login form submits to Login.php controller
- M_login model checks database
- If credentials don't match: Returns false
- Error message displayed: "Invalid email or password. Please check your credentials and try again."
- User stays on login page with error visible

SCENARIO 2: Artist/Service Provider - Pending Approval
======================================================
- User registers as artist or service provider
- is_verified field = 0, rejection_reason = NULL
- User tries to login with correct credentials
- Login.php detects is_verified = 0 (pending state)
- Error message displayed: "Your account is pending admin approval. Please wait for verification. You will receive an email once approved."
- User stays on login page
- Admin must approve in Admin Dashboard

SCENARIO 3: Artist/Service Provider - Rejected
===============================================
- Admin rejected user's registration in Admin Dashboard
- is_verified field = 0, rejection_reason = "Insufficient documentation"
- User tries to login
- Login.php detects rejection_reason is not empty
- Error message displayed: "Your registration was rejected. Reason: [Rejection Reason]. Please contact admin support."
- User stays on login page
- User should contact admin or reapply

SCENARIO 4: Audience Member - Auto Approved
=============================================
- User registers as audience member
- is_verified field = 1 (auto-approved)
- User can login immediately with correct credentials
- No verification needed
- Direct access to audience dashboard

SCENARIO 5: Admin/Verified User - Login Success
================================================
- User with is_verified = 1 logs in successfully
- All verification checks pass
- Session created with user data
- Success message: "Welcome back, [Full Name]! Login successful."
- Redirected to appropriate dashboard

-- =============================================
-- DATABASE QUERIES FOR REFERENCE
-- =============================================

-- Get user with verification status
SELECT 
    id, 
    full_name, 
    email, 
    password, 
    role, 
    is_verified,
    rejection_reason,
    verified_at
FROM users 
WHERE email = ?;

-- Check verification status
SELECT is_verified, rejection_reason, verified_at 
FROM users 
WHERE id = ? 
AND role IN ('artist', 'service_provider');

-- Get unverified count (for admin notification)
SELECT COUNT(*) as pending_count 
FROM users 
WHERE is_verified = 0 
AND role IN ('artist', 'service_provider');

-- =============================================
-- LOGIN FLOW DIAGRAM
-- =============================================

User enters Email & Password
        ↓
Click Login Button
        ↓
Form POST to /Login controller
        ↓
Validate Email & Password Format ─→ If invalid → Show validation error
        ↓
M_login::authenticate() queries database
        ↓
Check if email exists & password matches ─→ If no → Show "Invalid credentials"
        ↓
User found & password correct
        ↓
Check user role ─→ If audience/admin → Proceed to login
        ↓
If artist/service_provider:
        ├→ Check is_verified = 1 ─→ If yes → Proceed to login
        └→ Check rejection_reason ─→ If set → Show rejection message
                                  ─→ If NULL → Show pending approval message
        ↓
Create session with user data
        ↓
Set success message
        ↓
Redirect to appropriate dashboard

-- =============================================
-- TESTING THE ERROR MESSAGES
-- =============================================

TEST 1: Wrong Password
=======================
1. Go to /Login page
2. Enter valid email: "artist@example.com"
3. Enter wrong password: "wrongpassword123"
4. Click Login
5. Expected: Error message "Invalid email or password..."

TEST 2: Wrong Email
====================
1. Go to /Login page
2. Enter invalid email: "notauser@example.com"
3. Enter any password
4. Click Login
5. Expected: Error message "Invalid email or password..."

TEST 3: Pending Approval (Artist)
===================================
1. Register as artist
2. Skip admin approval in dashboard
3. Go to /Login page
4. Enter artist email and password
5. Click Login
6. Expected: Error message "Your account is pending admin approval..."

TEST 4: Rejected Registration
==============================
1. Register as service provider
2. In Admin Dashboard → Registrations
3. Click Reject with reason "Incomplete documentation"
4. Try to login
5. Expected: Error message includes rejection reason

TEST 5: Successful Login
==========================
1. Go to /Login page
2. Enter audience member credentials
3. Click Login
4. Expected: Success toast appears, redirected to audience dashboard

-- =============================================
-- ERROR MESSAGE STYLING
-- =============================================

/* The error messages use: */
- Background: Linear gradient (red)
- Text: White
- Border: Red left border
- Icon: ✕ (cross symbol)
- Animation: Slide down from top
- Border radius: 8px
- Shadow: Subtle shadow for depth
- Display: Flex with centered alignment

File: Signin.css
Classes: .error-message, .success-message

-- =============================================
-- USER EXPERIENCE IMPROVEMENTS
-- =============================================

1. Clear Error Messages ✓
   - Users know exactly what went wrong
   - Specific messages for different scenarios
   - Helpful suggestions in messages

2. Session Persistence ✓
   - Error message displays after form submit
   - Email field retains user input
   - User can easily retry

3. Visual Feedback ✓
   - Color-coded messages (red for error, green for success)
   - Icons for quick visual recognition
   - Smooth animations

4. Security ✓
   - Never reveals if email exists
   - Same message for wrong email/password
   - Verification prevents unauthorized access

5. Admin Control ✓
   - Admins can approve/reject registrations
   - Admins can see rejection reasons
   - Audit trail of all approvals/rejections
