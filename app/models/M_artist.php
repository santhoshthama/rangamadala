<?php

class M_artist extends M_signup {
    public function register($full_name, $email, $password, $phone, $nic_photo = null) {
        return $this->registerUser($full_name, $email, $password, $phone, 'artist', $nic_photo);
    }
}
