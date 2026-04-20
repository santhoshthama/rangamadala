# User Verification System Documentation

## Overview

The Rangamadala platform implements a comprehensive user verification system for **Artists** and **Service Providers**. This ensures that only verified professionals can access the platform's features.

## User Roles & Verification Requirements

| Role | Verification Required | Auto-Approved |
|------|----------------------|---------------|
| Audience | No | Yes ✓ |
| Admin | No | Yes ✓ |
| Artist | **Yes** | No - Requires Admin Approval |
| Service Provider | **Yes** | No - Requires Admin Approval |

---

## Database Schema

### Users Table - Verification Fields

```sql
ALTER TABLE `users` 
    ADD COLUMN `is_verified` TINYINT(1) NOT NULL DEFAULT 1,
    ADD COLUMN `verification_status` ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
    ADD COLUMN `rejection_reason` TEXT DEFAULT NULL,
    ADD COLUMN `verified_by` INT(11) DEFAULT NULL,
    ADD COLUMN `verified_at` DATETIME DEFAULT NULL;
```

### Field Descriptions

| Field | Type | Description |
|-------|------|-------------|
| `is_verified` | TINYINT(1) | 1 = verified/can login, 0 = cannot login |
| `verification_status` | ENUM | 'pending', 'approved', 'rejected' |
| `rejection_reason` | TEXT | Reason provided when admin rejects |
| `verified_by` | INT | Admin user ID who performed verification |
| `verified_at` | DATETIME | Timestamp of verification action |

---

## Signup Flow

### Artist Registration Flow

```
1. User visits /ArtistRegister
2. User fills form:
   - Full Name
   - Email
   - Password
   - Phone
   - NIC Photo (required for verification)
3. System creates user with:
   - is_verified = 0
   - verification_status = 'pending'
4. User redirected to Login with success message
5. User CANNOT login until admin approves
```

### Service Provider Registration Flow

```
1. User visits /ServiceProviderRegister
2. User fills form:
   - Full Name
   - Professional Title
   - Email
   - Password
   - Phone
   - NIC Number
   - NIC Photo Front (required)
   - NIC Photo Back (required)
   - Professional Details
3. System creates user with:
   - is_verified = 0
   - verification_status = 'pending'
4. Additional profile saved to serviceprovider table
5. User redirected to Login with success message
6. User CANNOT login until admin approves
```

---

## Login Authentication Flow

```php
// In Login Controller
$user = $this->model->authenticate($email, $password);

if ($user) {
    // Check verification for artists and service providers
    if (in_array($user->role, ['artist', 'service_provider'])) {
        if ($user->is_verified == 0) {
            if ($user->verification_status === 'rejected') {
                // Show rejection message with reason
                $error = "Your registration was rejected. Reason: " . $user->rejection_reason;
            } else {
                // Show pending message
                $error = "Your account is pending admin approval.";
            }
            // DO NOT allow login
            return;
        }
    }
    
    // Proceed with login only if verified
    $_SESSION['user_id'] = $user->id;
    // ... set other session variables
}
```

### Login Status Messages

| Status | User Experience |
|--------|----------------|
| Pending | "Your account is pending admin approval. This usually takes 1-2 business days." |
| Rejected | "Your registration was rejected. Reason: [Admin's reason]" |
| Approved | Normal login proceeds |

---

## Admin Verification Flow

### Accessing Verification Dashboard

```
URL: /UserVerification/pending
Access: Admin only (role = 'admin')
```

### Available Admin Actions

1. **View Pending Users** - `/UserVerification/pending`
2. **View Verified Users** - `/UserVerification/verified`
3. **View Rejected Users** - `/UserVerification/rejected`
4. **View User Details** - `/UserVerification/viewUser?id={user_id}`
5. **Approve User** - POST `/UserVerification/approve`
6. **Reject User** - POST `/UserVerification/reject`

### Approve User Action

```php
// In M_user model
public function approveUser($user_id, $admin_id) {
    $sql = "UPDATE users 
            SET is_verified = 1, 
                verification_status = 'approved', 
                verified_at = NOW(), 
                verified_by = ?,
                rejection_reason = NULL
            WHERE id = ?
            AND role IN ('artist', 'service_provider')";
    
    return $this->db->query($sql, [$admin_id, $user_id]);
}
```

### Reject User Action

```php
// In M_user model
public function rejectUser($user_id, $reason, $admin_id) {
    $sql = "UPDATE users 
            SET is_verified = 0,
                verification_status = 'rejected', 
                rejection_reason = ?, 
                verified_at = NOW(), 
                verified_by = ?
            WHERE id = ?
            AND role IN ('artist', 'service_provider')";
    
    return $this->db->query($sql, [$reason, $admin_id, $user_id]);
}
```

---

## File Structure

### Controllers

| File | Purpose |
|------|---------|
| `ArtistRegister.php` | Handle artist signup with NIC upload |
| `ServiceProviderRegister.php` | Handle service provider signup |
| `Login.php` | Authentication with verification check |
| `UserVerification.php` | Admin verification management |
| `Admindashboard.php` | Admin dashboard with verification APIs |

### Models

| File | Purpose |
|------|---------|
| `M_signup.php` | Base registration with verification status |
| `M_artist.php` | Artist-specific registration |
| `M_service_provider.php` | Service provider registration |
| `M_login.php` | Authentication with verification fields |
| `M_user.php` | Admin verification operations |

### Views

| File | Purpose |
|------|---------|
| `artist_register.view.php` | Artist registration form |
| `service_provider_register.view.php` | Service provider registration form |
| `login.view.php` | Login form with verification messages |
| `user_verification.view.php` | Admin pending users list |
| `user_verification_detail.view.php` | Detailed user view for admin |
| `verified_users.view.php` | List of approved users |
| `rejected_users.view.php` | List of rejected users |

---

## API Endpoints (Admin Dashboard)

### Get Pending Registrations
```
GET /admindashboard/getPendingRegistrations
Response: JSON array of pending users
```

### Get Registration Details
```
GET /admindashboard/getRegistrationDetails?user_id={id}
Response: JSON with user and service_provider details
```

### Approve User
```
POST /admindashboard/approveUser
Body: { "user_id": 123 }
Response: { "success": true/false, "message": "..." }
```

### Reject User
```
POST /admindashboard/rejectUser
Body: { "user_id": 123, "reason": "Rejection reason" }
Response: { "success": true/false, "message": "..." }
```

---

## Security Considerations

1. **Admin Access Control**: All verification endpoints check for admin role
2. **Role Restriction**: Only artists and service providers can be approved/rejected
3. **Session Validation**: User ID and role stored in session after login
4. **Password Hashing**: Passwords hashed with `password_hash()` using `PASSWORD_DEFAULT`

---

## Setup Instructions

### 1. Run Database Migration

```bash
# In phpMyAdmin or MySQL CLI
mysql -u root -p rangamandala_db < dev/database_verification_migration.sql
```

### 2. Verify Upload Directories Exist

```
public/uploads/nic/           # Artist NIC photos
public/uploads/nic_photos/    # Service provider NIC photos
```

### 3. Create Admin Account (if not exists)

```sql
INSERT INTO users (full_name, email, password, phone, role, is_verified, verification_status)
VALUES (
    'Admin User',
    'admin@rangamadala.lk',
    '$2y$10$...hashed_password...',  -- Use password_hash('your_password', PASSWORD_DEFAULT)
    '0771234567',
    'admin',
    1,
    'approved'
);
```

---

## Testing Checklist

- [ ] Artist can register with NIC photo
- [ ] Service Provider can register with NIC front/back
- [ ] Pending user cannot login
- [ ] Pending user sees appropriate message
- [ ] Admin can view pending users
- [ ] Admin can approve user
- [ ] Approved user can login
- [ ] Admin can reject user with reason
- [ ] Rejected user cannot login
- [ ] Rejected user sees rejection reason
- [ ] Audience users can register and login immediately (no verification)

---

## Troubleshooting

### User Can't Login After Approval
1. Check `is_verified = 1` in users table
2. Check `verification_status = 'approved'` in users table
3. Clear browser cookies and try again

### NIC Images Not Displaying
1. Verify file exists in `public/uploads/nic/` or `public/uploads/nic_photos/`
2. Check file permissions (should be readable)
3. Verify path stored in database matches actual file location

### Admin Can't See Pending Users
1. Verify admin session is active (`$_SESSION['role'] === 'admin'`)
2. Check for users with `verification_status = 'pending'` in database
3. Ensure users have `role IN ('artist', 'service_provider')`
