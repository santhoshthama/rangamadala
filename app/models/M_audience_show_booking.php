<?php

class M_audience_show_booking
{
    protected $db;
    protected $tableReady = false;
    protected $table = 'audience_show_bookings';
    protected $columnCache = [];

    public function __construct()
    {
        $this->db = new Database();
        $this->tableReady = $this->checkTableExists();
    }

    protected function checkTableExists()
    {
        try {
            $this->db->query("SELECT 1
                             FROM information_schema.tables
                             WHERE table_schema = DATABASE()
                               AND table_name = :table_name
                             LIMIT 1");
            $this->db->bind(':table_name', $this->table);
            $exists = (bool)$this->db->single();
            if ($exists) {
                return true;
            }

            // Fallback for environments where metadata visibility is restricted.
            $this->db->query("SELECT 1 FROM {$this->table} LIMIT 1");
            $this->db->single();
            return true;
        } catch (Exception $e) {
            error_log('Error in M_audience_show_booking::checkTableExists: ' . $e->getMessage());
            return false;
        }
    }

    protected function canUseTable()
    {
        if ($this->tableReady) {
            return true;
        }

        $this->tableReady = $this->checkTableExists();
        if (!$this->tableReady) {
            error_log('M_audience_show_booking: audience_show_bookings table not found. Run the booking migration SQL first.');
        }
        return $this->tableReady;
    }

    protected function tableHasColumn($column)
    {
        if (array_key_exists($column, $this->columnCache)) {
            return $this->columnCache[$column];
        }

        try {
            $this->db->query("SELECT 1
                             FROM information_schema.columns
                             WHERE table_schema = DATABASE()
                               AND table_name = :table_name
                               AND column_name = :column_name
                             LIMIT 1");
            $this->db->bind(':table_name', $this->table);
            $this->db->bind(':column_name', $column);
            $exists = (bool)$this->db->single();

                        if (!$exists) {
                                // Fallback for restricted information_schema visibility.
                                $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE :column_name");
                                $this->db->bind(':column_name', $column);
                                $exists = (bool)$this->db->single();
                        }

            $this->columnCache[$column] = $exists;
            return $exists;
        } catch (Exception $e) {
            error_log('Error in M_audience_show_booking::tableHasColumn: ' . $e->getMessage());
            $this->columnCache[$column] = false;
            return false;
        }
    }

    protected function hasWorkflowColumns()
    {
        $required = [
            'request_details_json',
            'rejection_reason',
            'payhere_order_id',
            'paid_at',
        ];

        foreach ($required as $column) {
            if (!$this->tableHasColumn($column)) {
                return false;
            }
        }

        return true;
    }

    public function createBooking($audienceId, $dramaId, $ticketPrice)
    {
        $result = $this->createBookingRequest($audienceId, $dramaId, $ticketPrice, []);
        return $result['success'];
    }

    public function createBookingRequest($audienceId, $dramaId, $ticketPrice, $requestDetails = [])
    {
        if (!$this->canUseTable()) {
            return ['success' => false, 'message' => 'Booking table is not available.'];
        }

        if (!$this->hasWorkflowColumns()) {
            return ['success' => false, 'message' => 'Booking request workflow columns are missing. Run the migration first.'];
        }

        try {
            $this->db->query("INSERT INTO audience_show_bookings (
                                audience_id,
                                drama_id,
                                ticket_price,
                                booking_status,
                                request_details_json,
                                rejection_reason,
                                payhere_order_id,
                                paid_at
                             ) VALUES (
                                :audience_id,
                                :drama_id,
                                :ticket_price,
                                'pending',
                                :request_details_json,
                                NULL,
                                NULL,
                                NULL
                             )");
            $this->db->bind(':audience_id', (int)$audienceId);
            $this->db->bind(':drama_id', (int)$dramaId);
            $this->db->bind(':ticket_price', number_format((float)$ticketPrice, 2, '.', ''));
            $this->db->bind(':request_details_json', json_encode($requestDetails));

            if (!$this->db->execute()) {
                return ['success' => false, 'message' => 'Unable to submit your request right now.'];
            }

            return ['success' => true, 'message' => 'Request sent to artist. Wait for approval before payment.'];
        } catch (Exception $e) {
            error_log('Error in M_audience_show_booking::createBookingRequest: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Unable to submit your request right now.'];
        }
    }

    public function updateBookingRequest($bookingId, $audienceId, $dramaId, $ticketPrice, $requestDetails = [])
    {
        if (!$this->canUseTable() || !$this->hasWorkflowColumns()) {
            return ['success' => false, 'message' => 'Booking table is not available.'];
        }

        try {
            $this->db->query("UPDATE audience_show_bookings
                              SET drama_id = :drama_id,
                                  ticket_price = :ticket_price,
                                  booking_status = 'pending',
                                  request_details_json = :request_details_json,
                                  rejection_reason = NULL,
                                  payhere_order_id = NULL,
                                  paid_at = NULL
                              WHERE id = :booking_id
                                AND audience_id = :audience_id
                                AND LOWER(booking_status) = 'pending'");
            $this->db->bind(':booking_id', (int)$bookingId);
            $this->db->bind(':audience_id', (int)$audienceId);
            $this->db->bind(':drama_id', (int)$dramaId);
            $this->db->bind(':ticket_price', number_format((float)$ticketPrice, 2, '.', ''));
            $this->db->bind(':request_details_json', json_encode($requestDetails));

            if (!$this->db->execute() || $this->db->rowCount() < 1) {
                return ['success' => false, 'message' => 'Unable to update your request right now.'];
            }

            return ['success' => true, 'message' => 'Request updated successfully.'];
        } catch (Exception $e) {
            error_log('Error in M_audience_show_booking::updateBookingRequest: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Unable to update your request right now.'];
        }
    }

    public function deleteBookingRequest($bookingId, $audienceId)
    {
        if (!$this->canUseTable()) {
            return ['success' => false, 'message' => 'Booking table is not available.'];
        }

        try {
            $this->db->query("DELETE FROM audience_show_bookings
                              WHERE id = :booking_id
                                AND audience_id = :audience_id
                                AND LOWER(booking_status) = 'pending'") ;
            $this->db->bind(':booking_id', (int)$bookingId);
            $this->db->bind(':audience_id', (int)$audienceId);

            if (!$this->db->execute() || $this->db->rowCount() < 1) {
                return ['success' => false, 'message' => 'Unable to remove your request right now.'];
            }

            return ['success' => true, 'message' => 'Request removed successfully.'];
        } catch (Exception $e) {
            error_log('Error in M_audience_show_booking::deleteBookingRequest: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Unable to remove your request right now.'];
        }
    }

    public function getShowRequestsByAudience($audienceId)
    {
        return $this->getBookingsByAudience($audienceId);
    }

    public function hasBooking($audienceId, $dramaId)
    {
        if (!$this->canUseTable()) {
            return false;
        }

        try {
            $this->db->query("SELECT COUNT(*) AS cnt
                             FROM audience_show_bookings
                             WHERE audience_id = :audience_id
                                                             AND drama_id = :drama_id
                                                             AND LOWER(booking_status) <> 'rejected'");
            $this->db->bind(':audience_id', (int)$audienceId);
            $this->db->bind(':drama_id', (int)$dramaId);
            $row = $this->db->single();
            return !empty($row) && (int)($row->cnt ?? 0) > 0;
        } catch (Exception $e) {
            error_log('Error in M_audience_show_booking::hasBooking: ' . $e->getMessage());
            return false;
        }
    }

    public function canRateDrama($audienceId, $dramaId)
    {
        if (!$this->canUseTable()) {
            return false;
        }

        if (!$this->tableHasColumn('booking_status')) {
            return false;
        }

        try {
            $selectRequestDetails = $this->tableHasColumn('request_details_json') ? 'b.request_details_json,' : "NULL AS request_details_json,";
            $selectPaidAt = $this->tableHasColumn('paid_at') ? 'b.paid_at,' : "NULL AS paid_at,";
            $selectPayOrder = $this->tableHasColumn('payhere_order_id') ? 'b.payhere_order_id,' : "NULL AS payhere_order_id,";

            $this->db->query("SELECT b.booking_status,
                                     {$selectRequestDetails}
                                     {$selectPaidAt}
                                     {$selectPayOrder}
                                     d.event_date
                              FROM audience_show_bookings b
                              LEFT JOIN dramas d ON d.id = b.drama_id
                              WHERE b.audience_id = :audience_id
                                AND b.drama_id = :drama_id");
            $this->db->bind(':audience_id', (int)$audienceId);
            $this->db->bind(':drama_id', (int)$dramaId);

            $rows = $this->db->resultSet();
            if (empty($rows)) {
                return false;
            }

            $todayYmd = date('Y-m-d');

            foreach ($rows as $row) {
                $status = strtolower(trim((string)($row->booking_status ?? '')));
                $hasPaymentRecord = !empty($row->paid_at) || !empty($row->payhere_order_id);

                if (in_array($status, ['watched', 'completed', 'attended'], true)) {
                    return true;
                }

                if (!$hasPaymentRecord) {
                    continue;
                }

                $showDateRaw = '';
                if (!empty($row->request_details_json)) {
                    $decodedRequestDetails = json_decode((string)$row->request_details_json, true);
                    if (is_array($decodedRequestDetails) && !empty($decodedRequestDetails['show_date'])) {
                        $showDateRaw = trim((string)$decodedRequestDetails['show_date']);
                    }
                }

                if ($showDateRaw === '' && !empty($row->event_date)) {
                    $showDateRaw = trim((string)$row->event_date);
                }

                if ($showDateRaw !== '' && strtotime($showDateRaw) !== false) {
                    $showDateYmd = date('Y-m-d', strtotime($showDateRaw));
                    if ($showDateYmd < $todayYmd) {
                        return true;
                    }
                }
            }

            return false;
        } catch (Exception $e) {
            error_log('Error in M_audience_show_booking::canRateDrama: ' . $e->getMessage());
            return false;
        }
    }

    public function getBookingsByAudience($audienceId)
    {
        if (!$this->canUseTable()) {
            return [];
        }

        try {
            $selectRequestDetails = $this->tableHasColumn('request_details_json') ? 'b.request_details_json,' : "NULL AS request_details_json,";
            $selectRejectionReason = $this->tableHasColumn('rejection_reason') ? 'b.rejection_reason,' : "NULL AS rejection_reason,";
            $selectPayOrder = $this->tableHasColumn('payhere_order_id') ? 'b.payhere_order_id,' : "NULL AS payhere_order_id,";
            $selectPaidAt = $this->tableHasColumn('paid_at') ? 'b.paid_at,' : "NULL AS paid_at,";

            $this->db->query("SELECT b.id,
                                     b.drama_id,
                                     b.booking_status,
                                     b.created_at,
                                     {$selectRequestDetails}
                                     {$selectRejectionReason}
                                     {$selectPayOrder}
                                     {$selectPaidAt}
                                     d.drama_name AS title,
                              d.showing_prices,
                                     d.event_date,
                                     d.event_time,
                                     d.venue
                              FROM audience_show_bookings b
                              LEFT JOIN dramas d ON d.id = b.drama_id
                              WHERE b.audience_id = :audience_id
                              ORDER BY b.created_at DESC");
            $this->db->bind(':audience_id', (int)$audienceId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('Error in M_audience_show_booking::getBookingsByAudience: ' . $e->getMessage());
            return [];
        }
    }

    public function getLatestBookingByAudienceDrama($audienceId, $dramaId)
    {
        if (!$this->canUseTable()) {
            return null;
        }

        try {
            $selectRequestDetails = $this->tableHasColumn('request_details_json') ? 'b.request_details_json,' : "NULL AS request_details_json,";
            $selectRejectionReason = $this->tableHasColumn('rejection_reason') ? 'b.rejection_reason,' : "NULL AS rejection_reason,";
            $selectPayOrder = $this->tableHasColumn('payhere_order_id') ? 'b.payhere_order_id,' : "NULL AS payhere_order_id,";
            $selectPaidAt = $this->tableHasColumn('paid_at') ? 'b.paid_at,' : "NULL AS paid_at,";

            $this->db->query("SELECT b.id,
                                     b.audience_id,
                                     b.drama_id,
                                     b.ticket_price,
                                     b.booking_status,
                                     {$selectRequestDetails}
                                     {$selectRejectionReason}
                                     {$selectPayOrder}
                                     {$selectPaidAt}
                                     b.created_at,
                                     d.drama_name AS title,
                                     COALESCE(d.creator_artist_id, d.created_by) AS artist_id,
                                     d.event_date,
                                     d.event_time,
                                     d.venue
                              FROM audience_show_bookings b
                              LEFT JOIN dramas d ON d.id = b.drama_id
                              WHERE b.audience_id = :audience_id
                                AND b.drama_id = :drama_id
                              ORDER BY b.id DESC
                              LIMIT 1");
            $this->db->bind(':audience_id', (int)$audienceId);
            $this->db->bind(':drama_id', (int)$dramaId);
            return $this->db->single();
        } catch (Exception $e) {
            error_log('Error in M_audience_show_booking::getLatestBookingByAudienceDrama: ' . $e->getMessage());
            return null;
        }
    }

    public function getBookingByIdForAudience($bookingId, $audienceId)
    {
        if (!$this->canUseTable()) {
            return null;
        }

        try {
                        $this->db->query("SELECT b.*, d.drama_name AS title, d.showing_prices, COALESCE(d.creator_artist_id, d.created_by) AS artist_id
                              FROM audience_show_bookings b
                              LEFT JOIN dramas d ON d.id = b.drama_id
                              WHERE b.id = :booking_id
                                AND b.audience_id = :audience_id
                              LIMIT 1");
            $this->db->bind(':booking_id', (int)$bookingId);
            $this->db->bind(':audience_id', (int)$audienceId);
            return $this->db->single();
        } catch (Exception $e) {
            error_log('Error in M_audience_show_booking::getBookingByIdForAudience: ' . $e->getMessage());
            return null;
        }
    }

    public function createPaymentOrder($bookingId, $audienceId, $orderId)
    {
        if (!$this->canUseTable() || !$this->tableHasColumn('payhere_order_id')) {
            return false;
        }

        try {
            $this->db->query("UPDATE audience_show_bookings
                              SET payhere_order_id = :order_id
                              WHERE id = :booking_id
                                AND audience_id = :audience_id
                                AND LOWER(booking_status) = 'accepted'");
            $this->db->bind(':order_id', $orderId);
            $this->db->bind(':booking_id', (int)$bookingId);
            $this->db->bind(':audience_id', (int)$audienceId);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Error in M_audience_show_booking::createPaymentOrder: ' . $e->getMessage());
            return false;
        }
    }

    public function markPaymentCompletedByOrder($orderId, $audienceId)
    {
        if (!$this->canUseTable() || !$this->hasWorkflowColumns()) {
            return false;
        }

        try {
            $this->db->query("UPDATE audience_show_bookings
                              SET booking_status = 'confirmed',
                                  paid_at = NOW()
                              WHERE payhere_order_id = :order_id
                                AND audience_id = :audience_id
                                AND LOWER(booking_status) = 'accepted'");
            $this->db->bind(':order_id', $orderId);
            $this->db->bind(':audience_id', (int)$audienceId);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Error in M_audience_show_booking::markPaymentCompletedByOrder: ' . $e->getMessage());
            return false;
        }
    }

    public function getShowingPaymentsByAudience($audienceId)
    {
        if (!$this->canUseTable()) {
            return [];
        }

        try {
            $selectPaidAt = $this->tableHasColumn('paid_at') ? 'b.paid_at,' : "NULL AS paid_at,";
            $selectPayOrder = $this->tableHasColumn('payhere_order_id') ? 'b.payhere_order_id,' : "NULL AS payhere_order_id,";

            $this->db->query("SELECT b.id,
                                     b.drama_id,
                                     b.ticket_price,
                                     b.booking_status,
                                     b.created_at,
                                     {$selectPaidAt}
                                     {$selectPayOrder}
                                     d.drama_name AS title,
                                     d.showing_prices,
                                     d.event_date,
                                     d.venue
                              FROM audience_show_bookings b
                              LEFT JOIN dramas d ON d.id = b.drama_id
                              WHERE b.audience_id = :audience_id
                                AND (
                                    (b.paid_at IS NOT NULL)
                                    OR (LOWER(b.booking_status) IN ('confirmed', 'completed', 'watched', 'attended')
                                        AND b.payhere_order_id IS NOT NULL
                                        AND b.payhere_order_id <> '')
                                )
                              ORDER BY COALESCE(b.paid_at, b.created_at) DESC");
            $this->db->bind(':audience_id', (int)$audienceId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('Error in M_audience_show_booking::getShowingPaymentsByAudience: ' . $e->getMessage());
            return [];
        }
    }

    public function getPendingShowRequestsByArtist($artistId)
    {
        if (!$this->canUseTable()) {
            return [];
        }

        try {
            $selectRequestDetails = $this->tableHasColumn('request_details_json') ? 'b.request_details_json,' : "NULL AS request_details_json,";
            $this->db->query("SELECT b.id,
                                     b.audience_id,
                                     b.drama_id,
                                     b.ticket_price,
                                     b.booking_status,
                                     b.created_at,
                                     {$selectRequestDetails}
                                     u.full_name AS audience_name,
                                     u.email AS audience_email,
                             u.phone AS audience_phone,
                             d.drama_name AS drama_title
                              FROM audience_show_bookings b
                              INNER JOIN dramas d ON d.id = b.drama_id
                              LEFT JOIN users u ON u.id = b.audience_id
                                                            WHERE (
                                            d.created_by = :artist_id
                                            OR d.creator_artist_id = :artist_id
                                                                        OR EXISTS (
                                                                                SELECT 1
                                                                                FROM drama_manager_assignments dma
                                                                                WHERE dma.drama_id = b.drama_id
                                                                                    AND dma.manager_artist_id = :artist_id
                                                                                    AND dma.status = 'active'
                                                                        )
                                                                )
                                AND LOWER(b.booking_status) = 'pending'
                              ORDER BY b.created_at DESC");
            $this->db->bind(':artist_id', (int)$artistId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('Error in M_audience_show_booking::getPendingShowRequestsByArtist: ' . $e->getMessage());
            return [];
        }
    }

    public function getShowRequestsByArtist($artistId, $statuses = [])
    {
        if (!$this->canUseTable()) {
            return [];
        }

        $allowedStatuses = ['pending', 'accepted', 'rejected', 'confirmed', 'completed', 'watched', 'attended'];
        $statuses = array_values(array_filter(array_map(function ($status) {
            return strtolower(trim((string)$status));
        }, (array)$statuses), function ($status) use ($allowedStatuses) {
            return in_array($status, $allowedStatuses, true);
        }));

        if (empty($statuses)) {
            $statuses = ['pending', 'accepted', 'rejected'];
        }

        try {
            $selectRequestDetails = $this->tableHasColumn('request_details_json') ? 'b.request_details_json,' : "NULL AS request_details_json,";
            $selectRejectionReason = $this->tableHasColumn('rejection_reason') ? 'b.rejection_reason,' : "NULL AS rejection_reason,";

            $statusPlaceholders = [];
            foreach ($statuses as $idx => $status) {
                $statusPlaceholders[] = ':status_' . $idx;
            }

            $this->db->query("SELECT b.id,
                                     b.audience_id,
                                     b.drama_id,
                                     b.ticket_price,
                                     b.booking_status,
                                     b.created_at,
                                     {$selectRequestDetails}
                                     {$selectRejectionReason}
                                     u.full_name AS audience_name,
                                     u.email AS audience_email,
                                     u.phone AS audience_phone,
                                         d.drama_name AS drama_title
                              FROM audience_show_bookings b
                              INNER JOIN dramas d ON d.id = b.drama_id
                              LEFT JOIN users u ON u.id = b.audience_id
                                                            WHERE (
                                                        d.created_by = :artist_id
                                                        OR d.creator_artist_id = :artist_id
                                                                        OR EXISTS (
                                                                                SELECT 1
                                                                                FROM drama_manager_assignments dma
                                                                                WHERE dma.drama_id = b.drama_id
                                                                                    AND dma.manager_artist_id = :artist_id
                                                                                    AND dma.status = 'active'
                                                                        )
                                                                )
                                AND LOWER(b.booking_status) IN (" . implode(', ', $statusPlaceholders) . ")
                              ORDER BY b.created_at DESC");
            $this->db->bind(':artist_id', (int)$artistId);
            foreach ($statuses as $idx => $status) {
                $this->db->bind(':status_' . $idx, $status);
            }

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('Error in M_audience_show_booking::getShowRequestsByArtist: ' . $e->getMessage());
            return [];
        }
    }

    public function respondToShowRequest($requestId, $artistId, $response, $reason = null)
    {
        if (!$this->canUseTable()) {
            return ['success' => false, 'message' => 'Booking table is not available.'];
        }

        if (!$this->hasWorkflowColumns()) {
            return ['success' => false, 'message' => 'Booking request workflow columns are missing. Run the migration first.'];
        }

        $status = strtolower((string)$response) === 'accept' ? 'accepted' : 'rejected';
        $reason = trim((string)$reason);

        if ($status === 'rejected' && $reason === '') {
            return ['success' => false, 'message' => 'Rejection reason is required.'];
        }

        try {
            $this->db->query("UPDATE audience_show_bookings b
                              INNER JOIN dramas d ON d.id = b.drama_id
                              SET b.booking_status = :status,
                                  b.rejection_reason = :reason,
                                  b.payhere_order_id = NULL,
                                  b.paid_at = NULL
                              WHERE b.id = :request_id
                                AND (
                                    d.created_by = :artist_id
                                    OR d.creator_artist_id = :artist_id
                                    OR EXISTS (
                                        SELECT 1
                                        FROM drama_manager_assignments dma
                                        WHERE dma.drama_id = b.drama_id
                                          AND dma.manager_artist_id = :artist_id
                                          AND dma.status = 'active'
                                    )
                                )
                                AND LOWER(b.booking_status) = 'pending'");
            $this->db->bind(':status', $status);
            $this->db->bind(':reason', $status === 'rejected' ? $reason : null);
            $this->db->bind(':request_id', (int)$requestId);
            $this->db->bind(':artist_id', (int)$artistId);

            $ok = $this->db->execute();
            if (!$ok) {
                return ['success' => false, 'message' => 'Unable to update this request.'];
            }

            return [
                'success' => true,
                'message' => $status === 'accepted' ? 'Show request accepted.' : 'Show request rejected with reason.'
            ];
        } catch (Exception $e) {
            error_log('Error in M_audience_show_booking::respondToShowRequest: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Unable to update this request.'];
        }
    }
}

?>