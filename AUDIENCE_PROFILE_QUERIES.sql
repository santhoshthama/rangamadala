-- =============================================
-- AUDIENCE PROFILE - ALL QUERIES
-- =============================================
-- Based on existing database structure from rangamandala_db

-- =============================================
-- 1. GET COMPLETE AUDIENCE PROFILE
-- =============================================
SELECT 
    u.id,
    u.full_name,
    u.email,
    u.phone,
    u.role,
    u.nic_photo,
    u.created_at as user_created_at,
    ub.bio,
    ub.profile_image,
    ub.created_at as profile_created_at,
    ub.updated_at as profile_updated_at
FROM users u
LEFT JOIN user_bios ub ON u.id = ub.user_id
WHERE u.id = ? AND u.role = 'audience';

-- =============================================
-- 2. INSERT NEW PROFILE (First Time)
-- =============================================
INSERT INTO user_bios (
    user_id, 
    bio, 
    profile_image
) VALUES (
    ?, 
    ?, 
    ?
);

-- =============================================
-- 3. UPDATE EXISTING PROFILE
-- =============================================
UPDATE user_bios 
SET 
    bio = ?,
    profile_image = ?,
    updated_at = CURRENT_TIMESTAMP
WHERE user_id = ?;

-- =============================================
-- 4. UPSERT PROFILE (INSERT OR UPDATE)
-- =============================================
-- Best for profile updates - handles both new and existing profiles
INSERT INTO user_bios (
    user_id, 
    bio, 
    profile_image
) VALUES (
    ?, 
    ?, 
    ?
)
ON DUPLICATE KEY UPDATE
    bio = VALUES(bio),
    profile_image = VALUES(profile_image),
    updated_at = CURRENT_TIMESTAMP;

-- =============================================
-- 5. UPDATE PROFILE IMAGE ONLY
-- =============================================
UPDATE user_bios 
SET 
    profile_image = ?,
    updated_at = CURRENT_TIMESTAMP
WHERE user_id = ?;

-- =============================================
-- 6. UPDATE BIO TEXT ONLY
-- =============================================
UPDATE user_bios 
SET 
    bio = ?,
    updated_at = CURRENT_TIMESTAMP
WHERE user_id = ?;

-- =============================================
-- 7. UPDATE USER INFO (users table)
-- =============================================
UPDATE users 
SET 
    full_name = ?,
    phone = ?,
    nic_photo = ?
WHERE id = ? AND role = 'audience';

-- =============================================
-- 8. UPDATE USER PHONE ONLY
-- =============================================
UPDATE users 
SET phone = ?
WHERE id = ? AND role = 'audience';

-- =============================================
-- 9. CHECK IF PROFILE EXISTS
-- =============================================
SELECT COUNT(*) as profile_exists 
FROM user_bios 
WHERE user_id = ?;

-- =============================================
-- 10. DELETE PROFILE (Keep user, remove bio)
-- =============================================
DELETE FROM user_bios 
WHERE user_id = ?;

-- =============================================
-- 11. GET ALL AUDIENCE MEMBERS WITH PROFILES
-- =============================================
SELECT 
    u.id,
    u.full_name,
    u.email,
    u.phone,
    ub.bio,
    ub.profile_image,
    u.created_at
FROM users u
LEFT JOIN user_bios ub ON u.id = ub.user_id
WHERE u.role = 'audience'
ORDER BY u.created_at DESC;

-- =============================================
-- 12. SEARCH AUDIENCE BY NAME OR EMAIL
-- =============================================
SELECT 
    u.id,
    u.full_name,
    u.email,
    u.phone,
    ub.profile_image
FROM users u
LEFT JOIN user_bios ub ON u.id = ub.user_id
WHERE u.role = 'audience'
AND (u.full_name LIKE ? OR u.email LIKE ?)
ORDER BY u.full_name ASC;

-- =============================================
-- 13. COUNT TOTAL AUDIENCE MEMBERS
-- =============================================
SELECT COUNT(*) as total_audience 
FROM users 
WHERE role = 'audience';

-- =============================================
-- 14. COUNT AUDIENCE WITH PROFILES
-- =============================================
SELECT COUNT(DISTINCT ub.user_id) as profiles_created
FROM user_bios ub
INNER JOIN users u ON ub.user_id = u.id
WHERE u.role = 'audience';

-- =============================================
-- 15. GET RECENTLY JOINED AUDIENCE
-- =============================================
SELECT 
    u.id,
    u.full_name,
    u.email,
    u.phone,
    ub.profile_image,
    u.created_at
FROM users u
LEFT JOIN user_bios ub ON u.id = ub.user_id
WHERE u.role = 'audience'
ORDER BY u.created_at DESC
LIMIT 10;

-- =============================================
-- 16. GET AUDIENCE PROFILE FOR EDIT FORM
-- =============================================
SELECT 
    u.id,
    u.full_name,
    u.email,
    u.phone,
    u.nic_photo,
    IFNULL(ub.bio, '') as bio,
    IFNULL(ub.profile_image, '') as profile_image
FROM users u
LEFT JOIN user_bios ub ON u.id = ub.user_id
WHERE u.id = ?;

-- =============================================
-- 17. UPDATE COMPLETE PROFILE (User + Bio)
-- =============================================
-- Step 1: Update users table
UPDATE users 
SET full_name = ?, phone = ?
WHERE id = ?;

-- Step 2: Update or insert bio
INSERT INTO user_bios (user_id, bio, profile_image)
VALUES (?, ?, ?)
ON DUPLICATE KEY UPDATE
    bio = VALUES(bio),
    profile_image = VALUES(profile_image),
    updated_at = CURRENT_TIMESTAMP;

-- =============================================
-- 18. CHANGE EMAIL
-- =============================================
UPDATE users 
SET email = ?
WHERE id = ? AND role = 'audience';

-- =============================================
-- 19. CHANGE PASSWORD
-- =============================================
UPDATE users 
SET password = ?
WHERE id = ? AND role = 'audience';

-- =============================================
-- 20. VERIFY CURRENT PASSWORD
-- =============================================
SELECT id, password 
FROM users 
WHERE id = ? AND role = 'audience';
