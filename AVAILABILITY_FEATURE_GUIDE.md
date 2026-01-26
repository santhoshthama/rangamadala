# Implementation Guide: Mark Service Request Dates in Availability Calendar

## Overview
This implementation allows service providers to automatically mark dates as "booked" in their availability calendar when they accept a service request. When requests are rejected or cancelled, the dates are automatically unmarked.

## Changes Made

### 1. Database Table Created
**File:** `add_availability_table.sql`
- Created `provider_availability` table to store:
  - Provider's available dates
  - Date status (available/booked)
  - Booking details and client information
  - Link to service request ID

### 2. New Model Class
**File:** `app/models/M_provider_availability.php`
- `addAvailableDate()` - Add an available date
- `markAsBooked()` - Mark a date as booked when request is accepted
- `getAvailability()` - Fetch all availability for a provider
- `removeAvailableDate()` - Remove an available date
- `updateAvailableDate()` - Update date description
- `unmarkBooked()` - Unmark dates when request is rejected/cancelled

### 3. Updated M_service_request Model
**File:** `app/models/M_service_request.php`
- Modified `updateStatusDetailed()` to automatically:
  - Mark all dates as booked when status = 'accepted'
  - Unmark dates when status = 'rejected' or 'cancelled'
- Added helper methods:
  - `getRequestById()` - Fetch request details
  - `markDatesAsBooked()` - Mark date range as booked
  - `unmarkBookedDates()` - Unmark date range

### 4. Updated ServiceAvailability Controller
**File:** `app/controllers/ServiceAvailability.php`
- `index()` - Load availability data from database
- `addDate()` - AJAX endpoint to add dates
- `removeDate()` - AJAX endpoint to remove dates
- `updateDate()` - AJAX endpoint to update dates
- Helper methods for date format conversion (JS ↔ Database)

### 5. Updated service_availability View
**File:** `app/views/service_availability.view.php`
- Updated JavaScript to load availability data from backend
- Added AJAX calls for add/remove/update operations
- Frontend now syncs with backend database

## How It Works

### Flow When Service Request is Accepted:
1. Service provider views service requests
2. Provider accepts a request via `ServiceRequests/updateStatus` 
3. Backend updates request status to 'accepted'
4. `updateStatusDetailed()` automatically calls `markDatesAsBooked()`
5. All dates from start_date to end_date are marked as "booked" in provider_availability table
6. Booked dates display as "booked" in the calendar with client info

### Flow When Service Request is Rejected:
1. Provider rejects a request
2. Backend updates request status to 'rejected'
3. `updateStatusDetailed()` automatically calls `unmarkBookedDates()`
4. All dates are reset to "available"

### Manual Availability Management:
- Provider can manually add/remove/edit available dates
- All operations save to the database
- Frontend loads data from backend on page load

## Database Schema

```sql
CREATE TABLE `provider_availability` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `provider_id` int NOT NULL,
  `available_date` date NOT NULL,
  `status` enum('available','booked') DEFAULT 'available',
  `description` text,
  `booked_for` varchar(255),
  `booking_details` text,
  `service_request_id` int,
  `added_on` timestamp DEFAULT CURRENT_TIMESTAMP,
  `booked_on` timestamp,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `provider_date` (`provider_id`, `available_date`),
  FOREIGN KEY (`provider_id`) REFERENCES `serviceprovider`(`user_id`),
  FOREIGN KEY (`service_request_id`) REFERENCES `service_requests`(`id`)
)
```

## Installation Steps

1. **Run the SQL migration:**
   ```bash
   mysql -u root -p rangamadala_db < add_availability_table.sql
   ```

2. **Files to check for dependencies:**
   - Ensure `M_provider_availability.php` is in `app/models/`
   - Ensure `ServiceAvailability.php` controller is updated
   - Ensure view file includes the updated JavaScript

3. **Test the feature:**
   - Create a service request
   - Accept it as a service provider
   - Check the availability calendar - dates should be marked as "booked"
   - Reject the request - dates should revert to "available"

## API Endpoints

### Add Available Date
- **Endpoint:** `POST /ServiceAvailability/addDate`
- **Parameters:** `date` (M/D/YYYY), `description`
- **Response:** JSON with success status

### Remove Available Date
- **Endpoint:** `POST /ServiceAvailability/removeDate`
- **Parameters:** `date` (M/D/YYYY)
- **Response:** JSON with success status

### Update Available Date
- **Endpoint:** `POST /ServiceAvailability/updateDate`
- **Parameters:** `date` (M/D/YYYY), `description`
- **Response:** JSON with success status

## Key Features

✅ Automatic date booking when request is accepted
✅ Automatic date release when request is rejected
✅ Manual availability management
✅ Database persistence
✅ Calendar displays booked dates with client info
✅ Date range support (all dates from start to end are marked)
✅ AJAX-based operations for smooth UX
✅ Booked dates cannot be edited by provider (read-only)

## Notes

- Date format in JavaScript: `M/D/YYYY` (e.g., `1/15/2025`)
- Date format in database: `Y-m-d` (e.g., `2025-01-15`)
- All dates in a request range (start_date to end_date) are marked as booked
- Booked dates display client name and drama name in the calendar
- Past dates cannot be selected for availability
