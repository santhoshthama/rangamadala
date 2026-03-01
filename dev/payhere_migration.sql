-- PayHere Integration Migration
-- Add PayHere-specific columns to payments table

ALTER TABLE payments ADD COLUMN IF NOT EXISTS `gateway_payment_id` VARCHAR(100) DEFAULT NULL COMMENT 'PayHere payment ID';
ALTER TABLE payments ADD COLUMN IF NOT EXISTS `gateway_order_id` VARCHAR(100) DEFAULT NULL COMMENT 'PayHere order ID (unique reference)';
ALTER TABLE payments ADD COLUMN IF NOT EXISTS `transaction_response` JSON DEFAULT NULL COMMENT 'PayHere webhook response data';

-- Add index for faster lookups
ALTER TABLE payments ADD INDEX IF NOT EXISTS idx_gateway_order_id (gateway_order_id);
ALTER TABLE payments ADD INDEX IF NOT EXISTS idx_gateway_payment_id (gateway_payment_id);

-- Update payment_status column comment
ALTER TABLE payments MODIFY COLUMN payment_status ENUM('pending', 'success', 'failed', 'canceled', 'chargedback', 'expired') DEFAULT 'pending' COMMENT 'Payment status from gateway';

-- Verify the table structure
DESCRIBE payments;
