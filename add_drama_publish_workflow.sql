-- Add director publish workflow for approved dramas
-- Run this once on the Rangamadala database.

ALTER TABLE dramas
    ADD COLUMN IF NOT EXISTS category_id INT(11) DEFAULT NULL AFTER description,
    ADD COLUMN IF NOT EXISTS public_description TEXT DEFAULT NULL AFTER certificate_image,
    ADD COLUMN IF NOT EXISTS genre VARCHAR(100) DEFAULT NULL AFTER public_description,
    ADD COLUMN IF NOT EXISTS language VARCHAR(50) DEFAULT NULL AFTER genre,
    ADD COLUMN IF NOT EXISTS duration_minutes INT(11) DEFAULT NULL AFTER language,
    ADD COLUMN IF NOT EXISTS venue VARCHAR(255) DEFAULT NULL AFTER duration_minutes,
    ADD COLUMN IF NOT EXISTS event_date DATE DEFAULT NULL AFTER venue,
    ADD COLUMN IF NOT EXISTS event_time TIME DEFAULT NULL AFTER event_date,
    ADD COLUMN IF NOT EXISTS ticket_price DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER event_time,
    ADD COLUMN IF NOT EXISTS poster_image VARCHAR(255) DEFAULT NULL AFTER ticket_price,
    ADD COLUMN IF NOT EXISTS is_published TINYINT(1) NOT NULL DEFAULT 0 AFTER poster_image,
    ADD COLUMN IF NOT EXISTS published_at DATETIME DEFAULT NULL AFTER is_published,
    ADD COLUMN IF NOT EXISTS published_by INT(11) DEFAULT NULL AFTER published_at;

ALTER TABLE dramas
    ADD INDEX IF NOT EXISTS idx_dramas_is_published (is_published),
    ADD INDEX IF NOT EXISTS idx_dramas_event_date (event_date),
    ADD INDEX IF NOT EXISTS idx_dramas_category_id (category_id),
    ADD INDEX IF NOT EXISTS idx_dramas_published_by (published_by);
