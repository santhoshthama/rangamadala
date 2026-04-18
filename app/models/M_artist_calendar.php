<?php

class M_artist_calendar
{
    protected $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
         * Returns all dramas where the artist currently participates as:
         * - Actor (active role assignment)
         * - Director (drama creator)
         * - Production Manager (active manager assignment)
     */
    public function getArtistDramaOptions(int $artistId): array
    {
        try {
            $this->db->query("SELECT DISTINCT d.id, d.drama_name
                                                         FROM dramas d
                                                         WHERE d.creator_artist_id = :artist_as_director
                                                                OR EXISTS (
                                                                        SELECT 1
                                                                        FROM drama_manager_assignments dma
                                                                        WHERE dma.drama_id = d.id
                                                                            AND dma.manager_artist_id = :artist_as_pm
                                                                            AND dma.status = 'active'
                                                                )
                                                                OR EXISTS (
                                                                        SELECT 1
                                                                        FROM drama_roles r
                                                                        INNER JOIN role_assignments ra ON ra.role_id = r.id
                                                                        WHERE r.drama_id = d.id
                                                                            AND ra.artist_id = :artist_as_actor
                                                                            AND ra.status = 'active'
                                                                )
                             ORDER BY d.drama_name ASC");
                        $this->db->bind(':artist_as_director', $artistId);
                        $this->db->bind(':artist_as_pm', $artistId);
                        $this->db->bind(':artist_as_actor', $artistId);
            return $this->db->resultSet() ?: [];
        } catch (Exception $e) {
            error_log('Error in M_artist_calendar::getArtistDramaOptions: ' . $e->getMessage());
            return [];
        }
    }

    /**
        * Unified calendar events across all dramas where the user participates
        * as actor/director/production manager.
     */
    public function getArtistCalendarEvents(int $artistId, array $filters = []): array
    {
        try {
            $startDate = isset($filters['start_date']) ? trim((string)$filters['start_date']) : '';
            $endDate = isset($filters['end_date']) ? trim((string)$filters['end_date']) : '';
            $dramaId = !empty($filters['drama_id']) ? (int)$filters['drama_id'] : 0;
            $participation = isset($filters['participation']) ? strtolower(trim((string)$filters['participation'])) : 'all';

            $sql = "SELECT DISTINCT
                        s.id,
                        s.drama_id,
                        d.drama_name,
                        s.event_type,
                        s.event_title,
                        s.event_description,
                        s.scheduled_date,
                        s.start_time,
                        s.end_time,
                        s.venue,
                        s.status,
                        s.role_id,
                                                r.role_name,
                                                CASE WHEN d.creator_artist_id = :artist_case_director THEN 1 ELSE 0 END AS is_director_drama,
                                                CASE WHEN EXISTS (
                                                        SELECT 1
                                                        FROM drama_manager_assignments dma_case
                                                        WHERE dma_case.drama_id = s.drama_id
                                                            AND dma_case.manager_artist_id = :artist_case_pm
                                                            AND dma_case.status = 'active'
                                                ) THEN 1 ELSE 0 END AS is_pm_drama,
                                                CASE WHEN EXISTS (
                                                        SELECT 1
                                                        FROM role_assignments ra_case
                                                        INNER JOIN drama_roles dr_case ON dr_case.id = ra_case.role_id
                                                        WHERE dr_case.drama_id = s.drama_id
                                                            AND ra_case.artist_id = :artist_case_actor
                                                            AND ra_case.status = 'active'
                                                ) THEN 1 ELSE 0 END AS is_actor_drama
                    FROM drama_schedules s
                    INNER JOIN dramas d ON d.id = s.drama_id
                    LEFT JOIN drama_roles r ON r.id = s.role_id
                    WHERE s.status != 'cancelled'
                      AND (
                                                     d.creator_artist_id = :artist_as_director
                                                     OR EXISTS (
                                                             SELECT 1
                                                             FROM drama_manager_assignments dma
                                                             WHERE dma.drama_id = s.drama_id
                                                                 AND dma.manager_artist_id = :artist_as_pm
                                                                 AND dma.status = 'active'
                                                     )
                                                     OR
                           (s.role_id IS NOT NULL AND EXISTS (
                               SELECT 1
                               FROM role_assignments ra_role
                               WHERE ra_role.role_id = s.role_id
                                 AND ra_role.artist_id = :artist_for_role
                                 AND ra_role.status = 'active'
                           ))
                           OR
                           (s.role_id IS NULL AND EXISTS (
                               SELECT 1
                               FROM role_assignments ra_drama
                               INNER JOIN drama_roles dr ON dr.id = ra_drama.role_id
                               WHERE ra_drama.artist_id = :artist_for_drama
                                 AND ra_drama.status = 'active'
                                 AND dr.drama_id = s.drama_id
                           ))
                      )";

            if ($startDate !== '') {
                $sql .= " AND s.scheduled_date >= :start_date";
            }

            if ($endDate !== '') {
                $sql .= " AND s.scheduled_date <= :end_date";
            }

            if ($dramaId > 0) {
                $sql .= " AND s.drama_id = :drama_id";
            }

            if ($participation === 'director') {
                $sql .= " AND d.creator_artist_id = :filter_director";
            } elseif ($participation === 'pm') {
                $sql .= " AND EXISTS (
                                SELECT 1
                                FROM drama_manager_assignments dma_filter
                                WHERE dma_filter.drama_id = s.drama_id
                                  AND dma_filter.manager_artist_id = :filter_pm
                                  AND dma_filter.status = 'active'
                            )";
            } elseif ($participation === 'actor') {
                $sql .= " AND EXISTS (
                                SELECT 1
                                FROM role_assignments ra_filter
                                INNER JOIN drama_roles dr_filter ON dr_filter.id = ra_filter.role_id
                                WHERE dr_filter.drama_id = s.drama_id
                                  AND ra_filter.artist_id = :filter_actor
                                  AND ra_filter.status = 'active'
                            )";
            }

            $sql .= " ORDER BY s.scheduled_date ASC, s.start_time ASC";

            $this->db->query($sql);
            $this->db->bind(':artist_case_director', $artistId);
            $this->db->bind(':artist_case_pm', $artistId);
            $this->db->bind(':artist_case_actor', $artistId);
            $this->db->bind(':artist_as_director', $artistId);
            $this->db->bind(':artist_as_pm', $artistId);
            $this->db->bind(':artist_for_role', $artistId);
            $this->db->bind(':artist_for_drama', $artistId);

            if ($startDate !== '') {
                $this->db->bind(':start_date', $startDate);
            }

            if ($endDate !== '') {
                $this->db->bind(':end_date', $endDate);
            }

            if ($dramaId > 0) {
                $this->db->bind(':drama_id', $dramaId);
            }

            if ($participation === 'director') {
                $this->db->bind(':filter_director', $artistId);
            } elseif ($participation === 'pm') {
                $this->db->bind(':filter_pm', $artistId);
            } elseif ($participation === 'actor') {
                $this->db->bind(':filter_actor', $artistId);
            }

            return $this->db->resultSet() ?: [];
        } catch (Exception $e) {
            error_log('Error in M_artist_calendar::getArtistCalendarEvents: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Returns conflicting schedule records for one or more artists on a proposed timeslot.
     */
    public function findConflictsForArtists(array $artistIds, string $date, string $startTime, string $endTime, ?int $excludeEventId = null): array
    {
        $artistIds = array_values(array_unique(array_filter(array_map('intval', $artistIds))));
        if (empty($artistIds)) {
            return [];
        }

        try {
            $inPlaceholders = [];
            foreach ($artistIds as $idx => $artistId) {
                $inPlaceholders[] = ':artist_' . $idx;
            }

            $sql = "SELECT DISTINCT
                        s.id AS schedule_id,
                        s.drama_id,
                        d.drama_name,
                        s.event_title,
                        s.event_type,
                        s.scheduled_date,
                        s.start_time,
                        s.end_time,
                        s.venue,
                        u.id AS artist_id,
                        u.full_name AS artist_name
                    FROM drama_schedules s
                    INNER JOIN dramas d ON d.id = s.drama_id
                    INNER JOIN role_assignments ra
                        ON ra.status = 'active'
                       AND ra.artist_id IN (" . implode(',', $inPlaceholders) . ")
                       AND (
                            (s.role_id IS NOT NULL AND ra.role_id = s.role_id)
                            OR
                            (s.role_id IS NULL AND EXISTS (
                                SELECT 1
                                FROM drama_roles dr_all
                                WHERE dr_all.id = ra.role_id
                                  AND dr_all.drama_id = s.drama_id
                            ))
                       )
                    INNER JOIN users u ON u.id = ra.artist_id
                    WHERE s.status != 'cancelled'
                      AND s.scheduled_date = :scheduled_date
                      AND s.start_time < :end_time
                      AND s.end_time > :start_time";

            if (!empty($excludeEventId)) {
                $sql .= " AND s.id != :exclude_event_id";
            }

            $sql .= " ORDER BY s.scheduled_date ASC, s.start_time ASC, u.full_name ASC";

            $this->db->query($sql);

            foreach ($artistIds as $idx => $artistId) {
                $this->db->bind(':artist_' . $idx, $artistId);
            }

            $this->db->bind(':scheduled_date', $date);
            $this->db->bind(':start_time', $startTime);
            $this->db->bind(':end_time', $endTime);

            if (!empty($excludeEventId)) {
                $this->db->bind(':exclude_event_id', (int)$excludeEventId);
            }

            return $this->db->resultSet() ?: [];
        } catch (Exception $e) {
            error_log('Error in M_artist_calendar::findConflictsForArtists: ' . $e->getMessage());
            return [];
        }
    }
}
