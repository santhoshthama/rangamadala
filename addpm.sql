-- addpm.sql
-- One-time migration to enable Production Manager assignments
-- Run this against your `rangamandala_db` database.

START TRANSACTION;

-- Table: drama_manager_assignments
-- Tracks the current Production Manager for each drama (one active PM per drama)
CREATE TABLE IF NOT EXISTS `drama_manager_assignments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `drama_id` INT(11) NOT NULL COMMENT 'Reference to dramas.id',
  `manager_artist_id` INT(11) NOT NULL COMMENT 'Artist assigned as Production Manager',
  `assigned_by` INT(11) NOT NULL COMMENT 'Director who assigned the PM',
  `assigned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When the PM was assigned',
  `status` ENUM('active','removed') NOT NULL DEFAULT 'active' COMMENT 'Current or removed',
  `removed_at` DATETIME DEFAULT NULL COMMENT 'When the PM was removed (if applicable)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_drama_active_manager` (`drama_id`, `status`),
  KEY `idx_manager_artist` (`manager_artist_id`),
  KEY `idx_assigned_by` (`assigned_by`),
  CONSTRAINT `dma_dramas_fk` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dma_manager_fk` FOREIGN KEY (`manager_artist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dma_assigned_by_fk` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: drama_manager_requests
-- Tracks invitations sent by directors to artists to become PMs
CREATE TABLE IF NOT EXISTS `drama_manager_requests` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `drama_id` INT(11) NOT NULL COMMENT 'Reference to dramas.id',
  `artist_id` INT(11) NOT NULL COMMENT 'Artist invited to be PM',
  `director_id` INT(11) NOT NULL COMMENT 'Director who sent the request',
  `status` ENUM('pending','accepted','rejected','cancelled') NOT NULL DEFAULT 'pending' COMMENT 'Request state',
  `message` TEXT DEFAULT NULL COMMENT 'Optional note from director',
  `requested_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When the request was sent',
  `responded_at` DATETIME DEFAULT NULL COMMENT 'When the artist responded',
  `response_note` TEXT DEFAULT NULL COMMENT 'Optional note from artist on response',
  PRIMARY KEY (`id`),
  KEY `idx_dmr_drama` (`drama_id`),
  KEY `idx_dmr_artist` (`artist_id`),
  KEY `idx_dmr_director` (`director_id`),
  KEY `idx_dmr_status` (`status`),
  KEY `idx_pending_requests` (`artist_id`, `status`, `requested_at`),
  CONSTRAINT `dmr_dramas_fk` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dmr_artist_fk` FOREIGN KEY (`artist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dmr_director_fk` FOREIGN KEY (`director_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Clean up any NULL status values (safety if tables pre-exist without defaults)
UPDATE `drama_manager_assignments` SET `status` = 'removed' WHERE `status` IS NULL;
UPDATE `drama_manager_requests` SET `status` = 'cancelled' WHERE `status` IS NULL;

COMMIT;

-- How to run:
-- 1) Open phpMyAdmin, select `rangamandala_db`
-- 2) SQL tab → paste this script → Go
-- or: mysql -u <user> -p rangamandala_db < addpm.sql
