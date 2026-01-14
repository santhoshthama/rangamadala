-- Migration: Simplify dramas table to only store Public Performance Board Certificate details
-- Date: January 13, 2026
-- Run this migration to update existing dramas table structure

-- BACKUP YOUR DATA FIRST! This migration will drop existing columns and data

-- Step 1: Create a backup of existing dramas data (optional, for safety)
-- You can skip this if you don't have important data
-- CREATE TABLE dramas_backup AS SELECT * FROM dramas;

-- Step 2: Drop existing foreign key constraints
ALTER TABLE `dramas` 
DROP FOREIGN KEY IF EXISTS `dramas_ibfk_1`;

-- Step 3: Drop old columns that are no longer needed
ALTER TABLE `dramas`
DROP COLUMN IF EXISTS `title`,
DROP COLUMN IF EXISTS `description`,
DROP COLUMN IF EXISTS `category_id`,
DROP COLUMN IF EXISTS `venue`,
DROP COLUMN IF EXISTS `event_date`,
DROP COLUMN IF EXISTS `event_time`,
DROP COLUMN IF EXISTS `duration`,
DROP COLUMN IF EXISTS `ticket_price`,
DROP COLUMN IF EXISTS `image`;

-- Step 4: Add new certificate-related columns
ALTER TABLE `dramas`
ADD COLUMN IF NOT EXISTS `drama_name` VARCHAR(255) NOT NULL COMMENT 'Drama name as in public performance board certificate' AFTER `id`,
ADD COLUMN IF NOT EXISTS `certificate_number` VARCHAR(100) NOT NULL COMMENT 'Public performance certificate number' AFTER `drama_name`,
ADD COLUMN IF NOT EXISTS `owner_name` VARCHAR(255) NOT NULL COMMENT 'Owner name' AFTER `certificate_number`,
ADD COLUMN IF NOT EXISTS `description` TEXT DEFAULT NULL COMMENT 'Artist provided synopsis for the drama' AFTER `owner_name`,
ADD COLUMN IF NOT EXISTS `certificate_image` VARCHAR(255) DEFAULT NULL COMMENT 'Image of public performance board certificate' AFTER `owner_name`;

-- Step 5: Add unique constraint on certificate_number
ALTER TABLE `dramas`
DROP INDEX IF EXISTS `certificate_number`,
ADD UNIQUE KEY `certificate_number` (`certificate_number`);

-- Step 6: Update column order (reorganize to match new schema)
-- This step is optional, as MySQL maintains column order internally

-- Verification query - Check the new structure
-- DESCRIBE dramas;

-- Note: After running this migration:
-- 1. The dramas table will only store certificate information
-- 2. All existing drama entries will be lost (unless you created a backup)
-- 3. Categories table is no longer used by dramas (can be kept for future use or dropped)
