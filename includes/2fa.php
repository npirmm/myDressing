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
}
?>
