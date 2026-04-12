-- Rangamadala Unified SQL (deduplicated merge)
-- Generated: 2026-04-02

-- ========================
-- 1) Core Database Setup
-- ========================
-- Rangamadala Database Setup
-- Run this SQL script in your rangamandala_db database

-- Create users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','artist','audience','service_provider') NOT NULL DEFAULT 'audience',
  `nic_photo` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `years_experience` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Universal profile fields used by the merged profile flow
ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `bio` text DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `location` varchar(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `website` varchar(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `nic_number` varchar(20) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `nic_photo_back` varchar(255) DEFAULT NULL,
  ADD INDEX IF NOT EXISTS `idx_users_location` (`location`);

-- Create categories table
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create dramas table
CREATE TABLE IF NOT EXISTS `dramas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `drama_name` varchar(255) NOT NULL COMMENT 'Drama name as in public performance board certificate',
  `certificate_number` varchar(100) NOT NULL COMMENT 'Public performance certificate number',
  `owner_name` varchar(255) NOT NULL COMMENT 'Owner name',
  `description` text DEFAULT NULL COMMENT 'Artist provided synopsis for the drama',
  `certificate_image` varchar(255) DEFAULT NULL COMMENT 'Image of public performance board certificate',
  `created_by` int(11) DEFAULT NULL,
  `creator_artist_id` int(11) DEFAULT NULL COMMENT 'The artist who is the director',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `certificate_number` (`certificate_number`),
  KEY `created_by` (`created_by`),
  KEY `creator_artist_id` (`creator_artist_id`),
  CONSTRAINT `dramas_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dramas_ibfk_3` FOREIGN KEY (`creator_artist_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample categories
INSERT INTO `categories` (`name`) VALUES
('Classical Drama'),
('Musical Drama'),
('Comedy Drama'),
('Traditional Dance'),
('Modern Theatre'),
('Street Drama'),
('Folk Theatre'),
('Experimental Theatre')
ON DUPLICATE KEY UPDATE name=name;

-- Insert sample dramas (optional - remove if not needed)
INSERT INTO `dramas` (`drama_name`, `certificate_number`, `owner_name`, `description`, `certificate_image`, `created_by`, `creator_artist_id`) VALUES
('Maname', 'PPB-2025-001', 'Chandrasena Perera', 'Iconic Sinhala stage drama Maname.', NULL, NULL, NULL),
('Sinhabahu', 'PPB-2025-002', 'Ediriweera Sarachchandra', 'Legendary drama about King Sinhabahu and Princess Suppadevi.', NULL, NULL, NULL)
ON DUPLICATE KEY UPDATE drama_name=VALUES(drama_name);



-- =====================================================================
-- DRAMA MANAGEMENT TABLES
-- =====================================================================

-- Drama Roles (for casting/auditions)
CREATE TABLE IF NOT EXISTS `drama_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `drama_id` int(11) NOT NULL COMMENT 'Reference to drama',
  `role_name` varchar(100) NOT NULL COMMENT 'Role/Character name',
  `role_description` text DEFAULT NULL COMMENT 'Role description and requirements',
  `role_type` enum('lead','supporting','ensemble','dancer','musician','other') DEFAULT 'supporting' COMMENT 'Type of role',
  `salary` decimal(10,2) DEFAULT NULL COMMENT 'Salary offered for this role',
  `positions_available` int(11) NOT NULL DEFAULT 1,
  `positions_filled` int(11) NOT NULL DEFAULT 0,
  `status` enum('open','closed','filled') NOT NULL DEFAULT 'open',
  `requirements` text DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `published_message` text DEFAULT NULL,
  `published_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
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

-- Role Applications (artists applying into open vacancies)
CREATE TABLE IF NOT EXISTS `role_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `application_message` text DEFAULT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_application` (`role_id`,`artist_id`),
  KEY `idx_role_applications_role_id` (`role_id`),
  KEY `idx_role_applications_artist_id` (`artist_id`),
  KEY `idx_role_applications_reviewed_by` (`reviewed_by`),
  CONSTRAINT `role_applications_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `drama_roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_applications_ibfk_2` FOREIGN KEY (`artist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_applications_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Role Assignments (artists formally assigned to roles)
CREATE TABLE IF NOT EXISTS `role_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `assigned_by` int(11) DEFAULT NULL,
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

-- Role Requests (direct invites sent by directors to artists)
CREATE TABLE IF NOT EXISTS `role_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `director_id` int(11) NOT NULL,
  `status` enum('pending','interview','accepted','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `note` text DEFAULT NULL,
  `interview_at` datetime DEFAULT NULL,
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
  KEY `idx_pending_requests` (`artist_id`, `status`, `requested_at`),
  CONSTRAINT `drama_manager_requests_ibfk_1` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drama_manager_requests_ibfk_2` FOREIGN KEY (`artist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drama_manager_requests_ibfk_3` FOREIGN KEY (`director_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Bios (for audience members)
CREATE TABLE IF NOT EXISTS `user_bios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL UNIQUE,
  `bio` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `user_bios_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Drama Budgets
CREATE TABLE IF NOT EXISTS `drama_budgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `drama_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `allocated_amount` decimal(10,2) NOT NULL,
  `spent_amount` decimal(10,2) DEFAULT 0,
  `status` enum('pending','approved','completed','cancelled') DEFAULT 'pending',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `drama_id` (`drama_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `drama_budgets_ibfk_1` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drama_budgets_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




-- Service Provider and Services Tables

-- Create serviceprovider table
CREATE TABLE IF NOT EXISTS `serviceprovider` (
  `user_id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `professional_title` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `nic_number` varchar(20) DEFAULT NULL,
  `social_media_link` varchar(255) DEFAULT NULL,
  `years_experience` int DEFAULT NULL,
  `professional_summary` text,
  `availability` tinyint(1) DEFAULT '1',
  `availability_notes` varchar(255) DEFAULT NULL,
  `nic_photo_front` varchar(255) DEFAULT NULL,
  `nic_photo_back` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  CONSTRAINT `serviceprovider_ibfk_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create service_types table
CREATE TABLE IF NOT EXISTS `service_types` (
  `service_type_id` int NOT NULL AUTO_INCREMENT,
  `service_type` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`service_type_id`),
  UNIQUE KEY `service_type` (`service_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert service types
INSERT INTO `service_types` (`service_type`) VALUES
('Theater Production'),
('Lighting Design'),
('Sound Systems'),
('Video Production'),
('Set Design'),
('Costume Design'),
('Other')
ON DUPLICATE KEY UPDATE service_type=service_type;

-- Create services table
CREATE TABLE IF NOT EXISTS `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `provider_id` int NOT NULL,
  `service_type_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `provider_id` (`provider_id`),
  KEY `service_type_id` (`service_type_id`),
  CONSTRAINT `services_ibfk_provider` FOREIGN KEY (`provider_id`) REFERENCES `serviceprovider` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `services_ibfk_service_type` FOREIGN KEY (`service_type_id`) REFERENCES `service_types` (`service_type_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Theater Production details
CREATE TABLE IF NOT EXISTS `service_theater_details` (
  `service_id` int NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `rate_type` enum('hourly','daily') DEFAULT 'hourly',
  `description` text,
  `theatre_name` varchar(255) DEFAULT NULL,
  `seating_capacity` int DEFAULT NULL,
  `stage_dimensions` varchar(255) DEFAULT NULL,
  `stage_type` varchar(100) DEFAULT NULL,
  `available_facilities` text,
  `technical_facilities` text,
  `equipment_rent` text,
  `stage_crew_available` varchar(10) DEFAULT NULL,
  `location_address` text,
  `theatre_photos` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`service_id`),
  CONSTRAINT `theater_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Lighting Design details
CREATE TABLE IF NOT EXISTS `service_lighting_details` (
  `service_id` int NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `rate_type` enum('hourly','daily') DEFAULT 'hourly',
  `description` text,
  `lighting_equipment_provided` text,
  `max_stage_size` varchar(255) DEFAULT NULL,
  `lighting_design_service` varchar(10) DEFAULT NULL,
  `lighting_crew_available` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`service_id`),
  CONSTRAINT `lighting_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sound Systems details
CREATE TABLE IF NOT EXISTS `service_sound_details` (
  `service_id` int NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `rate_type` enum('hourly','daily') DEFAULT 'hourly',
  `description` text,
  `sound_equipment_provided` text,
  `max_audience_size` int DEFAULT NULL,
  `sound_effects_handling` varchar(10) DEFAULT NULL,
  `sound_engineer_included` varchar(10) DEFAULT NULL,
  `equipment_brands` text,
  PRIMARY KEY (`service_id`),
  CONSTRAINT `sound_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Video Production details
CREATE TABLE IF NOT EXISTS `service_video_details` (
  `service_id` int NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `rate_type` enum('hourly','daily') DEFAULT 'hourly',
  `description` text,
  `services_offered` text,
  `equipment_used` text,
  `num_crew_members` int DEFAULT NULL,
  `editing_software` varchar(255) DEFAULT NULL,
  `drone_service_available` varchar(10) DEFAULT NULL,
  `max_video_resolution` varchar(50) DEFAULT NULL,
  `photo_editing_included` varchar(10) DEFAULT NULL,
  `delivery_time` varchar(255) DEFAULT NULL,
  `raw_footage_provided` varchar(10) DEFAULT NULL,
  `portfolio_links` text,
  `sample_videos` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`service_id`),
  CONSTRAINT `video_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Set Design details
CREATE TABLE IF NOT EXISTS `service_set_details` (
  `service_id` int NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `rate_type` enum('hourly','daily') DEFAULT 'hourly',
  `description` text,
  `types_of_sets_designed` text,
  `set_construction_provided` varchar(10) DEFAULT NULL,
  `stage_installation_support` varchar(10) DEFAULT NULL,
  `max_stage_size_supported` varchar(255) DEFAULT NULL,
  `materials_used` text,
  `sample_set_designs` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`service_id`),
  CONSTRAINT `set_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Costume Design details
CREATE TABLE IF NOT EXISTS `service_costume_details` (
  `service_id` int NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `rate_type` enum('hourly','daily') DEFAULT 'hourly',
  `description` text,
  `types_of_costumes_provided` text,
  `custom_costume_design_available` varchar(10) DEFAULT NULL,
  `available_sizes` varchar(100) DEFAULT NULL,
  `alterations_provided` varchar(10) DEFAULT NULL,
  `number_of_costumes_available` int DEFAULT NULL,
  PRIMARY KEY (`service_id`),
  CONSTRAINT `costume_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Makeup & Hair details
CREATE TABLE IF NOT EXISTS `service_makeup_details` (
  `service_id` int NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `rate_type` enum('hourly','daily') DEFAULT 'hourly',
  `description` text,
  `type_of_makeup_services` text,
  `experience_stage_makeup_years` int DEFAULT NULL,
  `character_based_makeup_available` varchar(10) DEFAULT NULL,
  `can_handle_full_cast` varchar(10) DEFAULT NULL,
  `maximum_actors_per_show` int DEFAULT NULL,
  `bring_own_makeup_kit` varchar(10) DEFAULT NULL,
  `onsite_service_available` varchar(10) DEFAULT NULL,
  `touchup_service_during_show` varchar(10) DEFAULT NULL,
  `traditional_cultural_makeup_expertise` text,
  `sample_makeup_photos` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`service_id`),
  CONSTRAINT `makeup_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Other Services details
CREATE TABLE IF NOT EXISTS `service_other_details` (
  `service_id` int NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `rate_type` enum('hourly','daily') DEFAULT 'hourly',
  `description` text,
  `service_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`service_id`),
  CONSTRAINT `other_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create projects table
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `provider_id` int NOT NULL,
  `year` int DEFAULT NULL,
  `project_name` varchar(100) DEFAULT NULL,
  `services_provided` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `provider_id` (`provider_id`),
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `serviceprovider` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Create service_requests table 
CREATE TABLE IF NOT EXISTS `service_requests` (
  `id` int(11) NOT NULL,
  `drama_id` int(11) DEFAULT NULL,
  `provider_id` int(11) NOT NULL,
  `requested_by` int(11) DEFAULT NULL,
  `requester_name` varchar(100) NOT NULL,
  `requester_email` varchar(100) NOT NULL,
  `requester_phone` varchar(20) NOT NULL,
  `drama_name` varchar(255) NOT NULL,
  `service_type` varchar(255) NOT NULL,
  `service_required` varchar(255) DEFAULT NULL,
  `service_date` date DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `budget` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `service_details_json` longtext DEFAULT NULL COMMENT 'JSON object containing service-specific details',
  `notes` text DEFAULT NULL,
  `provider_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `status` enum('pending','accepted','rejected','completed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','partially_paid','paid') DEFAULT 'unpaid',
  `accepted_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ensure service_requests.id is FK-ready before creating provider_availability
SET @sr_has_pk_pre := (
  SELECT COUNT(*)
  FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'service_requests'
    AND CONSTRAINT_TYPE = 'PRIMARY KEY'
);
SET @sr_add_pk_pre_sql := IF(@sr_has_pk_pre = 0,
  'ALTER TABLE service_requests ADD PRIMARY KEY (id)',
  'SELECT 1'
);
PREPARE stmt FROM @sr_add_pk_pre_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sr_is_auto_pre := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'service_requests'
    AND COLUMN_NAME = 'id'
    AND EXTRA LIKE '%auto_increment%'
);
SET @sr_auto_pre_sql := IF(@sr_is_auto_pre = 0,
  'ALTER TABLE service_requests MODIFY COLUMN id INT(11) NOT NULL AUTO_INCREMENT',
  'SELECT 1'
);
PREPARE stmt FROM @sr_auto_pre_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sr_id_index_pre := (
  SELECT COUNT(*)
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'service_requests'
    AND COLUMN_NAME = 'id'
);
SET @sr_add_idx_pre_sql := IF(@sr_id_index_pre = 0,
  'CREATE INDEX idx_service_requests_id ON service_requests (id)',
  'SELECT 1'
);
PREPARE stmt FROM @sr_add_idx_pre_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- Add provider_availability table to store booked dates
CREATE TABLE IF NOT EXISTS `provider_availability` (
  `id` int NOT NULL AUTO_INCREMENT,
  `provider_id` int NOT NULL,
  `available_date` date NOT NULL,
  `status` enum('available','booked') NOT NULL DEFAULT 'available',
  `description` text,
  `booked_for` varchar(255) DEFAULT NULL,
  `booking_details` text,
  `service_request_id` int DEFAULT NULL,
  `added_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `booked_on` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `provider_date` (`provider_id`, `available_date`),
  KEY `provider_id` (`provider_id`),
  KEY `available_date` (`available_date`),
  CONSTRAINT `availability_ibfk_provider` FOREIGN KEY (`provider_id`) REFERENCES `serviceprovider` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `availability_ibfk_request` FOREIGN KEY (`service_request_id`) REFERENCES `service_requests` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================
-- 2) Admin Content Management Migration
-- ======================================
-- =============================================
-- CONTENT MANAGEMENT TABLES FOR ADMIN DASHBOARD
-- Run this SQL in phpMyAdmin to create the tables
-- =============================================

-- Swiper/Drama Slides Table
CREATE TABLE IF NOT EXISTS `swiper_slides` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `image_path` VARCHAR(255) NOT NULL,
    `title` VARCHAR(100) DEFAULT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `drama_id` INT DEFAULT NULL COMMENT 'Link to dramas table for View More button',
    `display_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add drama_id column if table already exists (run separately if needed)
-- ALTER TABLE `swiper_slides` ADD COLUMN `drama_id` INT DEFAULT NULL AFTER `description`;

-- Gallery/Stage Highlights Table
CREATE TABLE IF NOT EXISTS `gallery_images` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `image_path` VARCHAR(255) NOT NULL,
    `title` VARCHAR(100) DEFAULT NULL,
    `alt_text` VARCHAR(100) DEFAULT NULL,
    `display_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Testimonials Table
CREATE TABLE IF NOT EXISTS `testimonials` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `role` VARCHAR(50) NOT NULL COMMENT 'Artist, Director, Audience, Service Provider',
    `message` TEXT NOT NULL,
    `image_path` VARCHAR(255) DEFAULT NULL,
    `rating` INT DEFAULT 5 COMMENT '1-5 star rating',
    `display_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default swiper slides (existing images)
INSERT INTO `swiper_slides` (`image_path`, `title`, `display_order`, `is_active`) VALUES
('assets/images/drama1.png', 'Drama 1', 1, 1),
('assets/images/drama2.png', 'Drama 2', 2, 1),
('assets/images/drama3.png', 'Drama 3', 3, 1),
('assets/images/drama4.png', 'Drama 4', 4, 1),
('assets/images/drama5.png', 'Drama 5', 5, 1);

-- Insert default gallery images (existing images)
INSERT INTO `gallery_images` (`image_path`, `title`, `alt_text`, `display_order`, `is_active`) VALUES
('assets/images/stagePer.png', 'Stage Performance', 'Stage Performance', 1, 1),
('assets/images/Rehersal.png', 'Rehearsal', 'Rehearsal', 2, 1),
('assets/images/AudienceView.png', 'Audience View', 'Audience View', 3, 1);

-- Insert default testimonials
INSERT INTO `testimonials` (`name`, `role`, `message`, `image_path`, `rating`, `display_order`, `is_active`) VALUES
('Nuwan', 'Artist', 'Rangamadala helped me find my first acting opportunity. The platform is amazing for upcoming artists!', 'https://i.postimg.cc/VNs6dtw4/profile2.jpg', 5, 1, 1),
('Nirahsha', 'Director', 'Managing my stage team became so much easier. Great platform for directors and managers!', 'https://i.postimg.cc/XYkqj8Rp/profile3.jpg', 5, 2, 1),
('Tharindu', 'Audience', 'As an audience member, I can easily book shows and discover new performances every week.', 'https://i.postimg.cc/g0M0R0kp/profile1.jpg', 5, 3, 1);


-- =================================
-- 3) User Verification Migration
-- =================================
-- =====================================================================
-- VERIFICATION SYSTEM DATABASE MIGRATION
-- Run this SQL script to add/update verification fields in users table
-- =====================================================================

-- Add verification columns to users table if they don't exist
ALTER TABLE `users` 
    ADD COLUMN IF NOT EXISTS `is_verified` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Whether user is verified (1=yes, 0=no)',
    ADD COLUMN IF NOT EXISTS `verification_status` ENUM('pending', 'approved', 'rejected') DEFAULT 'approved' COMMENT 'Current verification status',
    ADD COLUMN IF NOT EXISTS `rejection_reason` TEXT DEFAULT NULL COMMENT 'Reason for rejection if status is rejected',
  ADD COLUMN IF NOT EXISTS `verified_by_admin_id` INT(11) DEFAULT NULL COMMENT 'Admin user ID who verified/rejected',
    ADD COLUMN IF NOT EXISTS `verified_by` INT(11) DEFAULT NULL COMMENT 'Admin user ID who verified',
    ADD COLUMN IF NOT EXISTS `verified_at` DATETIME DEFAULT NULL COMMENT 'Timestamp of verification action';

-- Add foreign key for verified_by if not exists
-- First check if it exists and drop if needed, then recreate
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
                  WHERE CONSTRAINT_SCHEMA = DATABASE() 
                  AND TABLE_NAME = 'users' 
                  AND CONSTRAINT_NAME = 'fk_users_verified_by');

-- Add index on verified_by for better performance
CREATE INDEX IF NOT EXISTS idx_users_verified_by ON users(verified_by);
CREATE INDEX IF NOT EXISTS idx_users_verified_by_admin_id ON users(verified_by_admin_id);
CREATE INDEX IF NOT EXISTS idx_users_verification_status ON users(verification_status);
CREATE INDEX IF NOT EXISTS idx_users_is_verified ON users(is_verified);

-- Backward compatibility: if older column has values, copy them to the column used by current app logic
UPDATE users
SET verified_by_admin_id = verified_by
WHERE verified_by_admin_id IS NULL
  AND verified_by IS NOT NULL;

-- Update existing artist and service_provider accounts that don't have verification status set
-- Set them to approved if is_verified = 1, otherwise pending
UPDATE users 
SET verification_status = 'approved', is_verified = 1 
WHERE role IN ('artist', 'service_provider') 
AND verification_status IS NULL 
AND is_verified = 1;

UPDATE users 
SET verification_status = 'pending', is_verified = 0 
WHERE role IN ('artist', 'service_provider') 
AND verification_status IS NULL 
AND is_verified = 0;

-- Ensure audience users are always verified (they don't need admin approval)
UPDATE users 
SET is_verified = 1, verification_status = 'approved' 
WHERE role = 'audience';

-- Ensure admin users are always verified
UPDATE users 
SET is_verified = 1, verification_status = 'approved' 
WHERE role = 'admin';

-- =====================================================================
-- VERIFICATION STATUS VIEW (Optional - for reporting)
-- =====================================================================
CREATE OR REPLACE VIEW verification_summary AS
SELECT 
    role,
    verification_status,
    COUNT(*) as count,
    MAX(created_at) as latest_registration
FROM users
WHERE role IN ('artist', 'service_provider')
GROUP BY role, verification_status
ORDER BY role, verification_status;

-- =====================================================================
-- SAMPLE QUERY: Get pending verifications with details
-- =====================================================================
-- SELECT 
--     u.id, u.full_name, u.email, u.phone, u.role, 
--     u.nic_photo, u.created_at, u.verification_status,
--     sp.professional_title, sp.nic_photo_front, sp.nic_photo_back
-- FROM users u
-- LEFT JOIN serviceprovider sp ON u.id = sp.user_id
-- WHERE u.verification_status = 'pending' 
-- AND u.role IN ('artist', 'service_provider')
-- ORDER BY u.created_at ASC;



-- =====================================
-- 4) Service Type ID Data Migration
-- =====================================
-- Migration: Ensure services table has service_type_id and populated data
SET @db := DATABASE();

-- Add the service_type_id column if it does not exist
SET @col_exists := (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'services' 
    AND COLUMN_NAME = 'service_type_id'
);

SET @add_col := IF(@col_exists = 0, 
    'ALTER TABLE services ADD COLUMN service_type_id INT NULL AFTER provider_id', 
    'SELECT 1'
);
PREPARE stmt FROM @add_col;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add supporting index if it doesn't exist
SET @idx_exists := (
    SELECT COUNT(*) 
    FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'services' 
    AND INDEX_NAME = 'idx_services_service_type_id'
);

SET @add_idx := IF(@idx_exists = 0, 
    'CREATE INDEX idx_services_service_type_id ON services (service_type_id)', 
    'SELECT 1'
);
PREPARE stmt FROM @add_idx;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create or confirm the foreign key to service_types
-- First drop existing constraint if it exists, then recreate
SET @fk_exists := (
    SELECT COUNT(*) 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'services' 
    AND CONSTRAINT_NAME = 'services_ibfk_service_type'
);

SET @drop_fk := IF(@fk_exists > 0, 
    'ALTER TABLE services DROP FOREIGN KEY services_ibfk_service_type', 
    'SELECT 1'
);
PREPARE stmt FROM @drop_fk;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Now add the foreign key
ALTER TABLE services
    ADD CONSTRAINT services_ibfk_service_type
        FOREIGN KEY (service_type_id)
        REFERENCES service_types (service_type_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE;

-- Seed core service types in case they are missing
INSERT INTO service_types (service_type)
SELECT t.name
FROM (
    SELECT 'Theater Production' AS name UNION ALL
    SELECT 'Lighting Design' UNION ALL
    SELECT 'Sound Systems' UNION ALL
    SELECT 'Video Production' UNION ALL
    SELECT 'Set Design' UNION ALL
    SELECT 'Costume Design' UNION ALL
    SELECT 'Makeup & Hair' UNION ALL
    SELECT 'Other'
) AS t
LEFT JOIN service_types st ON LOWER(st.service_type) = LOWER(t.name)
WHERE st.service_type_id IS NULL;

-- Capture any custom entries recorded under other services
INSERT INTO service_types (service_type)
SELECT DISTINCT sod.service_type
FROM service_other_details sod
LEFT JOIN service_types st ON LOWER(st.service_type) = LOWER(sod.service_type)
WHERE sod.service_type IS NOT NULL
  AND st.service_type_id IS NULL;

-- Backfill from legacy services.service_type column when available
SET @legacy_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db
      AND TABLE_NAME = 'services'
      AND COLUMN_NAME = 'service_type'
);

SET @sql := IF(
    @legacy_col > 0,
    'UPDATE services s JOIN service_types st ON LOWER(st.service_type) = LOWER(s.service_type)
     SET s.service_type_id = st.service_type_id
     WHERE s.service_type_id IS NULL AND s.service_type IS NOT NULL;',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Backfill using detail tables
UPDATE services s
JOIN service_theater_details d ON d.service_id = s.id
JOIN service_types st ON LOWER(st.service_type) = 'theater production'
SET s.service_type_id = st.service_type_id
WHERE s.service_type_id IS NULL;

UPDATE services s
JOIN service_lighting_details d ON d.service_id = s.id
JOIN service_types st ON LOWER(st.service_type) = 'lighting design'
SET s.service_type_id = st.service_type_id
WHERE s.service_type_id IS NULL;

UPDATE services s
JOIN service_sound_details d ON d.service_id = s.id
JOIN service_types st ON LOWER(st.service_type) = 'sound systems'
SET s.service_type_id = st.service_type_id
WHERE s.service_type_id IS NULL;

UPDATE services s
JOIN service_video_details d ON d.service_id = s.id
JOIN service_types st ON LOWER(st.service_type) = 'video production'
SET s.service_type_id = st.service_type_id
WHERE s.service_type_id IS NULL;

UPDATE services s
JOIN service_set_details d ON d.service_id = s.id
JOIN service_types st ON LOWER(st.service_type) = 'set design'
SET s.service_type_id = st.service_type_id
WHERE s.service_type_id IS NULL;

UPDATE services s
JOIN service_costume_details d ON d.service_id = s.id
JOIN service_types st ON LOWER(st.service_type) = 'costume design'
SET s.service_type_id = st.service_type_id
WHERE s.service_type_id IS NULL;

UPDATE services s
JOIN service_makeup_details d ON d.service_id = s.id
JOIN service_types st ON LOWER(st.service_type) = 'makeup & hair'
SET s.service_type_id = st.service_type_id
WHERE s.service_type_id IS NULL;

UPDATE services s
JOIN service_other_details d ON d.service_id = s.id
JOIN service_types st ON LOWER(st.service_type) = LOWER(d.service_type)
SET s.service_type_id = st.service_type_id
WHERE s.service_type_id IS NULL
  AND d.service_type IS NOT NULL;

-- Clean up any rows still lacking a type by setting them to the generic bucket
UPDATE services s
JOIN service_types st ON LOWER(st.service_type) = 'other'
SET s.service_type_id = st.service_type_id
WHERE s.service_type_id IS NULL;

-- =====================================
-- 5) Built-in Admin Account Seed
-- =====================================
-- Login credentials:
-- Email: rangamadala@admin.com
-- Password: admin@2003
-- Note: password is stored as bcrypt hash.
INSERT INTO users (
  full_name,
  email,
  password,
  phone,
  role,
  nic_photo,
  is_verified,
  verification_status,
  verified_at
) VALUES (
  'System Administrator',
  'rangamadala@admin.com',
  '$2y$10$70F5ytuaGcMIfW6VUoidGeN6mWePXajJqUpeUjF8Uylzfra5hHoXu',
  '+94701234567',
  'admin',
  NULL,
  1,
  'approved',
  NOW()
)
ON DUPLICATE KEY UPDATE
  full_name = VALUES(full_name),
  password = VALUES(password),
  phone = VALUES(phone),
  role = 'admin',
  is_verified = 1,
  verification_status = 'approved',
  rejection_reason = NULL,
  verified_at = NOW();


-- =====================================
-- 6) Consolidated Missing Migrations
-- =====================================

-- Drama services used by production manager and provider request flow
CREATE TABLE IF NOT EXISTS `drama_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `drama_id` int(11) NOT NULL COMMENT 'Reference to drama',
  `service_type` varchar(100) NOT NULL COMMENT 'Type of service (Theater Production, Lighting Design, etc.)',
  `budget` decimal(12,2) DEFAULT NULL COMMENT 'Expected budget for this service type',
  `description` text DEFAULT NULL COMMENT 'Description or requirements for this service',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_drama_service` (`drama_id`, `service_type`),
  KEY `idx_drama_services_drama_id` (`drama_id`),
  KEY `idx_drama_services_service_type` (`service_type`),
  CONSTRAINT `drama_services_ibfk_1` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Artist drama creation approval workflow
CREATE TABLE IF NOT EXISTS `drama_creation_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `drama_name` varchar(255) NOT NULL,
  `certificate_number` varchar(100) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `certificate_image` varchar(255) DEFAULT NULL,
  `requested_by` int(11) NOT NULL,
  `status` enum('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_drama_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dcr_certificate_status` (`certificate_number`, `status`),
  KEY `idx_dcr_requested_by` (`requested_by`),
  KEY `idx_dcr_status` (`status`),
  KEY `idx_dcr_reviewed_by` (`reviewed_by`),
  KEY `idx_dcr_created_drama_id` (`created_drama_id`),
  CONSTRAINT `fk_dcr_requested_by` FOREIGN KEY (`requested_by`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_dcr_reviewed_by` FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_dcr_created_drama_id` FOREIGN KEY (`created_drama_id`) REFERENCES `dramas`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audience ratings and reviews for dramas
CREATE TABLE IF NOT EXISTS `drama_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `drama_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `comment` text DEFAULT NULL,
  `helpful_count` int(11) NOT NULL DEFAULT 0,
  `is_helpful` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_drama_user_rating` (`drama_id`, `user_id`),
  KEY `idx_drama_ratings_drama` (`drama_id`),
  KEY `idx_drama_ratings_user` (`user_id`),
  KEY `idx_drama_ratings_rating` (`rating`),
  CONSTRAINT `fk_drama_ratings_drama` FOREIGN KEY (`drama_id`) REFERENCES `dramas`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_drama_ratings_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_drama_ratings_rating` CHECK (`rating` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Director schedule management (rehearsal/interview/meeting/performance)
CREATE TABLE IF NOT EXISTS `drama_schedules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `drama_id` int NOT NULL,
  `event_type` enum('rehearsal', 'interview', 'meeting', 'performance') NOT NULL DEFAULT 'rehearsal',
  `event_title` varchar(255) NOT NULL,
  `event_description` text DEFAULT NULL,
  `scheduled_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `venue` varchar(255) NOT NULL,
  `role_id` int DEFAULT NULL,
  `status` enum('scheduled', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'scheduled',
  `participants` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_drama_date` (`drama_id`, `scheduled_date`),
  KEY `idx_drama_status` (`drama_id`, `status`),
  KEY `idx_scheduled_date` (`scheduled_date`),
  CONSTRAINT `drama_schedules_ibfk_1` FOREIGN KEY (`drama_id`) REFERENCES `dramas`(`id`) ON DELETE CASCADE,
  CONSTRAINT `drama_schedules_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `drama_roles`(`id`) ON DELETE SET NULL,
  CONSTRAINT `drama_schedules_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Artist notifications
CREATE TABLE IF NOT EXISTS `artist_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'The artist receiving the notification',
  `drama_id` int(11) DEFAULT NULL COMMENT 'Related drama',
  `type` varchar(50) NOT NULL COMMENT 'Notification type',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notifications_user` (`user_id`),
  KEY `idx_notifications_user_read` (`user_id`, `is_read`),
  KEY `idx_notifications_drama` (`drama_id`),
  KEY `idx_notifications_created` (`created_at`),
  CONSTRAINT `artist_notifications_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `artist_notifications_fk_drama` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Director publish workflow and audience showing fields
ALTER TABLE `dramas`
  ADD COLUMN IF NOT EXISTS `category_id` int(11) DEFAULT NULL AFTER `description`,
  ADD COLUMN IF NOT EXISTS `public_description` text DEFAULT NULL AFTER `certificate_image`,
  ADD COLUMN IF NOT EXISTS `genre` varchar(100) DEFAULT NULL AFTER `public_description`,
  ADD COLUMN IF NOT EXISTS `language` varchar(50) DEFAULT NULL AFTER `genre`,
  ADD COLUMN IF NOT EXISTS `duration_minutes` int(11) DEFAULT NULL AFTER `language`,
  ADD COLUMN IF NOT EXISTS `venue` varchar(255) DEFAULT NULL AFTER `duration_minutes`,
  ADD COLUMN IF NOT EXISTS `event_date` date DEFAULT NULL AFTER `venue`,
  ADD COLUMN IF NOT EXISTS `event_time` time DEFAULT NULL AFTER `event_date`,
  ADD COLUMN IF NOT EXISTS `ticket_price` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `event_time`,
  ADD COLUMN IF NOT EXISTS `showing_prices` varchar(500) DEFAULT NULL AFTER `ticket_price`,
  ADD COLUMN IF NOT EXISTS `poster_image` varchar(255) DEFAULT NULL AFTER `showing_prices`,
  ADD COLUMN IF NOT EXISTS `is_published` tinyint(1) NOT NULL DEFAULT 0 AFTER `poster_image`,
  ADD COLUMN IF NOT EXISTS `published_at` datetime DEFAULT NULL AFTER `is_published`,
  ADD COLUMN IF NOT EXISTS `published_by` int(11) DEFAULT NULL AFTER `published_at`;

ALTER TABLE `dramas`
  ADD INDEX IF NOT EXISTS `idx_dramas_is_published` (`is_published`),
  ADD INDEX IF NOT EXISTS `idx_dramas_event_date` (`event_date`),
  ADD INDEX IF NOT EXISTS `idx_dramas_category_id` (`category_id`),
  ADD INDEX IF NOT EXISTS `idx_dramas_published_by` (`published_by`);

-- Role application enhancements (interview flow + media links)
ALTER TABLE `role_applications`
  ADD COLUMN IF NOT EXISTS `media_links` text DEFAULT NULL COMMENT 'Artist portfolio or media links',
  ADD COLUMN IF NOT EXISTS `profile_viewed_at` datetime DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `profile_viewed_by` int(11) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `interview_at` datetime DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `interview_scheduled_at` datetime DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `interview_scheduled_by` int(11) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `interview_status` enum('pending','completed','cancelled') NOT NULL DEFAULT 'pending',
  ADD COLUMN IF NOT EXISTS `interview_notes` text DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `interview_confirmation_status` enum('pending','confirmed','declined') NOT NULL DEFAULT 'pending',
  ADD COLUMN IF NOT EXISTS `interview_confirmed_at` datetime DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `interview_confirmation_note` text DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `interview_confirmation_seen_at` datetime DEFAULT NULL,
  ADD INDEX IF NOT EXISTS `idx_role_applications_profile_viewed_by` (`profile_viewed_by`),
  ADD INDEX IF NOT EXISTS `idx_role_applications_interview_scheduled_by` (`interview_scheduled_by`);

SET @ra_fk_profile_exists := (
  SELECT COUNT(*)
  FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'role_applications'
    AND CONSTRAINT_NAME = 'role_applications_profile_viewed_fk'
);
SET @ra_fk_profile_sql := IF(
  @ra_fk_profile_exists = 0,
  'ALTER TABLE role_applications ADD CONSTRAINT role_applications_profile_viewed_fk FOREIGN KEY (profile_viewed_by) REFERENCES users(id) ON DELETE SET NULL',
  'SELECT 1'
);
PREPARE stmt FROM @ra_fk_profile_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @ra_fk_sched_exists := (
  SELECT COUNT(*)
  FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'role_applications'
    AND CONSTRAINT_NAME = 'role_applications_interview_scheduled_fk'
);
SET @ra_fk_sched_sql := IF(
  @ra_fk_sched_exists = 0,
  'ALTER TABLE role_applications ADD CONSTRAINT role_applications_interview_scheduled_fk FOREIGN KEY (interview_scheduled_by) REFERENCES users(id) ON DELETE SET NULL',
  'SELECT 1'
);
PREPARE stmt FROM @ra_fk_sched_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ensure service_requests has expected shape for PM/provider/payment flow
ALTER TABLE `service_requests`
  MODIFY COLUMN `id` int(11) NOT NULL,
  MODIFY COLUMN `status` enum('pending','provider_responded','confirmed','accepted','rejected','completed','completed_paid','cancelled') NOT NULL DEFAULT 'pending',
  MODIFY COLUMN `provider_id` int(11) NOT NULL;

SET @sr_has_pk := (
  SELECT COUNT(*)
  FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'service_requests'
    AND CONSTRAINT_TYPE = 'PRIMARY KEY'
);
SET @sr_add_pk_sql := IF(@sr_has_pk = 0,
  'ALTER TABLE service_requests ADD PRIMARY KEY (id)',
  'SELECT 1'
);
PREPARE stmt FROM @sr_add_pk_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sr_is_auto := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'service_requests'
    AND COLUMN_NAME = 'id'
    AND EXTRA LIKE '%auto_increment%'
);
SET @sr_auto_sql := IF(@sr_is_auto = 0,
  'ALTER TABLE service_requests MODIFY COLUMN id INT(11) NOT NULL AUTO_INCREMENT',
  'SELECT 1'
);
PREPARE stmt FROM @sr_auto_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

ALTER TABLE `service_requests`
  ADD INDEX IF NOT EXISTS `idx_drama_id` (`drama_id`),
  ADD INDEX IF NOT EXISTS `idx_provider_id` (`provider_id`),
  ADD INDEX IF NOT EXISTS `idx_status` (`status`),
  ADD INDEX IF NOT EXISTS `idx_created_at` (`created_at`);

-- Payments table used by Payment controller/model
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_request_id` int NOT NULL,
  `payment_type` enum('advance','remaining','full') NOT NULL COMMENT 'Type of payment',
  `amount` decimal(12,2) NOT NULL,
  `payment_gateway` varchar(50) DEFAULT 'payhere' COMMENT 'Gateway used',
  `payment_status` enum('pending','completed','success','failed','refunded','canceled','cancelled','chargedback','expired') DEFAULT 'pending',
  `paid_by` int DEFAULT NULL COMMENT 'User who made payment',
  `paid_to` int DEFAULT NULL COMMENT 'User receiving payment',
  `paid_at` timestamp NULL DEFAULT NULL,
  `gateway_payment_id` varchar(100) DEFAULT NULL COMMENT 'Gateway payment ID',
  `gateway_order_id` varchar(100) DEFAULT NULL COMMENT 'Gateway order ID',
  `reference_number` varchar(100) DEFAULT NULL COMMENT 'Internal payment reference',
  `transaction_response` json DEFAULT NULL COMMENT 'Gateway response payload',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_service_request` (`service_request_id`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_payment_type` (`payment_type`),
  KEY `idx_paid_by` (`paid_by`),
  KEY `idx_paid_to` (`paid_to`),
  KEY `idx_gateway_order_id` (`gateway_order_id`),
  KEY `idx_gateway_payment_id` (`gateway_payment_id`),
  CONSTRAINT `payments_ibfk_request` FOREIGN KEY (`service_request_id`) REFERENCES `service_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_ibfk_paid_by` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_ibfk_paid_to` FOREIGN KEY (`paid_to`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `payments`
  ADD COLUMN IF NOT EXISTS `gateway_payment_id` varchar(100) DEFAULT NULL COMMENT 'Gateway payment ID',
  ADD COLUMN IF NOT EXISTS `gateway_order_id` varchar(100) DEFAULT NULL COMMENT 'Gateway order ID',
  ADD COLUMN IF NOT EXISTS `reference_number` varchar(100) DEFAULT NULL COMMENT 'Internal payment reference',
  ADD COLUMN IF NOT EXISTS `transaction_response` json DEFAULT NULL COMMENT 'Gateway response payload',
  ADD INDEX IF NOT EXISTS `idx_gateway_order_id` (`gateway_order_id`),
  ADD INDEX IF NOT EXISTS `idx_gateway_payment_id` (`gateway_payment_id`);

ALTER TABLE `payments`
  MODIFY COLUMN `payment_status` enum('pending','completed','success','failed','refunded','canceled','cancelled','chargedback','expired') DEFAULT 'pending';

-- Drama Classes (artist-led classes/workshops)
CREATE TABLE IF NOT EXISTS `drama_classes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created_by` int NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `class_level` enum('beginner','intermediate','advanced','all_levels') NOT NULL DEFAULT 'all_levels',
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `capacity` int NOT NULL DEFAULT '30',
  `class_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `duration_minutes` int NOT NULL DEFAULT '120',
  `venue` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_drama_classes_created_by` (`created_by`),
  KEY `idx_drama_classes_class_date` (`class_date`),
  CONSTRAINT `fk_drama_classes_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Class Enrollments (audience + artists in class sessions)
CREATE TABLE IF NOT EXISTS `class_enrollments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class_id` int NOT NULL,
  `user_id` int NOT NULL,
  `status` enum('enrolled','cancelled','completed') NOT NULL DEFAULT 'enrolled',
  `enrolled_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_class_enrollment` (`class_id`,`user_id`),
  KEY `idx_class_enrollments_class_id` (`class_id`),
  KEY `idx_class_enrollments_user_id` (`user_id`),
  CONSTRAINT `fk_class_enrollments_class_id` FOREIGN KEY (`class_id`) REFERENCES `drama_classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_class_enrollments_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
