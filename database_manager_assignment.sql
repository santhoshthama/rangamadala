-- Production Manager Assignment System
-- Migration script to add PM assignment functionality
-- Date: January 23, 2026

-- Table to track current Production Manager assignments
CREATE TABLE IF NOT EXISTS `drama_manager_assignments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `drama_id` INT(11) NOT NULL COMMENT 'Reference to drama',
  `manager_artist_id` INT(11) NOT NULL COMMENT 'Artist assigned as Production Manager',
  `assigned_by` INT(11) NOT NULL COMMENT 'Director who assigned the PM',
  `assigned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When the PM was assigned',
  `status` ENUM('active','removed') NOT NULL DEFAULT 'active' COMMENT 'Assignment status',
  `removed_at` DATETIME DEFAULT NULL COMMENT 'When the PM was removed (if applicable)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_drama_active_manager` (`drama_id`, `status`),
  KEY `idx_manager_artist` (`manager_artist_id`),
  KEY `idx_assigned_by` (`assigned_by`),
  CONSTRAINT `drama_manager_assignments_ibfk_1` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drama_manager_assignments_ibfk_2` FOREIGN KEY (`manager_artist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drama_manager_assignments_ibfk_3` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add unique constraint to ensure only one active manager per drama
-- Note: The unique key above handles this with status included

-- Table to track PM requests (invitations)
CREATE TABLE IF NOT EXISTS `drama_manager_requests` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `drama_id` INT(11) NOT NULL COMMENT 'Reference to drama',
  `artist_id` INT(11) NOT NULL COMMENT 'Artist invited to be PM',
  `director_id` INT(11) NOT NULL COMMENT 'Director who sent the request',
  `status` ENUM('pending','accepted','rejected','cancelled') NOT NULL DEFAULT 'pending' COMMENT 'Request status',
  `message` TEXT DEFAULT NULL COMMENT 'Optional message from director',
  `requested_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When request was sent',
  `responded_at` DATETIME DEFAULT NULL COMMENT 'When artist responded',
  `response_note` TEXT DEFAULT NULL COMMENT 'Optional note from artist when responding',
  PRIMARY KEY (`id`),
  KEY `idx_drama_request` (`drama_id`),
  KEY `idx_artist_request` (`artist_id`),
  KEY `idx_director_request` (`director_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `drama_manager_requests_ibfk_1` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drama_manager_requests_ibfk_2` FOREIGN KEY (`artist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drama_manager_requests_ibfk_3` FOREIGN KEY (`director_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add index for pending requests lookup
ALTER TABLE `drama_manager_requests` 
ADD INDEX `idx_pending_requests` (`artist_id`, `status`, `requested_at`);

-- Note: This migration creates two tables:
-- 1. drama_manager_assignments: Tracks the current PM for each drama
-- 2. drama_manager_requests: Tracks PM invitations/requests

-- After assignment is accepted:
--   - drama_manager_requests status changes to 'accepted'
--   - A new record is created in drama_manager_assignments with status 'active'
--   - Any previous active assignment for that drama is set to 'removed'
