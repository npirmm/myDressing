<?php
require_once 'includes/auth.php';

session_start();

if (!isset($_SESSION['pending_otp_user_id'])) {
    header('Location: index.php');
    exit();
}

$auth = new Auth();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otpCode = $_POST['otp_code'];
    $userId = $_SESSION['pending_otp_user_id'];

    if ($auth->verifyOTP($userId, $otpCode)) {
        $_SESSION['user_id'] = $userId;
        unset($_SESSION['pending_otp_user_id']);
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Invalid OTP. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">OTP Verification</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="otp_code">Enter OTP</label>
                <input type="text" class="form-control" id="otp_code" name="otp_code" required>
            </div>
            <button type="submit" class="btn btn-primary">Verify</button>
        </form>
    </div>
</body>
</html>
