-- Add media links column to role_applications table
-- This allows artists to include portfolio links when applying for roles

ALTER TABLE `role_applications`
  ADD COLUMN `media_links` text DEFAULT NULL COMMENT 'Artist portfolio or media links (YouTube, social media, etc.)';
