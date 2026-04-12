-- Migration: Keep multi-booking decision simple in existing provider_availability table
ALTER TABLE `provider_availability` ADD COLUMN IF NOT EXISTS `allow_more_bookings` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Summary: Can more bookings be added to this date?' AFTER `status`;
