-- -------------------------------------------------------------------
-- Update script: role_applications interview + profile review fields
-- Run inside the Rangamadala database (e.g., USE rangamadala; )
-- -------------------------------------------------------------------

START TRANSACTION;

-- 1. Add workflow columns
ALTER TABLE `role_applications`
  ADD COLUMN `profile_viewed_at` DATETIME NULL DEFAULT NULL AFTER `reviewed_by`,
  ADD COLUMN `profile_viewed_by` INT(11) NULL DEFAULT NULL AFTER `profile_viewed_at`,
  ADD COLUMN `interview_at` DATETIME NULL DEFAULT NULL AFTER `profile_viewed_by`,
  ADD COLUMN `interview_scheduled_at` DATETIME NULL DEFAULT NULL AFTER `interview_at`,
  ADD COLUMN `interview_scheduled_by` INT(11) NULL DEFAULT NULL AFTER `interview_scheduled_at`,
  ADD COLUMN `interview_status` ENUM('pending','completed','cancelled') NOT NULL DEFAULT 'pending' AFTER `interview_scheduled_by`,
  ADD COLUMN `interview_notes` TEXT NULL DEFAULT NULL AFTER `interview_status`,
  ADD COLUMN `interview_confirmation_status` ENUM('pending','confirmed','declined') NOT NULL DEFAULT 'pending' AFTER `interview_notes`,
  ADD COLUMN `interview_confirmed_at` DATETIME NULL DEFAULT NULL AFTER `interview_confirmation_status`,
  ADD COLUMN `interview_confirmation_note` TEXT NULL DEFAULT NULL AFTER `interview_confirmed_at`,
  ADD COLUMN `interview_confirmation_seen_at` DATETIME NULL DEFAULT NULL AFTER `interview_confirmation_note`;

-- 2. Add supporting indexes
ALTER TABLE `role_applications`
  ADD INDEX `idx_role_applications_profile_viewed_by` (`profile_viewed_by`),
  ADD INDEX `idx_role_applications_interview_scheduled_by` (`interview_scheduled_by`);

-- 3. Add foreign key references to users table
ALTER TABLE `role_applications`
  ADD CONSTRAINT `role_applications_profile_viewed_fk`
    FOREIGN KEY (`profile_viewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `role_applications_interview_scheduled_fk`
    FOREIGN KEY (`interview_scheduled_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

COMMIT;
