# Artist Calendar Guide

## Overview

The Artist Calendar provides a unified schedule view for an artist across all dramas where they are actively assigned.

Implemented features:
- Unified event feed from `drama_schedules` across assigned dramas
- Day / Week / Month UI views
- Filter by drama and date range
- Near real-time synchronization using short polling (45s)
- Cross-drama conflict blocking during director schedule create/update

## Key MVC Components

### Models
- `app/models/M_artist_calendar.php`
  - `getArtistDramaOptions(int $artistId)`
  - `getArtistCalendarEvents(int $artistId, array $filters = [])`
  - `findConflictsForArtists(array $artistIds, string $date, string $startTime, string $endTime, ?int $excludeEventId = null)`

### Controllers
- `app/controllers/Artistdashboard.php`
  - `calendar()` renders artist calendar page
  - `calendar_feed()` returns JSON events for polling and filtering
- `app/controllers/director/DirectorScheduleController.php`
  - `create_schedule()` and `update_schedule()` now check cross-drama artist conflicts
  - helper methods:
    - `resolveImpactedArtists(int $dramaId, ?int $roleId = null)`
    - `buildArtistConflictMessage(array $conflicts, string $date)`

### Views / Frontend
- `app/views/artist/calendar.view.php`
- `public/assets/JS/artist-calendar.js`
- `public/assets/CSS/artist-calendar.css`

Navigation link added in:
- `app/views/artistdashboard.view.php` (sidebar -> Artist Calendar)

## Conflict Algorithm

When director schedules/updates an event:
1. Resolve impacted artists:
   - if `role_id` is set, use active assignments for that role
   - otherwise include all active role-assigned artists in that drama
2. Query existing events for those artists on the same date
3. Detect overlap by interval rule:

`existing.start_time < new.end_time AND existing.end_time > new.start_time`

4. If any conflict exists, block save and show clear error message.

## Sync Strategy

- Artist page polls `/artistdashboard/calendar_feed` every 45 seconds.
- Filter or view navigation triggers immediate refresh.
- This keeps Artist Calendar synchronized with Drama Calendar changes without websocket infrastructure.

## Notes

- Current scope uses `drama_schedules` as the event source.
- Conflict policy is strict blocking (no override path).
- Access scope is artist self-calendar only.
