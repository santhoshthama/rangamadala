# Admin Credentials & Setup Guide

## Current Status
There are **no default admin credentials** pre-configured in the system. You need to create an admin account manually.

## How to Create an Admin Account

### Option 1: Using phpMyAdmin (Recommended)

1. **Open phpMyAdmin**
   - Navigate to `http://localhost/phpmyadmin`

2. **Access your database**
   - Select `rangamandala_db` database

3. **Insert Admin User**
   - Go to the `users` table
   - Click "Insert"
   - Fill in the following details:
     - **full_name**: Admin Name (e.g., "Admin User")
     - **email**: admin@rangamadala.com (or your preferred email)
     - **password**: Hash using PHP's `password_hash()` function
     - **phone**: Any phone number
     - **role**: `admin` (important!)
     - **created_at**: Current timestamp (auto-filled)

4. **Generate Password Hash**
   - Use this PHP script to generate a secure password hash:
   ```php
   <?php
   echo password_hash("your_password_here", PASSWORD_DEFAULT);
   ?>
   ```
   - Copy the generated hash and paste it in the password field

### Option 2: Using SQL Query

Execute this SQL query in phpMyAdmin or your database client:

```sql
INSERT INTO `users` 
(`full_name`, `email`, `password`, `phone`, `role`, `created_at`, `updated_at`) 
VALUES 
(
  'Admin User',
  'admin@rangamadala.com',
  '$2y$10$YourPasswordHashHere',
  '+94701234567',
  'admin',
  NOW(),
  NOW()
);
```

**Note**: Replace `'$2y$10$YourPasswordHashHere'` with an actual bcrypt password hash.

### Option 3: Using PHP Command Line

Create a temporary PHP file to generate the hash:

```php
<?php
define('ROOT', 'http://localhost');
require_once 'app/core/init.php';

$password = "your_secure_password";
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n";
?>
```

Run: `php script.php` and use the output.

## Admin Login Credentials (Template)

Once you've created an admin account:

```
Email: admin@rangamadala.com
Password: (whatever you set during creation)
Role: admin
```

## Testing Admin Login

1. Navigate to `http://localhost/Rangamadala/Login`
2. Enter your admin email
3. Enter your admin password
4. Click "Login"
5. You should be redirected to `/Admindashboard`

## Verify Admin Account

Run this SQL query to check existing admin accounts:

```sql
SELECT id, full_name, email, role FROM users WHERE role = 'admin';
```

## Security Notes

- ✅ Passwords are hashed using PHP's `PASSWORD_DEFAULT` algorithm (bcrypt)
- ✅ The login system validates passwords securely
- ✅ Admin access is protected with role-based checks
- ⚠️ Change the default admin password after first login
- ⚠️ Keep admin credentials secure and don't share

## Related Files

- Login Controller: `app/controllers/Login.php`
- Admin Dashboard: `app/controllers/Admindashboard.php`
- Login Model: `app/models/M_login.php`
- Login View: `app/views/login.view.php`

## Troubleshooting

**Issue**: "Invalid email or password"
- **Solution**: Double-check the email exists and role is set to "admin"

**Issue**: Redirect to home instead of admin dashboard
- **Solution**: Ensure the `role` field is exactly `'admin'` (case-sensitive)

**Issue**: Cannot log in at all
- **Solution**: Verify the database connection and that the users table exists
