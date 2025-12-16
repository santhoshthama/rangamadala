<?php

class M_audience extends M_signup {
    public function register($full_name, $email, $password, $confirm_password, $phone) {

        if ($password !== $confirm_password) {
            return false;
        }

        return $this->registerUser($full_name, $email, $password, $phone, 'audience');
    }
}


