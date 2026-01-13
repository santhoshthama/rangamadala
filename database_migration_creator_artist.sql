-- Migration to add creator_artist_id column to dramas table
-- This column will store the artist who created the drama (director)

-- Add the creator_artist_id column
ALTER TABLE `dramas` 
ADD COLUMN IF NOT EXISTS `creator_artist_id` int(11) DEFAULT NULL AFTER `created_by`;

-- Add index for the new column
ALTER TABLE `dramas` 
ADD KEY `creator_artist_id` (`creator_artist_id`);

-- Add foreign key constraint
ALTER TABLE `dramas` 
ADD CONSTRAINT `dramas_ibfk_3` FOREIGN KEY (`creator_artist_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Copy data from created_by to creator_artist_id if creator_artist_id is empty
UPDATE `dramas` 
SET `creator_artist_id` = `created_by` 
WHERE `creator_artist_id` IS NULL AND `created_by` IS NOT NULL;

-- Note: Both columns (created_by and creator_artist_id) will exist
-- created_by = general creator tracking
-- creator_artist_id = the artist who is the director of the drama
