<?php

class M_notification {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Create a notification for a specific user
     */
    public function createNotification($data) {
        try {
            $this->db->query("INSERT INTO artist_notifications 
                (user_id, drama_id, type, title, message, link)
                VALUES 
                (:user_id, :drama_id, :type, :title, :message, :link)");

            $this->db->bind(':user_id', $data['user_id']);
            $this->db->bind(':drama_id', $data['drama_id'] ?? null);
            $this->db->bind(':type', $data['type']);
            $this->db->bind(':title', $data['title']);
            $this->db->bind(':message', $data['message']);
            $this->db->bind(':link', $data['link'] ?? null);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in createNotification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a notification to all artists assigned to a drama
     */
    public function notifyDramaArtists($drama_id, $type, $title, $message, $link = null, $excludeUserId = null) {
        try {
            // Get all artists assigned to this drama via role_assignments
            $this->db->query("SELECT DISTINCT ra.artist_id 
                             FROM role_assignments ra
                             JOIN drama_roles dr ON ra.role_id = dr.id
                             WHERE dr.drama_id = :drama_id
                               AND ra.status = 'active'");
            $this->db->bind(':drama_id', $drama_id);
            $artists = $this->db->resultSet();

            $count = 0;
            foreach ($artists as $artist) {
                if ($excludeUserId && (int)$artist->artist_id === (int)$excludeUserId) {
                    continue;
                }
                $this->createNotification([
                    'user_id' => $artist->artist_id,
                    'drama_id' => $drama_id,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'link' => $link,
                ]);
                $count++;
            }
            return $count;
        } catch (Exception $e) {
            error_log("Error in notifyDramaArtists: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all notifications for a user, grouped by drama, newest first
     */
    public function getNotificationsByUser($user_id, $limit = 50) {
        try {
            $this->db->query("SELECT n.*, d.drama_name
                             FROM artist_notifications n
                             LEFT JOIN dramas d ON n.drama_id = d.id
                             WHERE n.user_id = :user_id
                             ORDER BY n.created_at DESC
                             LIMIT :lim");
            $this->db->bind(':user_id', $user_id);
            $this->db->bind(':lim', (int)$limit);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getNotificationsByUser: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get a single notification by id for a specific user
     */
    public function getNotificationByIdForUser($notification_id, $user_id) {
        try {
            $this->db->query("SELECT n.*, d.drama_name
                             FROM artist_notifications n
                             LEFT JOIN dramas d ON n.drama_id = d.id
                             WHERE n.id = :id AND n.user_id = :user_id
                             LIMIT 1");
            $this->db->bind(':id', (int)$notification_id);
            $this->db->bind(':user_id', (int)$user_id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getNotificationByIdForUser: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get notifications for a user filtered by notification types
     */
    public function getNotificationsByUserTypes($user_id, $types = [], $limit = 50) {
        try {
            if (empty($types)) {
                return $this->getNotificationsByUser($user_id, $limit);
            }

            $placeholders = [];
            foreach (array_values($types) as $index => $type) {
                $placeholders[] = ':type_' . $index;
            }

            $this->db->query("SELECT n.*, d.drama_name
                             FROM artist_notifications n
                             LEFT JOIN dramas d ON n.drama_id = d.id
                             WHERE n.user_id = :user_id
                               AND n.type IN (" . implode(', ', $placeholders) . ")
                             ORDER BY n.created_at DESC
                             LIMIT :lim");
            $this->db->bind(':user_id', $user_id);
            foreach (array_values($types) as $index => $type) {
                $this->db->bind(':type_' . $index, $type);
            }
            $this->db->bind(':lim', (int)$limit);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getNotificationsByUserTypes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get unread notification count for a user
     */
    public function getUnreadCount($user_id) {
        try {
            $this->db->query("SELECT COUNT(*) as cnt FROM artist_notifications 
                             WHERE user_id = :user_id AND is_read = 0");
            $this->db->bind(':user_id', $user_id);
            $row = $this->db->single();
            return $row ? (int)$row->cnt : 0;
        } catch (Exception $e) {
            error_log("Error in getUnreadCount: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get unread count for a user filtered by notification types
     */
    public function getUnreadCountByTypes($user_id, $types = []) {
        try {
            if (empty($types)) {
                return $this->getUnreadCount($user_id);
            }

            $placeholders = [];
            foreach (array_values($types) as $index => $type) {
                $placeholders[] = ':type_' . $index;
            }

            $this->db->query("SELECT COUNT(*) as cnt FROM artist_notifications 
                             WHERE user_id = :user_id 
                               AND is_read = 0
                               AND type IN (" . implode(', ', $placeholders) . ")");
            $this->db->bind(':user_id', $user_id);
            foreach (array_values($types) as $index => $type) {
                $this->db->bind(':type_' . $index, $type);
            }

            $row = $this->db->single();
            return $row ? (int)$row->cnt : 0;
        } catch (Exception $e) {
            error_log("Error in getUnreadCountByTypes: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead($notification_id, $user_id) {
        try {
            $this->db->query("UPDATE artist_notifications SET is_read = 1 
                             WHERE id = :id AND user_id = :user_id");
            $this->db->bind(':id', $notification_id);
            $this->db->bind(':user_id', $user_id);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in markAsRead: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($user_id) {
        try {
            $this->db->query("UPDATE artist_notifications SET is_read = 1 
                             WHERE user_id = :user_id AND is_read = 0");
            $this->db->bind(':user_id', $user_id);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in markAllAsRead: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark all notifications as read for a user filtered by notification types
     */
    public function markAllAsReadByTypes($user_id, $types = []) {
        try {
            if (empty($types)) {
                return $this->markAllAsRead($user_id);
            }

            $placeholders = [];
            foreach (array_values($types) as $index => $type) {
                $placeholders[] = ':type_' . $index;
            }

            $this->db->query("UPDATE artist_notifications SET is_read = 1 
                             WHERE user_id = :user_id 
                               AND is_read = 0
                               AND type IN (" . implode(', ', $placeholders) . ")");
            $this->db->bind(':user_id', $user_id);
            foreach (array_values($types) as $index => $type) {
                $this->db->bind(':type_' . $index, $type);
            }
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in markAllAsReadByTypes: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get notifications grouped by drama for a user
     */
    public function getNotificationsGroupedByDrama($user_id, $limit = 100) {
        try {
            $this->db->query("SELECT n.*, d.drama_name
                             FROM artist_notifications n
                             LEFT JOIN dramas d ON n.drama_id = d.id
                             WHERE n.user_id = :user_id
                             ORDER BY n.created_at DESC
                             LIMIT :lim");
            $this->db->bind(':user_id', $user_id);
            $this->db->bind(':lim', (int)$limit);
            $results = $this->db->resultSet();

            // Group by drama
            $grouped = [];
            foreach ($results as $n) {
                $dramaKey = $n->drama_id ?? 0;
                if (!isset($grouped[$dramaKey])) {
                    $grouped[$dramaKey] = [
                        'drama_id' => $n->drama_id,
                        'drama_name' => $n->drama_name ?? 'General',
                        'notifications' => [],
                        'unread_count' => 0,
                    ];
                }
                $grouped[$dramaKey]['notifications'][] = $n;
                if (!(int)$n->is_read) {
                    $grouped[$dramaKey]['unread_count']++;
                }
            }

            return $grouped;
        } catch (Exception $e) {
            error_log("Error in getNotificationsGroupedByDrama: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Delete old read notifications (cleanup, older than 90 days)
     */
    public function cleanupOldNotifications($days = 90) {
        try {
            $this->db->query("DELETE FROM artist_notifications 
                             WHERE is_read = 1 AND created_at < DATE_SUB(NOW(), INTERVAL :days DAY)");
            $this->db->bind(':days', (int)$days);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in cleanupOldNotifications: " . $e->getMessage());
            return false;
        }
    }
}
