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
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `dramas_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dramas_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
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

-- Create services table
CREATE TABLE IF NOT EXISTS `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `provider_id` int NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `provider_id` (`provider_id`),
  CONSTRAINT `services_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `serviceprovider` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Theater Production details
CREATE TABLE IF NOT EXISTS `service_theater_details` (
  `service_id` int NOT NULL,
  `num_actors` int DEFAULT NULL,
  `expected_audience` int DEFAULT NULL,
  `stage_proscenium` tinyint(1) DEFAULT NULL,
  `stage_black_box` tinyint(1) DEFAULT NULL,
  `stage_open_floor` tinyint(1) DEFAULT NULL,
  `seating_requirement` varchar(255) DEFAULT NULL,
  `parking_requirement` varchar(255) DEFAULT NULL,
  `special_tech` text,
  PRIMARY KEY (`service_id`),
  CONSTRAINT `theater_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Lighting Design details
CREATE TABLE IF NOT EXISTS `service_lighting_details` (
  `service_id` int NOT NULL,
  `stage_lighting` tinyint(1) DEFAULT NULL,
  `spotlights` tinyint(1) DEFAULT NULL,
  `custom_programming` tinyint(1) DEFAULT NULL,
  `moving_heads` tinyint(1) DEFAULT NULL,
  `num_lights` int DEFAULT NULL,
  `effects` varchar(255) DEFAULT NULL,
  `technician_needed` enum('Yes','No') DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`service_id`),
  CONSTRAINT `lighting_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sound Systems details
CREATE TABLE IF NOT EXISTS `service_sound_details` (
  `service_id` int NOT NULL,
  `pa_system` tinyint(1) DEFAULT NULL,
  `microphones` tinyint(1) DEFAULT NULL,
  `sound_mixing` tinyint(1) DEFAULT NULL,
  `background_music` tinyint(1) DEFAULT NULL,
  `special_effects` tinyint(1) DEFAULT NULL,
  `num_mics` int DEFAULT NULL,
  `stage_monitor` enum('Yes','No') DEFAULT NULL,
  `sound_engineer` enum('Yes','No') DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`service_id`),
  CONSTRAINT `sound_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Video Production details
CREATE TABLE IF NOT EXISTS `service_video_details` (
  `service_id` int NOT NULL,
  `full_event` tinyint(1) DEFAULT NULL,
  `highlight_reel` tinyint(1) DEFAULT NULL,
  `short_promo` tinyint(1) DEFAULT NULL,
  `num_cameras` int DEFAULT NULL,
  `drone_needed` enum('Yes','No') DEFAULT NULL,
  `gimbals` enum('Yes','No') DEFAULT NULL,
  `editing` enum('Yes','No') DEFAULT NULL,
  `delivery_mp4` tinyint(1) DEFAULT NULL,
  `delivery_raw` tinyint(1) DEFAULT NULL,
  `delivery_social` tinyint(1) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`service_id`),
  CONSTRAINT `video_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Set Design details
CREATE TABLE IF NOT EXISTS `service_set_details` (
  `service_id` int NOT NULL,
  `set_design` tinyint(1) DEFAULT NULL,
  `set_construction` tinyint(1) DEFAULT NULL,
  `set_rental` tinyint(1) DEFAULT NULL,
  `production_stage` varchar(50) DEFAULT NULL,
  `materials` varchar(255) DEFAULT NULL,
  `dimensions` varchar(255) DEFAULT NULL,
  `budget_range` varchar(100) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`service_id`),
  CONSTRAINT `set_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Costume Design details
CREATE TABLE IF NOT EXISTS `service_costume_details` (
  `service_id` int NOT NULL,
  `costume_design` tinyint(1) DEFAULT NULL,
  `costume_creation` tinyint(1) DEFAULT NULL,
  `costume_rental` tinyint(1) DEFAULT NULL,
  `num_characters` int DEFAULT NULL,
  `num_costumes` int DEFAULT NULL,
  `measurements_required` enum('Yes','No') DEFAULT NULL,
  `fitting_dates` date DEFAULT NULL,
  `budget_range` varchar(100) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`service_id`),
  CONSTRAINT `costume_details_fk_service` FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE
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
