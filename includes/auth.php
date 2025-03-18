<?php
require_once 'db.php';

class Auth {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // User login function
    public function login($identifier, $password) {
        $query = "SELECT * FROM users WHERE email = :identifier OR username = :identifier";
        $user = $this->db->fetch($query, ['identifier' => $identifier]);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            return true;
        }

        return false;
    }

    // User logout function
    public function logout() {
        session_unset();
        session_destroy();
    }

    // User registration function
    public function register($username, $email, $password, $role = 'standard') {
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
