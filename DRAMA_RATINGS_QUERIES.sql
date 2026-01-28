-- ========================================
-- DRAMA RATINGS - HELPFUL QUERIES
-- ========================================
-- This file contains useful queries for
-- testing, admin dashboards, and reporting
-- ========================================

USE rangamandala_db;

-- ========================================
-- 1. GET DRAMA RATING SUMMARY
-- ========================================
-- Shows average rating, total ratings, and distribution
-- Parameters: @drama_id

SELECT 
    d.id,
    d.title,
    COUNT(dr.id) as total_ratings,
    ROUND(AVG(dr.rating), 2) as average_rating,
    COUNT(CASE WHEN dr.rating = 5 THEN 1 END) as five_star_count,
    COUNT(CASE WHEN dr.rating = 4 THEN 1 END) as four_star_count,
    COUNT(CASE WHEN dr.rating = 3 THEN 1 END) as three_star_count,
    COUNT(CASE WHEN dr.rating = 2 THEN 1 END) as two_star_count,
    COUNT(CASE WHEN dr.rating = 1 THEN 1 END) as one_star_count
FROM dramas d
LEFT JOIN drama_ratings dr ON d.id = dr.drama_id
WHERE d.id = 1
GROUP BY d.id, d.title;

-- ========================================
-- 2. GET ALL RATINGS FOR A DRAMA
-- ========================================
-- Returns all ratings with user information

SELECT 
    dr.id,
    dr.drama_id,
    dr.user_id,
    dr.rating,
    dr.comment,
    dr.helpful_count,
    dr.is_helpful,
    dr.created_at,
    dr.updated_at,
    u.full_name,
    u.email
FROM drama_ratings dr
JOIN users u ON dr.user_id = u.id
WHERE dr.drama_id = 1
ORDER BY dr.created_at DESC;

-- ========================================
-- 3. GET USER'S RATING FOR A DRAMA
-- ========================================
-- Check if specific user has rated and get their rating

SELECT * FROM drama_ratings
WHERE drama_id = 1 AND user_id = 2;

-- ========================================
-- 4. CHECK IF USER HAS RATED
-- ========================================
-- Simple count query

SELECT COUNT(*) as has_rated FROM drama_ratings
WHERE drama_id = 1 AND user_id = 2;

-- ========================================
-- 5. GET TOP RATED DRAMAS
-- ========================================
-- Shows dramas with highest average ratings (min 1 rating)

SELECT 
    d.id,
    d.title,
    d.image,
    d.ticket_price,
    d.event_date,
    ROUND(AVG(dr.rating), 2) as average_rating,
    COUNT(dr.id) as total_ratings,
    MIN(dr.rating) as lowest_rating,
    MAX(dr.rating) as highest_rating
FROM dramas d
LEFT JOIN drama_ratings dr ON d.id = dr.drama_id
GROUP BY d.id, d.title, d.image, d.ticket_price, d.event_date
HAVING COUNT(dr.id) >= 1
ORDER BY average_rating DESC, total_ratings DESC
LIMIT 0, 20;

-- ========================================
-- 6. GET LOWEST RATED DRAMAS
-- ========================================
-- Shows dramas needing improvement (min 3 ratings)

SELECT 
    d.id,
    d.title,
    ROUND(AVG(dr.rating), 2) as average_rating,
    COUNT(dr.id) as total_ratings
FROM dramas d
LEFT JOIN drama_ratings dr ON d.id = dr.drama_id
GROUP BY d.id, d.title
HAVING COUNT(dr.id) >= 3
ORDER BY average_rating ASC
LIMIT 0, 10;

-- ========================================
-- 7. GET MOST HELPFUL RATINGS
-- ========================================
-- Shows ratings marked as helpful by others

SELECT 
    dr.id,
    d.title as drama_title,
    u.full_name as reviewer_name,
    dr.rating,
    dr.comment,
    dr.helpful_count,
    dr.created_at
FROM drama_ratings dr
JOIN dramas d ON dr.drama_id = d.id
JOIN users u ON dr.user_id = u.id
WHERE dr.helpful_count > 0
ORDER BY dr.helpful_count DESC
LIMIT 0, 20;

-- ========================================
-- 8. GET RECENT RATINGS
-- ========================================
-- Latest ratings across all dramas

SELECT 
    dr.id,
    dr.drama_id,
    d.title as drama_title,
    dr.user_id,
    u.full_name,
    dr.rating,
    dr.comment,
    dr.created_at
FROM drama_ratings dr
JOIN dramas d ON dr.drama_id = d.id
JOIN users u ON dr.user_id = u.id
ORDER BY dr.created_at DESC
LIMIT 0, 50;

-- ========================================
-- 9. GET RATINGS WITH COMMENTS
-- ========================================
-- Only reviews with written comments

SELECT 
    dr.id,
    d.title as drama_title,
    u.full_name,
    dr.rating,
    dr.comment,
    dr.created_at
FROM drama_ratings dr
JOIN dramas d ON dr.drama_id = d.id
JOIN users u ON dr.user_id = u.id
WHERE dr.comment IS NOT NULL AND dr.comment != ''
ORDER BY dr.created_at DESC
LIMIT 0, 30;

-- ========================================
-- 10. GET OVERALL RATING STATISTICS
-- ========================================
-- Admin dashboard stats

SELECT 
    COUNT(DISTINCT drama_id) as total_dramas_rated,
    COUNT(DISTINCT user_id) as total_reviewers,
    COUNT(id) as total_ratings,
    ROUND(AVG(rating), 2) as overall_average_rating,
    MIN(rating) as lowest_rating,
    MAX(rating) as highest_rating,
    COUNT(CASE WHEN rating = 5 THEN 1 END) as total_five_star,
    COUNT(CASE WHEN rating = 4 THEN 1 END) as total_four_star,
    COUNT(CASE WHEN rating = 3 THEN 1 END) as total_three_star,
    COUNT(CASE WHEN rating = 2 THEN 1 END) as total_two_star,
    COUNT(CASE WHEN rating = 1 THEN 1 END) as total_one_star,
    COUNT(CASE WHEN comment IS NOT NULL THEN 1 END) as ratings_with_comments,
    COUNT(CASE WHEN helpful_count > 0 THEN 1 END) as helpful_ratings,
    ROUND(100.0 * COUNT(CASE WHEN rating >= 4 THEN 1 END) / COUNT(id), 2) as positive_percentage
FROM drama_ratings;

-- ========================================
-- 11. GET RATING DISTRIBUTION BY STAR
-- ========================================
-- Shows breakdown by star rating

SELECT 
    5 as star_rating,
    COUNT(*) as count,
    ROUND(100.0 * COUNT(*) / (SELECT COUNT(*) FROM drama_ratings), 2) as percentage
FROM drama_ratings
WHERE rating = 5
UNION ALL
SELECT 4, COUNT(*), ROUND(100.0 * COUNT(*) / (SELECT COUNT(*) FROM drama_ratings), 2)
FROM drama_ratings WHERE rating = 4
UNION ALL
SELECT 3, COUNT(*), ROUND(100.0 * COUNT(*) / (SELECT COUNT(*) FROM drama_ratings), 2)
FROM drama_ratings WHERE rating = 3
UNION ALL
SELECT 2, COUNT(*), ROUND(100.0 * COUNT(*) / (SELECT COUNT(*) FROM drama_ratings), 2)
FROM drama_ratings WHERE rating = 2
UNION ALL
SELECT 1, COUNT(*), ROUND(100.0 * COUNT(*) / (SELECT COUNT(*) FROM drama_ratings), 2)
FROM drama_ratings WHERE rating = 1
ORDER BY star_rating DESC;

-- ========================================
-- 12. GET USER RATING HISTORY
-- ========================================
-- All ratings submitted by a specific user

SELECT 
    dr.id,
    d.title as drama_title,
    dr.rating,
    dr.comment,
    dr.created_at,
    dr.updated_at,
    CASE WHEN dr.created_at = dr.updated_at THEN 'Original' ELSE 'Updated' END as status
FROM drama_ratings dr
JOIN dramas d ON dr.drama_id = d.id
WHERE dr.user_id = 2
ORDER BY dr.created_at DESC;

-- ========================================
-- 13. GET DRAMAS NOT YET RATED BY USER
-- ========================================
-- Shows which dramas a user hasn't rated

SELECT 
    d.id,
    d.title,
    d.ticket_price
FROM dramas d
WHERE d.id NOT IN (
    SELECT DISTINCT drama_id FROM drama_ratings 
    WHERE user_id = 2
)
ORDER BY d.title;

-- ========================================
-- 14. GET RATING ACTIVITY BY DATE
-- ========================================
-- Shows rating submissions over time

SELECT 
    DATE(created_at) as rating_date,
    COUNT(*) as ratings_submitted,
    ROUND(AVG(rating), 2) as avg_rating_that_day,
    MIN(rating) as min_rating,
    MAX(rating) as max_rating
FROM drama_ratings
GROUP BY DATE(created_at)
ORDER BY rating_date DESC
LIMIT 0, 30;

-- ========================================
-- 15. GET DRAMAS WITH NO RATINGS
-- ========================================
-- Identify dramas that need promotion

SELECT 
    d.id,
    d.title,
    d.category_id,
    d.event_date,
    d.ticket_price,
    d.created_at
FROM dramas d
LEFT JOIN drama_ratings dr ON d.id = dr.drama_id
WHERE dr.id IS NULL
ORDER BY d.created_at DESC;

-- ========================================
-- 16. UPDATE HELPFUL COUNT
-- ========================================
-- Mark a rating as helpful

UPDATE drama_ratings 
SET helpful_count = helpful_count + 1, is_helpful = 1
WHERE id = 1;

-- ========================================
-- 17. DELETE A RATING
-- ========================================
-- Remove a rating (typically by admin or user)

DELETE FROM drama_ratings 
WHERE id = 1 AND user_id = 2;

-- ========================================
-- 18. RESET RATING FOR DRAMA
-- ========================================
-- Clear all ratings for a drama (admin only)

DELETE FROM drama_ratings WHERE drama_id = 1;

-- ========================================
-- 19. GET RATING ANALYSIS BY CATEGORY
-- ========================================
-- Shows average ratings by drama category

SELECT 
    c.name as category,
    COUNT(DISTINCT d.id) as dramas_in_category,
    COUNT(DISTINCT dr.id) as total_ratings,
    ROUND(AVG(dr.rating), 2) as avg_rating,
    COUNT(CASE WHEN dr.rating = 5 THEN 1 END) as five_star_count,
    COUNT(CASE WHEN dr.rating >= 4 THEN 1 END) as positive_ratings
FROM categories c
LEFT JOIN dramas d ON c.id = d.category_id
LEFT JOIN drama_ratings dr ON d.id = dr.drama_id
GROUP BY c.name
ORDER BY avg_rating DESC;

-- ========================================
-- 20. GET RATING SUBMISSION TRENDS
-- ========================================
-- Shows which dramas are getting rated most frequently

SELECT 
    d.title,
    COUNT(dr.id) as total_ratings,
    COUNT(CASE WHEN dr.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as ratings_last_7_days,
    COUNT(CASE WHEN dr.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as ratings_last_30_days,
    ROUND(AVG(dr.rating), 2) as avg_rating
FROM dramas d
LEFT JOIN drama_ratings dr ON d.id = dr.drama_id
GROUP BY d.id, d.title
HAVING COUNT(dr.id) > 0
ORDER BY ratings_last_7_days DESC
LIMIT 0, 20;

-- ========================================
-- VERIFICATION QUERIES
-- ========================================

-- Check table exists
SHOW TABLES LIKE 'drama_ratings';

-- Count total ratings
SELECT COUNT(*) as total_ratings FROM drama_ratings;

-- Check constraints
SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
WHERE TABLE_NAME = 'drama_ratings' AND CONSTRAINT_TYPE = 'UNIQUE';

-- Check foreign keys
SELECT * FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS 
WHERE TABLE_NAME = 'drama_ratings';

-- Check indexes
SHOW INDEXES FROM drama_ratings;
