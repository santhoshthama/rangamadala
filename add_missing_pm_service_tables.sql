-- Ensure missing Production Manager related tables exist
-- Safe to run multiple times

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
  `service_details_json` longtext DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `provider_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `status` enum('pending','provider_responded','confirmed','accepted','rejected','completed','completed_paid','cancelled') NOT NULL DEFAULT 'pending',
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

CREATE TABLE IF NOT EXISTS `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_request_id` int NOT NULL,
  `payment_type` enum('advance','remaining','full') NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_gateway` varchar(50) DEFAULT 'payhere',
  `payment_status` enum('pending','completed','success','failed','refunded','canceled','cancelled','chargedback','expired') DEFAULT 'pending',
  `paid_by` int DEFAULT NULL,
  `paid_to` int DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `gateway_order_id` varchar(100) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `transaction_response` JSON DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_service_request` (`service_request_id`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_payment_type` (`payment_type`),
  KEY `idx_paid_by` (`paid_by`),
  KEY `idx_paid_to` (`paid_to`),
  KEY `idx_gateway_order_id` (`gateway_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  KEY `available_date` (`available_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `payments` ADD COLUMN IF NOT EXISTS `reference_number` varchar(100) DEFAULT NULL;
ALTER TABLE `payments` ADD INDEX IF NOT EXISTS `idx_reference_number` (`reference_number`);
