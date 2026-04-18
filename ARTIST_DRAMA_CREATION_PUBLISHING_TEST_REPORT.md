# Artist, Drama Creation, and Drama Publishing - Test Report

## Date: April 17, 2026

## Scope

This report covers:

1. Artist-side access and navigation relevant to drama management.
2. Drama creation request flow (`/createDrama`).
3. Admin approval dependency for created dramas.
4. Drama publishing flow (`/director/publish_drama`).

---

## Test Cases

### A. Artist Access and Navigation

No: 1  
Test Case ID: ART-001  
Test Description: Artist dashboard opens for valid artist user.  
Test Steps: 1) Login as artist. 2) Open /artistdashboard.  
Test Data: Role = artist, session = valid.  
Expected Results: Dashboard loads with Director tab and Create Drama CTA.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 2  
Test Case ID: ART-002  
Test Description: Artist dashboard blocked for non-artist role.  
Test Steps: 1) Login as non-artist. 2) Open /artistdashboard.  
Test Data: Role = audience.  
Expected Results: Access denied or redirected from artist-only dashboard.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 3  
Test Case ID: ART-003  
Test Description: Create Drama form is reachable from Director tab.  
Test Steps: 1) Open artist dashboard. 2) Click Create Drama in Director tab.  
Test Data: Valid artist session.  
Expected Results: Create drama form opens at /createDrama.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 4  
Test Case ID: ART-004  
Test Description: Unauthorized user cannot open create drama form.  
Test Steps: 1) Logout. 2) Open /createDrama.  
Test Data: No session.  
Expected Results: Redirect to /login.  
Actual Results: As Expected  
Pass/Fail: Pass

### B. Drama Creation Request

No: 5  
Test Case ID: DC-001  
Test Description: Successful drama request submission with required valid fields.  
Test Steps: 1) Open /createDrama. 2) Fill required fields. 3) Upload valid certificate file. 4) Submit.  
Test Data: Drama Name = Maname, Certificate = PPB/2026/0101, Owner = Nimal Perera, Description = valid synopsis, File = certificate.jpg.  
Expected Results: Request saved in drama_creation_requests with pending status, success message shown, redirect to artist dashboard.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 6  
Test Case ID: DC-002  
Test Description: Submit fails when drama name is empty.  
Test Steps: 1) Open /createDrama. 2) Leave drama name empty. 3) Submit with others valid.  
Test Data: drama_name = empty.  
Expected Results: Blocked with "Drama name is required", entered values remain.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 7  
Test Case ID: DC-003  
Test Description: Submit fails when certificate number is empty.  
Test Steps: 1) Leave certificate number empty. 2) Submit with others valid.  
Test Data: certificate_number = empty.  
Expected Results: Blocked with "Certificate number is required".  
Actual Results: As Expected  
Pass/Fail: Pass

No: 8  
Test Case ID: DC-004  
Test Description: Submit fails when owner name is empty.  
Test Steps: 1) Leave owner name empty. 2) Submit with others valid.  
Test Data: owner_name = empty.  
Expected Results: Blocked with "Owner name is required".  
Actual Results: As Expected  
Pass/Fail: Pass

No: 9  
Test Case ID: DC-005  
Test Description: Submit fails when description is empty.  
Test Steps: 1) Leave description empty. 2) Submit with others valid.  
Test Data: description = empty.  
Expected Results: Blocked with "Drama description is required".  
Actual Results: As Expected  
Pass/Fail: Pass

No: 10  
Test Case ID: DC-006  
Test Description: Submit fails when certificate image is missing.  
Test Steps: 1) Fill text fields validly. 2) Do not upload file. 3) Submit.  
Test Data: No file upload.  
Expected Results: Blocked with "Certificate image is required".  
Actual Results: As Expected  
Pass/Fail: Pass

No: 11  
Test Case ID: DC-007  
Test Description: Submit fails for invalid certificate file type.  
Test Steps: 1) Fill valid fields. 2) Upload unsupported file. 3) Submit.  
Test Data: File = certificate.exe.  
Expected Results: Upload rejected, error shown for certificate image upload.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 12  
Test Case ID: DC-008  
Test Description: Submit fails for certificate file size above 5MB.  
Test Steps: 1) Fill valid fields. 2) Upload oversized file. 3) Submit.  
Test Data: File = certificate_big.jpg greater than 5MB.  
Expected Results: Upload rejected and request not created.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 13  
Test Case ID: DC-009  
Test Description: Duplicate certificate already in dramas is blocked.  
Test Steps: 1) Ensure certificate exists in dramas. 2) Submit same certificate in request.  
Test Data: Existing cert = PPB/2026/0009.  
Expected Results: Error shown: certificate already registered, no new request created.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 14  
Test Case ID: DC-010  
Test Description: Duplicate certificate with pending request is blocked.  
Test Steps: 1) Keep one pending request for a certificate. 2) Submit again with same certificate.  
Test Data: Pending cert = PPB/2026/0101.  
Expected Results: Error shown: pending request already exists for this certificate.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 15  
Test Case ID: DC-011  
Test Description: Successful request stores pending approval state.  
Test Steps: 1) Submit valid request. 2) Verify DB row.  
Test Data: Valid request payload.  
Expected Results: Row created in drama_creation_requests with pending status and correct requested_by.  
Actual Results: As Expected  
Pass/Fail: Pass

### C. Admin Approval Flow

No: 16  
Test Case ID: DA-001  
Test Description: Admin sees pending drama creation requests.  
Test Steps: 1) Login as admin. 2) Open pending requests endpoint or UI.  
Test Data: Existing pending requests.  
Expected Results: Newly submitted request appears with artist details.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 17  
Test Case ID: DA-002  
Test Description: Admin approves request and creates drama row.  
Test Steps: 1) Approve one pending request. 2) Verify DB in drama_creation_requests and dramas.  
Test Data: Valid request ID.  
Expected Results: Request marked approved, new dramas row created, owner mapped, initial is_published equals 0.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 18  
Test Case ID: DA-003  
Test Description: Admin rejects request with reason.  
Test Steps: 1) Reject pending request. 2) Verify DB.  
Test Data: Request ID valid, Reason = Invalid certificate.  
Expected Results: Request marked rejected, rejection reason stored, no new dramas row created.  
Actual Results: As Expected  
Pass/Fail: Pass

### D. Drama Publishing

No: 19  
Test Case ID: DP-001  
Test Description: Owner artist can publish approved drama with valid data.  
Test Steps: 1) Open drama details as owner. 2) Fill publish fields. 3) Upload valid poster. 4) Submit publish.  
Test Data: category_id = 2, public_description = valid, language = Sinhala, duration_minutes = 120, showing_prices = 1000, poster = poster.jpg.  
Expected Results: Drama updated and published, is_published set to 1, publish timestamp set, success message shown.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 20  
Test Case ID: DP-002  
Test Description: Publish fails when category is missing.  
Test Steps: 1) Submit publish form without category.  
Test Data: category_id = empty.  
Expected Results: Error shown for required category, publish blocked.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 21  
Test Case ID: DP-003  
Test Description: Publish fails when public description is missing.  
Test Steps: 1) Submit without public description.  
Test Data: public_description = empty.  
Expected Results: Error shown for required public description.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 22  
Test Case ID: DP-004  
Test Description: Publish fails when language is missing.  
Test Steps: 1) Submit without language.  
Test Data: language = empty.  
Expected Results: Error shown for required language.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 23  
Test Case ID: DP-005  
Test Description: Publish fails when duration is invalid.  
Test Steps: 1) Submit duration as 0 or non-numeric.  
Test Data: duration_minutes = 0 or text.  
Expected Results: Error shown for positive whole number duration.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 24  
Test Case ID: DP-006  
Test Description: Publish fails when showing price is missing.  
Test Steps: 1) Submit without showing price.  
Test Data: showing_prices = empty.  
Expected Results: Error shown for required showing prices.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 25  
Test Case ID: DP-007  
Test Description: Publish normalizes numeric showing price input.  
Test Steps: 1) Enter 1000 as showing price. 2) Submit valid form.  
Test Data: showing_prices = 1000.  
Expected Results: Value normalized to LKR 1,000.00 and accepted.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 26  
Test Case ID: DP-008  
Test Description: Publish fails for invalid poster file type or size.  
Test Steps: 1) Upload invalid poster format or oversized file. 2) Submit.  
Test Data: Invalid type or file greater than 8MB.  
Expected Results: Error shown for invalid poster image constraints.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 27  
Test Case ID: DP-009  
Test Description: Publish blocked when no poster exists at all.  
Test Steps: 1) Use drama without poster. 2) Submit without new upload.  
Test Data: No existing poster_image and no new upload.  
Expected Results: Error shown: drama poster image is required to publish.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 28  
Test Case ID: DP-010  
Test Description: Only drama owner can publish.  
Test Steps: 1) Login as different artist. 2) Attempt publish on another artist drama.  
Test Data: Non-owner session with foreign drama_id.  
Expected Results: Authorization denies operation and publish not executed.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 29  
Test Case ID: DP-011  
Test Description: Published drama appears as published in artist dashboard.  
Test Steps: 1) Publish drama. 2) Return to artist dashboard list.  
Test Data: Published drama record.  
Expected Results: Public status badge shows Published.  
Actual Results: As Expected  
Pass/Fail: Pass

No: 30  
Test Case ID: DP-012  
Test Description: Published drama becomes eligible for audience listings.  
Test Steps: 1) Publish drama. 2) Open audience browse or public listing.  
Test Data: Drama with is_published equal to 1.  
Expected Results: Drama appears in published-only listing sources.  
Actual Results: As Expected  
Pass/Fail: Pass

---

## Coverage Notes

- Create request validations are based on `CreateDrama` controller checks.
- Publish validations are based on `director::publish_drama()` checks.
- Approval-based flow has been included because publish can only happen on a created/approved drama row.

---

## References

- Artist dashboard create links: [app/views/artistdashboard.view.php](app/views/artistdashboard.view.php)
- Create form: [app/views/create_drama.view.php](app/views/create_drama.view.php)
- Create controller: [app/controllers/CreateDrama.php](app/controllers/CreateDrama.php)
- Publish controller action: [app/controllers/director.php](app/controllers/director.php)
- Drama model: [app/models/M_drama.php](app/models/M_drama.php)
- Admin approval endpoints: [app/controllers/Admindashboard.php](app/controllers/Admindashboard.php)
