-- Fix existing user profile images
-- This updates the database to point to already uploaded images

-- User ID 4 (lakindu)
UPDATE users 
SET profile_image = 'artist_4_1769603838.png' 
WHERE id = 4;

-- User ID 9
UPDATE users 
SET profile_image = 'artist_9_1768294846.png' 
WHERE id = 9;

-- User ID 20
UPDATE users 
SET profile_image = 'artist_20_1768302802.png' 
WHERE id = 20;

-- User ID 17
UPDATE users 
SET profile_image = 'profile_17_1768293765.png' 
WHERE id = 17;

-- User ID 40
UPDATE users 
SET profile_image = 'profile_40_1767766652.jpeg' 
WHERE id = 40;

-- Verify updates
SELECT id, full_name, profile_image 
FROM users 
WHERE id IN (4, 9, 17, 20, 40);
