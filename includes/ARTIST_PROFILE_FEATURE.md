# Artist Profile Feature

This guide explains how the artist profile page works after the recent implementation. Use it to understand the request flow, storage locations, and integration touchpoints.

## Prerequisites
- Apply the following database changes if your environment predates this update:
  - `ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) NULL;`
  - `ALTER TABLE users ADD COLUMN years_experience INT NULL;`
- Ensure the directory `public/uploads/profile_images/` exists and is writable by the web server.

## Routing Overview
- URL: `/artistprofile`
- Controller: [app/controllers/Artistprofile.php](app/controllers/Artistprofile.php)
- View: [app/views/artistprofile.view.php](app/views/artistprofile.view.php)
- Data access: [app/models/M_artist.php](app/models/M_artist.php)

You must be logged in as an artist. Unauthorized users are redirected to the login page.

## Controller Flow
1. Validates session role and loads the current artist record via `M_artist::get_artist_by_id`.
2. Prefills the form values (`full_name`, `phone`, `years_experience`) and current profile image.
3. On POST:
   - Sanitizes text fields and enforces:
     - Name and phone cannot be empty.
     - Years of experience must be a positive whole number (optional field).
   - Validates the uploaded file (≤5 MB, image MIME, allowed extensions: jpg, jpeg, png, gif, webp).
   - Stores the image under `public/uploads/profile_images/artist_{userId}_{timestamp}.{ext}`.
   - Calls `M_artist::update_artist_profile` to persist the changes.
   - Deletes the previous profile image file when a new one is saved successfully (if it exists and belongs to the artist).
   - Refreshes the view data and surfaces success or error alerts.

## Model Responsibilities
- `M_artist::get_artist_by_id($user_id)` reads the artist row (including new columns `profile_image` and `years_experience`).
- `M_artist::update_artist_profile($user_id, $fields)` accepts a filtered associative array (full_name, phone, profile_image, years_experience) and applies the update to the `users` table. Non-whitelisted keys are ignored.

## View Highlights
- Displays the artist details, current image, contact information, and NIC document link (if uploaded during signup).
- Form fields:
  - Editable: Full Name, Phone, Years of Experience, Profile Image upload.
  - Read-only: Email (mirrors signup data).
- Shows contextual alerts for validation errors and success confirmation.
- Uses default avatar imagery when no profile image exists.

## File Storage Rules
- Upload path: `public/uploads/profile_images/`
- Filenames are timestamped per user to avoid collisions.
- Old files are removed only if the artist uploads a new image and the previous filename resolves under the upload directory.

## Typical Usage Steps
1. Artist logs in and visits `/artistprofile`.
2. Reviews preloaded info and updates any field.
3. (Optional) Chooses a new profile photo and submits.
4. On success, the dashboard and other touchpoints using `$_SESSION['user_name']` reflect the updated name.

## Troubleshooting
- If images do not appear, verify file permissions on `public/uploads/profile_images/` and confirm the generated filename exists.
- Ensure PHP file upload limits (`upload_max_filesize`, `post_max_size`) in `php.ini` support files up to 5 MB.
- Confirm the migrations ran on the target database; missing columns will cause update errors that are logged via `error_log`.
