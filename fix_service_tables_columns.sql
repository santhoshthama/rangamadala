-- Fix: Add missing columns to service tables
-- Run this in your rangamandala_db database

-- Fix services table - add service_type_id column
ALTER TABLE `services` 
ADD COLUMN `service_type_id` int DEFAULT NULL AFTER `provider_id`,
ADD KEY `service_type_id` (`service_type_id`),
ADD CONSTRAINT `services_ibfk_service_type` FOREIGN KEY (`service_type_id`) REFERENCES `service_types` (`service_type_id`) ON DELETE SET NULL;

-- Fix service_theater_details - add rate_type column
ALTER TABLE `service_theater_details` 
ADD COLUMN `rate_type` enum('hourly','daily') DEFAULT 'hourly' AFTER `rate_per_hour`;

-- Fix service_lighting_details - add rate_type column if missing
ALTER TABLE `service_lighting_details` 
ADD COLUMN `rate_type` enum('hourly','daily') DEFAULT 'hourly' AFTER `rate_per_hour`;

-- Fix service_sound_details - add rate_type column if missing
ALTER TABLE `service_sound_details` 
ADD COLUMN `rate_type` enum('hourly','daily') DEFAULT 'hourly' AFTER `rate_per_hour`;

-- Fix service_video_details - add rate_type column if missing
ALTER TABLE `service_video_details` 
ADD COLUMN `rate_type` enum('hourly','daily') DEFAULT 'hourly' AFTER `rate_per_hour`;

-- Fix service_set_details - add rate_type column if missing
ALTER TABLE `service_set_details` 
ADD COLUMN `rate_type` enum('hourly','daily') DEFAULT 'hourly' AFTER `rate_per_hour`;

-- Fix service_costume_details - add rate_type column if missing
ALTER TABLE `service_costume_details` 
ADD COLUMN `rate_type` enum('hourly','daily') DEFAULT 'hourly' AFTER `rate_per_hour`;

-- Add missing columns and timestamps to services table
ALTER TABLE `services`
ADD COLUMN IF NOT EXISTS `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
