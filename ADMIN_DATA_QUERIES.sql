-- ========================================
-- ADMIN DATA QUERIES FOR RANGAMADALA
-- ========================================

-- ⚠️ IMPORTANT: Before running any queries below, ensure your database is set up correctly!
-- If you get error "#1054 - Unknown column", it means the tables don't exist.
-- Solution: Run database_setup.sql file in your database first!
-- See TROUBLESHOOTING_QUERIES.sql for verification steps.

-- ========================================
-- 1. USER MANAGEMENT QUERIES
-- ========================================

-- Get all admin users
SELECT * FROM users WHERE role = 'admin';

-- Get all users with their role counts
SELECT role, COUNT(*) as count FROM users GROUP BY role;

-- Get all users with creation date
SELECT id, full_name, email, phone, role, created_at, updated_at 
FROM users 
ORDER BY created_at DESC;

-- Get specific user details by email
SELECT * FROM users WHERE email = 'admin@rangamadala.com';

-- Get all users created in the last 30 days
SELECT id, full_name, email, role, created_at 
FROM users 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
ORDER BY created_at DESC;

-- Get user count by role
SELECT 
    role,
    COUNT(*) as total_users,
    DATE(MIN(created_at)) as first_user_date,
    DATE(MAX(created_at)) as latest_user_date
FROM users
GROUP BY role;

-- ========================================
-- 2. DRAMA/EVENT MANAGEMENT QUERIES
-- ========================================

-- Get all dramas with creator information
SELECT 
    d.id,
    d.title,
    d.description,
    c.name as category,
    d.venue,
    d.event_date,
    d.event_time,
    d.duration,
    d.ticket_price,
    u.full_name as created_by,
    d.created_at,
    d.updated_at
FROM dramas d
LEFT JOIN categories c ON d.category_id = c.id
LEFT JOIN users u ON d.created_by = u.id
ORDER BY d.created_at DESC;

-- Get drama count by category
SELECT 
    c.name as category,
    COUNT(d.id) as drama_count
FROM categories c
LEFT JOIN dramas d ON c.id = d.category_id
GROUP BY c.id, c.name
ORDER BY drama_count DESC;

-- Get upcoming dramas
SELECT 
    id,
    title,
    venue,
    event_date,
    event_time,
    ticket_price,
    created_at
FROM dramas
WHERE event_date >= CURDATE()
ORDER BY event_date ASC;

-- Get dramas created by specific artist
SELECT * FROM dramas 
WHERE created_by = [user_id] OR creator_artist_id = [user_id]
ORDER BY created_at DESC;

-- Get total drama statistics
SELECT 
    COUNT(*) as total_dramas,
    COUNT(DISTINCT category_id) as total_categories,
    SUM(CASE WHEN event_date >= CURDATE() THEN 1 ELSE 0 END) as upcoming_dramas,
    AVG(ticket_price) as average_ticket_price,
    MIN(ticket_price) as min_ticket_price,
    MAX(ticket_price) as max_ticket_price
FROM dramas;

-- ========================================
-- 3. SERVICE PROVIDER QUERIES
-- ========================================

-- Get all service providers with details
SELECT 
    sp.user_id,
    sp.full_name,
    sp.professional_title,
    sp.email,
    sp.phone,
    sp.location,
    sp.years_experience,
    sp.availability,
    u.created_at
FROM serviceprovider sp
LEFT JOIN users u ON sp.user_id = u.id
ORDER BY sp.created_at DESC;

-- Get service providers by availability
SELECT 
    sp.user_id,
    sp.full_name,
    sp.professional_title,
    sp.phone,
    sp.availability,
    sp.availability_notes
FROM serviceprovider sp
WHERE sp.availability = 1
ORDER BY sp.years_experience DESC;

-- Get service providers with their service types
SELECT 
    sp.full_name,
    sp.professional_title,
    sp.email,
    st.service_type,
    sp.years_experience
FROM serviceprovider sp
LEFT JOIN services s ON sp.user_id = s.provider_id
LEFT JOIN service_types st ON s.service_type_id = st.service_type_id
ORDER BY sp.full_name ASC;

-- Get service provider count by service type
SELECT 
    st.service_type,
    COUNT(DISTINCT sp.user_id) as provider_count
FROM service_types st
LEFT JOIN services s ON st.service_type_id = s.service_type_id
LEFT JOIN serviceprovider sp ON s.provider_id = sp.user_id
GROUP BY st.service_type_id, st.service_type
ORDER BY provider_count DESC;

-- ========================================
-- 4. ARTIST QUERIES
-- ========================================

-- Get all artists (users with role = 'artist')
SELECT 
    id,
    full_name,
    email,
    phone,
    created_at,
    updated_at
FROM users
WHERE role = 'artist'
ORDER BY full_name ASC;

-- Get artists with their drama count
SELECT 
    u.id,
    u.full_name,
    u.email,
    COUNT(d.id) as drama_count
FROM users u
LEFT JOIN dramas d ON u.id = d.created_by
WHERE u.role = 'artist'
GROUP BY u.id, u.full_name, u.email
ORDER BY drama_count DESC;

-- ========================================
-- 5. AUDIENCE QUERIES
-- ========================================

-- Get all audience members
SELECT 
    id,
    full_name,
    email,
    phone,
    created_at,
    updated_at
FROM users
WHERE role = 'audience'
ORDER BY created_at DESC;

-- Get audience member count
SELECT COUNT(*) as total_audience_members FROM users WHERE role = 'audience';

-- ========================================
-- 6. DASHBOARD STATISTICS QUERIES
-- ========================================

-- Get comprehensive platform statistics
SELECT 
    (SELECT COUNT(*) FROM users WHERE role = 'admin') as total_admins,
    (SELECT COUNT(*) FROM users WHERE role = 'artist') as total_artists,
    (SELECT COUNT(*) FROM users WHERE role = 'audience') as total_audience,
    (SELECT COUNT(*) FROM users WHERE role = 'service_provider') as total_service_providers,
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM dramas) as total_dramas,
    (SELECT COUNT(*) FROM categories) as total_categories,
    (SELECT COUNT(*) FROM serviceprovider) as total_service_providers_detailed
;

-- Get user growth over time (last 6 months)
SELECT 
    MONTH(created_at) as month,
    YEAR(created_at) as year,
    role,
    COUNT(*) as new_users
FROM users
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
GROUP BY YEAR(created_at), MONTH(created_at), role
ORDER BY year DESC, month DESC;

-- Get drama statistics
SELECT 
    c.name as category,
    COUNT(d.id) as drama_count,
    AVG(d.ticket_price) as avg_price,
    COUNT(CASE WHEN d.event_date >= CURDATE() THEN 1 END) as upcoming
FROM categories c
LEFT JOIN dramas d ON c.id = d.category_id
GROUP BY c.id, c.name
ORDER BY drama_count DESC;

-- ========================================
-- 7. VERIFICATION & STATUS QUERIES
-- ========================================

-- Get users with profile pictures
SELECT 
    id,
    full_name,
    email,
    role,
    nic_photo,
    created_at
FROM users
WHERE nic_photo IS NOT NULL AND nic_photo != ''
ORDER BY role ASC;

-- Get users without profile pictures
SELECT 
    id,
    full_name,
    email,
    role,
    created_at
FROM users
WHERE nic_photo IS NULL OR nic_photo = ''
ORDER BY created_at DESC;

-- Get service providers with incomplete profiles
SELECT 
    sp.user_id,
    sp.full_name,
    sp.email,
    CASE 
        WHEN sp.professional_title IS NULL THEN 'Missing Title'
        WHEN sp.phone IS NULL THEN 'Missing Phone'
        WHEN sp.location IS NULL THEN 'Missing Location'
        WHEN sp.professional_summary IS NULL THEN 'Missing Summary'
        ELSE 'Complete'
    END as missing_field
FROM serviceprovider sp
WHERE sp.professional_title IS NULL 
   OR sp.phone IS NULL 
   OR sp.location IS NULL 
   OR sp.professional_summary IS NULL;

-- ========================================
-- 8. MANAGEMENT QUERIES
-- ========================================

-- Update admin password (use with caution)
-- UPDATE users SET password = '$2y$10$[bcrypt_hash]' WHERE email = 'admin@rangamadala.com';

-- Delete user (use with caution - all related data should be handled by foreign keys)
-- DELETE FROM users WHERE id = [user_id] AND role != 'admin';

-- Activate/Deactivate service provider
-- UPDATE serviceprovider SET availability = 0, availability_notes = 'Temporarily unavailable' WHERE user_id = [user_id];

-- Update user role
-- UPDATE users SET role = 'service_provider' WHERE id = [user_id];

-- ========================================
-- 9. REPORTING QUERIES
-- ========================================

-- Get recent registration report
SELECT 
    DATE(created_at) as registration_date,
    role,
    COUNT(*) as count
FROM users
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY DATE(created_at), role
ORDER BY registration_date DESC;

-- Get service provider rating report (if ratings table exists)
-- SELECT 
--     sp.full_name,
--     st.service_type,
--     AVG(r.rating) as avg_rating,
--     COUNT(r.id) as review_count
-- FROM serviceprovider sp
-- LEFT JOIN services s ON sp.user_id = s.provider_id
-- LEFT JOIN service_types st ON s.service_type_id = st.service_type_id
-- LEFT JOIN ratings r ON s.id = r.service_id
-- GROUP BY sp.user_id, sp.full_name, st.service_type
-- ORDER BY avg_rating DESC;

-- Get highest priced dramas
SELECT 
    title,
    venue,
    event_date,
    ticket_price,
    (SELECT full_name FROM users WHERE id = created_by) as created_by
FROM dramas
ORDER BY ticket_price DESC
LIMIT 10;

-- Get most popular categories
SELECT 
    c.name as category,
    COUNT(d.id) as drama_count,
    SUM(CASE WHEN d.event_date >= CURDATE() THEN 1 ELSE 0 END) as upcoming_count
FROM categories c
LEFT JOIN dramas d ON c.id = d.category_id
GROUP BY c.id, c.name
HAVING COUNT(d.id) > 0
ORDER BY drama_count DESC;

-- ========================================
-- NOTES:
-- 1. Replace [user_id] with actual user ID
-- 2. Use $2y$10$ bcrypt hashing for passwords
-- 3. Always backup database before running UPDATE/DELETE queries
-- 4. Test queries on development environment first
-- ========================================
