-- Production Manager System Tables
-- Add these tables to support PM dashboard features

-- Service Requests table (REQUIRED - Used by all PM pages)
CREATE TABLE IF NOT EXISTS `service_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `drama_id` int NOT NULL,
  `service_provider_id` int NOT NULL,
  `service_type` varchar(100) NOT NULL,
  `status` enum('pending','accepted','completed','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `request_date` date NOT NULL,
  `required_date` date,
  `budget_range` varchar(100),
  `description` text,
  `special_requirements` text,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `drama_id` (`drama_id`),
  KEY `service_provider_id` (`service_provider_id`),
  KEY `status` (`status`),
  CONSTRAINT `service_requests_fk_drama` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_requests_fk_provider` FOREIGN KEY (`service_provider_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_requests_fk_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Theater Bookings table
CREATE TABLE IF NOT EXISTS `theater_bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `drama_id` int NOT NULL,
  `theater_name` varchar(255) NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `capacity` int DEFAULT NULL,
  `rental_cost` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  `special_requests` text,
  `booking_reference` varchar(100) UNIQUE,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `drama_id` (`drama_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `theater_bookings_fk_drama` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `theater_bookings_fk_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Service Schedule table
CREATE TABLE IF NOT EXISTS `service_schedules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `drama_id` int NOT NULL,
  `service_request_id` int DEFAULT NULL,
  `service_name` varchar(255) NOT NULL,
  `scheduled_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `venue` varchar(255),
  `assigned_to` int DEFAULT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `notes` text,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `drama_id` (`drama_id`),
  KEY `service_request_id` (`service_request_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `service_schedules_fk_drama` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_schedules_fk_user_assigned` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `service_schedules_fk_user_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Drama Budget table
CREATE TABLE IF NOT EXISTS `drama_budgets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `drama_id` int NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `category` enum('venue','technical','costume','marketing','transport','other') NOT NULL,
  `allocated_amount` decimal(12,2) NOT NULL DEFAULT 0,
  `spent_amount` decimal(12,2) NOT NULL DEFAULT 0,
  `status` enum('pending','approved','paid','partial') NOT NULL DEFAULT 'pending',
  `notes` text,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `drama_id` (`drama_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `drama_budgets_fk_drama` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drama_budgets_fk_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create indexes for better query performance
CREATE INDEX idx_theater_bookings_drama_date ON theater_bookings(drama_id, booking_date);
CREATE INDEX idx_service_schedules_drama_date ON service_schedules(drama_id, scheduled_date);
CREATE INDEX idx_drama_budgets_drama_category ON drama_budgets(drama_id, category);
