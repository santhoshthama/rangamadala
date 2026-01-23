-- ============================================
-- Production Manager Complete Database Migration
-- ============================================
-- This script creates all necessary tables for the PM system
-- Run this in your database to set up the Production Manager features
-- ============================================

-- 1. SERVICE REQUESTS TABLE (Core for all PM pages)
-- ============================================
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
  KEY `idx_drama_id` (`drama_id`),
  KEY `idx_service_provider_id` (`service_provider_id`),
  KEY `idx_status` (`status`),
  KEY `idx_drama_status` (`drama_id`, `status`),
  CONSTRAINT `service_requests_fk_drama` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_requests_fk_provider` FOREIGN KEY (`service_provider_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_requests_fk_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. THEATER BOOKINGS TABLE
-- ============================================
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
  KEY `idx_drama_id` (`drama_id`),
  KEY `idx_booking_date` (`booking_date`),
  KEY `idx_status` (`status`),
  KEY `idx_drama_date` (`drama_id`, `booking_date`),
  CONSTRAINT `theater_bookings_fk_drama` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `theater_bookings_fk_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. SERVICE SCHEDULES TABLE
-- ============================================
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
  KEY `idx_drama_id` (`drama_id`),
  KEY `idx_service_request_id` (`service_request_id`),
  KEY `idx_assigned_to` (`assigned_to`),
  KEY `idx_status` (`status`),
  KEY `idx_drama_date` (`drama_id`, `scheduled_date`),
  CONSTRAINT `service_schedules_fk_drama` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_schedules_fk_service_request` FOREIGN KEY (`service_request_id`) REFERENCES `service_requests` (`id`) ON DELETE SET NULL,
  CONSTRAINT `service_schedules_fk_user_assigned` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `service_schedules_fk_user_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. DRAMA BUDGETS TABLE
-- ============================================
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
  KEY `idx_drama_id` (`drama_id`),
  KEY `idx_category` (`category`),
  KEY `idx_status` (`status`),
  KEY `idx_drama_category` (`drama_id`, `category`),
  CONSTRAINT `drama_budgets_fk_drama` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drama_budgets_fk_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Indexes for Performance (Additional)
-- ============================================
CREATE INDEX idx_service_requests_created ON service_requests(created_at);
CREATE INDEX idx_theater_bookings_status ON theater_bookings(status);
CREATE INDEX idx_service_schedules_status ON service_schedules(status);
CREATE INDEX idx_drama_budgets_category ON drama_budgets(drama_id, category);

-- ============================================
-- Insert Sample Data (Optional - for testing)
-- ============================================
-- Uncomment the lines below to insert test data
-- You'll need to replace drama_id values with actual IDs from your dramas table

/*
-- Sample service requests (requires drama_id and service_provider_id from your database)
INSERT INTO service_requests (drama_id, service_provider_id, service_type, status, request_date, description, created_by)
VALUES 
  (1, 5, 'sound_system', 'accepted', '2026-01-20', 'Professional sound system for main stage', 2),
  (1, 6, 'lighting', 'pending', '2026-01-21', 'Advanced lighting setup with effects', 2),
  (1, 7, 'costume', 'accepted', '2026-01-19', 'Costume design for 15 actors', 2);

-- Sample theater bookings
INSERT INTO theater_bookings (drama_id, theater_name, booking_date, start_time, end_time, capacity, rental_cost, status, created_by)
VALUES 
  (1, 'Elphinstone Theatre', '2026-02-15', '18:00:00', '22:00:00', 500, 250000.00, 'confirmed', 2),
  (1, 'Colombo Auditorium', '2026-02-22', '19:00:00', '23:00:00', 800, 300000.00, 'confirmed', 2),
  (1, 'Galle Face Green', '2026-03-01', '17:00:00', '21:00:00', 1000, 350000.00, 'pending', 2);

-- Sample service schedules
INSERT INTO service_schedules (drama_id, service_name, scheduled_date, start_time, end_time, venue, status, created_by)
VALUES 
  (1, 'Sound System Setup', '2026-02-14', '10:00:00', '14:00:00', 'Elphinstone Theatre', 'scheduled', 2),
  (1, 'Lighting Installation', '2026-02-14', '15:00:00', '18:00:00', 'Elphinstone Theatre', 'scheduled', 2),
  (1, 'Costume Final Fitting', '2026-02-13', '14:00:00', '17:00:00', 'Studio', 'scheduled', 2);

-- Sample budget items
INSERT INTO drama_budgets (drama_id, item_name, category, allocated_amount, spent_amount, status, created_by)
VALUES 
  (1, 'Elphinstone Theatre Rental', 'venue', 250000.00, 250000.00, 'paid', 2),
  (1, 'Sound System & Audio Setup', 'technical', 120000.00, 80000.00, 'partial', 2),
  (1, 'Professional Lighting & Effects', 'technical', 150000.00, 0.00, 'pending', 2),
  (1, 'Costume & Makeup Design', 'costume', 160000.00, 100000.00, 'partial', 2);
*/

-- ============================================
-- Verification Queries
-- ============================================
-- Run these queries to verify the tables were created successfully:

-- SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'rangamadala_db';
-- DESCRIBE service_requests;
-- DESCRIBE theater_bookings;
-- DESCRIBE service_schedules;
-- DESCRIBE drama_budgets;

-- ============================================
-- Migration Complete
-- ============================================
-- All Production Manager tables have been created successfully.
-- Tables created:
--   1. service_requests (Core - used by all PM pages)
--   2. theater_bookings (Used by book_theater page)
--   3. service_schedules (Used by manage_schedule page)
--   4. drama_budgets (Used by manage_budget page)
--
-- All tables include:
--   - Proper foreign key constraints
--   - Cascade deletion for referential integrity
--   - Optimized indexes for performance
--   - Timestamp fields for auditing
--   - Status enums for workflow management
--
-- Next steps:
--   1. Verify all tables were created: SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE();
--   2. Insert test data using the sample data section above (uncomment to use)
--   3. Navigate to Production Manager pages to verify they load correctly
--   4. Check console for any errors
-- ============================================
