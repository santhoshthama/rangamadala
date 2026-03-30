<?php

class M_signup {

    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function registerUser($full_name, $email, $password, $phone, $role, $nic_photo = null) {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $this->db->query("INSERT INTO users 
            (full_name, email, password, phone, nic_photo, role) 
            VALUES 
            (:full_name, :email, :password, :phone, :nic_photo, :role)");

        $this->db->bind(':full_name', $full_name);
        $this->db->bind(':email', $email);
        $this->db->bind(':password', $hashedPassword);
        $this->db->bind(':phone', $phone);
        $this->db->bind(':nic_photo', $nic_photo);
        $this->db->bind(':role', $role);

        return $this->db->execute();
    }
}
?>
