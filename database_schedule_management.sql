-- Schedule Management Table for Director
-- Stores rehearsals, interviews, meetings, performances scheduled by the director

CREATE TABLE IF NOT EXISTS drama_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    drama_id INT NOT NULL,
    event_type ENUM('rehearsal', 'interview', 'meeting', 'performance') NOT NULL DEFAULT 'rehearsal',
    event_title VARCHAR(255) NOT NULL,
    event_description TEXT NULL,
    scheduled_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    venue VARCHAR(255) NOT NULL,
    -- For interview type: link to role
    role_id INT NULL,
    -- Status tracking
    status ENUM('scheduled', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'scheduled',
    -- Participants info (JSON array of artist IDs or 'all')
    participants TEXT NULL,
    notes TEXT NULL,
    -- Creator tracking
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (drama_id) REFERENCES dramas(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES drama_roles(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id),

    INDEX idx_drama_date (drama_id, scheduled_date),
    INDEX idx_drama_status (drama_id, status),
    INDEX idx_scheduled_date (scheduled_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
