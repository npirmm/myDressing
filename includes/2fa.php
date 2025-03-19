<?php
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die('Error: Composer dependencies are not installed. Please run "composer install".');
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once 'db.php';

use OTPHP\TOTP;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class TwoFactorAuth {
    private $db;
    private $timezone;

    public function __construct() {
        $this->db = new Database();
        $this->timezone = new DateTimeZone('Europe/Brussels');
    }

    // Function to generate OTP secret
    public function generateOTPSecret() {
        return TOTP::create()->getSecret();
    }

    // Function to get QR code URL for OTP
    public function getQRCodeURL($username, $secret) {
        $totp = TOTP::create($secret);
        $totp->setLabel($username);
        return $totp->getProvisioningUri();
    }

    // Function to get QR code image for OTP
    public function getQRCodeImage($username, $secret) {
        $totp = TOTP::create($secret);
        $totp->setLabel($username);
        $totp->setIssuer('MyDressing');
        $provisioningUri = $totp->getProvisioningUri();

        $qrCode = QrCode::create($provisioningUri);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Return the QR code as a base64-encoded image
        return 'data:image/png;base64,' . base64_encode($result->getString());
    }

    // Function to verify OTP code
    public function verifyOTP($secret, $code) {
        $totp = TOTP::create($secret);
        $totp->setLabel('MyDressing');
        $totp->setIssuer('MyDressing');
        $totp->setTimezone($this->timezone); // Set timezone to Brussels/Europe
        return $totp->verify($code);
    }

    // Function to send 2FA code via email
    public function sendEmailCode($email) {
        $code = rand(100000, 999999);
        $subject = "Your 2FA Code";
        $message = "Your 2FA code is: $code";
        $headers = "From: no-reply@mydressing.com";

        if (mail($email, $subject, $message, $headers)) {
            $query = "UPDATE users SET 2fa_code = :code, 2fa_code_expiry = DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE email = :email";
            $this->db->executeQuery($query, [
                'code' => $code,
                'email' => $email
            ]);
            return true;
        }

        throw new Exception("Failed to send email.");
    }

    // Function to verify email code
    public function verifyEmailCode($email, $code) {
        $query = "SELECT 2fa_code, 2fa_code_expiry FROM users WHERE email = :email";
        $result = $this->db->fetch($query, ['email' => $email]);

        if ($result && $result['2fa_code'] == $code && strtotime($result['2fa_code_expiry']) > time()) {
            return true;
        }

        return false;
    }

    // Function to activate 2FA
    public function activate2FA($userId, $method, $secret = null) {
        $query = "SELECT email_verified FROM users WHERE id = :userId";
        $result = $this->db->fetch($query, ['userId' => $userId]);

        if (!$result || !$result['email_verified']) {
            throw new Exception("Email must be verified before activating 2FA.");
        }

        $query = "UPDATE users SET 2fa_enabled = 1, 2fa_method = :method, 2fa_secret = :secret WHERE id = :userId";
        $this->db->executeQuery($query, [
            'method' => $method,
            'secret' => $secret,
            'userId' => $userId
        ]);
    }

    // Function to deactivate 2FA
    public function deactivate2FA($userId) {
        $query = "UPDATE users SET 2fa_enabled = 0, 2fa_method = NULL, 2fa_secret = NULL WHERE id = :userId";
        $this->db->executeQuery($query, ['userId' => $userId]);
    }

    // Function to check if 2FA is enabled
    public function is2FAEnabled($userId) {
        $query = "SELECT 2fa_enabled FROM users WHERE id = :userId";
        $result = $this->db->fetch($query, ['userId' => $userId]);
        return $result && $result['2fa_enabled'];
    }
}
?>
