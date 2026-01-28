-- Fix Admin Password Hash
-- Password: Admin@123
UPDATE `users` 
SET `password` = '$2y$10$3.eGnDe40tp8fzew4s6gFOe8OyZNE/PbpB5QiocmBJl/pfkJ8bE9u'
WHERE `email` = 'admin@rangamadala.com';

-- Verify the update
SELECT id, full_name, email, role, password FROM users WHERE email='admin@rangamadala.com';
