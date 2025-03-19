<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Adjusted path to autoload.php

use RobThree\Auth\TwoFactorAuth;

class OTP {
    private $tfa;

    public function __construct() {
        $this->tfa = new TwoFactorAuth('MyDressing');
    }

    public function generateSecret() {
        return $this->tfa->createSecret();
    }

    public function getQRCodeUrl($label, $secret) {
        return $this->tfa->getQRCodeImageAsDataUri($label, $secret);
    }

    public function verifyCode($secret, $code) {
        return $this->tfa->verifyCode($secret, $code);
    }
}
?>
