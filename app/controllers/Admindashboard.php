<?php
class Admindashboard {
    use Controller;

    private function normalizeSlideImagePath(string $posterImage): string
    {
        $rawPosterPath = ltrim(trim($posterImage), '/');
        if ($rawPosterPath === '') {
            return '';
        }

        if (preg_match('~^https?://~i', $rawPosterPath)) {
            return $rawPosterPath;
        }

        if (strpos($rawPosterPath, 'uploads/') === 0 || strpos($rawPosterPath, 'assets/') === 0) {
            return $rawPosterPath;
        }

        return 'uploads/dramas/' . $rawPosterPath;
    }

    private function hasColumnFromList(array $columns, string $columnName): bool
    {
        foreach ($columns as $column) {
            if (strtolower((string)($column->Field ?? '')) === strtolower($columnName)) {
                return true;
            }
        }

        return false;
    }

    private function syncPublishedDramasToSwiper(Database $db, bool $hasDramaId, bool $hasDescription, bool $hasUpdatedAt): void
    {
        if (!$hasDramaId) {
            return;
        }

        try {
            $db->query("SHOW COLUMNS FROM dramas");
            $dramaColumns = $db->resultSet();
        } catch (Throwable $e) {
            error_log('Admindashboard::syncPublishedDramasToSwiper schema check failed: ' . $e->getMessage());
            return;
        }

        $hasPublished = $this->hasColumnFromList($dramaColumns, 'is_published');
        $hasPoster = $this->hasColumnFromList($dramaColumns, 'poster_image');
        $hasDramaName = $this->hasColumnFromList($dramaColumns, 'drama_name');

        if (!$hasPublished || !$hasPoster || !$hasDramaName) {
            return;
        }

        $db->query("SELECT d.id,
                           d.drama_name,
                           d.poster_image,
                           s.id AS slide_id,
                           s.image_path AS slide_image_path,
                           s.title AS slide_title
                    FROM dramas d
                    LEFT JOIN swiper_slides s ON s.drama_id = d.id
                    WHERE d.is_published = 1
                      AND d.poster_image IS NOT NULL
                      AND TRIM(d.poster_image) <> ''");
        $rows = $db->resultSet();

        foreach ($rows as $row) {
            $imagePath = $this->normalizeSlideImagePath((string)($row->poster_image ?? ''));
            if ($imagePath === '') {
                continue;
            }

            $slideTitle = trim((string)($row->drama_name ?? ''));
            if ($slideTitle === '') {
                $slideTitle = 'Drama';
            }

            if (empty($row->slide_id)) {
                $insertColumns = ['image_path', 'title', 'drama_id', 'display_order', 'is_active'];
                $insertValues = [':image_path', ':title', ':drama_id', '(SELECT COALESCE(MAX(display_order), 0) + 1 FROM swiper_slides)', '0'];

                if ($hasDescription) {
                    $insertColumns[] = 'description';
                    $insertValues[] = ':description';
                }

                $db->query("INSERT INTO swiper_slides (" . implode(', ', $insertColumns) . ") VALUES (" . implode(', ', $insertValues) . ")");
                $db->bind(':image_path', $imagePath);
                $db->bind(':title', $slideTitle);
                $db->bind(':drama_id', (int)$row->id);
                if ($hasDescription) {
                    $db->bind(':description', 'Submitted by director for home page approval');
                }
                $db->execute();
                continue;
            }

            $currentImagePath = trim((string)($row->slide_image_path ?? ''));
            $currentTitle = trim((string)($row->slide_title ?? ''));
            if ($currentImagePath === $imagePath && $currentTitle === $slideTitle) {
                continue;
            }

            $setParts = ['image_path = :image_path', 'title = :title'];
            if ($hasDescription) {
                $setParts[] = 'description = :description';
            }
            if ($hasUpdatedAt) {
                $setParts[] = 'updated_at = CURRENT_TIMESTAMP';
            }

            $db->query("UPDATE swiper_slides SET " . implode(', ', $setParts) . " WHERE id = :id");
            $db->bind(':image_path', $imagePath);
            $db->bind(':title', $slideTitle);
            if ($hasDescription) {
                $db->bind(':description', 'Submitted by director for home page approval');
            }
            $db->bind(':id', (int)$row->slide_id);
            $db->execute();
        }
    }

    private function tableExists(Database $db, string $tableName): bool
    {
        $db->query("SELECT COUNT(*) AS cnt FROM information_schema.TABLES
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = :table_name");
        $db->bind(':table_name', $tableName);
        $row = $db->single();
        return $row && (int)$row->cnt > 0;
    }

    private function columnExists(Database $db, string $tableName, string $columnName): bool
    {
        $db->query("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = :table_name
                    AND COLUMN_NAME = :column_name");
        $db->bind(':table_name', $tableName);
        $db->bind(':column_name', $columnName);
        $row = $db->single();
        return $row && (int)$row->cnt > 0;
    }

    /**
     * Return available verification admin column in users table.
     * Supports both old schema (`verified_by`) and newer (`verified_by_admin_id`).
     */
    private function getVerificationAdminColumn(Database $db): ?string
    {
        $db->query("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'users'
                    AND COLUMN_NAME = 'verified_by_admin_id'");
        $hasNew = $db->single();
        if ($hasNew && (int)$hasNew->cnt > 0) {
            return 'verified_by_admin_id';
        }

        $db->query("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'users'
                    AND COLUMN_NAME = 'verified_by'");
        $hasOld = $db->single();
        if ($hasOld && (int)$hasOld->cnt > 0) {
            return 'verified_by';
        }

        return null;
    }

    public function index(){
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        // Check if user has admin role
        if ($_SESSION['role'] !== 'admin') {
            header("Location: " . ROOT . "/Home");
            exit;
        }

        $this->view('admindashboard');
    }

    /**
     * Get overview card statistics for admin dashboard
     */
    public function getOverviewStats()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $db = new Database();

        $stats = [
            'total_users' => 0,
            'pending_user_approvals' => 0,
            'active_dramas' => 0,
            'pending_drama_approvals' => 0
        ];

        try {
            $db->query("SELECT COUNT(*) AS total_users FROM users WHERE role != 'admin'");
            $users = $db->single();
            $stats['total_users'] = (int)($users->total_users ?? 0);

            $db->query("SELECT COUNT(*) AS pending_user_approvals
                        FROM users
                        WHERE role IN ('artist', 'service_provider')
                        AND is_verified = 0");
            $pendingUsers = $db->single();
            $stats['pending_user_approvals'] = (int)($pendingUsers->pending_user_approvals ?? 0);

            if ($this->tableExists($db, 'dramas')) {
                $hasStatus = $this->columnExists($db, 'dramas', 'status');
                $hasPublished = $this->columnExists($db, 'dramas', 'is_published');

                if ($hasStatus) {
                    $db->query("SELECT COUNT(*) AS active_dramas FROM dramas WHERE status = 'active'");
                } elseif ($hasPublished) {
                    $db->query("SELECT COUNT(*) AS active_dramas FROM dramas WHERE is_published = 1");
                } else {
                    $db->query("SELECT COUNT(*) AS active_dramas FROM dramas");
                }

                $dramas = $db->single();
                $stats['active_dramas'] = (int)($dramas->active_dramas ?? 0);
            }

            if ($this->tableExists($db, 'drama_creation_requests')) {
                $db->query("SELECT COUNT(*) AS pending_drama_approvals
                            FROM drama_creation_requests
                            WHERE status = 'pending'");
                $pendingDrama = $db->single();
                $stats['pending_drama_approvals'] = (int)($pendingDrama->pending_drama_approvals ?? 0);
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'stats' => $stats]);
            exit;
        } catch (Throwable $e) {
            error_log('Admindashboard::getOverviewStats error: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to load overview stats']);
            exit;
        }
    }

    /**
     * Get overview chart data for admin dashboard
     */
    public function getOverviewChartData()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $db = new Database();

        try {
            // Last 6 months registration trend (excluding admins)
            $db->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS month_key,
                               DATE_FORMAT(created_at, '%b %Y') AS month_label,
                               COUNT(*) AS total
                        FROM users
                        WHERE role != 'admin'
                          AND created_at >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
                        GROUP BY DATE_FORMAT(created_at, '%Y-%m'), DATE_FORMAT(created_at, '%b %Y')
                        ORDER BY month_key ASC");
            $trendRows = $db->resultSet();

            $trendMap = [];
            foreach ($trendRows as $row) {
                $trendMap[$row->month_key] = [
                    'label' => $row->month_label,
                    'total' => (int)$row->total,
                ];
            }

            $trendLabels = [];
            $trendValues = [];
            for ($i = 5; $i >= 0; $i--) {
                $dt = new DateTime('first day of this month');
                $dt->modify('-' . $i . ' month');
                $key = $dt->format('Y-m');

                if (isset($trendMap[$key])) {
                    $trendLabels[] = $trendMap[$key]['label'];
                    $trendValues[] = $trendMap[$key]['total'];
                } else {
                    $trendLabels[] = $dt->format('M Y');
                    $trendValues[] = 0;
                }
            }

            // Role distribution (excluding admins)
            $db->query("SELECT role, COUNT(*) AS total
                        FROM users
                        WHERE role != 'admin'
                        GROUP BY role");
            $roleRows = $db->resultSet();

            $roleMap = [];
            foreach ($roleRows as $row) {
                $roleMap[$row->role] = (int)$row->total;
            }

            $roleLabels = ['Artist', 'Audience', 'Service Provider'];
            $roleValues = [
                $roleMap['artist'] ?? 0,
                $roleMap['audience'] ?? 0,
                $roleMap['service_provider'] ?? 0,
            ];

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'charts' => [
                    'registration_trend' => [
                        'labels' => $trendLabels,
                        'values' => $trendValues,
                    ],
                    'role_distribution' => [
                        'labels' => $roleLabels,
                        'values' => $roleValues,
                    ],
                ],
            ]);
            exit;
        } catch (Throwable $e) {
            error_log('Admindashboard::getOverviewChartData error: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to load chart data']);
            exit;
        }
    }

    /**
     * Get pending registrations (artists and service providers)
     */
    public function getPendingRegistrations()
    {
        // Check admin access
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $db = new Database();
        
        $db->query("SELECT 
                    u.id,
                    u.full_name,
                    u.email,
                    u.phone,
                    u.role,
                    u.nic_photo_front AS nic_photo,
                    u.created_at
                FROM users u
                WHERE u.is_verified = 0 
                AND u.role IN ('artist', 'service_provider')
                ORDER BY u.created_at ASC");
        
        $registrations = $db->resultSet();
        
        // Convert objects to arrays for JSON
        $result = [];
        foreach ($registrations as $reg) {
            $result[] = [
                'id' => $reg->id,
                'full_name' => $reg->full_name,
                'email' => $reg->email,
                'phone' => $reg->phone,
                'role' => $reg->role,
                'nic_photo' => $reg->nic_photo,
                'created_at' => $reg->created_at
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    /**
     * Get pending drama creation requests from artists
     */
    public function getPendingDramaRequests()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $dramaModel = $this->getModel('M_drama');
        $requests = $dramaModel ? $dramaModel->getPendingDramaRequests() : [];

        $result = [];
        foreach ($requests as $req) {
            $result[] = [
                'id' => (int)$req->id,
                'drama_name' => $req->drama_name,
                'certificate_number' => $req->certificate_number,
                'owner_name' => $req->owner_name,
                'description' => $req->description,
                'certificate_image' => $req->certificate_image,
                'requested_by' => (int)$req->requested_by,
                'artist_name' => $req->artist_name,
                'artist_email' => $req->artist_email,
                'artist_phone' => $req->artist_phone,
                'created_at' => $req->created_at
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    /**
     * Approve drama creation request and auto-create drama
     */
    public function approveDramaRequest()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $requestId = isset($input['request_id']) ? (int)$input['request_id'] : 0;

        if ($requestId <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Request ID is required']);
            exit;
        }

        $dramaModel = $this->getModel('M_drama');
        $result = $dramaModel
            ? $dramaModel->approveDramaRequest($requestId, (int)$_SESSION['user_id'])
            : ['success' => false, 'message' => 'Drama system is unavailable'];

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    /**
     * Reject drama creation request
     */
    public function rejectDramaRequest()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $requestId = isset($input['request_id']) ? (int)$input['request_id'] : 0;
        $reason = trim($input['reason'] ?? '');

        if ($requestId <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Request ID is required']);
            exit;
        }

        $dramaModel = $this->getModel('M_drama');
        $result = $dramaModel
            ? $dramaModel->rejectDramaRequest($requestId, (int)$_SESSION['user_id'], $reason)
            : ['success' => false, 'message' => 'Drama system is unavailable'];

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    /**
     * Get full registration details for a single pending user
     * (used in admin dashboard modal to verify signup info)
     */
    public function getRegistrationDetails()
    {
        // Check admin access
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
        if ($userId <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User ID is required']);
            exit;
        }

        $db = new Database();

        // Base user info
        $db->query("SELECT id, full_name, email, phone, role, nic_photo_front AS nic_photo, created_at, verification_status 
                    FROM users WHERE id = :user_id");
        $db->bind(':user_id', $userId);
        $user = $db->single();

        if (!$user) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        $details = [
            'success' => true,
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'nic_photo' => $user->nic_photo,
                'created_at' => $user->created_at,
                'verification_status' => $user->verification_status
            ],
        ];

        // Role-specific extra details
        if ($user->role === 'service_provider') {
                 $db->query("SELECT u.full_name, sp.professional_title, u.email, u.phone, sp.location, u.nic_number,
                           sp.social_media_link, sp.years_experience, u.bio AS professional_summary,
                           sp.availability, sp.availability_notes, u.nic_photo_front, u.nic_photo_back
                       FROM serviceprovider sp
                       INNER JOIN users u ON u.id = sp.user_id
                       WHERE sp.user_id = :user_id");
            $db->bind(':user_id', $userId);
            $spData = $db->single();
            if ($spData) {
                $details['service_provider'] = [
                    'full_name' => $spData->full_name,
                    'professional_title' => $spData->professional_title,
                    'email' => $spData->email,
                    'phone' => $spData->phone,
                    'location' => $spData->location,
                    'nic_number' => $spData->nic_number,
                    'social_media_link' => $spData->social_media_link ?? null,
                    'years_experience' => $spData->years_experience,
                    'professional_summary' => $spData->professional_summary,
                    'availability' => $spData->availability,
                    'availability_notes' => $spData->availability_notes,
                    'nic_photo_front' => $spData->nic_photo_front,
                    'nic_photo_back' => $spData->nic_photo_back
                ];
            }
        }

        // For artists, basic info (including NIC photo) is already in users table

        header('Content-Type: application/json');
        echo json_encode($details);
        exit;
    }

    /**
     * Approve user registration
     */
    public function approveUser()
    {
        // Check admin access
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Get POST data
        $input = json_decode(file_get_contents('php://input'), true);
        $userId = $input['user_id'] ?? null;

        if (!$userId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User ID is required']);
            exit;
        }

        $db = new Database();
        $adminId = $_SESSION['user_id'];

        try {
            $adminColumn = $this->getVerificationAdminColumn($db);

            $setAdmin = $adminColumn ? ", {$adminColumn} = :admin_id" : '';
            $db->query("UPDATE users 
                    SET 
                        is_verified = 1,
                        verification_status = 'approved'{$setAdmin},
                        verified_at = CURRENT_TIMESTAMP,
                        rejection_reason = NULL
                    WHERE id = :user_id 
                    AND role IN ('artist', 'service_provider')");

            if ($adminColumn) {
                $db->bind(':admin_id', $adminId);
            }
            $db->bind(':user_id', $userId);
            $ok = $db->execute();
        } catch (Throwable $e) {
            error_log('Admindashboard::approveUser error: ' . $e->getMessage());
            $ok = false;
        }

        if ($ok) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'User approved successfully']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to approve user']);
        }
        exit;
    }

    /**
     * Reject user registration
     */
    public function rejectUser()
    {
        // Check admin access
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Get POST data
        $input = json_decode(file_get_contents('php://input'), true);
        $userId = $input['user_id'] ?? null;
        $reason = $input['reason'] ?? 'No reason provided';

        if (!$userId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User ID is required']);
            exit;
        }

        $db = new Database();
        $adminId = $_SESSION['user_id'];

        try {
            $adminColumn = $this->getVerificationAdminColumn($db);

            $setAdmin = $adminColumn ? ", {$adminColumn} = :admin_id" : '';
            $db->query("UPDATE users 
                    SET 
                        is_verified = 0,
                        verification_status = 'rejected',
                        rejection_reason = :reason{$setAdmin},
                        verified_at = CURRENT_TIMESTAMP
                    WHERE id = :user_id 
                    AND role IN ('artist', 'service_provider')");

            $db->bind(':reason', $reason);
            if ($adminColumn) {
                $db->bind(':admin_id', $adminId);
            }
            $db->bind(':user_id', $userId);
            $ok = $db->execute();
        } catch (Throwable $e) {
            error_log('Admindashboard::rejectUser error: ' . $e->getMessage());
            $ok = false;
        }

        if ($ok) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'User rejected successfully']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to reject user']);
        }
        exit;
    }

    /**
     * Get all users for user management (ADM-01)
     */
    public function getAllUsers()
    {
        // Check admin access
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $db = new Database();
        
        $db->query("SELECT 
                    id,
                    full_name,
                    email,
                    phone,
                    role,
                    is_verified,
                    verification_status,
                    created_at
                FROM users
                WHERE role != 'admin'
                ORDER BY created_at DESC");
        
        $users = $db->resultSet();
        
        // Convert objects to arrays for JSON
        $result = [];
        foreach ($users as $user) {
            $result[] = [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'is_verified' => $user->is_verified,
                'verification_status' => $user->verification_status,
                'created_at' => $user->created_at
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    /**
     * Add a new user (ADM-01)
     * Admin can add artist, audience, or service provider users
     */
    public function addUser()
    {
        // Check admin access
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Get POST data
        $input = json_decode(file_get_contents('php://input'), true);
        
        $fullName = trim($input['full_name'] ?? '');
        $email = trim($input['email'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $role = trim($input['role'] ?? '');
        $password = trim($input['password'] ?? '');

        // Validate required fields
        if (empty($fullName) || empty($email) || empty($role) || empty($password)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Full name, email, role, and password are required']);
            exit;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            exit;
        }

        // Validate role
        $validRoles = ['artist', 'audience', 'service_provider'];
        if (!in_array($role, $validRoles)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid role. Must be artist, audience, or service_provider']);
            exit;
        }

        // Validate password length
        if (strlen($password) < 6) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
            exit;
        }

        $db = new Database();

        // Check if email already exists
        $db->query("SELECT id FROM users WHERE email = :email");
        $db->bind(':email', $email);
        if ($db->single()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Email already exists']);
            exit;
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Users added by admin are automatically verified and approved
        try {
            $adminColumn = $this->getVerificationAdminColumn($db);

            if ($adminColumn) {
                $db->query("INSERT INTO users 
                            (full_name, email, password, phone, role, is_verified, verification_status, created_at, verified_at, {$adminColumn}) 
                            VALUES 
                            (:full_name, :email, :password, :phone, :role, 1, 'approved', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, :admin_id)");
            } else {
                $db->query("INSERT INTO users 
                            (full_name, email, password, phone, role, is_verified, verification_status, created_at, verified_at) 
                            VALUES 
                            (:full_name, :email, :password, :phone, :role, 1, 'approved', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
            }

            $db->bind(':full_name', $fullName);
            $db->bind(':email', $email);
            $db->bind(':password', $hashedPassword);
            $db->bind(':phone', $phone);
            $db->bind(':role', $role);
            if ($adminColumn) {
                $db->bind(':admin_id', $_SESSION['user_id']);
            }

            $ok = $db->execute();
        } catch (Throwable $e) {
            error_log('Admindashboard::addUser error: ' . $e->getMessage());
            $ok = false;
        }

        if ($ok) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'User added successfully']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to add user']);
        }
        exit;
    }

    /**
     * Remove/Delete a user (ADM-01)
     */
    public function removeUser()
    {
        // Check admin access
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Get POST data
        $input = json_decode(file_get_contents('php://input'), true);
        $userId = $input['user_id'] ?? null;

        if (!$userId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User ID is required']);
            exit;
        }

        $db = new Database();

        // Check if user exists and is not an admin
        $db->query("SELECT id, role FROM users WHERE id = :user_id");
        $db->bind(':user_id', $userId);
        $user = $db->single();

        if (!$user) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        if ($user->role === 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Cannot remove admin users']);
            exit;
        }

        // Delete service provider data if applicable
        if ($user->role === 'service_provider') {
            $db->query("DELETE FROM serviceprovider WHERE user_id = :user_id");
            $db->bind(':user_id', $userId);
            $db->execute();
        }

        // Delete the user
        $db->query("DELETE FROM users WHERE id = :user_id AND role != 'admin'");
        $db->bind(':user_id', $userId);

        if ($db->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'User removed successfully']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to remove user']);
        }
        exit;
    }

    /**
     * Get single user details for editing (ADM-02)
     */
    public function getUserDetails()
    {
        // Check admin access
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
        if ($userId <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User ID is required']);
            exit;
        }

        $db = new Database();

        $db->query("SELECT id, full_name, email, phone, role, is_verified, verification_status, created_at 
                    FROM users WHERE id = :user_id AND role != 'admin'");
        $db->bind(':user_id', $userId);
        $user = $db->single();

        if (!$user) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'is_verified' => $user->is_verified,
                'verification_status' => $user->verification_status,
                'created_at' => $user->created_at
            ]
        ]);
        exit;
    }

    /**
     * Update user details (ADM-02)
     */
    public function updateUser()
    {
        // Check admin access
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Get POST data
        $input = json_decode(file_get_contents('php://input'), true);
        
        $userId = $input['user_id'] ?? null;
        $fullName = trim($input['full_name'] ?? '');
        $email = trim($input['email'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $role = trim($input['role'] ?? '');

        if (!$userId || empty($fullName) || empty($email) || empty($role)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User ID, full name, email, and role are required']);
            exit;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            exit;
        }

        // Validate role
        $validRoles = ['artist', 'audience', 'service_provider'];
        if (!in_array($role, $validRoles)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid role']);
            exit;
        }

        $db = new Database();

        // Check if email exists for another user
        $db->query("SELECT id FROM users WHERE email = :email AND id != :user_id");
        $db->bind(':email', $email);
        $db->bind(':user_id', $userId);
        if ($db->single()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Email already exists for another user']);
            exit;
        }

        // Update user
        $db->query("UPDATE users SET 
                    full_name = :full_name,
                    email = :email,
                    phone = :phone,
                    role = :role
                    WHERE id = :user_id AND role != 'admin'");
        
        $db->bind(':full_name', $fullName);
        $db->bind(':email', $email);
        $db->bind(':phone', $phone);
        $db->bind(':role', $role);
        $db->bind(':user_id', $userId);

        if ($db->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'User updated successfully']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to update user']);
        }
        exit;
    }

    /**
     * Get logged-in admin profile details
     */
    public function getAdminProfile()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $db = new Database();
        $db->query("SELECT id, full_name, email, phone FROM users WHERE id = :user_id AND role = 'admin' LIMIT 1");
        $db->bind(':user_id', (int)$_SESSION['user_id']);
        $admin = $db->single();

        if (!$admin) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Admin user not found']);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'admin' => [
                'id' => $admin->id,
                'full_name' => $admin->full_name,
                'email' => $admin->email,
                'phone' => $admin->phone
            ]
        ]);
        exit;
    }

    /**
     * Update logged-in admin profile details
     */
    public function updateAdminProfile()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        $fullName = trim($input['full_name'] ?? '');
        $email = trim($input['email'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $newPassword = trim($input['new_password'] ?? '');
        $confirmPassword = trim($input['confirm_password'] ?? '');

        if ($fullName === '' || $email === '') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Full name and email are required']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            exit;
        }

        if ($newPassword !== '' || $confirmPassword !== '') {
            if (strlen($newPassword) < 6) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters']);
                exit;
            }

            if ($newPassword !== $confirmPassword) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'New password and confirmation do not match']);
                exit;
            }
        }

        $db = new Database();
        $adminId = (int)$_SESSION['user_id'];

        $db->query("SELECT id, password FROM users WHERE id = :user_id AND role = 'admin' LIMIT 1");
        $db->bind(':user_id', $adminId);
        $admin = $db->single();

        if (!$admin) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Admin user not found']);
            exit;
        }

        $db->query("SELECT id FROM users WHERE email = :email AND id != :user_id LIMIT 1");
        $db->bind(':email', $email);
        $db->bind(':user_id', $adminId);
        if ($db->single()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Email is already used by another user']);
            exit;
        }

        $query = "UPDATE users SET full_name = :full_name, email = :email, phone = :phone";
        if ($newPassword !== '') {
            $query .= ", password = :password";
        }
        $query .= " WHERE id = :user_id AND role = 'admin'";

        $db->query($query);
        $db->bind(':full_name', $fullName);
        $db->bind(':email', $email);
        $db->bind(':phone', $phone);
        $db->bind(':user_id', $adminId);
        if ($newPassword !== '') {
            $db->bind(':password', password_hash($newPassword, PASSWORD_DEFAULT));
        }

        if ($db->execute()) {
            $_SESSION['user_name'] = $fullName;
            $_SESSION['full_name'] = $fullName;
            $_SESSION['email'] = $email;
            $_SESSION['phone'] = $phone;

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Profile saved successfully']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
        }
        exit;
    }

    // ===================================
    // CONTENT MANAGEMENT METHODS
    // ===================================

    /**
     * Get all swiper slides
     */
    public function getSwiperSlides()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $db = new Database();
        try {
            $db->query("SHOW COLUMNS FROM swiper_slides");
            $columns = $db->resultSet();

            $hasDramaId = $this->hasColumnFromList($columns, 'drama_id');
            $hasDescription = $this->hasColumnFromList($columns, 'description');
            $hasUpdatedAt = $this->hasColumnFromList($columns, 'updated_at');

            // Ensure newly published dramas appear in admin content slides for hide/show moderation.
            try {
                $this->syncPublishedDramasToSwiper($db, $hasDramaId, $hasDescription, $hasUpdatedAt);
            } catch (Throwable $e) {
                error_log('Admindashboard::getSwiperSlides sync warning: ' . $e->getMessage());
            }

            if ($hasDramaId) {
                $db->query("SELECT s.*, d.drama_name AS linked_drama_name
                        FROM swiper_slides s
                        LEFT JOIN dramas d ON s.drama_id = d.id
                        ORDER BY s.display_order ASC");
                $slides = $db->resultSet();
            } else {
                $db->query("SELECT s.*, NULL AS linked_drama_name
                        FROM swiper_slides s
                        ORDER BY s.display_order ASC");
                $slides = $db->resultSet();
            }
        } catch (Exception $e) {
            error_log('Error loading swiper slides: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Failed to load slides']);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode($slides);
        exit;
    }

    /**
     * Get published dramas with poster image for Add Slide dropdown
     */
    public function getPublishedDramasForSlides()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $db = new Database();
        try {
            $db->query("SHOW COLUMNS FROM swiper_slides");
            $columns = $db->resultSet();
            $hasDramaId = $this->hasColumnFromList($columns, 'drama_id');

            if ($hasDramaId) {
                $db->query("SELECT d.id,
                                   d.drama_name,
                                   d.poster_image,
                                   s.id AS slide_id,
                                   s.is_active AS slide_is_active
                            FROM dramas d
                            LEFT JOIN swiper_slides s ON s.drama_id = d.id
                            WHERE d.is_published = 1
                              AND d.poster_image IS NOT NULL
                              AND TRIM(d.poster_image) <> ''
                            ORDER BY d.drama_name ASC");
            } else {
                $db->query("SELECT d.id,
                                   d.drama_name,
                                   d.poster_image,
                                   NULL AS slide_id,
                                   NULL AS slide_is_active
                            FROM dramas d
                            WHERE d.is_published = 1
                              AND d.poster_image IS NOT NULL
                              AND TRIM(d.poster_image) <> ''
                            ORDER BY d.drama_name ASC");
            }

            $rows = $db->resultSet();
            $result = [];
            foreach ($rows as $row) {
                $result[] = [
                    'id' => (int)$row->id,
                    'drama_name' => (string)$row->drama_name,
                    'poster_image' => $this->normalizeSlideImagePath((string)($row->poster_image ?? '')),
                    'already_added' => !empty($row->slide_id),
                    'slide_is_active' => isset($row->slide_is_active) ? (int)$row->slide_is_active : null,
                ];
            }

            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        } catch (Throwable $e) {
            error_log('Error loading published dramas for slides: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Failed to load published dramas']);
            exit;
        }
    }

    /**
     * Add a new swiper slide
     */
    public function addSwiperSlide()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $title = trim($_POST['title'] ?? '');
        $dramaId = isset($_POST['drama_id']) ? (int)$_POST['drama_id'] : 0;

        if ($dramaId > 0) {
            $db = new Database();

            try {
                $db->query("SHOW COLUMNS FROM swiper_slides");
                $columns = $db->resultSet();
                $columnMap = [];
                foreach ($columns as $column) {
                    $columnMap[strtolower((string)$column->Field)] = true;
                }

                $hasDramaId = isset($columnMap['drama_id']);
                $hasDescription = isset($columnMap['description']);
                $hasUpdatedAt = isset($columnMap['updated_at']);

                $db->query("SELECT id, drama_name, poster_image
                            FROM dramas
                            WHERE id = :id
                              AND is_published = 1
                            LIMIT 1");
                $db->bind(':id', $dramaId);
                $drama = $db->single();

                if (!$drama || empty($drama->poster_image)) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Selected drama poster is not available']);
                    exit;
                }

                $imagePath = $this->normalizeSlideImagePath((string)$drama->poster_image);
                $slideTitle = $title !== '' ? $title : (string)$drama->drama_name;

                if ($hasDramaId) {
                    $db->query("SELECT id FROM swiper_slides WHERE drama_id = :drama_id LIMIT 1");
                    $db->bind(':drama_id', $dramaId);
                    $existing = $db->single();

                    if ($existing) {
                        $setParts = ['image_path = :image_path', 'title = :title'];
                        if ($hasDescription) {
                            $setParts[] = 'description = :description';
                        }
                        if ($hasUpdatedAt) {
                            $setParts[] = 'updated_at = CURRENT_TIMESTAMP';
                        }

                        $db->query("UPDATE swiper_slides SET " . implode(', ', $setParts) . " WHERE id = :id");
                        $db->bind(':image_path', $imagePath);
                        $db->bind(':title', $slideTitle);
                        if ($hasDescription) {
                            $db->bind(':description', 'Submitted by director for home page approval');
                        }
                        $db->bind(':id', (int)$existing->id);

                        if ($db->execute()) {
                            header('Content-Type: application/json');
                            echo json_encode(['success' => true, 'message' => 'Drama slide updated successfully']);
                        } else {
                            header('Content-Type: application/json');
                            echo json_encode(['success' => false, 'message' => 'Failed to update drama slide']);
                        }
                        exit;
                    }
                }

                $insertColumns = ['image_path', 'title', 'display_order'];
                $insertValues = [':image_path', ':title', '(SELECT COALESCE(MAX(display_order), 0) + 1 FROM swiper_slides s)'];

                if ($hasDescription) {
                    $insertColumns[] = 'description';
                    $insertValues[] = ':description';
                }

                if ($hasDramaId) {
                    $insertColumns[] = 'drama_id';
                    $insertValues[] = ':drama_id';
                }

                $insertColumns[] = 'is_active';
                $insertValues[] = '0';

                $db->query("INSERT INTO swiper_slides (" . implode(', ', $insertColumns) . ") VALUES (" . implode(', ', $insertValues) . ")");
                $db->bind(':image_path', $imagePath);
                $db->bind(':title', $slideTitle);
                if ($hasDescription) {
                    $db->bind(':description', 'Submitted by director for home page approval');
                }
                if ($hasDramaId) {
                    $db->bind(':drama_id', $dramaId);
                }

                if ($db->execute()) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Drama slide added successfully']);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Failed to save drama slide']);
                }
                exit;
            } catch (Throwable $e) {
                error_log('Error in addSwiperSlide (drama mode): ' . $e->getMessage());
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to add drama slide']);
                exit;
            }
        }

        // Handle file upload
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Please select a drama or upload an image']);
            exit;
        }

        $uploadDir = '../public/uploads/content/swiper/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid('swiper_') . '_' . basename($_FILES['image']['name']);
        $filePath = $uploadDir . $fileName;
        $dbPath = 'uploads/content/swiper/' . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
            $db = new Database();
            $db->query("INSERT INTO swiper_slides (image_path, title, display_order) 
                        VALUES (:image_path, :title, (SELECT COALESCE(MAX(display_order), 0) + 1 FROM swiper_slides s))");
            $db->bind(':image_path', $dbPath);
            $db->bind(':title', $title);

            if ($db->execute()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Slide added successfully']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to save slide']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
        }
        exit;
    }

    /**
     * Delete a swiper slide
     */
    public function deleteSwiperSlide()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $slideId = $input['id'] ?? null;

        if (!$slideId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Slide ID required']);
            exit;
        }

        $db = new Database();
        $db->query("DELETE FROM swiper_slides WHERE id = :id");
        $db->bind(':id', $slideId);

        if ($db->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Slide deleted']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to delete slide']);
        }
        exit;
    }

    /**
     * Get all gallery images
     */
    public function getGalleryImages()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $db = new Database();
        $db->query("SELECT * FROM gallery_images ORDER BY display_order ASC");
        $images = $db->resultSet();

        header('Content-Type: application/json');
        echo json_encode($images);
        exit;
    }

    /**
     * Add a new gallery image
     */
    public function addGalleryImage()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $title = trim($_POST['title'] ?? '');
        
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Please upload an image']);
            exit;
        }

        $uploadDir = '../public/uploads/content/gallery/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid('gallery_') . '_' . basename($_FILES['image']['name']);
        $filePath = $uploadDir . $fileName;
        $dbPath = 'uploads/content/gallery/' . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
            $db = new Database();
            $db->query("INSERT INTO gallery_images (image_path, title, alt_text, display_order) 
                        VALUES (:image_path, :title, :alt_text, (SELECT COALESCE(MAX(display_order), 0) + 1 FROM gallery_images g))");
            $db->bind(':image_path', $dbPath);
            $db->bind(':title', $title);
            $db->bind(':alt_text', $title);

            if ($db->execute()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Image added successfully']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to save image']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
        }
        exit;
    }

    /**
     * Delete a gallery image
     */
    public function deleteGalleryImage()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $imageId = $input['id'] ?? null;

        if (!$imageId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Image ID required']);
            exit;
        }

        $db = new Database();
        $db->query("DELETE FROM gallery_images WHERE id = :id");
        $db->bind(':id', $imageId);

        if ($db->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Image deleted']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to delete image']);
        }
        exit;
    }

    /**
     * Get all testimonials
     */
    public function getTestimonials()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $db = new Database();
        $db->query("SELECT * FROM testimonials ORDER BY display_order ASC");
        $testimonials = $db->resultSet();

        header('Content-Type: application/json');
        echo json_encode($testimonials);
        exit;
    }

    /**
     * Add a new testimonial
     */
    public function addTestimonial()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $rating = intval($_POST['rating'] ?? 5);

        if (empty($name) || empty($role) || empty($message)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Name, role and message are required']);
            exit;
        }

        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../public/uploads/content/testimonials/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = uniqid('testimonial_') . '_' . basename($_FILES['image']['name']);
            $filePath = $uploadDir . $fileName;
            $imagePath = 'uploads/content/testimonials/' . $fileName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                $imagePath = null;
            }
        }

        $db = new Database();
        $db->query("INSERT INTO testimonials (name, role, message, image_path, rating, display_order) 
                    VALUES (:name, :role, :message, :image_path, :rating, 
                    (SELECT COALESCE(MAX(display_order), 0) + 1 FROM testimonials t))");
        $db->bind(':name', $name);
        $db->bind(':role', $role);
        $db->bind(':message', $message);
        $db->bind(':image_path', $imagePath);
        $db->bind(':rating', $rating);

        if ($db->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Testimonial added successfully']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to add testimonial']);
        }
        exit;
    }

    /**
     * Delete a testimonial
     */
    public function deleteTestimonial()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $testimonialId = $input['id'] ?? null;

        if (!$testimonialId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Testimonial ID required']);
            exit;
        }

        $db = new Database();
        $db->query("DELETE FROM testimonials WHERE id = :id");
        $db->bind(':id', $testimonialId);

        if ($db->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Testimonial deleted']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to delete testimonial']);
        }
        exit;
    }

    /**
     * Toggle content item status (active/inactive)
     */
    public function toggleContentStatus()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $type = $input['type'] ?? '';
        $id = $input['id'] ?? null;
        $isActive = $input['is_active'] ?? 1;

        $tables = [
            'swiper' => 'swiper_slides',
            'gallery' => 'gallery_images',
            'testimonial' => 'testimonials'
        ];

        if (!isset($tables[$type]) || !$id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        $db = new Database();
        $db->query("UPDATE {$tables[$type]} SET is_active = :is_active WHERE id = :id");
        $db->bind(':is_active', $isActive);
        $db->bind(':id', $id);

        if ($db->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Status updated']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
        exit;
    }
}
?>