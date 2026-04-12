-- Minimal Profile Extensions for Rangamadala Database
-- Only adds essential columns that are actually used in the codebase

-- Add years_experience column to users table (USED in artist profiles)
ALTER TABLE `users`
  ADD COLUMN `years_experience` int DEFAULT NULL COMMENT 'Years of experience';

-- Add bio column to users table (USED in audience profiles)
ALTER TABLE `users`
  ADD COLUMN `bio` text DEFAULT NULL COMMENT 'User biography';

-- Add location column to users table (USED in profiles)
ALTER TABLE `users`
  ADD COLUMN `location` varchar(255) DEFAULT NULL COMMENT 'Current location/city';

-- Add website column to users table (USED in service provider profiles)
ALTER TABLE `users`
  ADD COLUMN `website` varchar(255) DEFAULT NULL COMMENT 'Personal or professional website';

-- Add index for location (helps with location-based searches)
ALTER TABLE `users`
  ADD INDEX `idx_users_location` (`location`);
