# Test Cases Generated From PDF (Admin + Audience)

Project: Rangamadala
Source: Extracted from your uploaded PDF report
Date: 2026-04-18

## Scope
- Admin module
- Audience module
- Authentication, validation, approval workflows, booking/payment/review flows

## Test Data Notes
- Valid Sri Lankan phone example: 0771234567, +94771234567
- Invalid phone examples: 12345, 07712AB567
- Valid password example: Abc@123
- Invalid password examples: abc123, ABC123, Abcdef, Abc123

---

## A. Admin Test Cases

### A1. Admin Login and Security

| TC ID | Scenario | Preconditions | Steps | Expected Result |
|---|---|---|---|---|
| ADM-LOGIN-001 | Login with valid admin credentials | Admin account exists | 1) Open Login 2) Enter valid email/password 3) Submit | Redirect to Admin Dashboard |
| ADM-LOGIN-002 | Login with invalid email format | None | 1) Enter `admin@invalid` + any password 2) Submit | Validation/error message for invalid email format |
| ADM-LOGIN-003 | Login with wrong password | Admin email exists | 1) Enter valid admin email 2) Enter wrong password 3) Submit | Error message, login denied |
| ADM-LOGIN-004 | Password storage security | Admin user exists in DB | 1) Inspect users table | Password is hashed (not plain text) |

### A2. Admin Dashboard Overview

| TC ID | Scenario | Preconditions | Steps | Expected Result |
|---|---|---|---|---|
| ADM-OVW-001 | View overview cards | Logged in as admin | Open Overview tab | Cards display: Total Users, Active Dramas, Pending User Approvals, Pending Drama Approvals |
| ADM-OVW-002 | User distribution chart | Logged in as admin | Open Overview tab | Pie chart loads with role-based distribution |
| ADM-OVW-003 | Registration trend chart | Logged in as admin | Open Overview tab | Trend chart loads over time |
| ADM-OVW-004 | Drama details section | Logged in as admin | Open Overview tab | Current drama statuses and key details are shown |

### A3. User Management

| TC ID | Scenario | Preconditions | Steps | Expected Result |
|---|---|---|---|---|
| ADM-USER-001 | Add audience user | Logged in as admin | 1) Go User Management 2) Add user as audience 3) Save | New audience user created |
| ADM-USER-002 | Add artist user | Logged in as admin | Add user with role artist | Artist account created |
| ADM-USER-003 | Add service provider user | Logged in as admin | Add user with role service_provider | Service provider account created |
| ADM-USER-004 | Edit user details | Existing non-admin user | 1) Open user edit 2) Change name/email/phone 3) Save | User details updated |
| ADM-USER-005 | Delete user account | Existing non-admin user | 1) Click delete 2) Confirm | User removed from user list and DB |
| ADM-USER-006 | Duplicate email blocked on add | Existing email in system | Try creating new user with same email | Error: email already exists |

### A4. User Approval Workflow

| TC ID | Scenario | Preconditions | Steps | Expected Result |
|---|---|---|---|---|
| ADM-APR-001 | Pending list loads | Pending artist/service_provider accounts exist | Open User Approvals | Pending accounts displayed |
| ADM-APR-002 | Approve pending user | One pending account | Click Approve | verification_status becomes approved; user can login |
| ADM-APR-003 | Reject pending user with reason | One pending account | 1) Click Reject 2) Enter reason 3) Submit | verification_status rejected with rejection reason saved |
| ADM-APR-004 | Rejected user sees reason on login | Rejected user exists | Attempt login with rejected account | Rejection message including reason is shown |

### A5. Drama Approval Workflow

| TC ID | Scenario | Preconditions | Steps | Expected Result |
|---|---|---|---|---|
| ADM-DRM-001 | View pending drama requests | Pending drama requests exist | Open Drama Approvals | Requests listed with details |
| ADM-DRM-002 | Approve drama request | Valid pending request | Click Approve | Request approved, drama moves forward to management/in-progress flow |
| ADM-DRM-003 | Reject drama request with reason | Valid pending request | Reject with reason | Request rejected, reason stored |
| ADM-DRM-004 | Certificate validation check | Request includes certificate | Review certificate no/image | Admin can validate before approve |

### A6. Content Management

| TC ID | Scenario | Preconditions | Steps | Expected Result |
|---|---|---|---|---|
| ADM-CNT-001 | Add published drama poster to swiper | Published dramas available | Add slide from content tab | Slide appears in swiper list |
| ADM-CNT-002 | Hide swiper slide | Existing active slide | Toggle active/inactive | Slide hidden from homepage |
| ADM-CNT-003 | Delete swiper slide | Existing slide | Delete slide | Slide removed from system |
| ADM-CNT-004 | Add gallery/special stage image | Content tab accessible | Upload image + title | Image saved and listed |
| ADM-CNT-005 | Add testimonial | Content tab accessible | Submit testimonial form | Testimonial added and visible |

### A7. Admin Profile

| TC ID | Scenario | Preconditions | Steps | Expected Result |
|---|---|---|---|---|
| ADM-PRO-001 | Update admin profile details | Logged in as admin | Change name/email/phone and save | Profile updated successfully |
| ADM-PRO-002 | Change admin password | Logged in as admin | Enter new password + confirm and save | Password updated (hashed), next login works with new password |
| ADM-PRO-003 | Logout | Logged in as admin | Click logout | Session ends and user redirected to login/home |

---

## B. Audience Test Cases

### B1. Audience Signup/Login + Validation

| TC ID | Scenario | Preconditions | Steps | Expected Result |
|---|---|---|---|---|
| AUD-AUTH-001 | Signup with valid data | None | Enter valid name/email/password/phone and submit | Account created successfully |
| AUD-AUTH-002 | Invalid email blocked at signup | None | Enter invalid email and submit | Error for invalid email format |
| AUD-AUTH-003 | Weak password blocked | None | Enter weak password and submit | Error for password complexity |
| AUD-AUTH-004 | Password confirmation mismatch | None | Enter different password/confirm | Error for mismatch |
| AUD-AUTH-005 | Invalid Sri Lankan phone blocked | None | Enter invalid phone and submit | Error for invalid contact number |
| AUD-AUTH-006 | Login with valid credentials | Audience account exists | Login with correct email/password | Redirect to Audience Dashboard |
| AUD-AUTH-007 | Login with wrong password | Audience account exists | Enter wrong password | Error and login denied |

### B2. Audience Overview/Dashboard

| TC ID | Scenario | Preconditions | Steps | Expected Result |
|---|---|---|---|---|
| AUD-OVW-001 | Overview cards load | Logged in as audience | Open dashboard | Cards show upcoming dramas, classes, pending requests, paid shows |
| AUD-OVW-002 | Booking status filter works | Existing my showings | Filter by paid/pending/rejected | Table updates correctly |
| AUD-OVW-003 | Search in my showings | Existing my showings | Enter search text | Matching rows only are shown |

### B3. Browse Dramas + Booking Request

| TC ID | Scenario | Preconditions | Steps | Expected Result |
|---|---|---|---|---|
| AUD-BRW-001 | Browse published dramas | Published dramas exist | Open Browse Dramas | Published dramas listed |
| AUD-BRW-002 | Category filter | Multiple categories exist | Apply category filter | Only selected category dramas shown |
| AUD-BRW-003 | Search drama by title | Drama exists | Search by keyword | Matching dramas shown |
| AUD-BRW-004 | Send show booking request | Logged in audience | Fill booking request form and submit | Request created with pending status |
| AUD-BRW-005 | Missing mandatory request fields | Logged in audience | Submit empty required fields | Validation message shown |

### B4. Booking Acceptance/Rejection Impact

| TC ID | Scenario | Preconditions | Steps | Expected Result |
|---|---|---|---|---|
| AUD-REQ-001 | Accepted request allows payment | Artist accepted request | Open request row | Action button allows Pay Now |
| AUD-REQ-002 | Rejected request shows reason | Artist rejected with reason | Open My Showings/View page | Rejection status and reason visible to audience |
| AUD-REQ-003 | Payment attempt after rejection blocked | Request rejected | Try payment flow | Error: rejected by artist (with reason if available) |

### B5. PayHere Payment Flow (Show Bookings)

| TC ID | Scenario | Preconditions | Steps | Expected Result |
|---|---|---|---|---|
| AUD-PAY-001 | Initiate booking payment for accepted request | Accepted request exists | Click Pay Now | PayHere order initialized successfully |
| AUD-PAY-002 | Successful return updates status | Payment completed | Return from PayHere | Booking marked paid/confirmed; success message shown |
| AUD-PAY-003 | Payment history entry for booking | Completed booking payment | Open Payment History | Payment row appears with order id and details |

### B6. Watched Dramas + Reviews

| TC ID | Scenario | Preconditions | Steps | Expected Result |
|---|---|---|---|---|
| AUD-WCH-001 | Watched drama list eligibility | Paid + past/completed show exists | Open Watched Dramas | Eligible booking appears in watched list |
| AUD-WCH-002 | View watched drama details | Watched booking exists | Open details | Show date/time/booking/payment details are displayed |
| AUD-WCH-003 | Submit rating/review for watched drama | Watched eligible drama | Submit rating/comment | Rating saved and shown in review list |
| AUD-WCH-004 | Block rating for non-watched drama | Not watched/not paid | Try open rating flow | User blocked with proper message |

### B7. Classes and Class Payments

| TC ID | Scenario | Preconditions | Steps | Expected Result |
|---|---|---|---|---|
| AUD-CLS-001 | View published classes | Classes exist | Open Classes tab | Published classes listed |
| AUD-CLS-002 | Enroll via payment | Class fee configured | Click Enroll and complete payment | Enrollment confirmed |
| AUD-CLS-003 | My enrolled classes updates | Enrollment completed | Open My Enrolled Classes | Class appears as enrolled |
| AUD-CLS-004 | Class payment history | Completed class payment | Open Payment History -> class payments | Entry shown with order id and status |

### B8. Audience Profile

| TC ID | Scenario | Preconditions | Steps | Expected Result |
|---|---|---|---|---|
| AUD-PRO-001 | Update profile data | Logged in audience | Change name/email/phone/bio and save | Profile updated successfully |
| AUD-PRO-002 | Invalid email blocked in profile edit | Logged in audience | Enter invalid email and save | Error for invalid email |
| AUD-PRO-003 | Upload profile image valid format | Logged in audience | Upload JPG/PNG/GIF under size limit | Image uploaded and shown |
| AUD-PRO-004 | Reject invalid/oversized image | Logged in audience | Upload unsupported/large file | Upload validation error shown |

---

## C. Suggested Priority Execution Order
1. Authentication and validation tests (ADM-LOGIN, AUD-AUTH)
2. Approval workflows (ADM-APR, ADM-DRM)
3. Booking and payment lifecycle (AUD-BRW, AUD-REQ, AUD-PAY)
4. Ratings and classes (AUD-WCH, AUD-CLS)
5. Profile and content management (ADM-CNT, ADM-PRO, AUD-PRO)

## D. Exit Criteria
- 100% pass for critical cases: login, approvals, booking rejection visibility, payment finalization
- No high-severity defects in role access control and payment flows
- Validation checks return user-friendly error messages for email/password/phone inputs
