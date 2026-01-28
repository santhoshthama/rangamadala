-- =============================================
-- ADMIN VERIFICATION SYSTEM QUERIES
-- =============================================
-- For verifying artist and service provider registrations

-- =============================================
-- 1. GET PENDING REGISTRATIONS (Artists & Service Providers)
-- =============================================
SELECT 
    u.id,
    u.full_name,
    u.email,
    u.phone,
    u.role,
    u.nic_photo,
    u.created_at,
    CASE 
        WHEN u.role = 'artist' THEN 'Artist Registration'
        WHEN u.role = 'service_provider' THEN 'Service Provider Registration'
        ELSE 'Other'
    END as registration_type
FROM users u
WHERE u.is_verified = 0 
AND u.role IN ('artist', 'service_provider')
ORDER BY u.created_at ASC;

-- =============================================
-- 2. GET PENDING REGISTRATION DETAILS (Single User)
-- =============================================
SELECT 
    u.id,
    u.full_name,
    u.email,
    u.phone,
    u.role,
    u.nic_photo,
    u.created_at,
    sp.professional_title,
    sp.location,
    sp.nic_number,
    sp.social_media_link,
    sp.years_experience,
    sp.professional_summary,
    sp.nic_photo_front,
    sp.nic_photo_back
FROM users u
LEFT JOIN serviceprovider sp ON u.id = sp.user_id
WHERE u.id = ?;

-- =============================================
-- 3. APPROVE USER (Artist or Service Provider)
-- =============================================
UPDATE users 
SET 
    is_verified = 1,
    verified_by = ?,
    verified_at = CURRENT_TIMESTAMP,
    rejection_reason = NULL
WHERE id = ? 
AND role IN ('artist', 'service_provider');

-- =============================================
-- 4. REJECT USER WITH REASON
-- =============================================
UPDATE users 
SET 
    is_verified = 0,
    rejection_reason = ?,
    verified_by = ?,
    verified_at = CURRENT_TIMESTAMP
WHERE id = ? 
AND role IN ('artist', 'service_provider');

-- =============================================
-- 5. DELETE REJECTED USER (Optional)
-- =============================================
DELETE FROM users 
WHERE id = ? 
AND is_verified = 0 
AND role IN ('artist', 'service_provider');

-- =============================================
-- 6. GET ALL VERIFIED USERS
-- =============================================
SELECT 
    u.id,
    u.full_name,
    u.email,
    u.phone,
    u.role,
    u.verified_at,
    v.full_name as verified_by_name
FROM users u
LEFT JOIN users v ON u.verified_by = v.id
WHERE u.is_verified = 1 
AND u.role IN ('artist', 'service_provider')
ORDER BY u.verified_at DESC;

-- =============================================
-- 7. GET ALL REJECTED USERS
-- =============================================
SELECT 
    u.id,
    u.full_name,
    u.email,
    u.role,
    u.rejection_reason,
    u.verified_at as rejected_at,
    v.full_name as rejected_by_name
FROM users u
LEFT JOIN users v ON u.verified_by = v.id
WHERE u.is_verified = 0 
AND u.rejection_reason IS NOT NULL
AND u.role IN ('artist', 'service_provider')
ORDER BY u.verified_at DESC;

-- =============================================
-- 8. COUNT PENDING REGISTRATIONS
-- =============================================
SELECT COUNT(*) as pending_count
FROM users
WHERE is_verified = 0 
AND role IN ('artist', 'service_provider');

-- =============================================
-- 9. COUNT BY ROLE
-- =============================================
SELECT 
    role,
    SUM(CASE WHEN is_verified = 0 THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN is_verified = 1 THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN is_verified = 0 AND rejection_reason IS NOT NULL THEN 1 ELSE 0 END) as rejected
FROM users
WHERE role IN ('artist', 'service_provider')
GROUP BY role;

-- =============================================
-- 10. RE-ACTIVATE REJECTED USER (Second Chance)
-- =============================================
UPDATE users 
SET 
    is_verified = 1,
    rejection_reason = NULL,
    verified_by = ?,
    verified_at = CURRENT_TIMESTAMP
WHERE id = ?;

-- =============================================
-- 11. REVOKE VERIFICATION (Admin Undo)
-- =============================================
UPDATE users 
SET 
    is_verified = 0,
    rejection_reason = 'Verification revoked by admin',
    verified_by = ?,
    verified_at = CURRENT_TIMESTAMP
WHERE id = ?;

-- =============================================
-- 12. GET VERIFICATION STATISTICS
-- =============================================
SELECT 
    COUNT(*) as total_registrations,
    SUM(CASE WHEN is_verified = 1 THEN 1 ELSE 0 END) as total_approved,
    SUM(CASE WHEN is_verified = 0 AND rejection_reason IS NULL THEN 1 ELSE 0 END) as total_pending,
    SUM(CASE WHEN rejection_reason IS NOT NULL THEN 1 ELSE 0 END) as total_rejected,
    ROUND(SUM(CASE WHEN is_verified = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) as approval_rate
FROM users
WHERE role IN ('artist', 'service_provider');

-- =============================================
-- 13. CHECK IF USER CAN LOGIN
-- =============================================
-- Use this in login controller
SELECT 
    id, 
    full_name, 
    email, 
    password, 
    role, 
    is_verified
FROM users
WHERE email = ?
AND (
    role = 'audience' 
    OR role = 'admin' 
    OR (role IN ('artist', 'service_provider') AND is_verified = 1)
);

-- =============================================
-- 14. AUTO-APPROVE AUDIENCE (No verification needed)
-- =============================================
-- Note: Audience members should be auto-approved
UPDATE users 
SET is_verified = 1
WHERE role = 'audience' AND is_verified = 0;

-- =============================================
-- 15. GET RECENT VERIFICATION ACTIVITY
-- =============================================
SELECT 
    u.id,
    u.full_name,
    u.email,
    u.role,
    CASE 
        WHEN u.is_verified = 1 THEN 'Approved'
        WHEN u.rejection_reason IS NOT NULL THEN 'Rejected'
        ELSE 'Pending'
    END as status,
    u.verified_at as action_date,
    v.full_name as admin_name,
    u.rejection_reason
FROM users u
LEFT JOIN users v ON u.verified_by = v.id
WHERE u.role IN ('artist', 'service_provider')
AND u.verified_at IS NOT NULL
ORDER BY u.verified_at DESC
LIMIT 20;
