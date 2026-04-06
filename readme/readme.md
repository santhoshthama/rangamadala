# Rangamadala – Drama Connectivity Platform

## Purpose of this README

This README is **intentionally written to guide GitHub Copilot** (and developers) when implementing the **frontend and backend** of the Rangamadala project.

👉 You can:
- Paste this README into your GitHub repository
- Let **Copilot read it** to generate cleaner, more accurate frontend & backend code
- Use it as a **single source of truth** for architecture, features, and coding expectations

---

## Project Overview

**Rangamadala** is a web-based drama connectivity platform designed to modernize and centralize stage drama production management in Sri Lanka.

It connects:
- 🎭 Artists
- 🎬 Directors
- 🧑‍💼 Production Managers
- 🛠️ Service Providers (theaters, lighting, makeup, sound)
- 👥 Audience
- 🛡️ Admins

The system improves talent discovery, production coordination, service booking, and audience engagement.

---

## Tech Stack (Strict – Academic Constraint)

> ⚠️ **Do NOT use frameworks** (Laravel, React, Vue, etc.)

### Frontend
- HTML5
- CSS3
- Vanilla JavaScript

### Backend
- PHP (procedural or MVC-style, no frameworks)

### Database
- MySQL

### Other
- Apache Web Server
- Git & GitHub
- Figma / Draw.io (design)

---

## Recommended Project Structure

Copilot should follow this structure when generating code:

```
rangamadala/
│
├── public/
│   ├── index.php
│   ├── login.php
│   ├── register.php
│   └── (other entry point files)
│
├── assets/ (shared resources - not in public folder)
│   ├── js/
│   │   ├── director-dashboard.js
│   │   ├── drama-details.js
│   │   ├── manage-roles.js
│   │   ├── assign-managers.js
│   │   ├── schedule-management.js
│   │   ├── view-services-budget.js
│   │   ├── search-artists.js
│   │   └── (other JS files)
│   ├── images/
│   │   └── default-avatar.jpg
│   └── (CSS files if needed)
│
├── ui-theme.css (root level - unified theme)
│
├── config/
│   └── database.php
│
├── controllers/
│   ├── AuthController.php
│   ├── AdminController.php
│   ├── ArtistController.php
│   ├── DramaController.php (handles director & PM contexts)
│   ├── ServiceProviderController.php
│   └── AudienceController.php
│
├── models/
│   ├── User.php
│   ├── Drama.php
│   ├── Role.php
│   ├── DramaManager.php (production managers)
│   ├── Booking.php
│   ├── Service.php
│   ├── Budget.php
│   ├── Schedule.php
│   └── Class.php
│
├── views/
│   ├── admin/
│   ├── artist/
│   │   ├── profile.php (shows categorized dramas)
│   │   ├── create_drama.php
│   │   └── portfolio.php
│   ├── director/ (drama-specific views with drama_id parameter)
│   │   ├── dashboard.php?drama_id=1
│   │   ├── drama_details.php?drama_id=1
│   │   ├── manage_roles.php?drama_id=1
│   │   ├── assign_managers.php?drama_id=1
│   │   ├── schedule_management.php?drama_id=1 (includes interactive calendar)
│   │   ├── view_services_budget.php?drama_id=1
│   │   └── search_artists.php?drama_id=1
│   ├── production_manager/ (drama-specific views with drama_id parameter)
│   │   ├── dashboard.php?drama_id=1
│   │   ├── manage_services.php?drama_id=1
│   │   ├── manage_budget.php?drama_id=1
│   │   └── book_theater.php?drama_id=1
│   ├── service_provider/
│   └── audience/
│
├── middleware/
│   ├── auth.php
│   └── drama_access_check.php (verify user has access to drama)
│
├── uploads/
│   ├── certificates/
│   ├── portfolios/
│   └── class_materials/
│
├── ui-theme.css (root level - unified theme)
└── README.md
```

---

## User Roles & Responsibilities (For Copilot Context)

> 🔁 **Updated Role Model**: Every **Director** and **Production Manager** is originally an **Artist**.
> - An **Artist creates a drama** → becomes the **Director** of that drama
> - The **Director assigns one or more Production Managers**
> - Production Managers manage **services and drama budget**

### UI Navigation Flow

**Artist Login → Artist Profile → View Dramas (Categorized)**

The Artist Profile shows dramas in three categories:
1. **As Actor** - Dramas where they are cast in roles
2. **As Director** - Dramas they created and are directing
3. **As Production Manager** - Dramas they manage for other directors

When clicking a drama they're directing → Opens **Drama-Specific Director Dashboard**

### Admin

* Approve / reject user registrations
* Verify drama certificates
* Manage users & permissions

### Artist (Base Role - Primary User)

* Register and maintain verified profile
* Upload portfolio (images, videos)
* View their dramas categorized by role (Actor/Director/Production Manager)
* **Create dramas** (becomes Director of created dramas)
* Apply for roles in other dramas
* Accept / reject role requests from directors
* Create training classes
* Enroll in classes

### Director (Context - Not a Separate Role)

**Important**: Director is NOT a separate role. It's a **context** when an Artist accesses a drama they created.

**Access**: Artist → My Dramas → Select Drama (as Director) → Director Dashboard for that specific drama

**Capabilities per Drama**:
* View and edit drama details (title, description, genre, language, certificate)
* Upload/manage Public Performance Board certificate
* **Assign ONE Production Manager** (assign artist as manager for this drama)
* **Manage Artist Roles**:
  - Create roles (Lead Actor, Villain, Supporting Actor, etc.)
  - View and manage role applications
  - **Send role requests** to artists (artist must accept)
  - Assign artists to roles after acceptance
  - Remove artists from roles
* **Search Artists** with advanced filters (experience, specialization, availability, rating)
* **View Services** (read-only) requested by Production Manager
* **View Drama Budget** (read-only) managed by Production Manager
* **Manage Schedule** (interactive calendar):
  - Schedule interviews for role candidates
  - Schedule rehearsals with participants
  - Schedule production meetings
  - View calendar with color-coded events
  - Navigate months and view all events
  - Export schedule to Google Calendar
  - Print schedule

### Production Manager (Context - Not a Separate Role)

**Important**: Production Manager is NOT a separate role. It's a **context** when an Artist is assigned by a Director to manage a specific drama.

**Note**: Each drama has **ONE** Production Manager (not multiple).

**Access**: Artist → My Dramas → Select Drama (as Production Manager) → Production Manager Dashboard for that drama

**Capabilities per Drama**:
* Book theaters for performances and rehearsals
* Request and manage services (lighting, sound, makeup, costumes, etc.)
* Coordinate with service providers
* **Manage drama budget** (full edit access):
  - Add budget items by category
  - Track expenses and payments
  - Update payment status
  - Handle service-related payments
  - View budget breakdown and statistics
* View drama schedule (shared with Director)
* Manage theater bookings with dates and times

### Service Provider

* Create service profile
* Set availability
* Accept / reject service requests
* Set pricing

### Audience

* View dramas & showtimes
* Book shows
* Rate & review dramas
* Enroll in classes

---

## Core Features (Implementation Guide)

### Authentication & Authorization

* PHP sessions
* Password hashing (`password_hash`, `password_verify`)
* Role-based access control
* Artist is the base user role

### Drama Creation & Verification

* Only **Artists** can create dramas
* Artist who creates a drama becomes its **Director**
* Drama creation requires **Public Performance Board Certificate upload**
* Admin must approve before drama becomes active
* Each drama has **ONE Production Manager** assigned by the Director

### Role Management

* Director can:
  * Create roles for the drama
  * Send role requests to specific artists
  * Review and accept/reject applications from artists
  * Assign artists to roles after acceptance
  * Remove artists from roles
* Artists can:
  * Apply for open roles
  * Accept or reject role requests from directors

### Scheduling System

* **Interactive Calendar View**:
  * Visual month-by-month calendar grid
  * Color-coded events (rehearsals, interviews, meetings)
  * Click dates to add events
  * Click events to view details
  * Navigate between months
  * Today highlighting
* Calendar-based rehearsal & interview scheduling
* Conflict checking (date/time overlaps)
* Participant management for scheduled events
* Export to Google Calendar
* Print schedule functionality

### Booking & Service Management

* Production Manager handles:
  * Theater booking
  * Service provider booking
* Accept / reject workflow

### Drama Budget Management

* Production Manager can:
  * Add budget items
  * Track service expenses
  * Update payment status

### Notification System

* In-app notifications (database table)
* Optional email notifications

### Payments (Basic)

* Simulated payment workflow
* No card data stored
* Payment status tracking only

---

## Database Design Guidelines

Copilot should assume tables like:

* **users** (id, role=artist|admin|audience, name, email, password, status, phone, location, experience_years, specialization, portfolio_path)
* **dramas** (id, creator_artist_id, title, description, genre, language, certificate_path, status=pending|active|completed, created_at)
* **drama_managers** (id, drama_id, artist_id, responsibilities, assigned_date, status=active|removed) - **NOTE: One manager per drama**
* **roles** (id, drama_id, role_name, role_description, salary, status=vacant|filled|published, requirements)
* **role_assignments** (id, role_id, artist_id, status=requested|accepted|rejected, requested_by=director|artist, request_date, response_date)
* **applications** (id, artist_id, role_id, application_message, status=pending|accepted|rejected, applied_date)
* **services** (id, provider_id, service_type, description, price_range, availability_status)
* **service_bookings** (id, service_id, drama_id, booked_by_manager_id, booking_date, status=pending|confirmed|completed, amount)
* **budgets** (id, drama_id, item_name, category, amount, paid_status=pending|paid, added_by_manager_id, payment_date, notes)
* **schedules** (id, drama_id, event_type=rehearsal|interview|meeting|performance, title, date, start_time, end_time, venue, description, created_by, status=confirmed|pending|cancelled)
* **schedule_participants** (id, schedule_id, artist_id, attendance_status)
* **theaters** (id, name, location, capacity, facilities, contact)
* **theater_bookings** (id, theater_id, drama_id, booking_date, start_time, end_time, booked_by_manager_id, status, amount)
* **classes** (id, artist_id, title, description, max_participants, schedule, status)
* **enrollments** (user_id, class_id, enrollment_date, status)
* **notifications** (id, user_id, message, type, is_read, created_at, related_drama_id)

### Key Relationships:
- Artist creates Drama (users.id → dramas.creator_artist_id)
- **One Production Manager per Drama** (drama_managers.artist_id → users.id, unique constraint on drama_id)
- Roles belong to Dramas (roles.drama_id → dramas.id)
- Role requests can be initiated by Director OR Artist
- Services booked by Production Manager for specific dramas
- Budget managed per drama by Production Manager
- Schedules are drama-specific with color-coded event types
- All Director/PM views use drama_id parameter for context

---

## Coding Standards (Important for Copilot)

- Separate **logic**, **views**, and **database access**
- Use reusable PHP functions
- Validate all inputs (server-side)
- Use prepared statements (PDO or MySQLi)
- Clean, readable variable names
- **All Director/Production Manager views must include drama_id parameter**
- JavaScript files stored in `assets/js/` folder, not in `views/`
- Use vanilla JavaScript (no jQuery or frameworks)
- All buttons must have onclick handlers for navigation
- Interactive elements should provide user feedback

---

## Frontend Guidelines

- Simple, clean UI (non-technical users)
- Form validation using JavaScript
- Use semantic HTML
- Responsive layout (basic CSS)
- **Unified theme** using `ui-theme.css` at root level
- **Drama-specific navigation** with drama_id parameter in URLs
- **Interactive elements**:
  * Buttons with onclick handlers
  * Tab navigation with active states
  * Modal dialogs for actions
  * Interactive calendar for scheduling
  * Color-coded visual indicators

---

## How to Use This README with Copilot

When coding:

✅ Ask Copilot like this:
> "Create a PHP controller for Artist role based on README.md"

> "Generate MySQL table for drama booking system described in README"

> "Create HTML + CSS dashboard for Director role from README"

Copilot will follow this document as **project context**.

---

## Project Status

🎓 University of Colombo School of Computing
📘 SCS2202 / IS2102 – Group Project I
👥 Group 42 CS

---

## License

Academic use only.

