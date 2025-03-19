<?php
require_once 'db.php';

class Auth {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function login($usernameOrEmail, $password, $otp = null) {
        $query = "SELECT id, password, role, 2fa_enabled, 2fa_method, 2fa_secret FROM users WHERE username = :username OR email = :email";
        $user = $this->db->fetch($query, [
            'username' => $usernameOrEmail,
            'email' => $usernameOrEmail
        ]);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['2fa_enabled'] && $user['2fa_method'] === 'otp') {
                $twoFactorAuth = new TwoFactorAuth();
                if (!$otp || !$twoFactorAuth->verifyOTP($user['2fa_secret'], $otp)) {
                    return ['success' => false, 'message' => 'Invalid OTP.'];
                }
            }

            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            return ['success' => true, 'message' => 'Authentication successful.'];
        }

        return ['success' => false, 'message' => 'Invalid username or password.'];
    }

    public function checkRole($requiredRole) {
        session_start();
        return isset($_SESSION['role']) && $_SESSION['role'] === $requiredRole;
    }

    public function logout() {
        session_start();
        session_destroy();
    }

    // User registration function
    public function register($username, $email, $password, $role = 'standard') {
        // Check if username or email already exists
        $query = "SELECT COUNT(*) FROM users WHERE username = :username OR email = :email";
        $exists = $this->db->fetch($query, [
            'username' => $username,
            'email' => $email
        ]);

        if ($exists['COUNT(*)'] > 0) {
            throw new Exception("Username or email already exists.");
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO users (username, email, password, role, created_at) VALUES (:username, :email, :password, :role, NOW())";
        return $this->db->executeQuery($query, [
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role
        ]);
    }

    // Password reset function
    public function resetPassword($email, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $query = "UPDATE users SET password = :password WHERE email = :email";
        return $this->db->executeQuery($query, [
            'password' => $hashedPassword,
            'email' => $email
        ]);
    }

    // Function to check if a user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    // Function to get the logged-in user's role
    public function getUserRole() {
        return $_SESSION['role'] ?? null;
    }
}
?>
