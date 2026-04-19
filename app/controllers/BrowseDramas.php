<?php

class BrowseDramas
{
    use Controller {
        view as protected renderView;
    }
    protected $model = null;
    protected $ratingModel = null;
    protected $bookingModel = null;
    protected $payHereHelper = null;

    public function __construct()
    {
        $this->model = $this->getModel("M_drama");
        $this->ratingModel = $this->getModel("M_rating");
        $this->bookingModel = $this->getModel("M_audience_show_booking");
        $this->payHereHelper = new PayHereHelper();
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/Login");
            exit;
        }
        header('Location: ' . ROOT . '/Audiencedashboard');
        exit;
    }

    public function view($drama_id = null)
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        if (!$drama_id || !$this->model) {
            header("Location: " . ROOT . "/Audiencedashboard");
            exit;
        }

        $data = [
            'drama' => $this->model->getDramaById($drama_id),
            'error' => '',
            'rating_summary' => null,
            'user_rating' => null,
            'ratings' => [],
            'has_rated' => false,
            'can_rate' => false
        ];

        if (!$data['drama']) {
            $data['error'] = 'Drama not found';
        } else if ((int)($data['drama']->is_published ?? 0) !== 1 && (($_SESSION['role'] ?? '') === 'audience')) {
            $data['error'] = 'Drama not found';
            $data['drama'] = null;
        } else if ($this->ratingModel) {
            try {
                $data['rating_summary'] = $this->ratingModel->getDramaRatingSummary((int)$drama_id);
                $data['user_rating'] = $this->ratingModel->getUserDramaRating((int)$drama_id, (int)$_SESSION['user_id']);
                $data['has_rated'] = !empty($data['user_rating']);
                $data['ratings'] = $this->ratingModel->getDramaRatings((int)$drama_id, 10, 0);
            } catch (Throwable $e) {
                error_log('BrowseDramas::view rating read skipped: ' . $e->getMessage());
                $data['rating_summary'] = null;
                $data['user_rating'] = null;
                $data['has_rated'] = false;
                $data['ratings'] = [];
            }

            if (!empty($this->bookingModel) && (($_SESSION['role'] ?? '') === 'audience')) {
                $data['can_rate'] = $this->bookingModel->canRateDrama((int)$_SESSION['user_id'], (int)$drama_id);
            }
        }

        $this->renderView('drama_details', $data);
    }

    public function rateReview($drama_id = null)
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        if (($_SESSION['role'] ?? '') !== 'audience') {
            $_SESSION['error_message'] = 'Only audience users can rate and review dramas.';
            header("Location: " . ROOT . "/Audiencedashboard");
            exit;
        }

        if (!$drama_id || !$this->model) {
            header("Location: " . ROOT . "/Audiencedashboard");
            exit;
        }

        $data = [
            'drama' => $this->model->getDramaById($drama_id),
            'error' => '',
            'rating_summary' => null,
            'user_rating' => null,
            'ratings' => [],
            'has_rated' => false
        ];

        if (!$data['drama']) {
            $data['error'] = 'Drama not found';
        } else if ((int)($data['drama']->is_published ?? 0) !== 1) {
            $data['error'] = 'Drama not found';
            $data['drama'] = null;
        } else {
            if (!$this->bookingModel) {
                $this->bookingModel = $this->getModel("M_audience_show_booking");
            }

            if (!$this->bookingModel || !$this->bookingModel->canRateDrama((int)$_SESSION['user_id'], (int)$drama_id)) {
                $_SESSION['error_message'] = 'You can rate this drama only after buying and watching the show.';
                header('Location: ' . ROOT . '/Audiencedashboard#watched-dramas');
                exit;
            }
        }

        if (!empty($data['drama']) && $this->ratingModel) {
            try {
                $data['rating_summary'] = $this->ratingModel->getDramaRatingSummary((int)$drama_id);
                $data['user_rating'] = $this->ratingModel->getUserDramaRating((int)$drama_id, (int)$_SESSION['user_id']);
                $data['has_rated'] = !empty($data['user_rating']);
                $data['ratings'] = $this->ratingModel->getDramaRatings((int)$drama_id, 20, 0);
            } catch (Throwable $e) {
                error_log('BrowseDramas::rateReview rating data load failed: ' . $e->getMessage());
                $data['rating_summary'] = null;
                $data['user_rating'] = null;
                $data['has_rated'] = false;
                $data['ratings'] = [];
            }
        }

        $this->renderView('drama_rate_review', $data);
    }

    public function watchedDetails($booking_id = null)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/Login');
            exit;
        }

        if (($_SESSION['role'] ?? '') !== 'audience') {
            $_SESSION['error_message'] = 'Only audience users can view watched drama details.';
            header('Location: ' . ROOT . '/Audiencedashboard');
            exit;
        }

        $bookingId = (int)($booking_id ?? ($_GET['booking_id'] ?? 0));
        if ($bookingId <= 0) {
            $_SESSION['error_message'] = 'Invalid watched drama selected.';
            header('Location: ' . ROOT . '/Audiencedashboard#watched-dramas');
            exit;
        }

        if (!$this->bookingModel) {
            $_SESSION['error_message'] = 'Booking service is currently unavailable.';
            header('Location: ' . ROOT . '/Audiencedashboard#watched-dramas');
            exit;
        }

        $userId = (int)$_SESSION['user_id'];
        $booking = $this->bookingModel->getBookingByIdForAudience($bookingId, $userId);
        if (!$booking) {
            $_SESSION['error_message'] = 'Watched drama not found.';
            header('Location: ' . ROOT . '/Audiencedashboard#watched-dramas');
            exit;
        }

        $requestDetails = [];
        if (!empty($booking->request_details_json)) {
            $decodedRequestDetails = json_decode((string)$booking->request_details_json, true);
            if (is_array($decodedRequestDetails)) {
                $requestDetails = $decodedRequestDetails;
            }
        }

        $showDateRaw = trim((string)($requestDetails['show_date'] ?? ''));
        $showDateYmd = '';
        if ($showDateRaw !== '' && strtotime($showDateRaw) !== false) {
            $showDateYmd = date('Y-m-d', strtotime($showDateRaw));
        }

        $hasPaymentRecord = !empty($booking->paid_at) || !empty($booking->payhere_order_id);
        $isPastShowing = $showDateYmd !== '' && $showDateYmd < date('Y-m-d');
        $bookingStatus = strtolower((string)($booking->booking_status ?? ''));
        $isWatchedBooking = $hasPaymentRecord && ($isPastShowing || in_array($bookingStatus, ['watched', 'completed', 'attended', 'confirmed'], true));

        if (!$isWatchedBooking) {
            $_SESSION['error_message'] = 'This booking is not yet available in watched dramas.';
            header('Location: ' . ROOT . '/Audiencedashboard#watched-dramas');
            exit;
        }

        $dramaId = (int)($booking->drama_id ?? 0);
        $data = [
            'drama' => $this->model ? $this->model->getDramaById($dramaId) : null,
            'booking' => $booking,
            'request_details' => $requestDetails,
            'booking_status' => $bookingStatus,
            'has_payment_record' => $hasPaymentRecord,
            'is_past_showing' => $isPastShowing,
            'can_rate' => $this->bookingModel->canRateDrama($userId, $dramaId),
            'rating_summary' => null,
            'ratings' => [],
            'user_rating' => null,
            'has_rated' => false,
        ];

        if (!empty($data['drama']) && $this->ratingModel) {
            try {
                $data['rating_summary'] = $this->ratingModel->getDramaRatingSummary($dramaId);
                $data['user_rating'] = $this->ratingModel->getUserDramaRating($dramaId, $userId);
                $data['has_rated'] = !empty($data['user_rating']);
                $data['ratings'] = $this->ratingModel->getDramaRatings($dramaId, 10, 0);
            } catch (Throwable $e) {
                error_log('BrowseDramas::watchedDetails rating data load failed: ' . $e->getMessage());
            }
        }

        $this->renderView('watched_drama_details', $data);
    }

    public function deleteRating()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }

        if (($_SESSION['role'] ?? '') !== 'audience') {
            echo json_encode(['success' => false, 'message' => 'Only audience users can delete reviews']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $ratingId = (int)($input['rating_id'] ?? 0);

        if ($ratingId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid review selected']);
            exit;
        }

        if (!$this->ratingModel) {
            echo json_encode(['success' => false, 'message' => 'Rating service unavailable']);
            exit;
        }

        $currentRating = $this->ratingModel->getUserRatingById($ratingId, (int)$_SESSION['user_id']);
        if (!$currentRating) {
            echo json_encode(['success' => false, 'message' => 'Review not found or not owned by you']);
            exit;
        }

        $deleted = $this->ratingModel->deleteRating($ratingId, (int)$_SESSION['user_id']);
        if ($deleted) {
            $summary = $this->ratingModel->getDramaRatingSummary((int)$currentRating->drama_id);
            echo json_encode([
                'success' => true,
                'message' => 'Review deleted successfully',
                'summary' => $summary
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete review']);
        }
        exit;
    }

    /**
     * Submit or update a drama rating via AJAX
     * POST request with: drama_id, rating (1-5), comment (optional)
     */
    public function submitRating()
    {
        header('Content-Type: application/json');

        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }

        if (($_SESSION['role'] ?? '') !== 'audience') {
            echo json_encode(['success' => false, 'message' => 'Only audience users can submit ratings']);
            exit;
        }

        // Check if request is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        // Get JSON data
        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (empty($input['drama_id']) || empty($input['rating'])) {
            echo json_encode(['success' => false, 'message' => 'Drama ID and rating are required']);
            exit;
        }

        $drama_id = (int)$input['drama_id'];
        $rating = (int)$input['rating'];
        $comment = $input['comment'] ?? null;
        $user_id = $_SESSION['user_id'];

        if ($comment !== null) {
            $comment = trim((string)$comment);
            if (strlen($comment) > 500) {
                echo json_encode(['success' => false, 'message' => 'Comment cannot exceed 500 characters']);
                exit;
            }
            if ($comment === '') {
                $comment = null;
            }
        }

        // Validate rating range
        if ($rating < 1 || $rating > 5) {
            echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5']);
            exit;
        }

        // Verify drama exists
        if (!$this->model) {
            echo json_encode(['success' => false, 'message' => 'Drama service unavailable']);
            exit;
        }

        $drama = $this->model->getDramaById($drama_id);
        if (!$drama) {
            echo json_encode(['success' => false, 'message' => 'Drama not found']);
            exit;
        }

        if ((int)($drama->is_published ?? 0) !== 1) {
            echo json_encode(['success' => false, 'message' => 'You can only rate published dramas']);
            exit;
        }

        if (!$this->bookingModel) {
            $this->bookingModel = $this->getModel("M_audience_show_booking");
        }

        if (!$this->bookingModel || !$this->bookingModel->canRateDrama((int)$user_id, (int)$drama_id)) {
            echo json_encode(['success' => false, 'message' => 'Only audience members who bought and watched this drama can rate it.']);
            exit;
        }

        // Submit/update rating
        if (!$this->ratingModel) {
            echo json_encode(['success' => false, 'message' => 'Rating service unavailable']);
            exit;
        }

        $success = $this->ratingModel->submitRating($drama_id, $user_id, $rating, $comment);

        if ($success) {
            // Get updated summary
            $summary = $this->ratingModel->getDramaRatingSummary($drama_id);
            
            echo json_encode([
                'success' => true,
                'message' => 'Rating submitted successfully',
                'summary' => $summary
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to submit rating']);
        }
        exit;
    }

    /**
     * Get ratings for a drama with pagination via AJAX
     */
    public function getRatings($drama_id = null)
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }

        if (!$drama_id) {
            echo json_encode(['success' => false, 'message' => 'Drama ID is required']);
            exit;
        }

        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

        if (!$this->ratingModel) {
            echo json_encode(['success' => false, 'message' => 'Rating service unavailable']);
            exit;
        }

        $ratings = $this->ratingModel->getDramaRatings($drama_id, $limit, $offset);
        $total = $this->ratingModel->countDramaRatings($drama_id);

        echo json_encode([
            'success' => true,
            'ratings' => $ratings,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ]);
        exit;
    }

    /**
     * Mark a rating as helpful via AJAX
     */
    public function markHelpful($rating_id = null)
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }

        if (!$rating_id) {
            echo json_encode(['success' => false, 'message' => 'Rating ID is required']);
            exit;
        }

        if (!$this->ratingModel) {
            echo json_encode(['success' => false, 'message' => 'Rating service unavailable']);
            exit;
        }

        $success = $this->ratingModel->markAsHelpful($rating_id);

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Rating marked as helpful'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to mark as helpful']);
        }
        exit;
    }

    public function bookShowings($drama_id = null)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/Login');
            exit;
        }

        if (($_SESSION['role'] ?? '') !== 'audience') {
            $_SESSION['error_message'] = 'Only audience users can book showings.';
            header('Location: ' . ROOT . '/Audiencedashboard');
            exit;
        }

        $drama_id = (int)$drama_id;
        if ($drama_id <= 0 || !$this->model) {
            $_SESSION['error_message'] = 'Invalid drama selected for booking.';
            header('Location: ' . ROOT . '/Audiencedashboard');
            exit;
        }

        $drama = $this->model->getDramaById($drama_id);
        if (!$drama || (int)($drama->is_published ?? 0) !== 1) {
            $_SESSION['error_message'] = 'Selected drama is not available for booking.';
            header('Location: ' . ROOT . '/Audiencedashboard');
            exit;
        }

        if (!$this->bookingModel) {
            $_SESSION['error_message'] = 'Booking service is currently unavailable.';
            header('Location: ' . ROOT . '/Audiencedashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $showingPricesRaw = trim((string)($drama->showing_prices ?? ''));
            $ticketPrice = 0.0;
            if ($showingPricesRaw !== '') {
                $normalizedPriceText = str_replace(',', '', $showingPricesRaw);
                if (preg_match('/\d+(?:\.\d+)?/', $normalizedPriceText, $priceMatch)) {
                    $ticketPrice = (float)$priceMatch[0];
                }
            }

            if ($ticketPrice <= 0) {
                $ticketPrice = (float)($drama->ticket_price ?? 0);
            }
            $requestSenderName = trim((string)($_POST['request_sender_name'] ?? ''));
            $requestContactPhone = trim((string)($_POST['request_contact_phone'] ?? ''));
            $requestContactEmail = trim((string)($_POST['request_contact_email'] ?? ''));
            $requestVenue = trim((string)($_POST['request_venue'] ?? ''));
            $showDate = trim((string)($_POST['show_date'] ?? ''));
            $showTimeStart = trim((string)($_POST['show_time_start'] ?? ''));
            $showTimeEnd = trim((string)($_POST['show_time_end'] ?? ''));
            $showTime = trim((string)($_POST['show_time'] ?? ''));
            $presentCount = (int)($_POST['present_count'] ?? 0);
            $requestNotes = trim((string)($_POST['request_notes'] ?? ''));

            if ($requestSenderName === '' || $requestContactPhone === '' || $requestVenue === '' || $showDate === '') {
                $_SESSION['error_message'] = 'Sender name, contact phone, place, show date, and show time are required for the request.';
                header('Location: ' . ROOT . '/BrowseDramas/bookShowings/' . $drama_id);
                exit;
            }

            if (mb_strlen($requestSenderName) > 120) {
                $_SESSION['error_message'] = 'Sender name is too long.';
                header('Location: ' . ROOT . '/BrowseDramas/bookShowings/' . $drama_id);
                exit;
            }

            if (mb_strlen($requestContactPhone) > 40) {
                $_SESSION['error_message'] = 'Contact phone is too long.';
                header('Location: ' . ROOT . '/BrowseDramas/bookShowings/' . $drama_id);
                exit;
            }

            if ($requestContactEmail !== '' && !filter_var($requestContactEmail, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error_message'] = 'Please enter a valid contact email address.';
                header('Location: ' . ROOT . '/BrowseDramas/bookShowings/' . $drama_id);
                exit;
            }

            $showDateTs = strtotime($showDate);
            if ($showDateTs === false) {
                $_SESSION['error_message'] = 'Invalid show date.';
                header('Location: ' . ROOT . '/BrowseDramas/bookShowings/' . $drama_id);
                exit;
            }

            if ($showTimeStart !== '' && $showTimeEnd !== '') {
                $startObj = DateTime::createFromFormat('H:i', $showTimeStart);
                $endObj = DateTime::createFromFormat('H:i', $showTimeEnd);

                if (!$startObj || !$endObj) {
                    $_SESSION['error_message'] = 'Invalid show time range.';
                    header('Location: ' . ROOT . '/BrowseDramas/bookShowings/' . $drama_id);
                    exit;
                }

                if ($startObj >= $endObj) {
                    $_SESSION['error_message'] = 'End time must be later than start time.';
                    header('Location: ' . ROOT . '/BrowseDramas/bookShowings/' . $drama_id);
                    exit;
                }

                $showTime = $startObj->format('g:i A') . ' to ' . $endObj->format('g:i A');
            }

            if ($showTime === '') {
                $_SESSION['error_message'] = 'Show time is required.';
                header('Location: ' . ROOT . '/BrowseDramas/bookShowings/' . $drama_id);
                exit;
            }

            if (mb_strlen($showTime) > 120) {
                $_SESSION['error_message'] = 'Show time is too long.';
                header('Location: ' . ROOT . '/BrowseDramas/bookShowings/' . $drama_id);
                exit;
            }

            $created = $this->bookingModel->createBookingRequest(
                (int)$_SESSION['user_id'],
                $drama_id,
                $ticketPrice,
                [
                    'request_sender_name' => $requestSenderName,
                    'request_contact_phone' => $requestContactPhone,
                    'request_contact_email' => $requestContactEmail,
                    'request_venue' => $requestVenue,
                    'show_date' => date('Y-m-d', $showDateTs),
                    'show_time' => $showTime,
                    'show_time_start' => $showTimeStart,
                    'show_time_end' => $showTimeEnd,
                    'show_datetime' => date('Y-m-d', $showDateTs) . ' ' . $showTime,
                    'present_count' => max(0, $presentCount),
                    'request_notes' => $requestNotes,
                ]
            );

            if (!$created['success']) {
                $_SESSION['error_message'] = $created['message'];
                header('Location: ' . ROOT . '/BrowseDramas/bookShowings/' . $drama_id);
                exit;
            }

            $_SESSION['success_message'] = $created['message'];
            header('Location: ' . ROOT . '/BrowseDramas/bookShowings/' . $drama_id);
            exit;
        }

        $userId = (int)$_SESSION['user_id'];
        $userModel = $this->getModel('M_login');
        $audienceUser = $userModel ? $userModel->getUserById($userId) : null;
        $bookingRequest = $this->bookingModel->getLatestBookingByAudienceDrama($userId, $drama_id);
        $bookingStatus = strtolower((string)($bookingRequest->booking_status ?? 'none'));

        $data = [
            'drama' => $drama,
            'rating_summary' => null,
            'ratings' => [],
            'has_booking' => $this->bookingModel->hasBooking($userId, $drama_id),
            'can_rate' => $this->bookingModel->canRateDrama($userId, $drama_id),
            'booking_request' => $bookingRequest,
            'booking_status' => $bookingStatus,
            'can_make_payment' => in_array($bookingStatus, ['accepted'], true),
            'audience_user' => $audienceUser,
            'payhere_config' => [
                'merchant_id' => $this->payHereHelper ? $this->payHereHelper->getConfig('merchant_id') : '',
                'sandbox' => $this->payHereHelper ? $this->payHereHelper->getConfig('sandbox') : false,
                'return_url' => ROOT . '/BrowseDramas/payment_return',
                'cancel_url' => ROOT . '/BrowseDramas/bookShowings/' . $drama_id,
                'notify_url' => ROOT . '/BrowseDramas/payment_notify',
            ],
        ];

        if ($this->ratingModel) {
            try {
                $data['rating_summary'] = $this->ratingModel->getDramaRatingSummary((int)$drama_id);
                $data['ratings'] = $this->ratingModel->getDramaRatings((int)$drama_id, 20, 0);
            } catch (Throwable $e) {
                error_log('BrowseDramas::bookShowings rating data load failed: ' . $e->getMessage());
                $data['rating_summary'] = null;
                $data['ratings'] = [];
            }
        }

        $this->renderView('drama_book_showings', $data);
    }

    public function createShowPayment()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || (($_SESSION['role'] ?? '') !== 'audience')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            exit;
        }

        $bookingId = (int)($_POST['booking_id'] ?? 0);
        if ($bookingId <= 0 || !$this->bookingModel || !$this->payHereHelper) {
            echo json_encode(['success' => false, 'error' => 'Invalid booking request']);
            exit;
        }

        $booking = $this->bookingModel->getBookingByIdForAudience($bookingId, (int)$_SESSION['user_id']);
        if (!$booking) {
            echo json_encode(['success' => false, 'error' => 'Booking request not found']);
            exit;
        }

        $status = strtolower((string)($booking->booking_status ?? ''));
        if ($status === 'rejected') {
            $reason = trim((string)($booking->rejection_reason ?? ''));
            echo json_encode([
                'success' => false,
                'error' => $reason !== '' ? ('Request rejected by artist: ' . $reason) : 'Request rejected by artist.'
            ]);
            exit;
        }

        if ($status !== 'accepted') {
            echo json_encode(['success' => false, 'error' => 'Payment is available only after artist approval.']);
            exit;
        }

        $orderId = 'SHOW-' . $bookingId . '-' . time();
        if (!$this->bookingModel->createPaymentOrder($bookingId, (int)$_SESSION['user_id'], $orderId)) {
            echo json_encode(['success' => false, 'error' => 'Unable to initialize payment']);
            exit;
        }

        $showingPricesRaw = trim((string)($booking->showing_prices ?? ''));
        $amountValue = 0.0;
        if ($showingPricesRaw !== '') {
            $normalizedPriceText = str_replace(',', '', $showingPricesRaw);
            if (preg_match('/\d+(?:\.\d+)?/', $normalizedPriceText, $priceMatch)) {
                $amountValue = (float)$priceMatch[0];
            }
        }

        if ($amountValue <= 0) {
            $amountValue = (float)($booking->ticket_price ?? 0);
        }

        $amount = number_format($amountValue, 2, '.', '');
        $hash = $this->payHereHelper->generateHash($orderId, $amount);

        echo json_encode([
            'success' => true,
            'order_id' => $orderId,
            'booking_id' => $bookingId,
            'amount' => $amount,
            'hash' => $hash,
            'drama_id' => (int)$booking->drama_id,
            'title' => $booking->title ?? 'Drama Show',
        ]);
        exit;
    }

    public function payment_return()
    {
        if (!isset($_SESSION['user_id']) || (($_SESSION['role'] ?? '') !== 'audience')) {
            header('Location: ' . ROOT . '/login');
            exit;
        }

        $orderId = trim((string)($_GET['order_id'] ?? ''));
        $dramaId = (int)($_GET['drama_id'] ?? 0);

        if ($orderId === '' || !$this->bookingModel) {
            $_SESSION['error_message'] = 'Invalid payment return details.';
            header('Location: ' . ROOT . '/Audiencedashboard#my-showings');
            exit;
        }

        $ok = $this->bookingModel->markPaymentCompletedByOrder($orderId, (int)$_SESSION['user_id']);
        if ($ok) {
            $_SESSION['success_message'] = 'Payment completed successfully. Your show request is confirmed.';
        } else {
            $_SESSION['error_message'] = 'Could not verify payment completion. Please contact support if amount was deducted.';
        }

        if ($dramaId > 0) {
            header('Location: ' . ROOT . '/BrowseDramas/bookShowings/' . $dramaId);
            exit;
        }

        header('Location: ' . ROOT . '/Audiencedashboard#my-showings');
        exit;
    }

    public function payment_notify()
    {
        http_response_code(200);
        echo 'OK';
        exit;
    }
}
