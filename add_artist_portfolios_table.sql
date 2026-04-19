-- Create table to store artist portfolio entries
CREATE TABLE IF NOT EXISTS `artist_portfolios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artist_id` int(11) NOT NULL,
  `past_dramas` text NOT NULL,
  `position_worked` varchar(150) NOT NULL,
  `years_in_industry` int(11) NOT NULL DEFAULT 0,
  `specialized_fields` text NOT NULL,
  `education_qualifications` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_artist_portfolios_artist_id` (`artist_id`),
  CONSTRAINT `fk_artist_portfolios_artist`
    FOREIGN KEY (`artist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
