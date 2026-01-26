# Service Request Calendar Widget - Complete Explanation

## Overview
The calendar widget allows production managers to visually select available date ranges for service requests while avoiding dates that are already booked by the service provider.

---

## How It Works (User Flow)

### **Step 1: User Opens Request Modal**
```
User clicks "Request Service" button
→ Modal opens
→ Calendar initializes with current month
→ Booked dates loaded from database (via data-booked-dates attribute)
```

### **Step 2: User Views Calendar**
- **Green dates** = Available (can click to select)
- **Red dates** = Booked (cannot select)
- **Blue dates** = Currently selected range
- **Gray dates** = Past dates (cannot select)

### **Step 3: User Selects Date Range**
```
1. User clicks first date (green) → Becomes "waiting for end date" state
2. User clicks second date (green) → Range is selected and validated
3. If range contains booked dates → Rejected, must try again
4. If range is valid → Hidden inputs populated, ready to submit
```

### **Step 4: Form Submission**
```
Hidden inputs (start_date, end_date) contain selected dates
→ Form submitted to ServiceProviderRequest controller
→ Dates saved to database
→ Request marked as "pending" for service provider
```

---

## Technical Architecture

### **1. CSS Structure (Styling)**

Located in `<style>` section at top of modal:

```css
.calendar-widget              /* Main container - white box */
.calendar-header              /* Month/year + navigation buttons */
.calendar-btn                 /* Prev/Next buttons */
.calendar-legend              /* Color legend (Booked, Available, Selected) */
.calendar-grid                /* 7-column grid for dates */
.calendar-day-header          /* Day names (Sun, Mon, etc) */
.calendar-day-cell            /* Individual date cell */
.calendar-day-available       /* Green background for available dates */
.calendar-day-selected        /* Blue background for selected range */
.calendar-day-booked          /* Red background for booked dates */
.calendar-day-past            /* Gray background for past dates */
.calendar-selection-info      /* Info box showing selected dates */
```

**Why CSS Classes?**
- ✅ Better performance (no inline style recalculation)
- ✅ Easier to maintain
- ✅ Reusable across similar widgets
- ✅ Professional best practice

---

### **2. JavaScript Object Pattern**

The calendar is managed by a single `CalendarWidget` object to avoid global variables:

```javascript
CalendarWidget = {
    // STATE (Data stored in the object)
    currentMonth,
    currentYear,
    selectedStartDate,
    selectedEndDate,
    bookedDates,
    
    // METHODS (Functions that do things)
    init(),              // Initialize on page load
    generate(),          // Render calendar HTML
    selectDate(),        // Handle date selection
    validateRange(),     // Check for booked dates in range
    formatDate(),        // Format dates for display
    updateInfo(),        // Update selection message
    validateDates(),     // Final validation before submit
    getConflicts(),      // Find conflicting booked dates
    reset()              // Reset state when modal opens
}
```

**Why Object Pattern?**
- ✅ All data stays together
- ✅ No global variables polluting scope
- ✅ Self-contained and reusable
- ✅ Easy to debug (all state visible in one place)

---

## Detailed Event Flow

### **A. Page Load**

```javascript
document.addEventListener('DOMContentLoaded', () => {
    CalendarWidget.init();  // Runs once when page loads
});

init() {
    // 1. Get booked dates from data attribute
    const bookedDatesJSON = form.getAttribute('data-booked-dates');
    this.bookedDates = JSON.parse(bookedDatesJSON);  // e.g., ["2026-02-15", "2026-02-16"]
    
    // 2. Cache DOM elements
    this.startDateInput = document.getElementById('startDateInput');
    this.endDateInput = document.getElementById('endDateInput');
    
    // 3. Attach click listeners to navigation buttons
    this.attachEventListeners();
    
    // 4. Render current month
    this.generate(currentMonth, currentYear);
}
```

**Data Attribute Example:**
```html
<form data-booked-dates='["2026-02-15", "2026-02-16", "2026-02-20"]'>
```
This comes from PHP: `<?= json_encode($data['booked_dates']) ?>`

---

### **B. Month Navigation**

```javascript
// User clicks "Next" button
document.getElementById('nextMonth').addEventListener('click', (e) => {
    e.preventDefault();  // Don't submit form
    
    this.currentMonth++;
    if (this.currentMonth > 11) {
        this.currentMonth = 0;
        this.currentYear++;
    }
    
    this.generate(this.currentMonth, this.currentYear);  // Redraw
});

// Same logic for "Previous" button
```

---

### **C. Calendar Generation**

```javascript
generate(month, year) {
    // 1. Update heading
    document.getElementById('calendarMonthYear').textContent = "January 2026";
    
    // 2. Clear grid
    calendarGrid.innerHTML = '';
    
    // 3. Add day headers (Sun, Mon, Tue...)
    dayNames.forEach(day => {
        const header = document.createElement('div');
        header.className = 'calendar-day-header';
        header.textContent = day;
        calendarGrid.appendChild(header);
    });
    
    // 4. Add empty cells for days before month starts
    // Example: If Feb 1 is Monday, add 1 empty cell for Sunday
    for (let i = 0; i < firstDay; i++) {
        calendarGrid.appendChild(document.createElement('div'));
    }
    
    // 5. Add actual day cells (1-28/29/30/31)
    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = "2026-02-15";  // Format: YYYY-MM-DD
        const dayCell = document.createElement('div');
        dayCell.className = 'calendar-day-cell';
        dayCell.textContent = day;
        
        // Determine which class to apply
        if (isPast) {
            dayCell.className += ' calendar-day-past';      // Gray
        } else if (isBooked) {
            dayCell.className += ' calendar-day-booked';    // Red
        } else if (isSelected) {
            dayCell.className += ' calendar-day-selected';  // Blue
            dayCell.addEventListener('click', () => this.selectDate(dateStr));
        } else {
            dayCell.className += ' calendar-day-available'; // Green
            dayCell.addEventListener('click', () => this.selectDate(dateStr));
        }
        
        calendarGrid.appendChild(dayCell);
    }
}
```

**Visual Result:**
```
Sun  Mon  Tue  Wed  Thu  Fri  Sat
                            1   2
3    4    5    6    7   8    9
10   11   12  (RED)14  (RED) 16
17   18   19   20  (BLUE-21-22-23-BLUE)
24   25   26   27   28
```

---

### **D. Date Selection**

```javascript
selectDate(dateStr) {  // e.g., "2026-02-15"
    
    // Check if already booked
    if (this.bookedDates.includes(dateStr)) {
        alert('This date is already booked.');
        return;
    }
    
    // FIRST CLICK: Start selection
    if (!this.selectedStartDate) {
        this.selectedStartDate = dateStr;      // "2026-02-15"
        this.selectedEndDate = null;
        this.updateInfo('Selected Feb 15 (Click another date for end date)');
    }
    
    // SECOND CLICK: Complete selection
    else if (this.selectedStartDate && !this.selectedEndDate) {
        
        // Swap if user clicked earlier date
        if (new Date(dateStr) < new Date(this.selectedStartDate)) {
            this.selectedEndDate = this.selectedStartDate;
            this.selectedStartDate = dateStr;
        } else {
            this.selectedEndDate = dateStr;
        }
        
        // Validate entire range
        if (this.validateRange(this.selectedStartDate, this.selectedEndDate)) {
            // ✅ Valid - populate hidden inputs
            this.startDateInput.value = this.selectedStartDate;  // Hidden input
            this.endDateInput.value = this.selectedEndDate;      // Hidden input
            this.updateInfo('Selected Feb 15 to Feb 20');
            this.validateDates();
        } else {
            // ❌ Invalid - contains booked dates
            this.selectedStartDate = null;
            this.selectedEndDate = null;
            this.updateInfo('Selection contains booked dates. Please try again.');
        }
    }
    
    // THIRD+ CLICK: Start new selection
    else {
        this.selectedStartDate = dateStr;
        this.selectedEndDate = null;
        this.updateInfo('Selected Feb 25 (Click another date for end date)');
    }
    
    // Redraw with new selections highlighted
    this.generate(this.currentMonth, this.currentYear);
}
```

**State Machine:**
```
START → Click date 1 → "waiting for end" → Click date 2 → "range selected" → Click date → back to START
```

---

### **E. Range Validation**

```javascript
validateRange(start, end) {  // "2026-02-15", "2026-02-20"
    
    // Loop through each day in range
    for (let d = new Date(start); d <= new Date(end); d.setDate(d.getDate() + 1)) {
        
        // Convert to string format for comparison
        const checkDate = "2026-02-15";
        
        // If this date is in booked array, entire range is invalid
        if (this.bookedDates.includes(checkDate)) {
            return false;  // ❌ Invalid
        }
    }
    return true;  // ✅ Valid
}

// Example:
// bookedDates = ["2026-02-16"]
// Range: Feb 15 - Feb 20
// Day 1: Feb 15 → Not booked ✅
// Day 2: Feb 16 → BOOKED ❌ RETURN FALSE
```

---

## Data Flow to Database

### **Step 1: Hidden Inputs Store Selection**
```html
<input type="hidden" name="start_date" id="startDateInput">
<input type="hidden" name="end_date" id="endDateInput">
```
These are hidden because users don't need to see them - calendar populates them.

### **Step 2: Form Submission**
```javascript
form.addEventListener('submit', (e) => {
    if (!CalendarWidget.validateDates()) {
        e.preventDefault();
        alert('Please select valid dates');
    }
    // Otherwise form submits to PHP
});
```

### **Step 3: PHP Receives Data**
```php
// ServiceProviderRequest.php
$start_date = $_POST['start_date'];  // "2026-02-15"
$end_date = $_POST['end_date'];      // "2026-02-20"

// Saves to database
$db->insert('service_requests', [
    'start_date' => $start_date,
    'end_date' => $end_date,
    'status' => 'pending'
]);

// Then ServiceAvailability marks these dates as booked
$db->insert('provider_availability', [
    'date' => $start_date,  // Multiple rows, one per day
    'status' => 'booked'
]);
```

---

## CSS Classes Explained

### **Color Coding**

| Class | Color | Background | When Used |
|-------|-------|-----------|-----------|
| `.calendar-day-available` | White text | Green (#28a745) | Can click |
| `.calendar-day-selected` | White text | Blue (#007bff) | Part of range |
| `.calendar-day-booked` | White text | Red (#dc3545) | Cannot click |
| `.calendar-day-past` | Light gray | Light gray (#e9ecef) | Cannot click |

### **Hover Effects**

```css
.calendar-day-available:hover {
    background: #218838;      /* Darker green */
    transform: scale(1.05);   /* Slightly larger */
}

.calendar-day-selected:hover {
    background: #0056b3;      /* Darker blue */
    transform: scale(1.05);
}
```

Users see visual feedback when hovering over clickable dates.

---

## How Booked Dates Get Into Calendar

### **Flow:**
```
1. Service provider adds availability
   → Provider availability added to database

2. User requests service with date range
   → Request saved as "pending"
   → Dates marked as "booked" in provider_availability

3. Next user opens request form
   → PHP queries: SELECT * FROM provider_availability WHERE status='booked'
   → Returns: ["2026-02-15", "2026-02-16", "2026-02-20"]
   → Passed to view as data attribute: data-booked-dates='[...]'
   → JavaScript parses and stores in CalendarWidget.bookedDates

4. Calendar renders
   → Any date in bookedDates array → RED (cannot click)
```

---

## Summary

### **What Makes This Good Code?**

✅ **Separation of Concerns**
- CSS handles styling
- JavaScript handles logic
- HTML handles structure

✅ **No Global Variables**
- Everything in CalendarWidget object
- Safe from conflicts with other scripts

✅ **Reusable**
- CalendarWidget can be used on multiple pages
- Just change the data-booked-dates attribute

✅ **Maintainable**
- Easy to find where date logic lives
- Easy to add features (e.g., drag-to-select)
- Easy to debug (state is centralized)

✅ **User Experience**
- Visual feedback (colors, hover effects)
- Clear instructions
- Prevents invalid selections
- Works without external libraries

### **Possible Future Improvements**

1. Add ability to click and drag to select date range
2. Show tooltips on booked dates with request details
3. Remember last selected date range in session
4. Add keyboard navigation (arrow keys to move between dates)
5. Mobile optimization (touch-friendly)

---

## Testing the Calendar

### **Test Case 1: Select Valid Range**
1. Open form
2. Click Feb 15 (green)
3. Click Feb 20 (green, with no red dates between)
4. Verify: Hidden inputs contain "2026-02-15" and "2026-02-20"
5. Submit form

### **Test Case 2: Reject Conflicting Range**
1. Click Feb 15 (green)
2. Click Feb 25 (if Feb 16 is red/booked)
3. Verify: Alert shows "Selection contains booked dates"
4. Selection resets

### **Test Case 3: Navigate Months**
1. Click "Next" button
2. Verify: Calendar shows next month
3. Click "Prev" button multiple times
4. Verify: Can navigate backwards

---

## Questions?

Refer to the code comments in `service_request_form.view.php` or this document!
