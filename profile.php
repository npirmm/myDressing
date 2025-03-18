<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/2fa.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$db = new Database();
$auth = new Auth();
$twoFactorAuth = new TwoFactorAuth();

$user_id = $_SESSION['user_id'];
$user = $db->fetch("SELECT * FROM users WHERE id = :id", ['id' => $user_id]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_email'])) {
        $new_email = $_POST['email'];
        $db->executeQuery("UPDATE users SET email = :email WHERE id = :id", [
            'email' => $new_email,
            'id' => $user_id
        ]);
        $user['email'] = $new_email;
    }

    if (isset($_POST['update_password'])) {
        $new_password = $_POST['password'];
        $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);
        $db->executeQuery("UPDATE users SET password = :password WHERE id = :id", [
            'password' => $hashedPassword,
            'id' => $user_id
        ]);
    }

    if (isset($_POST['enable_2fa'])) {
        $method = $_POST['2fa_method'];
        if ($method === 'otp') {
            $secret = $twoFactorAuth->generateOTPSecret();
            $db->executeQuery("UPDATE users SET 2fa_enabled = 1, 2fa_method = 'otp', 2fa_secret = :secret WHERE id = :id", [
                'secret' => $secret,
                'id' => $user_id
            ]);
            $user['2fa_enabled'] = 1;
            $user['2fa_method'] = 'otp';
            $user['2fa_secret'] = $secret;
        } elseif ($method === 'email') {
            $db->executeQuery("UPDATE users SET 2fa_enabled = 1, 2fa_method = 'email' WHERE id = :id", [
                'id' => $user_id
            ]);
            $user['2fa_enabled'] = 1;
            $user['2fa_method'] = 'email';
        }
    }

    if (isset($_POST['disable_2fa'])) {
        $db->executeQuery("UPDATE users SET 2fa_enabled = 0, 2fa_method = NULL, 2fa_secret = NULL WHERE id = :id", [
            'id' => $user_id
        ]);
        $user['2fa_enabled'] = 0;
        $user['2fa_method'] = NULL;
        $user['2fa_secret'] = NULL;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>User Profile</h1>
        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                <button type="submit" name="update_email" class="btn btn-primary">Update Email</button>
            </div>
        </form>
        <form method="POST">
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
            </div>
        </form>
        <form method="POST">
            <div class="form-group">
                <label for="2fa_method">Two-Factor Authentication</label>
                <select class="form-control" id="2fa_method" name="2fa_method" required>
                    <option value="otp" <?php echo $user['2fa_method'] === 'otp' ? 'selected' : ''; ?>>OTP</option>
                    <option value="email" <?php echo $user['2fa_method'] === 'email' ? 'selected' : ''; ?>>Email</option>
                </select>
                <button type="submit" name="enable_2fa" class="btn btn-primary">Enable 2FA</button>
                <button type="submit" name="disable_2fa" class="btn btn-danger">Disable 2FA</button>
            </div>
        </form>
        <?php if ($user['2fa_method'] === 'otp' && $user['2fa_secret']): ?>
            <div class="form-group">
                <label for="otp_qr">OTP QR Code</label>
                <img src="<?php echo $twoFactorAuth->getQRCodeURL($user['username'], $user['2fa_secret']); ?>" alt="OTP QR Code">
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
