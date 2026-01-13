<?php

class User
{
    private $conn;

    public function __construct()
    {
        $this->conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    }

    public function getUserByEmail($email)
    {
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($data)
    {
        $query = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }
}
