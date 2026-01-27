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
  KEY `user_id` (`user_id`),
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
  KEY `idx_drama_id` (`drama_id`),
  KEY `idx_service_type` (`service_type`),
  CONSTRAINT `drama_services_ibfk_1` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




--Service Provider and Services Tables

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
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `status` enum('pending','provider_responded','confirmed','accepted','rejected','completed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','partially_paid','paid') DEFAULT 'unpaid',
  `accepted_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_drama_id` (`drama_id`),
  KEY `idx_provider_id` (`provider_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


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