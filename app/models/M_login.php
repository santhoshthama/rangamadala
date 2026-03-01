<?php 

class M_login{
    private $db = null;
    public function __construct()
    {
        $this->db = new Database();
    }

    public function authenticate($email, $password){
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(":email", $email);
        $user = $this->db->single();
        if ($user && password_verify($password, $user->password)){
            return $user;
        }
        return false;
    }

    public function getUserById($user_id){
        $this->db->query("SELECT * FROM users WHERE id = :id");
        $this->db->bind(":id", $user_id);
        return $this->db->single();
    }
}


?>