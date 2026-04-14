-- Adds optional linkage columns so drama_budgets can sync with service/payment lifecycle
-- Safe to run multiple times.

SET @db_name := DATABASE();

-- service_request_id
SET @has_service_request_id := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db_name
      AND TABLE_NAME = 'drama_budgets'
      AND COLUMN_NAME = 'service_request_id'
);
SET @sql := IF(
    @has_service_request_id = 0,
    'ALTER TABLE drama_budgets ADD COLUMN service_request_id INT NULL AFTER drama_id',
    'SELECT ''service_request_id already exists'''
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- source_type
SET @has_source_type := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db_name
      AND TABLE_NAME = 'drama_budgets'
      AND COLUMN_NAME = 'source_type'
);
SET @sql := IF(
    @has_source_type = 0,
    'ALTER TABLE drama_budgets ADD COLUMN source_type VARCHAR(30) NULL DEFAULT ''manual'' AFTER service_request_id',
    'SELECT ''source_type already exists'''
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- last_synced_at
SET @has_last_synced_at := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db_name
      AND TABLE_NAME = 'drama_budgets'
      AND COLUMN_NAME = 'last_synced_at'
);
SET @sql := IF(
    @has_last_synced_at = 0,
    'ALTER TABLE drama_budgets ADD COLUMN last_synced_at DATETIME NULL AFTER updated_at',
    'SELECT ''last_synced_at already exists'''
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- indexes
SET @has_idx_service_request_id := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = @db_name
      AND TABLE_NAME = 'drama_budgets'
      AND INDEX_NAME = 'idx_drama_budgets_service_request_id'
);
SET @sql := IF(
    @has_idx_service_request_id = 0,
    'ALTER TABLE drama_budgets ADD INDEX idx_drama_budgets_service_request_id (service_request_id)',
    'SELECT ''idx_drama_budgets_service_request_id already exists'''
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_idx_source_type := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = @db_name
      AND TABLE_NAME = 'drama_budgets'
      AND INDEX_NAME = 'idx_drama_budgets_source_type'
);
SET @sql := IF(
    @has_idx_source_type = 0,
    'ALTER TABLE drama_budgets ADD INDEX idx_drama_budgets_source_type (source_type)',
    'SELECT ''idx_drama_budgets_source_type already exists'''
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
