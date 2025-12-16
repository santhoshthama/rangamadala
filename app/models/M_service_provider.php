<?php

class M_service_provider extends M_signup {
    public function register($full_name, $email, $password, $phone, $nic_photo = null) {
        return $this->registerUser($full_name, $email, $password, $phone, 'service_provider', $nic_photo);
    }
}
