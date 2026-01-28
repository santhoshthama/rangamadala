-- Add verification fields to users table if they don't exist

ALTER TABLE `users` ADD COLUMN `is_verified` tinyint(1) DEFAULT 0 AFTER `nic_photo`;
ALTER TABLE `users` ADD COLUMN `verification_status` enum('pending','approved','rejected') DEFAULT 'pending' AFTER `is_verified`;
ALTER TABLE `users` ADD COLUMN `rejection_reason` text DEFAULT NULL AFTER `verification_status`;
ALTER TABLE `users` ADD COLUMN `verified_at` timestamp NULL DEFAULT NULL AFTER `rejection_reason`;
ALTER TABLE `users` ADD COLUMN `verified_by_admin_id` int DEFAULT NULL AFTER `verified_at`;
