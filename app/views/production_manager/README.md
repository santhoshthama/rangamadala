# Production Manager UI - Implementation Guide

## Overview

The Production Manager UI is a complete suite of views and JavaScript controllers for managing drama services, budgets, and theater bookings. The Production Manager role is a **context-based role** assigned by Directors to specific artists for managing a particular drama.

## File Structure

```
views/production_manager/
├── dashboard.php              # Main PM dashboard
├── manage_budget.php          # Budget management interface
├── manage_services.php        # Service request management
└── book_theater.php           # Theater booking interface

assets/js/
├── production-manager-dashboard.js   # Dashboard navigation logic
├── manage-budget.js           # Budget operations
├── manage-services.js         # Service request operations
└── manage-theater.js          # Theater booking operations
```

## Features & Pages

### 1. Dashboard (`dashboard.php`)
**Purpose**: Quick overview of production manager responsibilities

**Key Components**:
- **Budget Overview**: Total allocated, spent, and remaining balance
- **Service Requests**: Recent service requests with status indicators
- **Theater Bookings**: Upcoming theater bookings calendar
- **Quick Action Buttons**: Navigate to major features

**Stats Displayed**:
- Total Budget Allocated (LKR)
- Budget Used with percentage
- Number of Active Service Requests
- Number of Theater Bookings

**User Actions**:
- Click "Manage Budget" → navigate to budget management
- Click "Manage Services" → navigate to service management
- Click "Book Theater" → navigate to theater booking
- Click "View Schedule" → opens shared schedule with director

---

### 2. Budget Management (`manage_budget.php`)
**Purpose**: Full control over drama budget tracking and management

**Key Components**:

#### Budget Summary Cards
- Total Allocated Budget
- Total Spent (with percentage)
- Remaining Balance
- Total Budget Items Count

#### Budget Breakdown
- Category-based visualization (Venue, Technical, Costumes, etc.)
- Pie chart showing budget distribution
- Category-wise breakdown with percentages

#### Budget Items Table
Columns:
- **Item Name**: Description of budget item
- **Category**: Venue Rental, Technical Services, Costumes & Makeup, Marketing, Other
- **Amount**: Item cost in LKR
- **Status**: Paid, Pending Payment, Partial Payment
- **Actions**: Edit, Delete buttons

**User Actions**:
- **Add Budget Item**: Opens modal form with fields:
  - Item Name (text)
  - Category (dropdown)
  - Amount (number)
  - Payment Status (dropdown)
  - Notes (textarea)
- **Edit Item**: Modify existing budget items
- **Delete Item**: Remove budget items (with confirmation)
- **Export Report**: Generate PDF/CSV budget report

---

### 3. Service Management (`manage_services.php`)
**Purpose**: Request, track, and manage services from service providers

**Key Components**:

#### Service Stats
- Total Services Requested
- Confirmed Services
- Pending Responses
- Estimated Service Costs

#### Filter Tabs
- All Services
- Confirmed
- Pending
- Rejected

#### Service Request Cards
Each card displays:
- **Service Type** with icon
- **Service Provider Name**
- **Estimated Cost**
- **Service Date**
- **Current Status**: Confirmed, Awaiting Response, Rejected
- **Action Buttons**: View Details, Cancel

**Available Service Types**:
1. **Sound & Audio**
   - Sri Lanka Sound Services (LKR 120,000)
   - Colombo Audio Solutions (LKR 100,000)
   - Professional Audio Colombo (LKR 150,000)

2. **Lighting & Effects**
   - Colombo Lighting Studio (LKR 150,000)
   - Professional Lighting Services (LKR 180,000)
   - Stage Effects Colombo (LKR 120,000)

3. **Makeup & Costume**
   - Elite Makeup Artistry (LKR 200,000)
   - Professional Makeup Studio (LKR 180,000)
   - Colombo Beauty Services (LKR 160,000)

4. **Transportation**
   - Colombo Transport Services (LKR 50,000)
   - Professional Transport Co (LKR 60,000)

5. **Catering**
   - Colombo Catering Services (LKR 80,000)
   - Elite Events Catering (LKR 100,000)

**User Actions**:
- **Request Service**: Opens modal with:
  - Service Type (dropdown) - auto-populates providers
  - Service Provider (dropdown) - shows ratings
  - Service Date (date picker)
  - Description (textarea)
  - Estimated Budget (number)
  - Special Requirements (textarea)
- **View Details**: Shows full service information in modal
- **Cancel Service**: Remove service request (with confirmation)

---

### 4. Theater Booking (`book_theater.php`)
**Purpose**: Search, compare, and book theaters for drama performances

**Key Components**:

#### Theater Stats
- Total Bookings
- Confirmed Bookings
- Pending Confirmations
- Total Theater Cost

#### Theater Bookings List
Each booking card shows:
- **Theater Name** with description
- **Location**
- **Booking Date** (📅 format)
- **Time** (🕐 start - end)
- **Capacity** (👥 number of seats)
- **Booking Cost** (LKR)
- **Facilities** (✓ list)
- **Status**: Confirmed (green), Pending (orange)
- **Action Buttons**: View Details, Edit, Cancel

#### Available Theaters

| Theater | Location | Capacity | Cost/Hour | Facilities |
|---------|----------|----------|-----------|-----------|
| Elphinstone Theatre | Colombo | 500 | LKR 50,000 | Full A/C, Sound, Lighting |
| Colombo Auditorium | City Center | 1000 | LKR 60,000 | Premium amenities, State-of-art |
| Galle Face Hotel Theatre | Beachfront | 300 | LKR 80,000 | Heritage, Unique ambiance |
| Kandy Arts Centre | Kandy | 400 | LKR 40,000 | Modern, Good acoustics |
| Peradeniya Open Air | Kandy | 2000 | LKR 30,000 | Large outdoor, Weather protected |

**User Actions**:
- **Book Theater**: Opens modal with:
  - Theater Selection (dropdown) - auto-updates details
  - Performance Date (date picker)
  - Performance Time (time picker)
  - End Time (time picker)
  - Estimated Attendance (number) - validates against capacity
  - Special Requests (textarea)
  - Theater Details Display (auto-updated):
    - Capacity
    - Cost per Hour
    - Estimated Total (auto-calculated)
    - Facilities
- **View Details**: Shows booking details
- **Edit Booking**: Modify existing booking
- **Cancel Booking**: Remove booking (with confirmation)

---

## JavaScript Functions

### production-manager-dashboard.js
- `navigateTo(page)` - Navigate to different production manager pages
- `viewSchedule()` - Open shared schedule with director
- `loadDashboardData()` - Fetch dashboard stats from backend
- `manageBudget()` - Navigate to budget management
- `manageServices()` - Navigate to service management
- `bookTheater()` - Navigate to theater booking

### manage-budget.js
- `openAddBudgetModal()` - Open add budget form
- `closeBudgetModal()` - Close modal
- `saveBudgetItem()` - Save new/edited budget item
- `editBudgetItem(itemId)` - Load item for editing
- `deleteBudgetItem(itemId)` - Remove budget item
- `loadBudgetItems()` - Fetch items from backend
- `exportBudgetReport()` - Generate downloadable report
- `clearBudgetForm()` - Reset form fields

### manage-services.js
- `openRequestServiceModal()` - Open service request form
- `closeRequestServiceModal()` - Close modal
- `updateServiceProviders()` - Populate providers based on type
- `submitServiceRequest()` - Submit new service request
- `filterServices(status)` - Filter by status (all, confirmed, pending, rejected)
- `viewServiceDetails(serviceId)` - Display service details
- `cancelService(serviceId)` - Cancel service request
- `loadServices()` - Fetch services from backend

### manage-theater.js
- `openBookTheaterModal()` - Open theater booking form
- `closeBookTheaterModal()` - Close modal
- `updateTheaterDetails()` - Load theater info when selected
- `calculateEstimatedCost()` - Auto-calculate total booking cost
- `submitTheaterBooking()` - Submit booking request
- `viewBookingDetails(bookingId)` - Display booking details
- `editBooking(bookingId)` - Load booking for editing
- `cancelBooking(bookingId)` - Cancel booking
- `loadTheaterBookings()` - Fetch bookings from backend

---

## Integration with Backend

All JavaScript files contain `TODO` comments for backend API integration. Follow this pattern:

```javascript
// Fetch data
fetch(`../../controllers/ProductionManagerController.php?action=getBudgetItems&drama_id=${dramaId}`)
    .then(response => response.json())
    .then(data => {
        // Process and display data
    })
    .catch(error => console.error('Error:', error));
```

---

## Design & Styling

All views use the unified **ui-theme.css**:
- Color scheme: Gold brand color (--brand: #ba8e23)
- Responsive grid layouts
- Card-based design with shadows
- Status badges: Confirmed (green), Pending (orange), Rejected (red)
- Modal dialogs for forms
- Interactive tables with action buttons

---

## Key Features

✅ **Budget Tracking**
- Add/Edit/Delete budget items
- Track payment status
- View budget breakdown by category
- Export reports

✅ **Service Management**
- Request services from various providers
- View provider ratings and costs
- Track service request status
- Cancel services

✅ **Theater Booking**
- Search available theaters
- View theater details (capacity, facilities, cost)
- Calculate booking cost automatically
- Manage multiple bookings
- Edit/Cancel bookings

✅ **Dashboard Overview**
- Quick statistics
- Recent activities
- Quick navigation
- Budget at-a-glance

---

## Database Tables (Backend Reference)

Required tables for full functionality:

```sql
-- Budget items
CREATE TABLE budgets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    drama_id INT,
    item_name VARCHAR(255),
    category VARCHAR(100),
    amount DECIMAL(10, 2),
    paid_status ENUM('pending', 'paid', 'partial'),
    added_by_manager_id INT,
    payment_date DATE,
    notes TEXT
);

-- Service bookings
CREATE TABLE service_bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_id INT,
    drama_id INT,
    booked_by_manager_id INT,
    booking_date DATE,
    status ENUM('pending', 'confirmed', 'rejected'),
    amount DECIMAL(10, 2)
);

-- Theater bookings
CREATE TABLE theater_bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    theater_id INT,
    drama_id INT,
    booking_date DATE,
    start_time TIME,
    end_time TIME,
    booked_by_manager_id INT,
    status ENUM('pending', 'confirmed', 'cancelled'),
    amount DECIMAL(10, 2)
);
```

---

## Testing Checklist

- [ ] Dashboard loads with correct drama_id
- [ ] Budget items can be added/edited/deleted
- [ ] Service providers filter by service type
- [ ] Theater cost calculates correctly based on hours
- [ ] All modals open and close properly
- [ ] Confirmations appear before destructive actions
- [ ] All navigation links work correctly
- [ ] Responsive design works on mobile/tablet
- [ ] Console shows proper debug logs

---

## Future Enhancements

- Real-time notifications for service confirmations
- Payment gateway integration
- Invoice generation
- Service provider ratings and reviews
- Calendar view integration with shared schedule
- Document upload for contracts/quotes
- Expense tracking and reconciliation
