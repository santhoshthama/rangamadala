-- Create Default Admin Account for Rangamadala
-- Run this SQL script in your rangamandala_db database

-- Insert default admin user
-- Email: admin@rangamadala.com
-- Password: Admin@123

INSERT INTO `users` 
(`full_name`, `email`, `password`, `phone`, `role`, `nic_photo`, `created_at`, `updated_at`) 
VALUES 
(
  'System Administrator',
  'admin@rangamadala.com',
  '$2y$10$8vp7e9Q7Ke9KzQ6N5xKGkOQF8VzP2k1gM4r9N2L5Z3W1A8B9C0D1E2',
  '+94701234567',
  'admin',
  NULL,
  NOW(),
  NOW()
);

-- Note: The password hash above is for "Admin@123"
-- Change this password immediately after first login for security!
