-- Add showing prices column to dramas table
-- Run this once on the Rangamadala database.

ALTER TABLE dramas
ADD COLUMN IF NOT EXISTS showing_prices VARCHAR(500) DEFAULT NULL AFTER ticket_price;
