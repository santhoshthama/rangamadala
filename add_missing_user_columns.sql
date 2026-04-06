-- Minimal Profile Extensions for Rangamadala Database
-- Adds the profile fields used by the merged profile flow

ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `years_experience` int DEFAULT NULL COMMENT 'Years of experience',
  ADD COLUMN IF NOT EXISTS `bio` text DEFAULT NULL COMMENT 'User biography',
  ADD COLUMN IF NOT EXISTS `location` varchar(255) DEFAULT NULL COMMENT 'Current location/city',
  ADD COLUMN IF NOT EXISTS `website` varchar(255) DEFAULT NULL COMMENT 'Personal or professional website',
  ADD INDEX IF NOT EXISTS `idx_users_location` (`location`);
