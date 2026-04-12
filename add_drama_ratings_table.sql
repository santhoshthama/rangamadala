-- Add drama ratings table for audience rating and review flow
-- Run this once on the Rangamadala database.

CREATE TABLE IF NOT EXISTS drama_ratings (
    id INT(11) NOT NULL AUTO_INCREMENT,
    drama_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    rating TINYINT(1) NOT NULL,
    comment TEXT DEFAULT NULL,
    helpful_count INT(11) NOT NULL DEFAULT 0,
    is_helpful TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_drama_user_rating (drama_id, user_id),
    KEY idx_drama_ratings_drama (drama_id),
    KEY idx_drama_ratings_user (user_id),
    KEY idx_drama_ratings_rating (rating),
    CONSTRAINT fk_drama_ratings_drama FOREIGN KEY (drama_id) REFERENCES dramas(id) ON DELETE CASCADE,
    CONSTRAINT fk_drama_ratings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT chk_drama_ratings_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
