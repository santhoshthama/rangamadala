-- Add artist drama creation approval workflow
-- Run this once on the Rangamadala database.

CREATE TABLE IF NOT EXISTS drama_creation_requests (
    id INT(11) NOT NULL AUTO_INCREMENT,
    drama_name VARCHAR(255) NOT NULL,
    certificate_number VARCHAR(100) NOT NULL,
    owner_name VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    certificate_image VARCHAR(255) DEFAULT NULL,
    requested_by INT(11) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    reviewed_by INT(11) DEFAULT NULL,
    reviewed_at DATETIME DEFAULT NULL,
    rejection_reason TEXT DEFAULT NULL,
    created_drama_id INT(11) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_dcr_certificate_status (certificate_number, status),
    KEY idx_dcr_requested_by (requested_by),
    KEY idx_dcr_status (status),
    KEY idx_dcr_reviewed_by (reviewed_by),
    KEY idx_dcr_created_drama_id (created_drama_id),
    CONSTRAINT fk_dcr_requested_by FOREIGN KEY (requested_by) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_dcr_reviewed_by FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_dcr_created_drama_id FOREIGN KEY (created_drama_id) REFERENCES dramas(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
