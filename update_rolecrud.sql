-- Update script for Role CRUD redesign (January 17, 2026)
-- Run this in phpMyAdmin against your Rangamadala database to align the
-- existing schema with the latest role management features.
--
-- Recommended: back up your database before executing.

START TRANSACTION;

-- Ensure new vacancy publication columns exist on drama_roles
ALTER TABLE `drama_roles`
  ADD COLUMN IF NOT EXISTS `is_published` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Whether the vacancy is publicly published' AFTER `requirements`,
  ADD COLUMN IF NOT EXISTS `published_at` DATETIME DEFAULT NULL COMMENT 'Timestamp when the vacancy was published' AFTER `is_published`,
  ADD COLUMN IF NOT EXISTS `published_message` TEXT DEFAULT NULL COMMENT 'Optional vacancy note shown to artists' AFTER `published_at`,
  ADD COLUMN IF NOT EXISTS `published_by` INT(11) DEFAULT NULL COMMENT 'Director who last published the vacancy' AFTER `published_message`;

-- Harden existing column definitions
ALTER TABLE `drama_roles`
  MODIFY COLUMN `role_description` TEXT DEFAULT NULL COMMENT 'Role description and requirements',
  MODIFY COLUMN `salary` DECIMAL(10,2) DEFAULT NULL COMMENT 'Salary offered for this role',
  MODIFY COLUMN `positions_available` INT(11) NOT NULL DEFAULT 1 COMMENT 'Number of positions for this role',
  MODIFY COLUMN `positions_filled` INT(11) NOT NULL DEFAULT 0 COMMENT 'Number of positions already filled',
  MODIFY COLUMN `status` ENUM('open','closed','filled') NOT NULL DEFAULT 'open' COMMENT 'Role status',
  MODIFY COLUMN `requirements` TEXT DEFAULT NULL COMMENT 'Specific requirements (age, experience, etc.)';

-- Refresh supporting indexes on drama_roles
ALTER TABLE `drama_roles`
  DROP INDEX IF EXISTS `drama_id`,
  DROP INDEX IF EXISTS `created_by`,
  ADD INDEX IF NOT EXISTS `idx_drama_roles_drama_id` (`drama_id`),
  ADD INDEX IF NOT EXISTS `idx_drama_roles_created_by` (`created_by`),
  ADD INDEX IF NOT EXISTS `idx_drama_roles_status` (`status`),
  ADD INDEX IF NOT EXISTS `idx_drama_roles_is_published` (`is_published`),
  ADD INDEX IF NOT EXISTS `idx_drama_roles_published_by` (`published_by`);

-- Recreate foreign key for the new published_by column
ALTER TABLE `drama_roles`
  DROP FOREIGN KEY IF EXISTS `drama_roles_ibfk_3`;

ALTER TABLE `drama_roles`
  ADD CONSTRAINT `drama_roles_ibfk_3` FOREIGN KEY (`published_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- Normalise existing role data to satisfy new NOT NULL requirements
UPDATE `drama_roles`
SET `is_published` = 0
WHERE `is_published` IS NULL;

UPDATE `drama_roles`
SET `status` = 'open'
WHERE `status` IS NULL OR `status` NOT IN ('open','closed','filled');

UPDATE `drama_roles`
SET `positions_available` = 1
WHERE `positions_available` IS NULL OR `positions_available` < 1;

UPDATE `drama_roles`
SET `positions_filled` = 0
WHERE `positions_filled` IS NULL;

UPDATE `drama_roles` dr
LEFT JOIN `users` u ON dr.`published_by` = u.`id`
SET dr.`published_by` = NULL
WHERE dr.`published_by` IS NOT NULL AND u.`id` IS NULL;

UPDATE `drama_roles`
SET `positions_filled` = LEAST(`positions_filled`, `positions_available`);

-- Refresh indexes on role_applications to match naming convention
ALTER TABLE `role_applications`
  DROP INDEX IF EXISTS `role_id`,
  DROP INDEX IF EXISTS `artist_id`,
  DROP INDEX IF EXISTS `reviewed_by`,
  ADD INDEX IF NOT EXISTS `idx_role_applications_role_id` (`role_id`),
  ADD INDEX IF NOT EXISTS `idx_role_applications_artist_id` (`artist_id`),
  ADD INDEX IF NOT EXISTS `idx_role_applications_reviewed_by` (`reviewed_by`);

-- Refresh indexes on role_assignments to match naming convention
ALTER TABLE `role_assignments`
  DROP INDEX IF EXISTS `role_id`,
  DROP INDEX IF EXISTS `artist_id`,
  DROP INDEX IF EXISTS `assigned_by`,
  ADD INDEX IF NOT EXISTS `idx_role_assignments_role_id` (`role_id`),
  ADD INDEX IF NOT EXISTS `idx_role_assignments_artist_id` (`artist_id`),
  ADD INDEX IF NOT EXISTS `idx_role_assignments_assigned_by` (`assigned_by`);

-- Create role_requests table used for direct invitations
CREATE TABLE IF NOT EXISTS `role_requests` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `role_id` INT(11) NOT NULL COMMENT 'Reference to drama role',
  `artist_id` INT(11) NOT NULL COMMENT 'Artist being invited',
  `director_id` INT(11) NOT NULL COMMENT 'Director sending the request',
  `status` ENUM('pending','interview','accepted','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `note` TEXT DEFAULT NULL COMMENT 'Optional note with additional context',
  `interview_at` DATETIME DEFAULT NULL COMMENT 'Scheduled interview time (if any)',
  `requested_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `responded_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_artist_request` (`role_id`,`artist_id`),
  KEY `idx_role_requests_role_id` (`role_id`),
  KEY `idx_role_requests_artist_id` (`artist_id`),
  KEY `idx_role_requests_director_id` (`director_id`),
  KEY `idx_role_requests_status` (`status`),
  CONSTRAINT `role_requests_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `drama_roles`(`id`) ON DELETE CASCADE,
  CONSTRAINT `role_requests_ibfk_2` FOREIGN KEY (`artist_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `role_requests_ibfk_3` FOREIGN KEY (`director_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;

-- If any statement above fails, execute ROLLBACK; to revert changes made
-- within this transaction before addressing the issue and re-running.
