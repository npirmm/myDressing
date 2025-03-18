<?php
require_once 'db.php';
require_once 'vendor/autoload.php'; // Assuming you are using a library like PHPGangsta/GoogleAuthenticator

class TwoFactorAuth {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Function to generate OTP secret
    public function generateOTPSecret() {
        $g = new \Google\Authenticator\GoogleAuthenticator();
        return $g->generateSecret();
    }

    // Function to get QR code URL for OTP
    public function getQRCodeURL($username, $secret) {
        $g = new \Google\Authenticator\GoogleAuthenticator();
        return $g->getQRCodeGoogleUrl($username, $secret);
    }

    // Function to verify OTP code
    public function verifyOTP($secret, $code) {
        $g = new \Google\Authenticator\GoogleAuthenticator();
        return $g->verifyCode($secret, $code, 2); // 2 = 2*30sec clock tolerance
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

        return false;
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
