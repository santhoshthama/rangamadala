# Drama Ratings - Database Update Instructions

## 🗄️ Adding Rating System to Existing Database

### Method 1: Run Complete Setup (Recommended for Fresh Install)

```bash
# If starting fresh, use complete setup
mysql -u root -p rangamandala_db < COMPLETE_DATABASE_SETUP.sql
```

### Method 2: Add to Existing Database (Recommended for Updates)

#### Option A: Run Standalone Setup
```bash
mysql -u root -p rangamandala_db < DRAMA_RATINGS_DATABASE_SETUP.sql
```

#### Option B: Run Individual Queries
```sql
USE rangamandala_db;

-- Create the drama_ratings table
CREATE TABLE IF NOT EXISTS `drama_ratings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `drama_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` tinyint NOT NULL COMMENT '1-5 star rating',
  `comment` text DEFAULT NULL COMMENT 'Optional user comment/feedback',
  `is_helpful` tinyint(1) DEFAULT 0 COMMENT '1=marked as helpful',
  `helpful_count` int DEFAULT 0 COMMENT 'Number of users who found this helpful',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_rating_per_user` (`drama_id`, `user_id`),
  KEY `drama_id` (`drama_id`),
  KEY `user_id` (`user_id`),
  KEY `rating` (`rating`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `ratings_ibfk_drama` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ratings_ibfk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## ✅ Verification After Setup

### 1. Verify Table Created
```sql
SHOW TABLES LIKE 'drama_ratings';
```
Expected output:
```
+----------------------------+
| Tables_in_rangamandala_db  |
+----------------------------+
| drama_ratings              |
+----------------------------+
```

### 2. Verify Table Structure
```sql
DESCRIBE drama_ratings;
```
Expected: 9 columns (id, drama_id, user_id, rating, comment, is_helpful, helpful_count, created_at, updated_at)

### 3. Verify Indexes
```sql
SHOW INDEXES FROM drama_ratings;
```
Expected: Primary key + 5 indexes (drama_id, user_id, rating, created_at, unique)

### 4. Verify Foreign Keys
```sql
SELECT CONSTRAINT_NAME, TABLE_NAME, REFERENCED_TABLE_NAME 
FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS 
WHERE TABLE_NAME = 'drama_ratings';
```
Expected: 2 foreign keys (drama_id, user_id)

### 5. Test Table with Sample Data
```sql
-- Insert test rating
INSERT INTO drama_ratings (drama_id, user_id, rating, comment) 
VALUES (1, 1, 5, 'Great drama!');

-- Verify insert
SELECT * FROM drama_ratings 
WHERE drama_id = 1 AND user_id = 1;

-- Test unique constraint (should fail)
INSERT INTO drama_ratings (drama_id, user_id, rating, comment) 
VALUES (1, 1, 4, 'Updated rating');
-- Error: Duplicate entry for key 'unique_rating_per_user'

-- Update instead (this should work)
UPDATE drama_ratings 
SET rating = 4, comment = 'Updated rating' 
WHERE drama_id = 1 AND user_id = 1;

-- Verify update
SELECT * FROM drama_ratings 
WHERE drama_id = 1 AND user_id = 1;
-- Should show: rating = 4, comment = 'Updated rating'

-- Cleanup test data
DELETE FROM drama_ratings 
WHERE drama_id = 1 AND user_id = 1;
```

---

## 🔄 Rollback Instructions (If Needed)

### Drop the Table
```sql
DROP TABLE IF EXISTS drama_ratings;
```

### Restore from Backup
```bash
mysql -u root -p rangamandala_db < backup_file.sql
```

---

## 📊 Database Maintenance

### Monitor Table Size
```sql
SELECT 
  TABLE_NAME,
  ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) AS 'Size (MB)'
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = 'rangamandala_db' 
AND TABLE_NAME = 'drama_ratings';
```

### Optimize Table
```sql
OPTIMIZE TABLE drama_ratings;
```

### Analyze Table
```sql
ANALYZE TABLE drama_ratings;
```

### Check Table for Errors
```sql
CHECK TABLE drama_ratings;
```

### Repair Table (If Corrupted)
```sql
REPAIR TABLE drama_ratings;
```

---

## 📈 Performance Tuning

### Add Comment Index (Optional)
```sql
-- If you frequently search by comment
ALTER TABLE drama_ratings ADD FULLTEXT INDEX ft_comment (comment);
```

### Archive Old Ratings (Optional)
```sql
-- Move ratings older than 1 year to archive table
CREATE TABLE drama_ratings_archive LIKE drama_ratings;

INSERT INTO drama_ratings_archive
SELECT * FROM drama_ratings
WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);

DELETE FROM drama_ratings
WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
```

---

## 🔐 Backup Strategy

### Before Deploying Rating System
```bash
# Full database backup
mysqldump -u root -p rangamandala_db > backup_$(date +%Y%m%d_%H%M%S).sql

# Just drama_ratings table backup
mysqldump -u root -p rangamandala_db drama_ratings > drama_ratings_backup.sql
```

### Regular Backups
```bash
# Daily backup script
#!/bin/bash
BACKUP_DIR="/backups/database"
DB_NAME="rangamandala_db"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

mysqldump -u root -p $DB_NAME > $BACKUP_DIR/backup_$TIMESTAMP.sql

# Keep only last 7 days
find $BACKUP_DIR -name "backup_*.sql" -mtime +7 -delete
```

---

## 🚀 Deployment Checklist

### Pre-Deployment
- [ ] Backup current database
- [ ] Backup current application files
- [ ] Review all code changes
- [ ] Test locally first
- [ ] Verify all new files present

### Deployment
- [ ] Stop application (optional, for safety)
- [ ] Run database migration
- [ ] Deploy application files
- [ ] Verify table created
- [ ] Run verification queries
- [ ] Clear application cache

### Post-Deployment
- [ ] Monitor error logs
- [ ] Test rating functionality
- [ ] Verify database performance
- [ ] Monitor disk space
- [ ] Check query performance
- [ ] Document any issues

---

## 💻 Command Line Quick Reference

### Login to MySQL
```bash
mysql -u root -p
# Enter password when prompted
```

### Select Database
```sql
USE rangamandala_db;
```

### Run SQL File
```bash
mysql -u root -p rangamandala_db < DRAMA_RATINGS_DATABASE_SETUP.sql
```

### Create Backup
```bash
mysqldump -u root -p rangamandala_db > backup.sql
```

### Restore from Backup
```bash
mysql -u root -p rangamandala_db < backup.sql
```

### Check Table Structure
```sql
DESCRIBE drama_ratings;
```

### View Table Data
```sql
SELECT * FROM drama_ratings LIMIT 10;
```

### Count Ratings
```sql
SELECT COUNT(*) FROM drama_ratings;
```

### View Indexes
```sql
SHOW INDEXES FROM drama_ratings;
```

---

## 🆘 Troubleshooting

### Error: "Unknown column"
**Cause**: Table not created or column name misspelled
**Solution**: Run verification query to check table structure
```sql
DESCRIBE drama_ratings;
```

### Error: "Foreign key constraint fails"
**Cause**: Referenced drama_id or user_id doesn't exist
**Solution**: Check that drama and user exist first
```sql
-- Verify drama exists
SELECT * FROM dramas WHERE id = 1;

-- Verify user exists
SELECT * FROM users WHERE id = 1;
```

### Error: "Duplicate entry for key 'unique_rating_per_user'"
**Cause**: User already rated this drama
**Solution**: Update instead of insert
```sql
UPDATE drama_ratings 
SET rating = ?, comment = ? 
WHERE drama_id = ? AND user_id = ?;
```

### Error: "Access denied"
**Cause**: Wrong MySQL user/password
**Solution**: Check credentials
```bash
mysql -u root -p  # Enter correct password
```

### Table Won't Optimize
**Cause**: Ongoing transactions
**Solution**: Wait for transactions to complete, try again
```sql
OPTIMIZE TABLE drama_ratings;
```

---

## 📋 Migration Checklist Template

```
Date: ___________
Performed By: __________
Database: rangamandala_db

Pre-Migration:
☐ Full database backup created: ___________
☐ Code changes reviewed
☐ Table structure verified locally
☐ Permissions verified

Migration:
☐ drama_ratings table created
☐ Indexes verified
☐ Foreign keys verified
☐ Sample data inserted and tested
☐ Test update executed
☐ Test delete executed

Post-Migration:
☐ Table optimization run
☐ Application tested (ratings work)
☐ Performance verified
☐ No errors in logs
☐ Backup location documented
☐ Team notified

Issues Encountered:
_________________________________

Resolution:
_________________________________

Sign-off:
Database Admin: __________________ Date: __________
Application Manager: _____________ Date: __________
```

---

## 📚 Additional Resources

- [MySQL Foreign Keys](https://dev.mysql.com/doc/refman/8.0/en/create-table-foreign-keys.html)
- [MySQL Indexes](https://dev.mysql.com/doc/refman/8.0/en/create-index.html)
- [MySQL UNIQUE Constraint](https://dev.mysql.com/doc/refman/8.0/en/constraint-unique.html)
- [MySQL Backup](https://dev.mysql.com/doc/refman/8.0/en/backup-and-recovery.html)

---

## 🎯 Final Verification

After migration, verify everything:

```sql
-- 1. Table exists
SHOW TABLES LIKE 'drama_ratings';

-- 2. Structure correct
DESCRIBE drama_ratings;

-- 3. Indexes exist
SHOW INDEXES FROM drama_ratings;

-- 4. Foreign keys configured
SELECT CONSTRAINT_NAME, TABLE_NAME, REFERENCED_TABLE_NAME 
FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS 
WHERE TABLE_NAME = 'drama_ratings';

-- 5. No data issues
SELECT COUNT(*) FROM drama_ratings;

-- 6. Unique constraint works
-- Try inserting duplicate (should fail)
INSERT INTO drama_ratings (drama_id, user_id, rating) 
VALUES (1, 1, 5);
-- Should error if already exists

-- 7. Performance acceptable
SHOW TABLE STATUS WHERE NAME = 'drama_ratings';
```

---

**Note**: Keep this guide handy for future updates and maintenance! 📝
