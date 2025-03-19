<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Adjusted path to autoload.php

use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\BaconQrCodeProvider;

class OTP {
    private $tfa;

    public function __construct() {
        // Ensure the QR code provider is correctly instantiated
        $qrCodeProvider = new BaconQrCodeProvider();
        $this->tfa = new TwoFactorAuth('MyDressing', 6, 30, 'sha1', $qrCodeProvider);
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
