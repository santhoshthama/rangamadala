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
  `title` varchar(255) NOT NULL,
  `description` text,
  `category_id` int(11) DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'Duration in minutes',
  `ticket_price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `creator_artist_id` int(11) DEFAULT NULL COMMENT 'The artist who is the director',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `created_by` (`created_by`),
  KEY `creator_artist_id` (`creator_artist_id`),
  CONSTRAINT `dramas_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
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
INSERT INTO `dramas` (`title`, `description`, `category_id`, `venue`, `event_date`, `event_time`, `duration`, `ticket_price`, `created_by`) VALUES
('Maname', 'A classical Sinhala drama exploring family relationships and societal values', 1, 'Lionel Wendt Theatre, Colombo', '2025-01-15', '19:00:00', 120, 1500.00, NULL),
('Sinhabahu', 'Epic tale of the legendary king Sinhabahu and his journey', 1, 'Nelum Pokuna Theatre, Colombo', '2025-01-20', '18:30:00', 150, 2000.00, NULL),
('Kolamba Kathawa', 'A comedy drama depicting urban life in Colombo', 3, 'Elphinstone Theatre, Maradana', '2025-02-05', '19:30:00', 90, 1000.00, NULL),
('Nari Bena', 'Traditional folk drama with dance and music', 7, 'BMICH, Colombo', '2025-02-10', '18:00:00', 105, 1200.00, NULL),
('Vijayaba Kollaya', 'Historical drama about ancient Sri Lankan warriors', 1, 'Regal Theatre, Colombo', '2025-02-15', '19:00:00', 135, 1800.00, NULL)
ON DUPLICATE KEY UPDATE title=title;

-- Create serviceprovider table
CREATE TABLE IF NOT EXISTS `serviceprovider` (
  `user_id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `professional_title` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `years_experience` int DEFAULT NULL,
  `professional_summary` text,
  `availability` tinyint(1) DEFAULT '1',
  `availability_notes` varchar(255) DEFAULT NULL,
  `business_cert_photo` varchar(255) DEFAULT NULL,
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

-- Create services table

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
