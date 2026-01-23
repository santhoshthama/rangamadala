# Production Manager Assignment System - Architecture Diagram

## System Overview

```
┌─────────────────────────────────────────────────────────────────────────┐
│                   PRODUCTION MANAGER ASSIGNMENT SYSTEM                   │
└─────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────┐                    ┌──────────────────────────┐
│   DIRECTOR WORKFLOW      │                    │   ARTIST WORKFLOW        │
└──────────────────────────┘                    └──────────────────────────┘
           │                                                 │
           ├─ View PM Status                               ├─ View Requests
           ├─ Search Artists                               ├─ Accept Request
           ├─ Send Request                                 ├─ Decline Request
           ├─ Remove PM                                    └─ Manage Dramas
           └─ Change PM
```

## Database Schema Relationships

```
┌─────────────────┐
│     dramas      │
│─────────────────│
│ id (PK)         │◄──────────┐
│ drama_name      │           │
│ certificate_#   │           │
│ creator_artist_id│◄─────┐   │
└─────────────────┘       │   │
                          │   │
┌─────────────────┐       │   │    ┌──────────────────────────┐
│     users       │       │   │    │ drama_manager_assignments│
│─────────────────│       │   └────│──────────────────────────│
│ id (PK)         │◄──────┼────────│ id (PK)                  │
│ full_name       │       │        │ drama_id (FK)            │
│ email           │       │        │ manager_artist_id (FK)   │
│ role            │       │        │ assigned_by (FK)         │
│ profile_image   │       │        │ assigned_at              │
└─────────────────┘       │        │ status (active/removed)  │
        ▲                 │        │ removed_at               │
        │                 │        └──────────────────────────┘
        │                 │                 │
        │                 │                 │ UNIQUE (drama_id, status)
        │                 │                 │ Only 1 active PM per drama
        │                 │
        │                 │        ┌──────────────────────────┐
        │                 │        │ drama_manager_requests   │
        │                 └────────│──────────────────────────│
        │                          │ id (PK)                  │
        └──────────────────────────│ drama_id (FK)            │
                                   │ artist_id (FK)           │
                                   │ director_id (FK)         │
                                   │ status (pending/accepted)│
                                   │ message                  │
                                   │ requested_at             │
                                   │ responded_at             │
                                   └──────────────────────────┘
```

## Request Workflow

```
┌─────────────────────────────────────────────────────────────────────┐
│                        PM ASSIGNMENT FLOW                            │
└─────────────────────────────────────────────────────────────────────┘

    DIRECTOR                          SYSTEM                    ARTIST
        │                                │                         │
        │  1. Click "Assign PM"          │                         │
        ├───────────────────────────────►│                         │
        │                                │                         │
        │  2. Search Artists             │                         │
        ├───────────────────────────────►│                         │
        │                                │                         │
        │  3. Send Request + Message     │                         │
        ├───────────────────────────────►│                         │
        │                                │                         │
        │                                │  4. Create Request      │
        │                                │     in database         │
        │                                │                         │
        │                                │  5. Notify Artist       │
        │                                ├────────────────────────►│
        │                                │                         │
        │                                │         6. View Request │
        │                                │◄────────────────────────┤
        │                                │                         │
        │                                │         7. Accept/Reject│
        │                                │◄────────────────────────┤
        │                                │                         │
        │  IF ACCEPTED:                  │                         │
        │  ────────────                  │                         │
        │                                │  8. Update Request      │
        │                                │     status = accepted   │
        │                                │                         │
        │                                │  9. Remove old PM       │
        │                                │     (if exists)         │
        │                                │                         │
        │                                │  10. Create Assignment  │
        │                                │      status = active    │
        │                                │                         │
        │                                │  11. Cancel other       │
        │                                │      pending requests   │
        │                                │                         │
        │  12. PM Assigned!              │                         │
        │◄───────────────────────────────┤                         │
        │                                │                         │
```

## Component Architecture

```
┌─────────────────────────────────────────────────────────────────────┐
│                           FRONTEND LAYER                             │
└─────────────────────────────────────────────────────────────────────┘

┌────────────────────────┐  ┌────────────────────────┐  ┌────────────────┐
│ assign_managers.view   │  │ search_managers.view   │  │ artistdashboard│
│                        │  │                        │  │     .view      │
├────────────────────────┤  ├────────────────────────┤  ├────────────────┤
│ • Show current PM      │  │ • Search bar           │  │ • PM requests  │
│ • Assign/Change button │  │ • Artist cards grid    │  │ • Role requests│
│ • Pending requests     │  │ • Send request modal   │  │ • Accept/Reject│
│ • Remove PM button     │  │ • Filter by name/email │  │ • Drama info   │
└────────────────────────┘  └────────────────────────┘  └────────────────┘
           │                            │                         │
           └────────────────────────────┼─────────────────────────┘
                                       │
┌─────────────────────────────────────────────────────────────────────┐
│                         CONTROLLER LAYER                             │
└─────────────────────────────────────────────────────────────────────┘

┌────────────────────────┐                    ┌────────────────────────┐
│   director.php         │                    │  Artistdashboard.php   │
├────────────────────────┤                    ├────────────────────────┤
│ • assign_managers()    │                    │ • index()              │
│ • search_managers()    │                    │ • respond_to_manager() │
│ • send_manager_request │                    │   _request()           │
│ • remove_manager()     │                    └────────────────────────┘
└────────────────────────┘
           │                                              │
           └──────────────────────┬───────────────────────┘
                                 │
┌─────────────────────────────────────────────────────────────────────┐
│                           MODEL LAYER                                │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│              M_production_manager.php                                │
├─────────────────────────────────────────────────────────────────────┤
│  CREATE                                                              │
│  • createRequest($drama_id, $artist_id, $director_id, $message)     │
│                                                                      │
│  READ                                                                │
│  • getAssignedManager($drama_id)                                    │
│  • getPendingRequestsForArtist($artist_id)                          │
│  • getRequestsByDrama($drama_id, $status)                           │
│  • getDramasByManager($artist_id)                                   │
│  • searchAvailableManagers($drama_id, $director_id, $search)        │
│  • isManagerForDrama($artist_id, $drama_id)                         │
│                                                                      │
│  UPDATE                                                              │
│  • acceptRequest($request_id, $artist_id)                           │
│  • rejectRequest($request_id, $artist_id, $note)                    │
│                                                                      │
│  DELETE                                                              │
│  • removeManager($drama_id, $director_id)                           │
└─────────────────────────────────────────────────────────────────────┘
                                 │
┌─────────────────────────────────────────────────────────────────────┐
│                         DATABASE LAYER                               │
└─────────────────────────────────────────────────────────────────────┘

┌──────────────────────────┐          ┌──────────────────────────┐
│ drama_manager_assignments│          │ drama_manager_requests   │
├──────────────────────────┤          ├──────────────────────────┤
│ Current PM tracking      │          │ PM invitation tracking   │
│ One active per drama     │          │ Request/Response flow    │
│ Automatic cleanup        │          │ Status management        │
└──────────────────────────┘          └──────────────────────────┘
```

## State Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                    PM REQUEST STATE TRANSITIONS                      │
└─────────────────────────────────────────────────────────────────────┘

                            ┌─────────┐
                            │ PENDING │ ◄─── Director sends request
                            └─────────┘
                                 │
                 ┌───────────────┼───────────────┐
                 │               │               │
                 ▼               ▼               ▼
           ┌──────────┐    ┌──────────┐   ┌───────────┐
           │ ACCEPTED │    │ REJECTED │   │ CANCELLED │
           └──────────┘    └──────────┘   └───────────┘
                 │               │               │
                 │               │               │
           Creates         Artist         New PM
           Assignment      declines       accepted
                                         (auto-cancel)


┌─────────────────────────────────────────────────────────────────────┐
│                   PM ASSIGNMENT STATE TRANSITIONS                    │
└─────────────────────────────────────────────────────────────────────┘

                            ┌────────┐
                            │ ACTIVE │ ◄─── Artist accepts request
                            └────────┘
                                 │
                                 │
                                 ▼
                            ┌─────────┐
                            │ REMOVED │ ◄─── Director removes PM
                            └─────────┘       or new PM assigned
```

## URL Routing Map

```
DIRECTOR ROUTES
├─ /director/dashboard?drama_id=X
├─ /director/assign_managers?drama_id=X ──► View PM status
├─ /director/search_managers?drama_id=X ──► Search & invite artists
│  └─ ?search=<term> ──────────────────► Filter results
├─ /director/send_manager_request ─────► POST: Send invitation
└─ /director/remove_manager ───────────► POST: Remove current PM

ARTIST ROUTES
├─ /artistdashboard ───────────────────► View all requests
│  ├─ Tab: Director ──────────────────► Dramas as director
│  ├─ Tab: Manager ───────────────────► Dramas as PM
│  ├─ Tab: Actor ─────────────────────► Dramas as actor
│  └─ Tab: Requests ──────────────────► PM + Role requests
└─ /artistdashboard/respond_to_manager_request ─► POST: Accept/Reject
```

## Data Flow Example

```
SCENARIO: Director assigns a PM to their drama

1. Director navigates to drama dashboard
   GET /director/dashboard?drama_id=1

2. Clicks "Production Manager" in sidebar
   GET /director/assign_managers?drama_id=1
   
   Controller: director->assign_managers()
   ├─ Calls authorizeDrama() → Verifies director owns drama
   ├─ Model: pmModel->getAssignedManager(1) → Returns current PM (null)
   └─ Renders: assign_managers.view.php with data

3. Clicks "Assign Production Manager" button
   GET /director/search_managers?drama_id=1
   
   Controller: director->search_managers()
   ├─ Model: pmModel->searchAvailableManagers(1, director_id, '')
   │  └─ Excludes: director, current PM
   └─ Renders: search_managers.view.php with artist list

4. Director searches for artist
   GET /director/search_managers?drama_id=1&search=John
   
   ├─ Model: pmModel->searchAvailableManagers(1, director_id, 'John')
   └─ Returns filtered results

5. Director clicks "Send Request" on artist card
   Modal opens with message field

6. Director submits request
   POST /director/send_manager_request
   Body: drama_id=1, artist_id=2, message="Would you like to be PM?"
   
   Controller: director->send_manager_request()
   ├─ Validates: artist_id != director_id
   ├─ Model: pmModel->createRequest(1, 2, director_id, message)
   │  ├─ Check for duplicate pending request
   │  └─ INSERT INTO drama_manager_requests
   └─ Redirect: /director/assign_managers?drama_id=1
      └─ Shows request in "Pending Requests" section

7. Artist logs in and views dashboard
   GET /artistdashboard
   
   Controller: Artistdashboard->index()
   ├─ Model: pmModel->getPendingRequestsForArtist(2)
   │  └─ SELECT ... WHERE artist_id=2 AND status='pending'
   └─ Renders: artistdashboard.view.php
      └─ Requests tab shows: 1 PM request + X role requests

8. Artist clicks "Accept" on PM request
   POST /artistdashboard/respond_to_manager_request
   Body: request_id=1, response=accept
   
   Controller: Artistdashboard->respond_to_manager_request()
   ├─ Model: pmModel->acceptRequest(1, 2)
   │  ├─ BEGIN TRANSACTION
   │  ├─ UPDATE drama_manager_requests SET status='accepted'
   │  ├─ UPDATE drama_manager_assignments SET status='removed'
   │  │  WHERE drama_id=1 AND status='active'
   │  ├─ INSERT INTO drama_manager_assignments
   │  │  (drama_id=1, manager_artist_id=2, status='active')
   │  ├─ UPDATE drama_manager_requests SET status='cancelled'
   │  │  WHERE drama_id=1 AND status='pending' AND id != 1
   │  └─ COMMIT
   └─ Redirect: /artistdashboard
      └─ Shows success message

9. Artist sees drama in "Manager" tab
   ├─ Model: M_drama->get_dramas_by_manager(2)
   │  └─ SELECT ... FROM drama_manager_assignments
   │     WHERE manager_artist_id=2 AND status='active'
   └─ Drama appears in manager dramas list

10. Director refreshes PM page
    GET /director/assign_managers?drama_id=1
    
    ├─ Model: pmModel->getAssignedManager(1)
    │  └─ Returns artist #2 data
    └─ Shows: PM profile card with Remove/Change buttons
```

## Security Flow

```
AUTHORIZATION CHECKS

┌────────────────────┐
│  User Request      │
└────────────────────┘
         │
         ▼
┌────────────────────┐
│ Session Check      │ ──► User logged in? (user_id, user_role)
└────────────────────┘
         │
         ▼
┌────────────────────┐
│ Role Check         │ ──► Is user an artist? (for all users)
└────────────────────┘
         │
         ▼
┌────────────────────┐
│ Ownership Check    │ ──► For directors: authorizeDrama()
└────────────────────┘     ├─ Drama exists?
         │                  ├─ User is creator_artist_id?
         │                  └─ Drama not deleted?
         ▼
┌────────────────────┐
│ Action Specific    │ ──► Additional validation:
└────────────────────┘     ├─ PM requests: artist_id != director_id
                            ├─ Accept: request belongs to artist
                            └─ Remove: drama belongs to director
```

This comprehensive diagram shows the complete architecture of the Production Manager assignment system!
