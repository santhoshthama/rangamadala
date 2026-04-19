<?php

class M_rating
{
    protected $db;
    protected $table = 'drama_ratings';
    protected $tableReady = false;

    public function __construct()
    {
        $this->db = new Database();
        $this->tableReady = true;
    }

    private function canUseTable()
    {
        return $this->tableReady;
    }

    private function isMissingRatingsTableError($e)
    {
        $message = $e instanceof Throwable ? $e->getMessage() : (string)$e;
        return stripos($message, '42S02') !== false
            || stripos($message, '1146') !== false
            || stripos($message, 'drama_ratings') !== false;
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
        if (!$this->canUseTable()) {
            return false;
        }

        // Validate inputs
        if (empty($drama_id) || !is_numeric($drama_id)) return false;
        if (empty($user_id) || !is_numeric($user_id)) return false;
        if (empty($rating) || $rating < 1 || $rating > 5) return false;

        // Check if user already rated this drama
        try {
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
        } catch (Throwable $e) {
            if ($this->isMissingRatingsTableError($e)) {
                $this->tableReady = false;
                error_log('M_rating::submitRating skipped because drama_ratings table is missing.');
                return false;
            }
            throw $e;
        }
    }

    /**
     * Get rating summary for a drama
     * @param int $drama_id The drama ID
     * @return object|null Summary object
     */
    public function getDramaRatingSummary($drama_id)
    {
        if (!$this->canUseTable()) {
            return (object)[
                'total_ratings' => 0,
                'average_rating' => null,
                'five_star_count' => 0,
                'four_star_count' => 0,
                'three_star_count' => 0,
                'two_star_count' => 0,
                'one_star_count' => 0,
            ];
        }

        try {
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
        } catch (Throwable $e) {
            if ($this->isMissingRatingsTableError($e)) {
                $this->tableReady = false;
                return (object)[
                    'total_ratings' => 0,
                    'average_rating' => null,
                    'five_star_count' => 0,
                    'four_star_count' => 0,
                    'three_star_count' => 0,
                    'two_star_count' => 0,
                    'one_star_count' => 0,
                ];
            }
            throw $e;
        }
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
        if (!$this->canUseTable()) {
            return [];
        }

        try {
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
        } catch (Throwable $e) {
            if ($this->isMissingRatingsTableError($e)) {
                $this->tableReady = false;
                return [];
            }
            throw $e;
        }
    }

    /**
     * Get a user's rating for a specific drama
     * @param int $drama_id The drama ID
     * @param int $user_id The user ID
     * @return object|null The rating object or null
     */
    public function getUserDramaRating($drama_id, $user_id)
    {
        if (!$this->canUseTable()) {
            return null;
        }

        try {
            $this->db->query("SELECT * FROM {$this->table} WHERE drama_id = :drama_id AND user_id = :user_id");
            $this->db->bind(':drama_id', $drama_id);
            $this->db->bind(':user_id', $user_id);
            return $this->db->single();
        } catch (Throwable $e) {
            if ($this->isMissingRatingsTableError($e)) {
                $this->tableReady = false;
                return null;
            }
            throw $e;
        }
    }

    /**
     * Get a user's rating by rating ID and user ID
     */
    public function getUserRatingById($rating_id, $user_id)
    {
        if (!$this->canUseTable()) {
            return null;
        }

        try {
            $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND user_id = :user_id LIMIT 1");
            $this->db->bind(':id', (int)$rating_id);
            $this->db->bind(':user_id', (int)$user_id);
            return $this->db->single();
        } catch (Throwable $e) {
            if ($this->isMissingRatingsTableError($e)) {
                $this->tableReady = false;
                return null;
            }
            throw $e;
        }
    }

    /**
     * Check if a user has already rated a drama
     * @param int $drama_id The drama ID
     * @param int $user_id The user ID
     * @return bool True if user has rated
     */
    public function hasUserRated($drama_id, $user_id)
    {
        if (!$this->canUseTable()) {
            return false;
        }

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
        if (!$this->canUseTable()) {
            return false;
        }

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
        if (!$this->canUseTable()) {
            return false;
        }

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
        if (!$this->canUseTable()) {
            return [];
        }

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
        if (!$this->canUseTable()) {
            return (object)[
                'total_dramas_rated' => 0,
                'total_ratings' => 0,
                'overall_average' => null,
                'total_five_star' => 0,
                'total_four_star' => 0,
                'total_three_star' => 0,
                'total_two_star' => 0,
                'total_one_star' => 0,
            ];
        }

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
        if (!$this->canUseTable()) {
            return [];
        }

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
        if (!$this->canUseTable()) {
            return 0;
        }

        try {
            $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE drama_id = :drama_id");
            $this->db->bind(':drama_id', $drama_id);
            $row = $this->db->single();
            return $row ? (int)$row->count : 0;
        } catch (Throwable $e) {
            if ($this->isMissingRatingsTableError($e)) {
                $this->tableReady = false;
                return 0;
            }
            throw $e;
        }
    }

    /**
     * Get rating distribution for a drama
     * @param int $drama_id The drama ID
     * @return object Distribution object
     */
    public function getRatingDistribution($drama_id)
    {
        if (!$this->canUseTable()) {
            return (object)[
                'one_star' => 0,
                'two_star' => 0,
                'three_star' => 0,
                'four_star' => 0,
                'five_star' => 0,
                'total' => 0,
            ];
        }

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
        if (!$this->canUseTable()) {
            return [];
        }

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
