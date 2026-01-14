-- Drama Roles Table
-- Run this SQL to add roles functionality to your database

CREATE TABLE IF NOT EXISTS `drama_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `drama_id` int(11) NOT NULL COMMENT 'Reference to drama',
  `role_name` varchar(100) NOT NULL COMMENT 'Role/Character name',
  `role_description` text COMMENT 'Role description and requirements',
  `role_type` enum('lead','supporting','ensemble','dancer','musician','other') DEFAULT 'supporting' COMMENT 'Type of role',
  `salary` decimal(10,2) DEFAULT NULL COMMENT 'Salary offered for this role',
  `positions_available` int DEFAULT 1 COMMENT 'Number of positions for this role',
  `positions_filled` int DEFAULT 0 COMMENT 'Number of positions already filled',
  `status` enum('open','closed','filled') DEFAULT 'open' COMMENT 'Role status',
  `requirements` text COMMENT 'Specific requirements (age, experience, etc.)',
  `created_by` int(11) DEFAULT NULL COMMENT 'Director who created the role',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `drama_id` (`drama_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `drama_roles_ibfk_1` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drama_roles_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Drama Role Applications Table (for artists applying to roles)
CREATE TABLE IF NOT EXISTS `role_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT 'Reference to drama role',
  `artist_id` int(11) NOT NULL COMMENT 'Artist applying',
  `application_message` text COMMENT 'Application message from artist',
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL COMMENT 'Director who reviewed',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_application` (`role_id`,`artist_id`),
  KEY `role_id` (`role_id`),
  KEY `artist_id` (`artist_id`),
  KEY `reviewed_by` (`reviewed_by`),
  CONSTRAINT `role_applications_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `drama_roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_applications_ibfk_2` FOREIGN KEY (`artist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_applications_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Drama Role Assignments Table (accepted artists)
CREATE TABLE IF NOT EXISTS `role_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT 'Reference to drama role',
  `artist_id` int(11) NOT NULL COMMENT 'Assigned artist',
  `assigned_by` int(11) DEFAULT NULL COMMENT 'Director who assigned',
  `assigned_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','completed','terminated') DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_assignment` (`role_id`,`artist_id`),
  KEY `role_id` (`role_id`),
  KEY `artist_id` (`artist_id`),
  KEY `assigned_by` (`assigned_by`),
  CONSTRAINT `role_assignments_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `drama_roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_assignments_ibfk_2` FOREIGN KEY (`artist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_assignments_ibfk_3` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
