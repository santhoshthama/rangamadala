-- ============================================================
-- Actor Schedule & Rehearsal Queries
-- Used by the Artist Drama View to display upcoming and past
-- rehearsals, performances, and other events scheduled by the director.
-- ============================================================

-- 1. Get all upcoming rehearsals & performances for a drama (artist view)
-- Shows events that are scheduled/confirmed and dated today or in the future.
SELECT 
    s.id,
    s.event_type,
    s.event_title,
    s.event_description,
    s.scheduled_date,
    s.start_time,
    s.end_time,
    s.venue,
    s.status,
    s.notes,
    r.role_name
FROM drama_schedules s
LEFT JOIN drama_roles r ON s.role_id = r.id
WHERE s.drama_id = :drama_id
  AND s.scheduled_date >= CURDATE()
  AND s.status NOT IN ('cancelled')
  AND s.event_type IN ('rehearsal', 'performance', 'meeting')
ORDER BY s.scheduled_date ASC, s.start_time ASC;


-- 2. Get past rehearsals & performances for a drama (artist view history)
SELECT 
    s.id,
    s.event_type,
    s.event_title,
    s.event_description,
    s.scheduled_date,
    s.start_time,
    s.end_time,
    s.venue,
    s.status,
    s.notes,
    r.role_name
FROM drama_schedules s
LEFT JOIN drama_roles r ON s.role_id = r.id
WHERE s.drama_id = :drama_id
  AND (s.scheduled_date < CURDATE() OR s.status IN ('completed', 'cancelled'))
  AND s.event_type IN ('rehearsal', 'performance', 'meeting')
ORDER BY s.scheduled_date DESC, s.start_time DESC;


-- 3. Get upcoming interview schedules for a specific artist in a drama
-- (from role_applications where the artist has an interview scheduled)
SELECT 
    ra.id AS application_id,
    ra.interview_at,
    ra.interview_status,
    ra.interview_notes,
    r.role_name,
    r.id AS role_id,
    d.drama_name
FROM role_applications ra
INNER JOIN drama_roles r ON ra.role_id = r.id
INNER JOIN dramas d ON r.drama_id = d.id
WHERE ra.artist_id = :artist_id
  AND r.drama_id = :drama_id
  AND ra.interview_at IS NOT NULL
  AND ra.interview_at >= NOW()
ORDER BY ra.interview_at ASC;


-- 4. Get ALL upcoming schedules for an artist across all their assigned dramas
-- Useful for the artist dashboard overview
SELECT 
    s.id,
    s.event_type,
    s.event_title,
    s.event_description,
    s.scheduled_date,
    s.start_time,
    s.end_time,
    s.venue,
    s.status,
    s.notes,
    d.drama_name,
    d.id AS drama_id,
    r2.role_name AS my_role_name
FROM drama_schedules s
INNER JOIN dramas d ON s.drama_id = d.id
INNER JOIN drama_roles r2 ON r2.drama_id = d.id AND r2.assigned_artist_id = :artist_id
WHERE s.scheduled_date >= CURDATE()
  AND s.status NOT IN ('cancelled')
  AND s.event_type IN ('rehearsal', 'performance', 'meeting')
ORDER BY s.scheduled_date ASC, s.start_time ASC;


-- 5. Count upcoming events by type for an artist in a specific drama
SELECT 
    SUM(CASE WHEN s.event_type = 'rehearsal' THEN 1 ELSE 0 END) AS upcoming_rehearsals,
    SUM(CASE WHEN s.event_type = 'performance' THEN 1 ELSE 0 END) AS upcoming_performances,
    SUM(CASE WHEN s.event_type = 'meeting' THEN 1 ELSE 0 END) AS upcoming_meetings,
    COUNT(*) AS total_upcoming
FROM drama_schedules s
WHERE s.drama_id = :drama_id
  AND s.scheduled_date >= CURDATE()
  AND s.status NOT IN ('cancelled')
  AND s.event_type IN ('rehearsal', 'performance', 'meeting');
