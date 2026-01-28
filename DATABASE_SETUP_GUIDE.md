# 🔧 RANGAMADALA DATABASE SETUP GUIDE

## Problem
Error: `#1109 - Unknown table 'dramas' in information_schema`

**This means:** Your database tables don't exist yet. The database setup was not completed.

---

## ✅ SOLUTION: Complete Database Setup

### **Option 1: Using PhpMyAdmin (Easiest)**

1. **Open PhpMyAdmin**
   - Go to: `http://localhost/phpmyadmin`
   - Login with your credentials

2. **Select Your Database**
   - Click on `rangamandala_db` in the left panel
   - If it doesn't exist, create it first

3. **Run Setup Script**
   - Click the **SQL** tab at the top
   - Open file: `COMPLETE_DATABASE_SETUP.sql`
   - Copy ALL the SQL code
   - Paste it into the SQL editor
   - Click **Go** button to execute

4. **Verify Success**
   - You should see green success messages
   - Check that all tables are created

### **Option 2: Using Command Line**

1. **Open Command Prompt**
   - Press `Win + R`
   - Type: `cmd`
   - Press Enter

2. **Navigate to MySQL**
   ```bash
   cd "C:\xampp\mysql\bin"
   ```

3. **Connect to MySQL**
   ```bash
   mysql -u root -p
   ```
   - Leave password empty (just press Enter)

4. **Select Database**
   ```sql
   USE rangamandala_db;
   ```

5. **Run Setup Script**
   ```bash
   SOURCE "C:\xampp\htdocs\Rangamadala\COMPLETE_DATABASE_SETUP.sql";
   ```

6. **Verify**
   ```sql
   SHOW TABLES;
   ```

### **Option 3: Using MySQL Workbench**

1. Open MySQL Workbench
2. Connect to your local MySQL server
3. File → Open SQL Script
4. Select: `COMPLETE_DATABASE_SETUP.sql`
5. Execute the script
6. Verify tables are created

---

## 📋 What Gets Created

The setup script creates these tables:
- ✅ `users` - All user accounts
- ✅ `categories` - Drama categories
- ✅ `dramas` - Drama/Event records
- ✅ `serviceprovider` - Service provider profiles
- ✅ `service_types` - Types of services
- ✅ `services` - Service offerings
- ✅ `service_theater_details` - Theater service details
- ✅ `service_lighting_details` - Lighting service details
- ✅ `service_sound_details` - Sound service details
- ✅ `service_video_details` - Video service details

---

## 🔍 Verification Steps

After running the setup, verify everything worked:

### **Check 1: List all tables**
```sql
SHOW TABLES;
```
You should see: 10 tables

### **Check 2: Count records**
```sql
SELECT COUNT(*) as total_dramas FROM dramas;
SELECT COUNT(*) as total_categories FROM categories;
```

### **Check 3: Verify sample data**
```sql
SELECT * FROM categories LIMIT 5;
```
You should see 8 drama categories inserted

---

## ✨ Now You Can Use These Queries

### **Count Dramas**
```sql
SELECT COUNT(*) as total_dramas FROM dramas;
```

### **Get All Dramas with Details**
```sql
SELECT 
    d.id,
    d.title,
    d.description,
    c.name as category,
    d.venue,
    d.event_date,
    d.ticket_price,
    u.full_name as created_by
FROM dramas d
LEFT JOIN categories c ON d.category_id = c.id
LEFT JOIN users u ON d.created_by = u.id
ORDER BY d.created_at DESC
LIMIT 25;
```

### **Get User Statistics**
```sql
SELECT role, COUNT(*) as count 
FROM users 
GROUP BY role;
```

---

## 🆘 Still Getting Errors?

### **Error: "Unknown database 'rangamandala_db'"**
Create the database first:
```sql
CREATE DATABASE rangamandala_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### **Error: "Access denied for user 'root'@'localhost'"**
- Make sure MySQL is running
- Check your XAMPP Control Panel
- Make sure Apache and MySQL are both started

### **Error: "Syntax error near..."**
- Make sure you copied the ENTIRE script
- Don't miss any semicolons (;) at end of lines
- Try running smaller sections first

### **Tables still don't show up**
- Refresh PhpMyAdmin (F5)
- Check that you selected the right database
- Verify you clicked "Go" to execute

---

## 📁 Important Files

- **COMPLETE_DATABASE_SETUP.sql** - Main setup script
- **database_setup.sql** - Original database setup (alternative)
- **ADMIN_DATA_QUERIES.sql** - Admin queries (use after setup)
- **TROUBLESHOOTING_QUERIES.sql** - Verification queries

---

## ⚡ Quick Reference

| File | Purpose |
|------|---------|
| COMPLETE_DATABASE_SETUP.sql | Create all tables + insert data |
| ADMIN_DATA_QUERIES.sql | Query admin information |
| TROUBLESHOOTING_QUERIES.sql | Verify database setup |

---

## ✅ Checklist

- [ ] Opened PhpMyAdmin or command line
- [ ] Selected database `rangamandala_db`
- [ ] Ran `COMPLETE_DATABASE_SETUP.sql`
- [ ] Saw success messages
- [ ] Ran `SHOW TABLES;` and saw 10 tables
- [ ] Ran `SELECT COUNT(*) FROM dramas;` successfully
- [ ] Ready to use admin queries!

---

**Once you complete these steps, all your admin queries will work! 🎉**
