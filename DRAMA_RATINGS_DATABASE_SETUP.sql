-- ========================================
-- DRAMA RATINGS DATABASE SETUP
-- ========================================
-- This script creates the drama_ratings table
-- and adds rating-related views and queries
-- ========================================

USE rangamandala_db;

-- Create drama_ratings table
CREATE TABLE IF NOT EXISTS `drama_ratings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `drama_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` tinyint NOT NULL COMMENT '1-5 star rating',
  `comment` text DEFAULT NULL COMMENT 'Optional user comment/feedback',
  `is_helpful` tinyint(1) DEFAULT 0 COMMENT '1=marked as helpful',
  `helpful_count` int DEFAULT 0 COMMENT 'Number of users who found this helpful',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_rating_per_user` (`drama_id`, `user_id`),
  KEY `drama_id` (`drama_id`),
  KEY `user_id` (`user_id`),
  KEY `rating` (`rating`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `ratings_ibfk_drama` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ratings_ibfk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- HELPFUL QUERIES FOR RATING OPERATIONS
-- ========================================

-- 1. GET DRAMA RATING SUMMARY (Average rating and count)
-- SELECT 
--     d.id,
--     d.title,
--     ROUND(AVG(dr.rating), 2) as average_rating,
--     COUNT(dr.id) as total_ratings,
--     COUNT(CASE WHEN dr.rating = 5 THEN 1 END) as five_star_count,
--     COUNT(CASE WHEN dr.rating = 4 THEN 1 END) as four_star_count,
--     COUNT(CASE WHEN dr.rating = 3 THEN 1 END) as three_star_count,
--     COUNT(CASE WHEN dr.rating = 2 THEN 1 END) as two_star_count,
--     COUNT(CASE WHEN dr.rating = 1 THEN 1 END) as one_star_count
-- FROM dramas d
-- LEFT JOIN drama_ratings dr ON d.id = dr.drama_id
-- WHERE d.id = ?
-- GROUP BY d.id, d.title;

-- 2. SUBMIT NEW RATING (Insert or Update)
-- INSERT INTO drama_ratings (drama_id, user_id, rating, comment)
-- VALUES (?, ?, ?, ?)
-- ON DUPLICATE KEY UPDATE
--     rating = VALUES(rating),
--     comment = VALUES(comment),
--     updated_at = CURRENT_TIMESTAMP;

-- 3. GET ALL RATINGS FOR A DRAMA WITH USER INFO
-- SELECT 
--     dr.id,
--     dr.drama_id,
--     dr.user_id,
--     dr.rating,
--     dr.comment,
--     dr.helpful_count,
--     dr.created_at,
--     u.full_name,
--     u.email
-- FROM drama_ratings dr
-- JOIN users u ON dr.user_id = u.id
-- WHERE dr.drama_id = ?
-- ORDER BY dr.created_at DESC
-- LIMIT ?, ?;

-- 4. GET USER'S RATING FOR A SPECIFIC DRAMA
-- SELECT * FROM drama_ratings
-- WHERE drama_id = ? AND user_id = ?;

-- 5. CHECK IF USER ALREADY RATED DRAMA
-- SELECT COUNT(*) as has_rated FROM drama_ratings
-- WHERE drama_id = ? AND user_id = ?;

-- 6. UPDATE HELPFULNESS COUNT
-- UPDATE drama_ratings 
-- SET helpful_count = helpful_count + 1, is_helpful = 1
-- WHERE id = ?;

-- 7. DELETE A RATING
-- DELETE FROM drama_ratings 
-- WHERE id = ? AND user_id = ?;

-- 8. GET TOP RATED DRAMAS
-- SELECT 
--     d.id,
--     d.title,
--     d.image,
--     d.ticket_price,
--     ROUND(AVG(dr.rating), 2) as average_rating,
--     COUNT(dr.id) as total_ratings
-- FROM dramas d
-- LEFT JOIN drama_ratings dr ON d.id = dr.drama_id
-- GROUP BY d.id, d.title
-- HAVING COUNT(dr.id) >= 1
-- ORDER BY average_rating DESC, total_ratings DESC
-- LIMIT 0, 10;

-- 9. GET RATING STATISTICS FOR ADMIN
-- SELECT 
--     COUNT(DISTINCT d.id) as total_dramas_rated,
--     COUNT(dr.id) as total_ratings,
--     ROUND(AVG(dr.rating), 2) as overall_average,
--     COUNT(CASE WHEN dr.rating = 5 THEN 1 END) as total_five_star,
--     COUNT(CASE WHEN dr.rating = 4 THEN 1 END) as total_four_star,
--     COUNT(CASE WHEN dr.rating = 3 THEN 1 END) as total_three_star,
--     COUNT(CASE WHEN dr.rating = 2 THEN 1 END) as total_two_star,
--     COUNT(CASE WHEN dr.rating = 1 THEN 1 END) as total_one_star
-- FROM dramas d
-- LEFT JOIN drama_ratings dr ON d.id = dr.drama_id;

-- 10. GET RECENT RATINGS
-- SELECT 
--     dr.id,
--     dr.drama_id,
--     d.title as drama_title,
--     dr.user_id,
--     u.full_name,
--     dr.rating,
--     dr.comment,
--     dr.created_at
-- FROM drama_ratings dr
-- JOIN dramas d ON dr.drama_id = d.id
-- JOIN users u ON dr.user_id = u.id
-- ORDER BY dr.created_at DESC
-- LIMIT 0, 20;

-- ========================================
-- VERIFICATION QUERY
-- ========================================
-- Run this to verify the table was created:
-- SHOW TABLES LIKE 'drama_ratings';
-- 
-- Count records:
-- SELECT COUNT(*) as total_ratings FROM drama_ratings;
-- ========================================
