-- Migration: Ensure services table has service_type_id and populated data
SET @db := DATABASE();

-- Add the service_type_id column if it does not exist
ALTER TABLE services
    ADD COLUMN IF NOT EXISTS service_type_id INT NULL AFTER provider_id;

-- Add supporting index
ALTER TABLE services
    ADD INDEX IF NOT EXISTS idx_services_service_type_id (service_type_id);

-- Create or confirm the foreign key to service_types
ALTER TABLE services
    ADD CONSTRAINT IF NOT EXISTS services_ibfk_service_type
        FOREIGN KEY (service_type_id)
        REFERENCES service_types (service_type_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE;

-- Seed core service types in case they are missing
INSERT INTO service_types (service_type)
SELECT t.name
FROM (
    SELECT 'Theater Production' AS name UNION ALL
    SELECT 'Lighting Design' UNION ALL
    SELECT 'Sound Systems' UNION ALL
    SELECT 'Video Production' UNION ALL
    SELECT 'Set Design' UNION ALL
    SELECT 'Costume Design' UNION ALL
    SELECT 'Makeup & Hair' UNION ALL
    SELECT 'Other'
) AS t
LEFT JOIN service_types st ON LOWER(st.service_type) = LOWER(t.name)
WHERE st.service_type_id IS NULL;

-- Capture any custom entries recorded under other services
INSERT INTO service_types (service_type)
SELECT DISTINCT sod.service_type
FROM service_other_details sod
LEFT JOIN service_types st ON LOWER(st.service_type) = LOWER(sod.service_type)
WHERE sod.service_type IS NOT NULL
  AND st.service_type_id IS NULL;

-- Backfill from legacy services.service_type column when available
SET @legacy_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db
      AND TABLE_NAME = 'services'
      AND COLUMN_NAME = 'service_type'
);

SET @sql := IF(
    @legacy_col > 0,
    'UPDATE services s JOIN service_types st ON LOWER(st.service_type) = LOWER(s.service_type)
     SET s.service_type_id = st.service_type_id
     WHERE s.service_type_id IS NULL AND s.service_type IS NOT NULL;',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Backfill using detail tables
UPDATE services s
JOIN service_theater_details d ON d.service_id = s.id
JOIN service_types st ON LOWER(st.service_type) = 'theater production'
SET s.service_type_id = st.service_type_id
WHERE s.service_type_id IS NULL;

UPDATE services s
JOIN service_lighting_details d ON d.service_id = s.id
JOIN service_types st ON LOWER(st.service_type) = 'lighting design'
SET s.service_type_id = st.service_type_id
WHERE s.service_type_id IS NULL;

UPDATE services s
JOIN service_sound_details d ON d.service_id = s.id
JOIN service_types st ON LOWER(st.service_type) = 'sound systems'
SET s.service_type_id = st.service_type_id
WHERE s.service_type_id IS NULL;

UPDATE services s
JOIN service_video_details d ON d.service_id = s.id
JOIN service_types st ON LOWER(st.service_type) = 'video production'
SET s.service_type_id = st.service_type_id
WHERE s.service_type_id IS NULL;

UPDATE services s
JOIN service_set_details d ON d.service_id = s.id
JOIN service_types st ON LOWER(st.service_type) = 'set design'
SET s.service_type_id = st.service_type_id
WHERE s.service_type_id IS NULL;

UPDATE services s
JOIN service_costume_details d ON d.service_id = s.id
JOIN service_types st ON LOWER(st.service_type) = 'costume design'
SET s.service_type_id = st.service_type_id
WHERE s.service_type_id IS NULL;

UPDATE services s
JOIN service_makeup_details d ON d.service_id = s.id
JOIN service_types st ON LOWER(st.service_type) = 'makeup & hair'
SET s.service_type_id = st.service_type_id
WHERE s.service_type_id IS NULL;

UPDATE services s
JOIN service_other_details d ON d.service_id = s.id
JOIN service_types st ON LOWER(st.service_type) = LOWER(d.service_type)
SET s.service_type_id = st.service_type_id
WHERE s.service_type_id IS NULL
  AND d.service_type IS NOT NULL;

-- Clean up any rows still lacking a type by setting them to the generic bucket
UPDATE services s
JOIN service_types st ON LOWER(st.service_type) = 'other'
SET s.service_type_id = st.service_type_id
WHERE s.service_type_id IS NULL;
