<?php

class M_rating
{
    protected $db;
    protected $table = 'drama_ratings';

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Submit or update a rating for a drama
     * @param int $drama_id The drama ID
     * @param int $user_id The user ID
     * @param int $rating The rating (1-5)
     * @param string $comment Optional comment/feedback
     * @return bool Success status
     */
    public function submitRating($drama_id, $user_id, $rating, $comment = null)
    {
        // Validate inputs
        if (empty($drama_id) || !is_numeric($drama_id)) return false;
        if (empty($user_id) || !is_numeric($user_id)) return false;
        if (empty($rating) || $rating < 1 || $rating > 5) return false;

        // Check if user already rated this drama
        $this->db->query("SELECT id FROM {$this->table} WHERE drama_id = :drama_id AND user_id = :user_id");
        $this->db->bind(':drama_id', $drama_id);
        $this->db->bind(':user_id', $user_id);
        $existing = $this->db->single();

        if ($existing) {
            // Update existing rating
            $this->db->query("UPDATE {$this->table} SET rating = :rating, comment = :comment, updated_at = NOW() WHERE drama_id = :drama_id AND user_id = :user_id");
        } else {
            // Insert new rating
            $this->db->query("INSERT INTO {$this->table} (drama_id, user_id, rating, comment, created_at, updated_at) VALUES (:drama_id, :user_id, :rating, :comment, NOW(), NOW())");
        }

        $this->db->bind(':drama_id', $drama_id);
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':rating', $rating);
        $this->db->bind(':comment', $comment);

        return $this->db->execute();
    }

    /**
     * Get rating summary for a drama
     * @param int $drama_id The drama ID
     * @return object|null Summary object
     */
    public function getDramaRatingSummary($drama_id)
    {
        $this->db->query("
            SELECT 
                COUNT(id) as total_ratings,
                ROUND(AVG(rating), 2) as average_rating,
                COUNT(CASE WHEN rating = 5 THEN 1 END) as five_star_count,
                COUNT(CASE WHEN rating = 4 THEN 1 END) as four_star_count,
                COUNT(CASE WHEN rating = 3 THEN 1 END) as three_star_count,
                COUNT(CASE WHEN rating = 2 THEN 1 END) as two_star_count,
                COUNT(CASE WHEN rating = 1 THEN 1 END) as one_star_count
            FROM {$this->table}
            WHERE drama_id = :drama_id
        ");
        $this->db->bind(':drama_id', $drama_id);
        return $this->db->single();
    }

    /**
     * Get all ratings for a drama with user information
     * @param int $drama_id The drama ID
     * @param int $limit Limit number of ratings
     * @param int $offset Offset for pagination
     * @return array Array of rating objects
     */
    public function getDramaRatings($drama_id, $limit = 10, $offset = 0)
    {
        $this->db->query("
            SELECT 
                dr.id,
                dr.drama_id,
                dr.user_id,
                dr.rating,
                dr.comment,
                dr.helpful_count,
                dr.created_at,
                u.full_name,
                u.email
            FROM {$this->table} dr
            JOIN users u ON dr.user_id = u.id
            WHERE dr.drama_id = :drama_id
            ORDER BY dr.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $this->db->bind(':drama_id', $drama_id);
        $this->db->bind(':limit', (int)$limit);
        $this->db->bind(':offset', (int)$offset);
        
        $results = $this->db->resultSet();
        // Convert to array format
        $ratings = [];
        foreach ($results as $row) {
            $ratings[] = (array)$row;
        }
        return $ratings;
    }

    /**
     * Get a user's rating for a specific drama
     * @param int $drama_id The drama ID
     * @param int $user_id The user ID
     * @return object|null The rating object or null
     */
    public function getUserDramaRating($drama_id, $user_id)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE drama_id = :drama_id AND user_id = :user_id");
        $this->db->bind(':drama_id', $drama_id);
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }

    /**
     * Check if a user has already rated a drama
     * @param int $drama_id The drama ID
     * @param int $user_id The user ID
     * @return bool True if user has rated
     */
    public function hasUserRated($drama_id, $user_id)
    {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE drama_id = :drama_id AND user_id = :user_id");
        $this->db->bind(':drama_id', $drama_id);
        $this->db->bind(':user_id', $user_id);
        $row = $this->db->single();
        return $row && $row->count > 0;
    }

    /**
     * Mark a rating as helpful
     * @param int $rating_id The rating ID
     * @return bool Success status
     */
    public function markAsHelpful($rating_id)
    {
        $this->db->query("UPDATE {$this->table} SET helpful_count = helpful_count + 1, is_helpful = 1 WHERE id = :id");
        $this->db->bind(':id', $rating_id);
        return $this->db->execute();
    }

    /**
     * Delete a rating (only by the user who created it)
     * @param int $rating_id The rating ID
     * @param int $user_id The user ID
     * @return bool Success status
     */
    public function deleteRating($rating_id, $user_id)
    {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND user_id = :user_id");
        $this->db->bind(':id', $rating_id);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    /**
     * Get top-rated dramas
     * @param int $limit Number of dramas to return
     * @return array Array of drama objects with ratings
     */
    public function getTopRatedDramas($limit = 10)
    {
        $this->db->query("
            SELECT 
                d.id,
                d.title,
                d.image,
                d.ticket_price,
                d.category_id,
                ROUND(AVG(dr.rating), 2) as average_rating,
                COUNT(dr.id) as total_ratings
            FROM dramas d
            LEFT JOIN {$this->table} dr ON d.id = dr.drama_id
            GROUP BY d.id, d.title, d.image, d.ticket_price, d.category_id
            HAVING COUNT(dr.id) >= 1
            ORDER BY average_rating DESC, total_ratings DESC
            LIMIT :limit
        ");
        $this->db->bind(':limit', (int)$limit);
        return $this->db->resultSet();
    }

    /**
     * Get rating statistics for admin dashboard
     * @return object Statistics object
     */
    public function getRatingStatistics()
    {
        $this->db->query("
            SELECT 
                COUNT(DISTINCT drama_id) as total_dramas_rated,
                COUNT(id) as total_ratings,
                ROUND(AVG(rating), 2) as overall_average,
                COUNT(CASE WHEN rating = 5 THEN 1 END) as total_five_star,
                COUNT(CASE WHEN rating = 4 THEN 1 END) as total_four_star,
                COUNT(CASE WHEN rating = 3 THEN 1 END) as total_three_star,
                COUNT(CASE WHEN rating = 2 THEN 1 END) as total_two_star,
                COUNT(CASE WHEN rating = 1 THEN 1 END) as total_one_star
            FROM {$this->table}
        ");
        return $this->db->single();
    }

    /**
     * Get recent ratings across all dramas
     * @param int $limit Number of ratings to return
     * @return array Array of recent ratings
     */
    public function getRecentRatings($limit = 20)
    {
        $this->db->query("
            SELECT 
                dr.id,
                dr.drama_id,
                d.title as drama_title,
                dr.user_id,
                u.full_name,
                dr.rating,
                dr.comment,
                dr.created_at
            FROM {$this->table} dr
            JOIN dramas d ON dr.drama_id = d.id
            JOIN users u ON dr.user_id = u.id
            ORDER BY dr.created_at DESC
            LIMIT :limit
        ");
        $this->db->bind(':limit', (int)$limit);
        return $this->db->resultSet();
    }

    /**
     * Count total ratings for a drama
     * @param int $drama_id The drama ID
     * @return int Count of ratings
     */
    public function countDramaRatings($drama_id)
    {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE drama_id = :drama_id");
        $this->db->bind(':drama_id', $drama_id);
        $row = $this->db->single();
        return $row ? (int)$row->count : 0;
    }

    /**
     * Get rating distribution for a drama
     * @param int $drama_id The drama ID
     * @return object Distribution object
     */
    public function getRatingDistribution($drama_id)
    {
        $this->db->query("
            SELECT 
                COUNT(CASE WHEN rating = 1 THEN 1 END) as one_star,
                COUNT(CASE WHEN rating = 2 THEN 1 END) as two_star,
                COUNT(CASE WHEN rating = 3 THEN 1 END) as three_star,
                COUNT(CASE WHEN rating = 4 THEN 1 END) as four_star,
                COUNT(CASE WHEN rating = 5 THEN 1 END) as five_star,
                COUNT(id) as total
            FROM {$this->table}
            WHERE drama_id = :drama_id
        ");
        $this->db->bind(':drama_id', $drama_id);
        return $this->db->single();
    }

    /**
     * Search ratings with filters
     * @param int $drama_id The drama ID
     * @param int|null $min_rating Filter by minimum rating
     * @param int|null $max_rating Filter by maximum rating
     * @param bool|null $has_comment Filter by presence of comment
     * @param int $limit Limit results
     * @return array Array of filtered ratings
     */
    public function searchRatings($drama_id, $min_rating = null, $max_rating = null, $has_comment = null, $limit = 20)
    {
        $sql = "
            SELECT 
                dr.*,
                u.full_name,
                u.email
            FROM {$this->table} dr
            JOIN users u ON dr.user_id = u.id
            WHERE dr.drama_id = :drama_id
        ";

        if ($min_rating !== null) {
            $sql .= " AND dr.rating >= " . (int)$min_rating;
        }
        if ($max_rating !== null) {
            $sql .= " AND dr.rating <= " . (int)$max_rating;
        }
        if ($has_comment === true) {
            $sql .= " AND dr.comment IS NOT NULL AND dr.comment != ''";
        } elseif ($has_comment === false) {
            $sql .= " AND (dr.comment IS NULL OR dr.comment = '')";
        }

        $sql .= " ORDER BY dr.created_at DESC LIMIT " . (int)$limit;

        $this->db->query($sql);
        $this->db->bind(':drama_id', $drama_id);
        return $this->db->resultSet();
    }
}
