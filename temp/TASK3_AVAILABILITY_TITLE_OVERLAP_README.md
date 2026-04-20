# Task 3 (Availability Title + Overlap Business Rule)

This task pair:
- Task 1: Add title field to provider availability entries (CRUD).
- Task 2: Prevent overlapping booked time slots.

## Existing files/functions/lines to update

1. Availability controller
- File: app/controllers/ServiceAvailability.php
- Function index: line 17
- Function addDate: line 106
- Function updateDate: line 172

2. Availability model
- File: app/models/M_provider_availability.php
- Function addAvailableDate: line 15
- Function getAvailability: line 58
- Function updateAvailableDate: line 122

3. Availability view
- File: app/views/service_availability.view.php
- Existing modal/description sections around line 88 onward
- JS add handler: function addDateWithDescription around line 430 (from current file comments)

## 0) DB Migration

~~~sql
ALTER TABLE provider_availability
ADD COLUMN title VARCHAR(150) NULL AFTER available_date;
~~~

## 1) Model changes (Task 1 CRUD)

### 1.1 Update addAvailableDate signature and query
File: app/models/M_provider_availability.php line 15

Add parameter $title = null and include it in INSERT/UPSERT.

~~~php
public function addAvailableDate($provider_id, $available_date, $title, $description, $status = 'available', $booked_for = null, $booking_details = null, $service_request_id = null, $allow_more_bookings = 1)
{
    // include title in SQL and bind
    $this->db->bind(':title', $title);
}
~~~

### 1.2 Update updateAvailableDate signature/query
File: app/models/M_provider_availability.php line 122

~~~php
public function updateAvailableDate($provider_id, $available_date, $title, $description, $status = 'available')
{
    // set title = :title and bind
}
~~~

## 2) Controller changes

### 2.1 addDate() read title
File: app/controllers/ServiceAvailability.php line 106

~~~php
$title = trim($_POST['title'] ?? '');
~~~

Pass title to model add call.

### 2.2 updateDate() read title
File: app/controllers/ServiceAvailability.php line 172

~~~php
$title = trim($_POST['title'] ?? '');
~~~

Pass title to model update call.

## 3) View changes (Task 1)

File: app/views/service_availability.view.php

Add title input in add/edit modal form section:

~~~html
<label for="dateTitle">Title:</label>
<input type="text" id="dateTitle" name="title" placeholder="e.g., Wedding shoot, Rehearsal support" />
~~~

Update JS payload in addDateWithDescription() and update handlers to send title.

## Task 2 Extension (Business Logic)

Rule:
- Reject overlapping bookings for same provider when allow_more_bookings = 0.

## 4) Add overlap checker in model

File: app/models/M_provider_availability.php
Add new method near getAvailability/getAvailabilityByDate:

~~~php
public function hasOverlappingBooking($provider_id, $date, $newStartTime, $newEndTime): bool
{
    // If your table has start_time/end_time columns, use time overlap SQL.
    // Overlap condition: (new_start < existing_end) AND (new_end > existing_start)
    // and allow_more_bookings = 0 and status='booked'.

    return false; // replace with actual query result
}
~~~

If start/end time columns do not exist yet, add them first via migration.

## 5) Enforce rule in controller

File: app/controllers/ServiceAvailability.php
Functions: addDate (line 106), updateDate (line 172)

Before insert/update, call hasOverlappingBooking(...). If true:

~~~php
return $this->jsonResponse([
    'success' => false,
    'message' => 'This time overlaps with an existing booking.'
], 422);
~~~

## 6) Optional UX

In availability calendar view, show booking title in tooltip/card so provider understands conflicts quickly.
