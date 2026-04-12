# Quick Start Guide - Drama Creation Feature

## 🚀 Setup (2 Minutes)

### Step 1: Run Database Migration
Open phpMyAdmin or MySQL command line and execute:

```sql
ALTER TABLE `dramas` 
ADD COLUMN `creator_artist_id` int(11) DEFAULT NULL COMMENT 'The artist who is the director' AFTER `created_by`,
ADD KEY `creator_artist_id` (`creator_artist_id`),
ADD CONSTRAINT `dramas_ibfk_3` FOREIGN KEY (`creator_artist_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
```

### Step 2: Done! ✅
The upload directory is already created: `app/uploads/drama_images/`

## 🎭 How to Use

1. **Login** as an artist
2. Go to **Artist Dashboard**
3. Click **"Create Drama"** button
4. Fill out the form
5. Click **"Create Drama"** to submit
6. See your new drama in the dashboard!

## 📁 What Was Added

### New Files:
- `app/controllers/CreateDrama.php` - Drama creation logic
- `app/views/create_drama.view.php` - Creation form
- `app/uploads/drama_images/` - Image storage

### Updated Files:
- `app/models/M_drama.php` - Enhanced to set director
- `app/views/artistdashboard.view.php` - Button links updated
- `database_setup.sql` - Schema updated

## 🔗 URLs

- Create Drama: `http://localhost/rangamadala/createDrama`
- Artist Dashboard: `http://localhost/rangamadala/artistdashboard`

## 🐛 Troubleshooting

**Issue**: Can't access the form
- Check: Are you logged in as an artist?
- Check: Does `CreateDrama.php` exist in `/app/controllers/`?

**Issue**: Form doesn't submit
- Check: Did you run the database migration?
- Check: Does the `creator_artist_id` column exist?

**Issue**: Image won't upload
- Check: Does `/app/uploads/drama_images/` directory exist?
- Check: Is the directory writable?
- Check: Is image under 5MB?

**Issue**: Drama doesn't appear in dashboard
- Check: Did the database INSERT succeed?
- Check: Is the `get_dramas_by_director()` method working?
- Run: `SELECT * FROM dramas WHERE creator_artist_id = [your_user_id];`

## ✨ Features

✅ Create drama with full details  
✅ Upload drama poster  
✅ Auto-assign as director  
✅ Form validation  
✅ Success/error messages  
✅ Beautiful responsive design  
✅ Consistent with site theme  

## 📊 Test SQL Queries

**Check if migration worked:**
```sql
DESCRIBE dramas;
-- Should show creator_artist_id column
```

**View created dramas:**
```sql
SELECT id, title, creator_artist_id, created_at 
FROM dramas 
ORDER BY created_at DESC;
```

**Check specific artist's dramas:**
```sql
SELECT d.*, u.full_name as director_name
FROM dramas d
LEFT JOIN users u ON d.creator_artist_id = u.id
WHERE d.creator_artist_id = [artist_user_id];
```

## 🎉 That's It!

Your drama creation feature is ready to use. Happy creating! 🎭
