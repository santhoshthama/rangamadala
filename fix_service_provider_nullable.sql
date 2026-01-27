-- Fix service_requests table to allow NULL service_provider_id
-- This allows PM to create services without assigning a provider immediately

ALTER TABLE `service_requests` 
MODIFY COLUMN `service_provider_id` int NULL;

-- Remove the foreign key constraint temporarily
ALTER TABLE `service_requests` 
DROP FOREIGN KEY `service_requests_fk_provider`;

-- Re-add the foreign key constraint with NULL allowed
ALTER TABLE `service_requests` 
ADD CONSTRAINT `service_requests_fk_provider` 
FOREIGN KEY (`service_provider_id`) REFERENCES `users` (`id`) 
ON DELETE CASCADE;
