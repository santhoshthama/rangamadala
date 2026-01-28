<?php

class M_rating
{
    use Model;

    protected $table = 'drama_ratings';
    protected $allowedColumns = [
        'id',
        'drama_id',
        'user_id',
        'rating',
        'comment',
        'is_helpful',
        'helpful_count',
        'created_at',
        'updated_at'
    ];

    /**
     * Submit or update a rating for a drama
     * Uses INSERT ... ON DUPLICATE KEY UPDATE to handle updates
     * @param int $drama_id The drama ID
     * @param int $user_id The user ID
     * @param int $rating The rating (1-5)
     * @param string $comment Optional comment/feedback
     * @return bool|object Success status or error object
     */
    public function submitRating($drama_id, $user_id, $rating, $comment = null)
    {
        if (!$this->validate($drama_id, 'required|numeric')) {
            return false;
        }
        if (!$this->validate($user_id, 'required|numeric')) {
            return false;
        }
        if (!$this->validate($rating, 'required|numeric|min:1|max:5')) {
            return false;
        }

        $query = "
            INSERT INTO {$this->table} (drama_id, user_id, rating, comment, created_at, updated_at)
            VALUES (?, ?, ?, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE
                rating = VALUES(rating),
                comment = VALUES(comment),
                updated_at = NOW()
        ";

        $result = $this->query($query, [$drama_id, $user_id, $rating, $comment]);
        return $result ? true : false;
    }

    /**
     * Get rating summary for a drama (average rating, counts, distribution)
     * @param int $drama_id The drama ID
     * @return object Summary object with average_rating, total_ratings, star distribution
     */
    public function getDramaRatingSummary($drama_id)
    {
        $query = "
            SELECT 
                COUNT(id) as total_ratings,
                ROUND(AVG(rating), 2) as average_rating,
                COUNT(CASE WHEN rating = 5 THEN 1 END) as five_star_count,
                COUNT(CASE WHEN rating = 4 THEN 1 END) as four_star_count,
                COUNT(CASE WHEN rating = 3 THEN 1 END) as three_star_count,
                COUNT(CASE WHEN rating = 2 THEN 1 END) as two_star_count,
                COUNT(CASE WHEN rating = 1 THEN 1 END) as one_star_count
            FROM {$this->table}
            WHERE drama_id = ?
        ";

        $result = $this->query($query, [$drama_id]);
        return $result ? $result->fetch_object() : null;
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
        $query = "
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
            WHERE dr.drama_id = ?
            ORDER BY dr.created_at DESC
            LIMIT ? OFFSET ?
        ";

        $result = $this->query($query, [$drama_id, $limit, $offset]);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Get a user's rating for a specific drama
     * @param int $drama_id The drama ID
     * @param int $user_id The user ID
     * @return object|null The rating object or null if not found
     */
    public function getUserDramaRating($drama_id, $user_id)
    {
        $query = "
            SELECT * FROM {$this->table}
            WHERE drama_id = ? AND user_id = ?
        ";

        $result = $this->query($query, [$drama_id, $user_id]);
        return $result ? $result->fetch_object() : null;
    }

    /**
     * Check if a user has already rated a drama
     * @param int $drama_id The drama ID
     * @param int $user_id The user ID
     * @return bool True if user has rated, false otherwise
     */
    public function hasUserRated($drama_id, $user_id)
    {
        $query = "
            SELECT COUNT(*) as count FROM {$this->table}
            WHERE drama_id = ? AND user_id = ?
        ";

        $result = $this->query($query, [$drama_id, $user_id]);
        if (!$result) {
            return false;
        }
        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }

    /**
     * Mark a rating as helpful and increment helpful count
     * @param int $rating_id The rating ID
     * @return bool Success status
     */
    public function markAsHelpful($rating_id)
    {
        $query = "
            UPDATE {$this->table} 
            SET helpful_count = helpful_count + 1, is_helpful = 1
            WHERE id = ?
        ";

        return $this->query($query, [$rating_id]) ? true : false;
    }

    /**
     * Delete a rating (only by the user who created it)
     * @param int $rating_id The rating ID
     * @param int $user_id The user ID
     * @return bool Success status
     */
    public function deleteRating($rating_id, $user_id)
    {
        $query = "
            DELETE FROM {$this->table} 
            WHERE id = ? AND user_id = ?
        ";

        return $this->query($query, [$rating_id, $user_id]) ? true : false;
    }

    /**
     * Get top-rated dramas
     * @param int $limit Number of dramas to return
     * @return array Array of drama objects with ratings
     */
    public function getTopRatedDramas($limit = 10)
    {
        $query = "
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
            LIMIT ?
        ";

        $result = $this->query($query, [$limit]);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Get rating statistics for admin dashboard
     * @return object Statistics object
     */
    public function getRatingStatistics()
    {
        $query = "
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
        ";

        $result = $this->query($query);
        return $result ? $result->fetch_object() : null;
    }

    /**
     * Get recent ratings across all dramas
     * @param int $limit Number of ratings to return
     * @return array Array of recent ratings
     */
    public function getRecentRatings($limit = 20)
    {
        $query = "
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
            LIMIT ?
        ";

        $result = $this->query($query, [$limit]);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Count total ratings for a drama
     * @param int $drama_id The drama ID
     * @return int Count of ratings
     */
    public function countDramaRatings($drama_id)
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE drama_id = ?";
        $result = $this->query($query, [$drama_id]);
        if (!$result) {
            return 0;
        }
        $row = $result->fetch_assoc();
        return (int)$row['count'];
    }

    /**
     * Get rating distribution for a drama (for chart visualization)
     * @param int $drama_id The drama ID
     * @return object Distribution object with counts for each star level
     */
    public function getRatingDistribution($drama_id)
    {
        $query = "
            SELECT 
                COUNT(CASE WHEN rating = 1 THEN 1 END) as one_star,
                COUNT(CASE WHEN rating = 2 THEN 1 END) as two_star,
                COUNT(CASE WHEN rating = 3 THEN 1 END) as three_star,
                COUNT(CASE WHEN rating = 4 THEN 1 END) as four_star,
                COUNT(CASE WHEN rating = 5 THEN 1 END) as five_star,
                COUNT(id) as total
            FROM {$this->table}
            WHERE drama_id = ?
        ";

        $result = $this->query($query, [$drama_id]);
        return $result ? $result->fetch_object() : null;
    }

    /**
     * Search ratings with filters
     * @param int $drama_id The drama ID
     * @param int|null $min_rating Filter by minimum rating
     * @param int|null $max_rating Filter by maximum rating
     * @param bool $has_comment Filter by presence of comment
     * @param int $limit Limit results
     * @return array Array of filtered ratings
     */
    public function searchRatings($drama_id, $min_rating = null, $max_rating = null, $has_comment = null, $limit = 20)
    {
        $query = "
            SELECT 
                dr.*,
                u.full_name,
                u.email
            FROM {$this->table} dr
            JOIN users u ON dr.user_id = u.id
            WHERE dr.drama_id = ?
        ";
        $params = [$drama_id];

        if ($min_rating !== null) {
            $query .= " AND dr.rating >= ?";
            $params[] = (int)$min_rating;
        }

        if ($max_rating !== null) {
            $query .= " AND dr.rating <= ?";
            $params[] = (int)$max_rating;
        }

        if ($has_comment === true) {
            $query .= " AND dr.comment IS NOT NULL AND dr.comment != ''";
        } elseif ($has_comment === false) {
            $query .= " AND (dr.comment IS NULL OR dr.comment = '')";
        }

        $query .= " ORDER BY dr.created_at DESC LIMIT ?";
        $params[] = (int)$limit;

        $result = $this->query($query, $params);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

?>
