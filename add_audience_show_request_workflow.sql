-- Audience show request and approval workflow migration
-- Run this once to enable artist approval + rejection reason + PayHere tracking for show bookings.

CREATE TABLE IF NOT EXISTS audience_show_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    audience_id INT NOT NULL,
    drama_id INT NOT NULL,
    ticket_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    booking_status ENUM('pending','accepted','rejected','confirmed','completed','watched','attended') NOT NULL DEFAULT 'pending',
    request_details_json JSON NULL,
    rejection_reason TEXT NULL,
    payhere_order_id VARCHAR(120) NULL,
    paid_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_show_booking_status (booking_status),
    INDEX idx_show_booking_order (payhere_order_id),
    INDEX idx_show_booking_audience_drama (audience_id, drama_id)
);

ALTER TABLE audience_show_bookings
    ADD COLUMN IF NOT EXISTS request_details_json JSON NULL AFTER booking_status,
    ADD COLUMN IF NOT EXISTS rejection_reason TEXT NULL AFTER request_details_json,
    ADD COLUMN IF NOT EXISTS payhere_order_id VARCHAR(120) NULL AFTER rejection_reason,
    ADD COLUMN IF NOT EXISTS paid_at DATETIME NULL AFTER payhere_order_id;

-- Ensure booking_status supports workflow states.
-- If booking_status is already VARCHAR/ENUM with these values, you can skip this statement.
ALTER TABLE audience_show_bookings
    MODIFY COLUMN booking_status ENUM('pending','accepted','rejected','confirmed','completed','watched','attended')
    NOT NULL DEFAULT 'pending';

ALTER TABLE audience_show_bookings
    ADD INDEX IF NOT EXISTS idx_show_booking_status (booking_status),
    ADD INDEX IF NOT EXISTS idx_show_booking_order (payhere_order_id),
    ADD INDEX IF NOT EXISTS idx_show_booking_audience_drama (audience_id, drama_id);
