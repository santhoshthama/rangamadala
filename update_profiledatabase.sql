-- =====================================================
-- Artist Profile Database Update
-- File: update_profiledatabase.sql
-- Date: January 28, 2026
-- Purpose: Add missing profile columns for artist profiles
-- =====================================================

-- Check if columns already exist before adding
-- This prevents errors if running the script multiple times

-- Add bio column (for "About Me" section)
SET @column_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'users' 
    AND COLUMN_NAME = 'bio'
);

SET @sql = IF(@column_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `bio` TEXT DEFAULT NULL COMMENT ''User biography/about me section''',
    'SELECT ''Column bio already exists'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add location column (for city/location)
SET @column_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'users' 
    AND COLUMN_NAME = 'location'
);

SET @sql = IF(@column_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `location` VARCHAR(255) DEFAULT NULL COMMENT ''User location/city''',
    'SELECT ''Column location already exists'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add website column (for portfolio/personal website)
SET @column_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'users' 
    AND COLUMN_NAME = 'website'
);

SET @sql = IF(@column_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `website` VARCHAR(255) DEFAULT NULL COMMENT ''Personal or portfolio website URL''',
    'SELECT ''Column website already exists'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add years_experience column (if not already exists)
SET @column_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'users' 
    AND COLUMN_NAME = 'years_experience'
);

SET @sql = IF(@column_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `years_experience` INT DEFAULT NULL COMMENT ''Years of professional experience''',
    'SELECT ''Column years_experience already exists'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index for location column (helps with location-based searches)
SET @index_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'users' 
    AND INDEX_NAME = 'idx_users_location'
);

SET @sql = IF(@index_exists = 0,
    'ALTER TABLE `users` ADD INDEX `idx_users_location` (`location`)',
    'SELECT ''Index idx_users_location already exists'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verification Query
SELECT 
    'Verification Complete' AS Status,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'bio') AS bio_exists,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'location') AS location_exists,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'website') AS website_exists,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'years_experience') AS years_experience_exists;

-- =====================================================
-- USAGE INSTRUCTIONS:
-- =====================================================
-- 1. Open phpMyAdmin
-- 2. Select your database: rangamandala_db
-- 3. Click on "SQL" tab
-- 4. Copy and paste this entire file
-- 5. Click "Go" to execute
-- 6. Check the verification results at the bottom
-- 
-- Expected Result:
-- All columns should show "1" indicating they exist
-- =====================================================

-- =====================================================
-- WHAT THIS SCRIPT DOES:
-- =====================================================
-- Adds 4 new columns to the users table:
-- 
-- 1. bio (TEXT) - Stores artist's biography/about me
-- 2. location (VARCHAR 255) - Stores city/location
-- 3. website (VARCHAR 255) - Stores portfolio/website URL
-- 4. years_experience (INT) - Stores years of experience
-- 
-- Plus creates an index on location for faster searches
-- 
-- Safe to run multiple times - checks if columns exist first
-- =====================================================
