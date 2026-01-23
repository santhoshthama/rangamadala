-- Production Manager Assignment System
-- Quick Reference & Sample Data

-- =====================================================
-- TABLE STRUCTURES
-- =====================================================

-- drama_manager_assignments: Tracks current PM for each drama
-- One active PM per drama (enforced by unique constraint)
CREATE TABLE IF NOT EXISTS `drama_manager_assignments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `drama_id` INT(11) NOT NULL,
  `manager_artist_id` INT(11) NOT NULL,
  `assigned_by` INT(11) NOT NULL,
  `assigned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('active','removed') NOT NULL DEFAULT 'active',
  `removed_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_drama_active_manager` (`drama_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- drama_manager_requests: Tracks PM invitations
CREATE TABLE IF NOT EXISTS `drama_manager_requests` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `drama_id` INT(11) NOT NULL,
  `artist_id` INT(11) NOT NULL,
  `director_id` INT(11) NOT NULL,
  `status` ENUM('pending','accepted','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `message` TEXT DEFAULT NULL,
  `requested_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `responded_at` DATETIME DEFAULT NULL,
  `response_note` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SAMPLE QUERIES
-- =====================================================

-- Get current PM for a drama
SELECT dma.*, u.full_name, u.email, u.phone
FROM drama_manager_assignments dma
INNER JOIN users u ON dma.manager_artist_id = u.id
WHERE dma.drama_id = 1 
AND dma.status = 'active';

-- Get pending PM requests for an artist
SELECT dmr.*, d.drama_name, director.full_name as director_name
FROM drama_manager_requests dmr
INNER JOIN dramas d ON dmr.drama_id = d.id
INNER JOIN users director ON dmr.director_id = director.id
WHERE dmr.artist_id = 2 
AND dmr.status = 'pending';

-- Get all dramas where user is Production Manager
SELECT d.*, dma.assigned_at
FROM drama_manager_assignments dma
INNER JOIN dramas d ON dma.drama_id = d.id
WHERE dma.manager_artist_id = 2 
AND dma.status = 'active';

-- Search available artists to be PM (excludes director and current PM)
SELECT u.id, u.full_name, u.email, u.phone
FROM users u
WHERE u.role = 'artist' 
AND u.id != 1  -- Not the director
AND u.id NOT IN (
    SELECT manager_artist_id 
    FROM drama_manager_assignments 
    WHERE drama_id = 1 
    AND status = 'active'
)
ORDER BY u.full_name ASC
LIMIT 50;

-- =====================================================
-- WORKFLOW EXAMPLE
-- =====================================================

-- Step 1: Director sends PM request
INSERT INTO drama_manager_requests 
(drama_id, artist_id, director_id, message, requested_at)
VALUES (1, 2, 1, 'Would you like to be the PM for my drama?', NOW());

-- Step 2: Artist accepts request
-- This triggers a transaction that:
-- a) Updates request status
UPDATE drama_manager_requests 
SET status = 'accepted', responded_at = NOW()
WHERE id = 1;

-- b) Removes old PM (if any)
UPDATE drama_manager_assignments 
SET status = 'removed', removed_at = NOW()
WHERE drama_id = 1 AND status = 'active';

-- c) Creates new assignment
INSERT INTO drama_manager_assignments 
(drama_id, manager_artist_id, assigned_by, assigned_at, status)
VALUES (1, 2, 1, NOW(), 'active');

-- d) Cancels other pending requests
UPDATE drama_manager_requests 
SET status = 'cancelled'
WHERE drama_id = 1 AND status = 'pending' AND id != 1;

-- =====================================================
-- USEFUL REPORTS
-- =====================================================

-- Count PMs per drama
SELECT d.drama_name, COUNT(dma.id) as pm_count
FROM dramas d
LEFT JOIN drama_manager_assignments dma ON d.id = dma.drama_id AND dma.status = 'active'
GROUP BY d.id, d.drama_name;

-- List all active PM assignments with details
SELECT 
    d.drama_name,
    d.certificate_number,
    pm.full_name as manager_name,
    pm.email as manager_email,
    director.full_name as director_name,
    dma.assigned_at
FROM drama_manager_assignments dma
INNER JOIN dramas d ON dma.drama_id = d.id
INNER JOIN users pm ON dma.manager_artist_id = pm.id
INNER JOIN users director ON dma.assigned_by = director.id
WHERE dma.status = 'active'
ORDER BY dma.assigned_at DESC;

-- Pending PM requests summary
SELECT 
    d.drama_name,
    artist.full_name as invited_artist,
    director.full_name as director_name,
    dmr.requested_at,
    dmr.message
FROM drama_manager_requests dmr
INNER JOIN dramas d ON dmr.drama_id = d.id
INNER JOIN users artist ON dmr.artist_id = artist.id
INNER JOIN users director ON dmr.director_id = director.id
WHERE dmr.status = 'pending'
ORDER BY dmr.requested_at DESC;

-- Artists with most PM assignments (active)
SELECT 
    u.full_name,
    u.email,
    COUNT(dma.id) as active_dramas
FROM users u
INNER JOIN drama_manager_assignments dma ON u.id = dma.manager_artist_id
WHERE dma.status = 'active'
GROUP BY u.id, u.full_name, u.email
ORDER BY active_dramas DESC;

-- =====================================================
-- MAINTENANCE QUERIES
-- =====================================================

-- Find dramas with multiple active PMs (should be empty due to unique constraint)
SELECT drama_id, COUNT(*) as pm_count
FROM drama_manager_assignments
WHERE status = 'active'
GROUP BY drama_id
HAVING COUNT(*) > 1;

-- Find orphaned requests (drama or user deleted)
SELECT dmr.*
FROM drama_manager_requests dmr
LEFT JOIN dramas d ON dmr.drama_id = d.id
LEFT JOIN users artist ON dmr.artist_id = artist.id
LEFT JOIN users director ON dmr.director_id = director.id
WHERE d.id IS NULL OR artist.id IS NULL OR director.id IS NULL;

-- Clean up old pending requests (older than 30 days)
UPDATE drama_manager_requests
SET status = 'cancelled'
WHERE status = 'pending' 
AND requested_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- =====================================================
-- TESTING DATA (Optional)
-- =====================================================

-- Insert sample PM request
-- INSERT INTO drama_manager_requests (drama_id, artist_id, director_id, message)
-- VALUES (1, 2, 1, 'Hello! Would you be interested in managing production for this drama?');

-- Insert sample PM assignment
-- INSERT INTO drama_manager_assignments (drama_id, manager_artist_id, assigned_by)
-- VALUES (1, 2, 1);

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================

-- These are created automatically by the migration, but listed here for reference:

-- drama_manager_assignments:
-- - PRIMARY KEY (id)
-- - UNIQUE KEY (drama_id, status)
-- - INDEX (manager_artist_id)
-- - INDEX (assigned_by)

-- drama_manager_requests:
-- - PRIMARY KEY (id)
-- - INDEX (drama_id)
-- - INDEX (artist_id)
-- - INDEX (director_id)
-- - INDEX (status)
-- - INDEX (artist_id, status, requested_at)
