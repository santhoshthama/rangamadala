-- =====================================================
-- Artist Notification System
-- File: database_notifications.sql
-- Purpose: Store notifications for artists about drama activities
-- =====================================================

CREATE TABLE IF NOT EXISTS `artist_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'The artist receiving the notification',
  `drama_id` int(11) DEFAULT NULL COMMENT 'Related drama',
  `type` varchar(50) NOT NULL COMMENT 'Notification type: role_assigned, event_scheduled, event_updated, event_cancelled, role_removed, application_accepted, application_rejected, interview_scheduled',
  `title` varchar(255) NOT NULL COMMENT 'Short notification title',
  `message` text NOT NULL COMMENT 'Full notification message',
  `link` varchar(500) DEFAULT NULL COMMENT 'URL to navigate to when clicked',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notifications_user` (`user_id`),
  KEY `idx_notifications_user_read` (`user_id`, `is_read`),
  KEY `idx_notifications_drama` (`drama_id`),
  KEY `idx_notifications_created` (`created_at`),
  CONSTRAINT `artist_notifications_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `artist_notifications_fk_drama` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
