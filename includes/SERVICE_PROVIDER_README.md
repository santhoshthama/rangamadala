# Service Provider Module - Rangamadala

## Overview

The Service Provider module is a comprehensive platform within Rangamadala that connects professional drama production service providers with production managers, directors, and drama teams. It enables service providers to showcase their expertise, manage their availability, receive service requests, and handle business operations efficiently.

---

## Table of Contents

1. [Service Provider Types](#service-provider-types)
2. [Key Features](#key-features)
3. [Registration & Onboarding](#registration--onboarding)
4. [Dashboard & Analytics](#dashboard--analytics)
5. [Profile Management](#profile-management)
6. [Availability Management](#availability-management)
7. [Service Request Management](#service-request-management)
8. [Payment Tracking](#payment-tracking)
9. [Reporting System](#reporting-system)
10. [Public Profile & Discovery](#public-profile--discovery)
11. [User Workflows](#user-workflows)

---

## Service Provider Types

The platform supports various drama production service providers:

- **Theater Production** - Complete theater rental and production services
- **Lighting Design** - Professional lighting equipment and design
- **Sound Systems** - Audio equipment and sound engineering
- **Video Production** - Recording and live streaming services
- **Set Design** - Stage design and construction
- **Costume Design** - Costume creation and rental
- **Makeup & Hair** - Professional makeup and hair styling
- **Other** - Additional specialized services

---

## Key Features

### 1. **Multi-Step Registration Process**
- Personal information collection
- Business details and certification
- Service offerings with detailed specifications
- Portfolio/past projects showcase
- Professional summary and social media links

### 2. **Professional Dashboard**
- Real-time metrics and KPIs
- Service request overview
- Revenue tracking
- Recent activity feed
- Top clients showcase
- Performance charts and analytics

### 3. **Comprehensive Profile Management**
- Profile image upload and management
- Basic information editing
- Service portfolio management (add/edit/delete services)
- Project history management
- Availability status toggle
- Password management
- Quick statistics display

### 4. **Smart Availability Calendar**
- Interactive monthly calendar view
- Mark dates as available/booked/unavailable
- Add descriptions for availability periods
- Visual status indicators (color-coded)
- Quick date selection and management
- Future booking management

### 5. **Service Request Handling**
- Categorized request views (All, Pending, Accepted, Completed, Rejected)
- Detailed request information display
- Accept/Reject functionality
- Request status tracking
- Client communication details
- Service-specific requirement fields

### 6. **Payment Management**
- Pending payments overview
- Received payments tracking
- Payment filtering by status
- Payment history
- Invoice details

### 7. **Business Intelligence & Reporting**
- Quick report templates
- Custom report generator
- Revenue reports
- Service distribution analysis
- Client activity reports
- Performance metrics
- Date range filtering
- Export capabilities

### 8. **Public Discovery & Booking**
- Searchable provider directory
- Advanced filtering (service type, location, rate range, availability)
- Detailed public profile pages
- Service showcase with rates
- Portfolio display
- Direct service request submission
- Contact information display

---

## Registration & Onboarding

### Registration Steps

**Step 1: Account Information**
- Full name
- Email address
- Password creation
- Phone number

**Step 2: Business Details**
- Business name
- Business registration number
- Years of experience
- Professional summary
- Business certificate upload (PDF)
- NIC/identification upload
- Social media links

**Step 3: Services & Rates**
Service providers can offer multiple services with specific details:

#### Theater Production
- Theater name
- Location
- Seating capacity
- Stage dimensions
- Available facilities
- Hourly/daily rates
- Photo gallery

#### Lighting Design
- Equipment inventory
- Lighting types available
- Setup time requirements
- Technical specifications
- Service rates
- Portfolio images

#### Sound Systems
- Equipment list
- System capacity
- Coverage area
- Technical specifications
- Hourly rates
- Sample work

#### Video Production
- Camera equipment
- Recording formats
- Editing capabilities
- Streaming services
- Rates structure

#### Set Design
- Design styles
- Materials expertise
- Past project examples
- Service rates

#### Costume Design
- Design specialties
- Rental availability
- Custom design services
- Price ranges

#### Makeup & Hair
- Service types
- Special effects capability
- Rates per artist/session

**Step 4: Portfolio & Projects**
- Project year
- Project name
- Services provided
- Description
- Achievements

---

## Dashboard & Analytics

### Overview Metrics
- **Total Bookings** - Complete booking history
- **Active Projects** - Currently running engagements
- **Total Revenue** - Lifetime earnings
- **Pending Requests** - Service requests awaiting action

### Visual Analytics
- **Revenue Chart** - Monthly revenue trends with bar graphs
- **Service Distribution** - Breakdown of services by percentage
- **Recent Activity** - Latest bookings, completions, and payments
- **Top Clients** - Most frequent collaborators

### Quick Actions
- View all requests
- Check availability calendar
- Generate reports
- Update profile

---

## Profile Management

### Profile Sections

#### 1. Header Section
- Profile image (upload/update capability)
- Name and service type
- Availability status toggle
- Contact information display

#### 2. Basic Information
- Full name (read-only)
- Email (read-only)
- Phone number (editable)
- Business name
- Business registration number
- Location
- Years of experience
- Professional summary
- Edit functionality

#### 3. Availability Status
- Global availability toggle
- Visual on/off indicator
- Quick status updates
- Links to detailed calendar

#### 4. Password Management
- Current password verification
- New password setting
- Password confirmation
- Secure password change

#### 5. Services & Rates
- List of all offered services
- Service type and description
- Rate information
- Edit individual services
- Delete services
- Add new services
- View full service details

#### 6. Recent Projects
- Project timeline
- Project details
- Services provided
- Edit/delete functionality
- Add new projects

#### 7. Quick Statistics
- Total services offered
- Projects completed
- Years of experience
- Average rating (if applicable)

---

## Availability Management

### Calendar Features

#### Visual Calendar Interface
- Month/year navigation
- Current date highlighting
- Color-coded date statuses:
  - **Green** - Available
  - **Blue** - Booked
  - **Red** - Unavailable
  - **Grey** - Past dates

#### Date Management
- Click any future date to manage availability
- Add availability with description
- Mark dates as unavailable
- View date details
- Edit existing availability
- Remove availability entries

#### Availability Data Structure
```javascript
{
  "YYYY-MM-DD": {
    "status": "available|booked|unavailable",
    "description": "Detailed notes about this date"
  }
}
```

#### Legend
- Visual guide for calendar status colors
- Quick reference for date meanings
- User-friendly interface

### Use Cases
- Block out personal time
- Mark availability for bookings
- Add notes about specific dates
- Track booking confirmations
- Manage seasonal availability

---

## Service Request Management

### Request Lifecycle

1. **Pending** - New requests awaiting provider action
2. **Accepted** - Provider has accepted the request
3. **Completed** - Service has been delivered
4. **Rejected** - Provider declined the request
5. **Cancelled** - Client cancelled the request

### Request Information

Each request contains:
- **Client Details**
  - Production manager name
  - Contact email
  - Phone number
  
- **Service Details**
  - Requested service type
  - Event date
  - Duration
  - Budget range
  
- **Service-Specific Requirements**
  - Theater: Seating needs, date range, facilities
  - Lighting: Setup type, venue size, special requirements
  - Sound: Event type, venue capacity, equipment needs
  - Video: Recording type, duration, delivery format
  - Set Design: Production type, theme, dimensions
  - Costume: Number of costumes, style, rental/custom
  - Makeup: Artist count, session length, special effects
  
- **Additional Information**
  - Special requirements
  - Budget details
  - Timeline expectations
  - Project description

### Request Actions

#### Accept Request
- Confirms provider availability
- Updates request status to "Accepted"
- Notifies client
- Moves to active projects

#### Reject Request
- Requires rejection reason
- Updates status to "Rejected"
- Notifies client
- Archived in rejected list

#### Mark as Completed
- Confirms service delivery
- Updates status to "Completed"
- Triggers payment processing
- Adds to portfolio (optional)

#### Update Payment
- Mark payment as received
- Track payment status
- Update financial records

### Request Filtering
- View all requests
- Filter by status
- Search functionality
- Date-based sorting

---

## Payment Tracking

### Payment Dashboard

#### Summary Cards
- **Pending Payments** - Total amount awaiting payment
- **Received Payments** - Total paid amount

#### Payment List
Each payment entry shows:
- Client name
- Project/service name
- Amount
- Status (Pending/Paid)
- Payment date
- Action buttons

#### Payment Actions
- View payment details
- Mark as paid
- Generate invoice
- Send payment reminder

#### Payment Filtering
- Filter by status (Pending/Received)
- View all payments
- Search by client or project

---

## Reporting System

### Quick Report Templates

1. **Revenue Report**
   - Total earnings
   - Payment breakdown
   - Revenue trends
   - Top revenue services

2. **Booking Analytics**
   - Total bookings
   - Booking sources
   - Popular services
   - Client demographics

3. **Service Performance**
   - Service utilization
   - Average ratings
   - Completion rates
   - Client feedback

4. **Client Activity**
   - New vs returning clients
   - Client retention rate
   - Top clients
   - Booking frequency

### Custom Report Generator

#### Filter Options
- **Date Range**
  - Last 7 days
  - Last 30 days
  - Last 90 days
  - This year
  - Custom date range

- **Report Type**
  - Revenue
  - Bookings
  - Services
  - Clients

- **Data Grouping**
  - By service type
  - By client
  - By date
  - By status

#### Report Actions
- Generate report
- Preview report
- Export as PDF
- Export as Excel
- Email report
- Schedule recurring reports

### Recent Reports
- Report history
- Quick access to generated reports
- Download previous reports
- Report regeneration

---

## Public Profile & Discovery

### Browse Service Providers

#### Search & Filter Features
- **Service Type Filter** - Filter by specific service categories
- **Location Filter** - Find providers by area
- **Rate Range Filter** - Search within budget (Min/Max hourly rate)
- **Availability Filter** - Show only available providers
- **Clear Filters** - Reset all filters

#### Provider Cards Display
- Profile image
- Provider name
- Primary service type
- Location
- Hourly rate
- Availability status
- Rating (if implemented)
- "View Profile" button

### Detailed Provider Profile

#### Profile Header
- Large profile image
- Full name
- Service types offered
- Location
- Contact information
- Years of experience
- Availability status
- Social media links
- "Request Service" button

#### About Section
- Professional summary
- Years of experience
- Certifications
- Social media presence

#### Contact Information
- Email address
- Phone number
- Business location
- Business registration number

#### Services Offered
Detailed cards for each service showing:
- Service name and icon
- Rate information
- Description
- Service-specific details (capacity, equipment, etc.)
- Photo gallery (for applicable services)
- "Request This Service" button

#### Recent Projects Portfolio
- Project year
- Project name
- Services provided
- Description
- Achievements

### Service Request Submission

#### Request Form (Dynamic based on service)
- Pre-filled provider information
- Client contact details
- Service selection
- Event details (date, duration, location)
- Budget range
- Service-specific fields
- Special requirements
- Additional notes
- Submit request

---

## User Workflows

### For Service Providers

#### Initial Setup
1. Register account with business details
2. Upload business certificates and identification
3. Add services with rates and details
4. Upload portfolio projects
5. Set availability status
6. Complete profile setup

#### Daily Operations
1. Check dashboard for new requests
2. Review and respond to service requests
3. Update availability calendar
4. Manage active projects
5. Track payments
6. Communicate with clients

#### Business Management
1. Generate reports for insights
2. Update service offerings
3. Adjust rates based on demand
4. Manage portfolio and projects
5. Monitor performance metrics
6. Handle payments and invoicing

### For Clients (Production Managers)

#### Finding Service Providers
1. Browse service providers directory
2. Apply filters (service type, location, rate, availability)
3. View provider profiles
4. Review services and portfolios
5. Check availability

#### Requesting Services
1. Select desired service
2. Fill out request form with details
3. Specify requirements and budget
4. Submit request
5. Wait for provider response

#### Managing Bookings
1. Receive acceptance/rejection
2. Coordinate with provider
3. Track service delivery
4. Process payments
5. Provide feedback

---

## Technical Implementation

### File Structure

```
app/
  controllers/
    ServiceProviderDashboard.php      # Dashboard controller
    ServiceProviderProfile.php        # Profile management
    ServiceProviderRegister.php       # Registration handling
    ServiceAvailability.php           # Availability calendar
    ServiceRequests.php               # Request management
    ServicePayment.php                # Payment tracking
    ServiceReports.php                # Report generation
    BrowseServiceProviders.php        # Public browsing
  
  models/
    M_service_provider.php            # Service provider data model
    M_provider_availability.php       # Availability data model
    M_service_request.php             # Request data model
  
  views/
    service_provider_dashboard.view.php
    service_provider_profile.view.php
    service_provider_register.view.php
    service_availability.view.php
    service_requests.view.php
    service_payment.view.php
    service_reports.view.php
    browse_service_providers.view.php
    service_provider_detail.view.php
    service_request_form.view.php
    service_add_service.view.php
    service_edit_service.view.php
    service_add_project.view.php
    service_edit_project.view.php
    service_edit_basic_info.view.php
    service_change_password.view.php
```

### Database Tables

- **service_providers** - Provider account and business information
- **services** - Services offered by providers
- **service_details** - Service-specific detailed information
- **provider_projects** - Portfolio and past projects
- **provider_availability** - Availability calendar data
- **service_requests** - Client service requests
- **payments** - Payment tracking and history

### Key Technologies

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP (MVC architecture)
- **Database**: MySQL
- **File Uploads**: Multipart form data handling
- **Session Management**: PHP sessions for authentication
- **Icons**: Font Awesome
- **Responsive Design**: Mobile-friendly interface

---

## Security Features

- Password hashing for account security
- Session-based authentication
- File upload validation (type, size)
- SQL injection prevention
- XSS protection
- CSRF token implementation (recommended)
- Secure file storage paths
- Input sanitization
- Access control by user role

---

## Future Enhancements

- [ ] Real-time chat between providers and clients
- [ ] Advanced booking calendar with conflicts detection
- [ ] Automated invoice generation
- [ ] Review and rating system
- [ ] Email notifications for requests and bookings
- [ ] SMS notifications
- [ ] Payment gateway integration
- [ ] Multi-language support
- [ ] Mobile application
- [ ] Advanced analytics and AI insights
- [ ] Automated availability sync with external calendars
- [ ] Contract management system
- [ ] Digital signature support
- [ ] Video portfolio uploads
- [ ] Virtual consultation scheduling

---

## Support & Documentation

For additional information or support:
- Review the main project README.md
- Check AVAILABILITY_FEATURE_GUIDE.md for availability calendar details
- Refer to database_setup.sql for database schema
- Contact system administrator for technical issues

---

## License

Part of the Rangamadala platform. All rights reserved.

---

**Last Updated**: January 2026  
**Version**: 1.0  
**Maintained By**: Rangamadala Development Team
