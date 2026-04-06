<?php
class Admindashboard {
    use Controller;

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
                    u.nic_photo,
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
        $db->query("SELECT id, full_name, email, phone, role, nic_photo, created_at, verification_status 
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
            $db->query("SELECT full_name, professional_title, email, phone, location, nic_number,
                               social_media_link, years_experience, professional_summary,
                               availability, availability_notes, nic_photo_front, nic_photo_back
                        FROM serviceprovider WHERE user_id = :user_id");
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
        
        $db->query("UPDATE users 
                SET 
                    is_verified = 1,
                    verification_status = 'approved',
                    verified_by_admin_id = :admin_id,
                    verified_at = CURRENT_TIMESTAMP,
                    rejection_reason = NULL
                WHERE id = :user_id 
                AND role IN ('artist', 'service_provider')");
        
        $db->bind(':admin_id', $adminId);
        $db->bind(':user_id', $userId);
        
        if ($db->execute()) {
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
        
        $db->query("UPDATE users 
                SET 
                    is_verified = 0,
                    verification_status = 'rejected',
                    rejection_reason = :reason,
                    verified_by_admin_id = :admin_id,
                    verified_at = CURRENT_TIMESTAMP
                WHERE id = :user_id 
                AND role IN ('artist', 'service_provider')");
        
        $db->bind(':reason', $reason);
        $db->bind(':admin_id', $adminId);
        $db->bind(':user_id', $userId);
        
        if ($db->execute()) {
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
        $db->query("INSERT INTO users 
                    (full_name, email, password, phone, role, is_verified, verification_status, created_at, verified_at, verified_by_admin_id) 
                    VALUES 
                    (:full_name, :email, :password, :phone, :role, 1, 'approved', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, :admin_id)");
        
        $db->bind(':full_name', $fullName);
        $db->bind(':email', $email);
        $db->bind(':password', $hashedPassword);
        $db->bind(':phone', $phone);
        $db->bind(':role', $role);
        $db->bind(':admin_id', $_SESSION['user_id']);

        if ($db->execute()) {
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
        $db->query("SELECT * FROM swiper_slides ORDER BY display_order ASC");
        $slides = $db->resultSet();

        header('Content-Type: application/json');
        echo json_encode($slides);
        exit;
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
        
        // Handle file upload
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Please upload an image']);
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