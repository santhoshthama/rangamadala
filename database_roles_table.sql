-- Drama Role Management Schema

-- Run this script inside your Rangamadala database to provision or update
-- the tables aligned with the redesigned Role CRUD workflow.

-- ---------------------------------------------------------------------------
-- Drama Roles (core role records maintained by directors)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `drama_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `drama_id` int(11) NOT NULL COMMENT 'Reference to drama',
  `role_name` varchar(100) NOT NULL COMMENT 'Role/Character name',
  `role_description` text DEFAULT NULL COMMENT 'Role description and requirements',
  `role_type` enum('lead','supporting','ensemble','dancer','musician','other') DEFAULT 'supporting' COMMENT 'Type of role',
  `salary` decimal(10,2) DEFAULT NULL COMMENT 'Salary offered for this role',
  `positions_available` int(11) NOT NULL DEFAULT 1 COMMENT 'Number of positions for this role',
  `positions_filled` int(11) NOT NULL DEFAULT 0 COMMENT 'Number of positions already filled',
  `status` enum('open','closed','filled') NOT NULL DEFAULT 'open' COMMENT 'Role status',
  `requirements` text DEFAULT NULL COMMENT 'Specific requirements (age, experience, etc.)',
  `is_published` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Whether the vacancy is publicly published',
  `published_at` datetime DEFAULT NULL COMMENT 'Timestamp when the vacancy was published',
  `published_message` text DEFAULT NULL COMMENT 'Optional vacancy note shown to artists',
  `published_by` int(11) DEFAULT NULL COMMENT 'Director who last published the vacancy',
  `created_by` int(11) DEFAULT NULL COMMENT 'Director who created the role',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_drama_roles_drama_id` (`drama_id`),
  KEY `idx_drama_roles_created_by` (`created_by`),
  KEY `idx_drama_roles_status` (`status`),
  KEY `idx_drama_roles_is_published` (`is_published`),
  KEY `idx_drama_roles_published_by` (`published_by`),
  CONSTRAINT `drama_roles_ibfk_1` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drama_roles_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `drama_roles_ibfk_3` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Patch existing drama_roles table with new vacancy columns/indexes if needed
ALTER TABLE `drama_roles`
  ADD COLUMN IF NOT EXISTS `is_published` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Whether the vacancy is publicly published' AFTER `requirements`,
  ADD COLUMN IF NOT EXISTS `published_at` datetime DEFAULT NULL COMMENT 'Timestamp when the vacancy was published' AFTER `is_published`,
  ADD COLUMN IF NOT EXISTS `published_message` text DEFAULT NULL COMMENT 'Optional vacancy note shown to artists' AFTER `published_at`,
  ADD COLUMN IF NOT EXISTS `published_by` int(11) DEFAULT NULL COMMENT 'Director who last published the vacancy' AFTER `published_message`,
  ADD COLUMN IF NOT EXISTS `positions_available` int(11) NOT NULL DEFAULT 1 COMMENT 'Number of positions for this role' AFTER `salary`,
  ADD COLUMN IF NOT EXISTS `positions_filled` int(11) NOT NULL DEFAULT 0 COMMENT 'Number of positions already filled' AFTER `positions_available`,
  ADD COLUMN IF NOT EXISTS `status` enum('open','closed','filled') NOT NULL DEFAULT 'open' COMMENT 'Role status' AFTER `positions_filled`,
  ADD INDEX IF NOT EXISTS `idx_drama_roles_status` (`status`),
  ADD INDEX IF NOT EXISTS `idx_drama_roles_is_published` (`is_published`),
  ADD INDEX IF NOT EXISTS `idx_drama_roles_published_by` (`published_by`);

ALTER TABLE `drama_roles`
  DROP FOREIGN KEY IF EXISTS `drama_roles_ibfk_3`,
  ADD CONSTRAINT `drama_roles_ibfk_3` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- ---------------------------------------------------------------------------
-- Role Applications (artists applying into open vacancies)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `role_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT 'Reference to drama role',
  `artist_id` int(11) NOT NULL COMMENT 'Artist applying',
  `application_message` text DEFAULT NULL COMMENT 'Application message from artist',
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL COMMENT 'Director who reviewed',
  `profile_viewed_at` datetime DEFAULT NULL COMMENT 'When the reviewing director opened the artist profile',
  `profile_viewed_by` int(11) DEFAULT NULL COMMENT 'Director who confirmed the profile review',
  `interview_at` datetime DEFAULT NULL COMMENT 'Scheduled interview datetime',
  `interview_scheduled_at` datetime DEFAULT NULL COMMENT 'When the interview was scheduled',
  `interview_scheduled_by` int(11) DEFAULT NULL COMMENT 'Director who scheduled the interview',
  `interview_status` enum('pending','completed','cancelled') NOT NULL DEFAULT 'pending' COMMENT 'Interview lifecycle status',
  `interview_notes` text DEFAULT NULL COMMENT 'Optional interview notes or agenda',
  `interview_confirmation_status` enum('pending','confirmed','declined') NOT NULL DEFAULT 'pending' COMMENT 'Artist confirmation state',
  `interview_confirmed_at` datetime DEFAULT NULL COMMENT 'When artist responded to interview invite',
  `interview_confirmation_note` text DEFAULT NULL COMMENT 'Optional note from artist when confirming/declining',
  `interview_confirmation_seen_at` datetime DEFAULT NULL COMMENT 'When director acknowledged confirmation',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_application` (`role_id`,`artist_id`),
  KEY `idx_role_applications_role_id` (`role_id`),
  KEY `idx_role_applications_artist_id` (`artist_id`),
  KEY `idx_role_applications_reviewed_by` (`reviewed_by`),
  KEY `idx_role_applications_profile_viewed_by` (`profile_viewed_by`),
  KEY `idx_role_applications_interview_scheduled_by` (`interview_scheduled_by`),
  CONSTRAINT `role_applications_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `drama_roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_applications_ibfk_2` FOREIGN KEY (`artist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_applications_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `role_applications_ibfk_4` FOREIGN KEY (`profile_viewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `role_applications_ibfk_5` FOREIGN KEY (`interview_scheduled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `role_applications`
  ADD COLUMN IF NOT EXISTS `profile_viewed_at` datetime DEFAULT NULL COMMENT 'When the reviewing director opened the artist profile' AFTER `reviewed_by`,
  ADD COLUMN IF NOT EXISTS `profile_viewed_by` int(11) DEFAULT NULL COMMENT 'Director who confirmed the profile review' AFTER `profile_viewed_at`,
  ADD COLUMN IF NOT EXISTS `interview_at` datetime DEFAULT NULL COMMENT 'Scheduled interview datetime' AFTER `profile_viewed_by`,
  ADD COLUMN IF NOT EXISTS `interview_scheduled_at` datetime DEFAULT NULL COMMENT 'When the interview was scheduled' AFTER `interview_at`,
  ADD COLUMN IF NOT EXISTS `interview_scheduled_by` int(11) DEFAULT NULL COMMENT 'Director who scheduled the interview' AFTER `interview_scheduled_at`,
  ADD COLUMN IF NOT EXISTS `interview_status` enum('pending','completed','cancelled') NOT NULL DEFAULT 'pending' COMMENT 'Interview lifecycle status' AFTER `interview_scheduled_by`,
  ADD COLUMN IF NOT EXISTS `interview_notes` text DEFAULT NULL COMMENT 'Optional interview notes or agenda' AFTER `interview_status`,
  ADD COLUMN IF NOT EXISTS `interview_confirmation_status` enum('pending','confirmed','declined') NOT NULL DEFAULT 'pending' COMMENT 'Artist confirmation state' AFTER `interview_notes`,
  ADD COLUMN IF NOT EXISTS `interview_confirmed_at` datetime DEFAULT NULL COMMENT 'When artist responded to interview invite' AFTER `interview_confirmation_status`,
  ADD COLUMN IF NOT EXISTS `interview_confirmation_note` text DEFAULT NULL COMMENT 'Optional note from artist when confirming/declining' AFTER `interview_confirmed_at`,
  ADD COLUMN IF NOT EXISTS `interview_confirmation_seen_at` datetime DEFAULT NULL COMMENT 'When director acknowledged confirmation' AFTER `interview_confirmation_note`,
  ADD KEY IF NOT EXISTS `idx_role_applications_profile_viewed_by` (`profile_viewed_by`),
  ADD KEY IF NOT EXISTS `idx_role_applications_interview_scheduled_by` (`interview_scheduled_by`),
  ADD CONSTRAINT IF NOT EXISTS `role_applications_ibfk_4` FOREIGN KEY (`profile_viewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT IF NOT EXISTS `role_applications_ibfk_5` FOREIGN KEY (`interview_scheduled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- ---------------------------------------------------------------------------
-- Role Assignments (artists formally assigned to roles)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `role_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT 'Reference to drama role',
  `artist_id` int(11) NOT NULL COMMENT 'Assigned artist',
  `assigned_by` int(11) DEFAULT NULL COMMENT 'Director who assigned',
  `assigned_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','completed','terminated') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_assignment` (`role_id`,`artist_id`),
  KEY `idx_role_assignments_role_id` (`role_id`),
  KEY `idx_role_assignments_artist_id` (`artist_id`),
  KEY `idx_role_assignments_assigned_by` (`assigned_by`),
  CONSTRAINT `role_assignments_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `drama_roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_assignments_ibfk_2` FOREIGN KEY (`artist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_assignments_ibfk_3` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Role Requests (direct invites sent by directors to artists)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `role_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT 'Reference to drama role',
  `artist_id` int(11) NOT NULL COMMENT 'Artist being invited',
  `director_id` int(11) NOT NULL COMMENT 'Director sending the request',
  `status` enum('pending','interview','accepted','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `note` text DEFAULT NULL COMMENT 'Optional note with additional context',
  `interview_at` datetime DEFAULT NULL COMMENT 'Scheduled interview time (if any)',
  `requested_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `responded_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_artist_request` (`role_id`,`artist_id`),
  KEY `idx_role_requests_role_id` (`role_id`),
  KEY `idx_role_requests_artist_id` (`artist_id`),
  KEY `idx_role_requests_director_id` (`director_id`),
  KEY `idx_role_requests_status` (`status`),
  CONSTRAINT `role_requests_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `drama_roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_requests_ibfk_2` FOREIGN KEY (`artist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_requests_ibfk_3` FOREIGN KEY (`director_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
