<?php

class M_audience {
    protected $db;
    public function __construct() {
        $this->db = new Database();
    }

    public function getProfile($user_id) {
        $this->db->query("SELECT * FROM users WHERE id = :id AND role = 'audience'");
        $this->db->bind(":id", $user_id);
        return $this->db->single();
    }

    public function getBio($user_id) {
        try {
            $this->db->query("SELECT * FROM user_bios WHERE user_id = :user_id");
            $this->db->bind(":user_id", $user_id);
            $result = $this->db->single();
            return $result ? $result->bio : null;
        } catch (Exception $e) {
            // If user_bios table doesn't exist, return null
            return null;
        }
    }

    public function getSignupDetails($user_id) {
        $this->db->query("SELECT full_name, email, phone, nic_photo FROM users WHERE id = :id AND role = 'audience'");
        $this->db->bind(":id", $user_id);
        return $this->db->single();
    }

    public function register($full_name, $email, $password, $confirm_password, $phone) {
        // Validate inputs
        if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password) || empty($phone)) {
            return false;
        }

        // Check if passwords match
        if ($password !== $confirm_password) {
            return false;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Check if email already exists
        $this->db->query("SELECT id FROM users WHERE email = :email");
        $this->db->bind(":email", $email);
        if ($this->db->single()) {
            return false; // Email already exists
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new audience user
        $this->db->query("INSERT INTO users 
            (full_name, email, password, phone, role) 
            VALUES 
            (:full_name, :email, :password, :phone, :role)");

        $this->db->bind(':full_name', $full_name);
        $this->db->bind(':email', $email);
        $this->db->bind(':password', $hashedPassword);
        $this->db->bind(':phone', $phone);
        $this->db->bind(':role', 'audience');

        return $this->db->execute();
    }

    public function updateProfile($user_id, $data) {
        // Update basic profile information
        $this->db->query("UPDATE users SET 
            full_name = :full_name,
            email = :email,
            phone = :phone
            WHERE id = :id AND role = 'audience'");

        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':id', $user_id);

        return $this->db->execute();
    }

    public function updateBio($user_id, $bio) {
        // Check if bio exists
        $this->db->query("SELECT id FROM user_bios WHERE user_id = :user_id");
        $this->db->bind(":user_id", $user_id);
        $exists = $this->db->single();

        if ($exists) {
            // Update existing bio
            $this->db->query("UPDATE user_bios SET bio = :bio WHERE user_id = :user_id");
        } else {
            // Insert new bio
            $this->db->query("INSERT INTO user_bios (user_id, bio) VALUES (:user_id, :bio)");
        }

        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':bio', $bio);

        return $this->db->execute();
    }

    public function updateProfileImage($user_id, $image_path) {
        // Check if bio record exists
        $this->db->query("SELECT id FROM user_bios WHERE user_id = :user_id");
        $this->db->bind(":user_id", $user_id);
        $exists = $this->db->single();

        if ($exists) {
            // Update existing record
            $this->db->query("UPDATE user_bios SET profile_image = :profile_image WHERE user_id = :user_id");
        } else {
            // Insert new record
            $this->db->query("INSERT INTO user_bios (user_id, profile_image) VALUES (:user_id, :profile_image)");
        }

        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':profile_image', $image_path);
        return $this->db->execute();
    }

    public function getProfileImage($user_id) {
        try {
            $this->db->query("SELECT profile_image FROM user_bios WHERE user_id = :user_id");
            $this->db->bind(":user_id", $user_id);
            $result = $this->db->single();
            return $result ? $result->profile_image : null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function checkEmailExists($email, $user_id) {
        $this->db->query("SELECT id FROM users WHERE email = :email AND id != :user_id");
        $this->db->bind(":email", $email);
        $this->db->bind(":user_id", $user_id);
        return $this->db->single() ? true : false;
    }
}
