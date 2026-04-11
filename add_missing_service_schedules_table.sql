-- Ensure Production Manager service schedule table exists
-- Safe to run multiple times

CREATE TABLE IF NOT EXISTS `service_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `drama_id` int(11) NOT NULL,
  `service_request_id` int(11) DEFAULT NULL,
  `service_name` varchar(255) NOT NULL,
  `scheduled_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `status` enum('scheduled','confirmed','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_service_schedules_drama` (`drama_id`),
  KEY `idx_service_schedules_request` (`service_request_id`),
  KEY `idx_service_schedules_assigned_to` (`assigned_to`),
  KEY `idx_service_schedules_date` (`scheduled_date`),
  KEY `idx_service_schedules_status` (`status`),
  KEY `idx_service_schedules_created_by` (`created_by`),
  CONSTRAINT `service_schedules_ibfk_drama` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_schedules_ibfk_request` FOREIGN KEY (`service_request_id`) REFERENCES `service_requests` (`id`) ON DELETE SET NULL,
  CONSTRAINT `service_schedules_ibfk_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `service_schedules_ibfk_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
