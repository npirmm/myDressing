<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/otp.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$db = new Database();
$auth = new Auth();
$otp = new OTP();

$user_id = $_SESSION['user_id'];
$user = $db->fetch("SELECT * FROM users WHERE id = :id", ['id' => $user_id]);
$qrCodeImage = null;

$is2FAEnabled = $auth->is2FAEnabled($user_id); // Check if 2FA is enabled
$otpSecret = null;
$qrCodeUrl = null;

if (!$is2FAEnabled) {
    $otpSecret = $otp->generateSecret();
    $qrCodeUrl = $otp->getQRCodeUrl('MyDressing', $otpSecret);
    $_SESSION['otp_secret'] = $otpSecret; // Store secret temporarily
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_email'])) {
        $new_email = $_POST['email'];
        $db->executeQuery("UPDATE users SET email = :email WHERE id = :id", [
            'email' => $new_email,
            'id' => $user_id
        ]);
        $user['email'] = $new_email;
        echo "<script>alert('Email updated successfully.');</script>";
    }

    if (isset($_POST['update_password'])) {
        $new_password = $_POST['password'];
        $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);
        $db->executeQuery("UPDATE users SET password = :password WHERE id = :id", [
            'password' => $hashedPassword,
            'id' => $user_id
        ]);
        echo "<script>alert('Password updated successfully.');</script>";
    }

    if (isset($_POST['enable_2fa'])) {
        if (!$is2FAEnabled && isset($_SESSION['otp_secret'])) {
            $auth->enable2FA($user_id, $_SESSION['otp_secret']);
            unset($_SESSION['otp_secret']);
            header('Location: profile.php');
            exit();
        }
    }

    if (isset($_POST['disable_2fa'])) {
        $db->executeQuery("UPDATE users SET 2fa_enabled = 0, 2fa_method = NULL, 2fa_secret = NULL WHERE id = :id", [
            'id' => $user_id
        ]);
        $user['2fa_enabled'] = 0;
        $user['2fa_method'] = NULL;
        $user['2fa_secret'] = NULL;
        echo "<script>alert('2FA disabled.');</script>";
    }

    if (isset($_POST['test_otp'])) {
        $enteredCode = $_POST['otp_code'];
        $isValid = $otp->verifyCode($user['2fa_secret'], $enteredCode);
        if ($isValid) {
            echo '<p class="text-success">OTP is valid!</p>';
        } else {
            echo '<p class="text-danger">Invalid OTP. Please try again.</p>';
        }
    }

    if (isset($_POST['logout'])) {
        session_destroy();
        header('Location: index.php');
        exit();
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <a href="dashboard.php" class="btn btn-primary mb-4">Back to Dashboard</a>
        <h1 class="mb-4">User Profile</h1>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Update Email</h5>
                <form method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <button type="submit" name="update_email" class="btn btn-primary">Update Email</button>
                </form>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Update Password</h5>
                <form method="POST">
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
                </form>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Two-Factor Authentication</h5>
                <form method="POST">
                    <div class="form-group">
                        <label for="2fa_method">2FA Method</label>
                        <select class="form-control" id="2fa_method" name="2fa_method" required>
                            <option value="otp" <?php echo $user['2fa_method'] === 'otp' ? 'selected' : ''; ?>>OTP</option>
                            <option value="email" <?php echo $user['2fa_method'] === 'email' ? 'selected' : ''; ?>>Email</option>
                        </select>
                    </div>
                    <button type="submit" name="enable_2fa" class="btn btn-primary" 
                        <?php echo $is2FAEnabled ? 'disabled' : ''; ?>>Enable 2FA</button>
                    <button type="submit" name="disable_2fa" class="btn btn-danger" 
                        <?php echo !$is2FAEnabled ? 'disabled' : ''; ?>>Disable 2FA</button>
                </form>
                <div>
                    <h3>Two-Factor Authentication (2FA)</h3>
                    <label>
                        2FA Status:
                        <input type="checkbox" disabled <?= $is2FAEnabled ? 'checked' : '' ?>>
                    </label>
                    <?php if (!$is2FAEnabled): ?>
                        <h6>OTP Secret</h6>
                        <p><strong><?php echo htmlspecialchars($otpSecret); ?></strong></p>
                        <h6>OTP QR Code</h6>
                        <p>Scan this QR code to enable 2FA:</p>
                        <img src="<?= htmlspecialchars($qrCodeUrl); ?>" alt="QR Code">
                    <?php else: ?>
                        <p>2FA is enabled for your account.</p>
                    <?php endif; ?>
                </div>
                <?php if ($user['2fa_method'] === 'otp' && $user['2fa_secret']): ?>
                    <div class="mt-4">
                        <form method="POST" class="mt-4">
                            <label for="otp_code">Enter OTP from your Authenticator app:</label>
                            <input type="text" name="otp_code" id="otp_code" class="form-control" required>
                            <button type="submit" name="test_otp" class="btn btn-primary mt-2">Test OTP</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
