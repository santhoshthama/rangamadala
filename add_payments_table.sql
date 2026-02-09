-- Add payments table for PayPal integration
-- Run this in your rangamandala_db database

CREATE TABLE IF NOT EXISTS `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_request_id` int NOT NULL,
  `payment_type` enum('advance','final') NOT NULL COMMENT 'Type of payment: advance or final',
  `amount` decimal(12,2) NOT NULL,
  `payment_gateway` varchar(50) DEFAULT 'paypal' COMMENT 'Payment gateway used',
  `transaction_id` varchar(255) DEFAULT NULL COMMENT 'Transaction ID from payment gateway',
  `payment_status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `gateway_response` longtext DEFAULT NULL COMMENT 'Full response from payment gateway',
  `paid_by` int DEFAULT NULL COMMENT 'User who made the payment (PM)',
  `paid_to` int DEFAULT NULL COMMENT 'User receiving payment (Provider)',
  `paid_at` timestamp NULL DEFAULT NULL COMMENT 'When payment was completed',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_service_request` (`service_request_id`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_payment_type` (`payment_type`),
  KEY `idx_paid_by` (`paid_by`),
  KEY `idx_paid_to` (`paid_to`),
  CONSTRAINT `payments_ibfk_request` FOREIGN KEY (`service_request_id`) REFERENCES `service_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_ibfk_paid_by` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_ibfk_paid_to` FOREIGN KEY (`paid_to`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
