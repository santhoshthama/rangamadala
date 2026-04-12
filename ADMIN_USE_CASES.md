# Admin Use Cases - Rangamadala

## Use Case ID: ADM-01
**Use Case Name:** Add/Remove User Accounts

**Primary Actor/s:** Admin

**Description:** Admin manages user accounts

**Pre-Conditions:** Admin is logged in

**Main Scenario:**
1. Admin navigates to the "User Management" section.
2. Admin chooses to add or remove a user.
3. For adding:
   - Admin clicks "Add User."
   - Enters necessary details (e.g., name, email, role).
   - Submits the form.
4. For removing:
   - Admin selects a user from the list.
   - Clicks the "Remove" button.
   - Confirms the deletion.
5. The system validates the action and updates the user list.

**Exceptions:** Invalid user details

**Post Conditions:** User list updated

---

## Use Case ID: ADM-02
**Use Case Name:** View/Edit User Details

**Primary Actor/s:** Admin

**Description:** Admin views and edits user profiles

**Pre-Conditions:** Admin is logged in

**Main Scenario:**
1. Admin opens the "User Management" section.
2. Admin selects a user from the list.
3. Admin clicks the "Edit" option.
4. System displays the user's profile information.
5. Admin modifies necessary fields.
6. Admin submits the changes.
7. System validates and saves the updates

**Exceptions:** Invalid content format

**Post Conditions:** User details updated

---

## Use Case ID: ADM-03
**Use Case Name:** Manage Website Content

**Primary Actor/s:** Admin

**Description:** Admin adds or modifies content

**Pre-Conditions:** Admin is logged in

**Main Scenario:**
1. Admin navigates to the content management section.
2. Admin selects to create, edit, or delete content.
3. Admin fills or updates content fields.
4. Admin submits the action.
5. System validates the input.
6. System updates the content repository.
7. A confirmation message is shown.

**Exceptions:** Invalid content format

**Post Conditions:** Website content updated

---

## Use Case ID: ADM-04
**Use Case Name:** Approve/Reject Registrations

**Primary Actor/s:** Admin

**Description:** Admin reviews new registrations

**Pre-Conditions:** Pending requests exist

**Main Scenario:**
1. Admin goes to the "Pending Registrations" section.
2. System displays a list of unapproved users.
3. Admin selects a request to review.
4. Admin chooses to approve or reject the request.
5. System updates the user's status.
6. Notification is sent to the user.

**Exceptions:** None

**Post Conditions:** Registrations processed

---

## Use Case ID: ADM-05
**Use Case Name:** Manage Permissions

**Primary Actor/s:** Admin

**Description:** Admin configures user roles and permissions

**Pre-Conditions:** Admin is logged in

**Main Scenario:**
1. Admin navigates to the "Permissions" section.
2. Admin selects a user.
3. Admin updates the user's role or permissions.
4. Admin submits the changes.
5. System validates and applies the updates.

**Exceptions:** Unauthorized changes

**Post Conditions:** Permissions updated

---

## Use Case ID: ADM-06
**Use Case Name:** View Dashboard

**Primary Actor/s:** Admin

**Description:** Admin sees metrics and an overview of the system

**Pre-Conditions:** Admin is logged in

**Main Scenario:**
1. Admin logs in and is directed to the dashboard.
2. Dashboard loads real-time metrics (e.g., user count, active dramas, pending requests).
3. Admin can view analytics and key performance indicators.

**Exceptions:** Data fetch error

**Post Conditions:** Dashboard displayed with current metrics

---

## Implementation Notes

### Features to Implement:
- **User Management**: Add, remove, view, and edit user accounts
- **Role & Permission Management**: Assign and manage user roles (admin, artist, audience, service_provider)
- **Registration Management**: Approve/reject pending registrations
- **Content Management**: Manage website content and data
- **Dashboard**: Real-time metrics and analytics
- **Admin Authentication**: Secure login system for admin users

### Database Tables Required:
- `users` - User accounts with role-based access
- `admin_logs` - Track admin actions for security
- `pending_registrations` - Store pending user registrations

### UI Components:
- Admin login page
- User management dashboard
- User list with actions (add, edit, delete)
- Permission/role management interface
- Registration approval interface
- Admin dashboard with metrics
