# Drama Creation Feature - Database Migration Guide

## Overview
This migration adds the ability for artists to create dramas and automatically become the director of those dramas.

## What's New

### Database Changes
- Added `creator_artist_id` column to the `dramas` table
- This column tracks which artist is the director/creator of each drama
- It references the `users` table (where user_id has role='artist')

### New Features
1. **Create Drama Form** - Artists can fill out a detailed form to create new dramas
2. **Automatic Director Assignment** - When an artist creates a drama, they automatically become the director
3. **Image Upload** - Support for uploading drama posters/images

## Migration Steps

### Step 1: Run the Database Migration
Execute the following SQL commands in your database:

```sql
-- Add the creator_artist_id column
ALTER TABLE `dramas` 
ADD COLUMN `creator_artist_id` int(11) DEFAULT NULL COMMENT 'The artist who is the director' AFTER `created_by`,
ADD KEY `creator_artist_id` (`creator_artist_id`),
ADD CONSTRAINT `dramas_ibfk_3` FOREIGN KEY (`creator_artist_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Copy existing data from created_by to creator_artist_id
UPDATE `dramas` 
SET `creator_artist_id` = `created_by` 
WHERE `creator_artist_id` IS NULL AND `created_by` IS NOT NULL;
```

**OR** run the migration file:
```bash
mysql -u your_username -p rangamandala_db < database_migration_creator_artist.sql
```

### Step 2: Create Upload Directory
Create the directory for drama images:
```bash
mkdir -p app/uploads/drama_images
chmod 777 app/uploads/drama_images
```

On Windows (PowerShell):
```powershell
New-Item -ItemType Directory -Force -Path "app\uploads\drama_images"
```

### Step 3: Verify Files
Ensure these new files exist:
- `/app/controllers/CreateDrama.php`
- `/app/views/create_drama.view.php`
- `/database_migration_creator_artist.sql`

## How It Works

### User Flow
1. Artist logs into their dashboard
2. Clicks "Create Drama" button
3. Fills out the drama creation form with:
   - Title (required)
   - Description (required)
   - Category (required)
   - Venue (required)
   - Event Date (required)
   - Event Time (required)
   - Duration (optional)
   - Ticket Price (optional)
   - Drama Poster/Image (optional)
4. Submits the form
5. Drama is created in the database with:
   - `created_by` = artist's user_id
   - `creator_artist_id` = artist's user_id (makes them director)
6. Artist is redirected back to dashboard where they can see their new drama

### Technical Details

**Controller**: `CreateDrama.php`
- Handles form display and submission
- Validates input data
- Handles image upload
- Creates drama record in database

**View**: `create_drama.view.php`
- Beautiful, responsive form
- Category dropdown populated from database
- Date/time pickers
- File upload with preview
- Validation messages

**Model**: `M_drama.php` (updated)
- Enhanced `createDrama()` method
- Now sets both `created_by` and `creator_artist_id`
- Returns the new drama ID on success

**Database**:
- `created_by`: General tracking of who created the record
- `creator_artist_id`: Specific tracking of the director/artist

## Testing

### Test the Feature
1. Log in as an artist
2. Go to Artist Dashboard
3. Click "Create Drama"
4. Fill out the form
5. Submit
6. Verify:
   - Success message appears
   - New drama shows in "Dramas You're Directing" tab
   - Database record has both `created_by` and `creator_artist_id` set

### SQL to Check
```sql
SELECT id, title, created_by, creator_artist_id 
FROM dramas 
WHERE creator_artist_id IS NOT NULL 
ORDER BY created_at DESC 
LIMIT 5;
```

## Rollback (If Needed)

To undo the database changes:
```sql
-- Remove the foreign key constraint
ALTER TABLE `dramas` DROP FOREIGN KEY `dramas_ibfk_3`;

-- Remove the index
ALTER TABLE `dramas` DROP KEY `creator_artist_id`;

-- Remove the column
ALTER TABLE `dramas` DROP COLUMN `creator_artist_id`;
```

## Notes

- Both `created_by` and `creator_artist_id` are kept for flexibility
- Images are stored in `app/uploads/drama_images/`
- Maximum image size: 5MB
- Supported image formats: JPG, PNG, GIF
- All artists automatically become directors of dramas they create
- The relationship is tracked via foreign key to maintain data integrity

## Support

If you encounter any issues:
1. Check that the database migration ran successfully
2. Verify the upload directory exists and has proper permissions
3. Check PHP error logs for any issues
4. Ensure your PHP configuration allows file uploads (check `upload_max_filesize` and `post_max_size`)
