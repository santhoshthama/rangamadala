# Multi-Booking Feature Implementation Guide

## Summary
This implementation allows service providers to accept multiple bookings on the same date. When a provider accepts a service request, they are asked whether to **allow additional bookings** on those dates or **fully block** them.

## Key Features
✅ **Provider Decision Modal**: When accepting a request, provider chooses allow more bookings (Yes/No)  
✅ **Dynamic Calendar Colors**: PMs see dates as available (green) if provider allowed more bookings, or booked (red) if blocked  
✅ **Multi-Booking Tracking**: New table tracks individual bookings per date with provider's decision  
✅ **Provider Calendar**: Shows all bookings on a date with their allow_more status  
✅ **Backward Compatible**: Old flow still works via acceptConfirmed() which defaults to blocking dates

---

## Database Changes Required

### 1. Run This Migration
Execute the SQL in: `/dev/add_provider_date_bookings_table.sql`

Or manually run:
```sql
-- Create new bookings tracking table
CREATE TABLE IF NOT EXISTS `provider_date_bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `provider_id` int NOT NULL,
  `available_date` date NOT NULL,
  `service_request_id` int NOT NULL,
  `allow_more_after_this` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Provider allowed additional bookings on this date (1=yes, 0=no)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_provider_date_request` (`provider_id`, `available_date`, `service_request_id`),
  KEY `idx_provider_date` (`provider_id`, `available_date`),
  KEY `idx_provider` (`provider_id`),
  KEY `idx_available_date` (`available_date`),
  KEY `idx_service_request_id` (`service_request_id`),
  CONSTRAINT `provider_date_bookings_ibfk_provider` FOREIGN KEY (`provider_id`) REFERENCES `serviceprovider` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `provider_date_bookings_ibfk_request` FOREIGN KEY (`service_request_id`) REFERENCES `service_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add column to provider_availability to store allow_more summary
ALTER TABLE `provider_availability` ADD COLUMN IF NOT EXISTS `allow_more_bookings` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Summary: Can more bookings be added to this date?' AFTER `status`;
```

---

## File Changes Summary

### 1. **Models** (`app/models/`)

#### M_provider_availability.php
**New Methods:**
- `addDateBooking($provider_id, $date, $request_id, $allow_more)` - Add booking with allow_more decision
- `getDateBookings($provider_id, $date)` - Get all bookings for a specific date
- `updateDateSummary($provider_id, $date)` - Recompute date status based on bookings
- `removeDateBooking($provider_id, $date, $request_id)` - Remove booking and recompute summary

**Logic:**
- Date summary status = 'booked' if ANY booking has allow_more=0
- Date summary status = 'available' if ALL bookings have allow_more=1

#### M_service_request.php
**Changed Methods:**
- `markDatesAsBooked($request_id, $allow_more = 1)` - Now accepts allow_more parameter
- `unmarkBookedDates($request_id)` - Uses new removeDateBooking method

**New Methods:**
- `acceptConfirmedWithDecision($request_id, $provider_id, $allow_more)` - Accept with decision on overlapping bookings

**Backward Compatibility:**
- Old `acceptConfirmed()` still works, defaults to allow_more=0 (fully blocks)

---

### 2. **Controllers** (`app/controllers/`)

#### ServiceProviderRequest.php
**Updated `acceptConfirmed()` action:**
- Now reads `allow_more` parameter from POST
- Passes it to `acceptConfirmedWithDecision()`

#### BrowseServiceProviders.php
**Updated booked dates logic:**
- Only marks date as blocked if: `status='booked' AND allow_more_bookings=0`
- Dates with `status='booked' AND allow_more_bookings=1` stay green for new bookings

#### ServiceAvailability.php
**Updated `index()` action:**
- Now includes `all_bookings` array in availability data
- Shows provider all bookings on each date in their calendar

**New `getDateBookings()` action:**
- AJAX endpoint to fetch existing bookings for a date range
- Called when provider opens accept dialog

---

### 3. **Views** (`app/views/`)

#### service_requests.view.php
**Updated JavaScript:**
- `acceptConfirmedRequest()` - Opens modal instead of simple confirm
- `openAcceptConfirmModal(requestId, reqData)` - Shows modal with:
  - Date range being booked
  - Existing bookings on those dates (fetched via AJAX)
  - Two buttons: "Allow More Bookings" (green) or "Block Date" (red)
- `confirmAcceptWithDecision(allowMore)` - Submits with provider's decision

**New Modal HTML:**
- `#acceptConfirmModal` - Clean, modern modal for decision-making

#### service_availability.view.php
**Updated `viewDateDetails()` function:**
- Shows `allow_more_bookings` status with color-coded badges
- If multiple bookings exist, displays all of them with their individual allow_more status
- Provider can see exactly which bookings block/allow overlap

---

## User Flow

### For Service Provider

1. **Accept Request** → Button shows "Confirmed" status requests
2. Click "Accept these terms" → Modal appears showing:
   - Dates being booked
   - Existing bookings on those dates (if any)
   - Two options:
     - **Green button**: "Allow More Bookings" → Other PMs can still book (allow_more=1)
     - **Red button**: "Block Date" → Others cannot book (allow_more=0)
3. Choose & confirm
4. Dates marked in their calendar with multi-booking icon if allow_more=1

### For Production Manager

1. Browse service providers
2. When requesting service, see provider's availability calendar
3. **Green dates** = Available OR provider allowed more bookings
4. **Red dates** = Fully blocked by another booking
5. Can book on green dates freely
6. System auto-prevents double-booking on red dates

### For Provider's Calendar Page

1. Provider sees their dates with bookings
2. Click a date → Modal shows:
   - Allow More Bookings: ✓ Yes / ✗ No
   - All bookings on that date with their requester info
   - Can edit/remove manual bookings
   - Cannot edit auto-booked dates (from accepted requests)

---

## Testing Checklist

- [ ] Migration runs successfully
- [ ] Provider accepts request with "Allow More" → PM calendar shows green
- [ ] Provider accepts request with "Block Date" → PM calendar shows red
- [ ] Multiple bookings on same date display correctly in provider calendar
- [ ] PM cannot book on dates marked as blocked (red)
- [ ] PM can book on green dates even with existing bookings
- [ ] Rejecting request clears the bookings correctly
- [ ] Provider's availability calendar shows correct allow_more status badges
- [ ] Old code still works (backward compatibility)

---

## Database Schema Diagram

```
provider_date_bookings (NEW)
├── id (PK)
├── provider_id (FK) → serviceprovider.user_id
├── available_date
├── service_request_id (FK) → service_requests.id
├── allow_more_after_this (1=yes, 0=no)
└── created_at

provider_availability (MODIFIED)
├── id
├── provider_id
├── available_date
├── status ('available' or 'booked')
├── allow_more_bookings ← NEW (1=yes, 0=no)
├── ... (other fields)
```

---

## API Endpoints

### New Endpoint: `POST /ServiceAvailability/getDateBookings`
**Request:**
```json
{
  "start_date": "2026-04-15",
  "end_date": "2026-04-20"
}
```

**Response:**
```json
{
  "success": true,
  "bookings": [
    {
      "date": "2026-04-15",
      "requester_name": "John Doe",
      "drama_name": "My Drama",
      "service_type": "Lighting Design",
      "allow_more": 1
    }
  ]
}
```

### Modified Endpoint: `POST /ServiceProviderRequest/acceptConfirmed`
**Request (NEW parameter):**
```json
{
  "request_id": 123,
  "allow_more": 1  // ← NEW: 1 for allow, 0 for block
}
```

---

## Notes for Developers

1. **Backward Compatibility**: The old `acceptConfirmed()` method still works - it defaults to allow_more=0
2. **Date Summary Logic**: The `updateDateSummary()` method automatically computes the overall date status based on all bookings
3. **Cascading Deletes**: Removing a request automatically removes its bookings and recomputes summaries
4. **Case Study**: 
   - Jan creates request for Apr 15 (Lighting) → Provider accepts with allow_more=1
   - Feb creates request for Apr 15 (Sound) → Provider can still accept on same date
   - PM's calendar shows Apr 15 as GREEN (available for more bookings)
   - If provider later accepts with allow_more=0, date turns RED

---

## Support Commands

Reset provider's bookings for a date:
```sql
DELETE FROM provider_date_bookings 
WHERE provider_id = ? AND available_date = ?;

-- Recompute summary (set status='available')
DELETE FROM provider_availability 
WHERE provider_id = ? AND available_date = ?;
```

---

**Implementation Date:** April 12, 2026  
**Status:** ✅ Complete - Ready for Testing
