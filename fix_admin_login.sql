-- Fix Admin Login Issues
-- Run this SQL script in your rangamandala_db database

-- 1. Add missing verification columns
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `verified_by` int DEFAULT NULL AFTER `is_verified`,
ADD COLUMN IF NOT EXISTS `verified_at` timestamp NULL DEFAULT NULL AFTER `verified_by`,
ADD COLUMN IF NOT EXISTS `rejection_reason` text DEFAULT NULL AFTER `verified_at`;

-- 2. Update admin user with correct password hash and verification status
-- Password: Admin@123
UPDATE `users` 
SET 
    `password` = '$2y$10$ixmA.3bWo2tLw91a7ucTm.JvHsjNS0A5fF3KYmmgtkF2dznz8r6lG',
    `is_verified` = 1,
    `status` = 'active'
WHERE `email` = 'admin@rangamadala.com' AND `role` = 'admin';

-- 3. Verify the changes
SELECT 
    id,
    full_name,
    email,
    role,
    is_verified,
    status,
    created_at
FROM users 
WHERE role = 'admin';

-- ADMIN LOGIN CREDENTIALS:
-- Email: admin@rangamadala.com
-- Password: Admin@123
