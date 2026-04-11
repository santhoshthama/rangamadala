-- Ensure all service detail tables exist
-- Safe to run multiple times

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
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `service_lighting_details` (
  `service_id` int NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `rate_type` enum('hourly','daily') DEFAULT 'hourly',
  `description` text,
  `lighting_equipment_provided` text,
  `max_stage_size` varchar(255) DEFAULT NULL,
  `lighting_design_service` varchar(10) DEFAULT NULL,
  `lighting_crew_available` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `service_other_details` (
  `service_id` int NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `rate_type` enum('hourly','daily') DEFAULT 'hourly',
  `description` text,
  `service_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add FKs only if missing (MySQL 8+ IF NOT EXISTS support for FK is limited, so use defensive checks)
SET @db := DATABASE();

SET @fk_exists := (
  SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @db AND CONSTRAINT_NAME = 'theater_details_fk_service'
);
SET @sql := IF(@fk_exists = 0,
  'ALTER TABLE service_theater_details ADD CONSTRAINT theater_details_fk_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk_exists := (
  SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @db AND CONSTRAINT_NAME = 'lighting_details_fk_service'
);
SET @sql := IF(@fk_exists = 0,
  'ALTER TABLE service_lighting_details ADD CONSTRAINT lighting_details_fk_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk_exists := (
  SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @db AND CONSTRAINT_NAME = 'sound_details_fk_service'
);
SET @sql := IF(@fk_exists = 0,
  'ALTER TABLE service_sound_details ADD CONSTRAINT sound_details_fk_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk_exists := (
  SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @db AND CONSTRAINT_NAME = 'video_details_fk_service'
);
SET @sql := IF(@fk_exists = 0,
  'ALTER TABLE service_video_details ADD CONSTRAINT video_details_fk_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk_exists := (
  SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @db AND CONSTRAINT_NAME = 'set_details_fk_service'
);
SET @sql := IF(@fk_exists = 0,
  'ALTER TABLE service_set_details ADD CONSTRAINT set_details_fk_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk_exists := (
  SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @db AND CONSTRAINT_NAME = 'costume_details_fk_service'
);
SET @sql := IF(@fk_exists = 0,
  'ALTER TABLE service_costume_details ADD CONSTRAINT costume_details_fk_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk_exists := (
  SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @db AND CONSTRAINT_NAME = 'makeup_details_fk_service'
);
SET @sql := IF(@fk_exists = 0,
  'ALTER TABLE service_makeup_details ADD CONSTRAINT makeup_details_fk_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk_exists := (
  SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @db AND CONSTRAINT_NAME = 'other_details_fk_service'
);
SET @sql := IF(@fk_exists = 0,
  'ALTER TABLE service_other_details ADD CONSTRAINT other_details_fk_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
