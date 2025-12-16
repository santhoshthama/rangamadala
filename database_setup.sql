-- Rangamadala Database Setup
-- Run this SQL script in your rangamandala_db database

-- Create categories table
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create dramas table
CREATE TABLE IF NOT EXISTS `dramas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `category_id` int(11) DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'Duration in minutes',
  `ticket_price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `dramas_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dramas_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample categories
INSERT INTO `categories` (`name`) VALUES
('Classical Drama'),
('Musical Drama'),
('Comedy Drama'),
('Traditional Dance'),
('Modern Theatre'),
('Street Drama'),
('Folk Theatre'),
('Experimental Theatre')
ON DUPLICATE KEY UPDATE name=name;

-- Insert sample dramas (optional - remove if not needed)
INSERT INTO `dramas` (`title`, `description`, `category_id`, `venue`, `event_date`, `event_time`, `duration`, `ticket_price`, `created_by`) VALUES
('Maname', 'A classical Sinhala drama exploring family relationships and societal values', 1, 'Lionel Wendt Theatre, Colombo', '2025-01-15', '19:00:00', 120, 1500.00, NULL),
('Sinhabahu', 'Epic tale of the legendary king Sinhabahu and his journey', 1, 'Nelum Pokuna Theatre, Colombo', '2025-01-20', '18:30:00', 150, 2000.00, NULL),
('Kolamba Kathawa', 'A comedy drama depicting urban life in Colombo', 3, 'Elphinstone Theatre, Maradana', '2025-02-05', '19:30:00', 90, 1000.00, NULL),
('Nari Bena', 'Traditional folk drama with dance and music', 7, 'BMICH, Colombo', '2025-02-10', '18:00:00', 105, 1200.00, NULL),
('Vijayaba Kollaya', 'Historical drama about ancient Sri Lankan warriors', 1, 'Regal Theatre, Colombo', '2025-02-15', '19:00:00', 135, 1800.00, NULL)
ON DUPLICATE KEY UPDATE title=title;
