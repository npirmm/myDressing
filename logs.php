<?php
require_once 'includes/db.php';

class Logs {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Function to add a log entry
    public function addLog($user_id, $action, $details) {
        $query = "INSERT INTO logs (user_id, action, details, created_at) VALUES (:user_id, :action, :details, NOW())";
        return $this->db->executeQuery($query, [
            'user_id' => $user_id,
            'action' => $action,
            'details' => $details
        ]);
    }

    // Function to retrieve all logs
    public function getAllLogs() {
        $query = "SELECT * FROM logs ORDER BY created_at DESC";
        return $this->db->fetchAll($query);
    }

    // Function to retrieve logs by user
    public function getLogsByUser($user_id) {
        $query = "SELECT * FROM logs WHERE user_id = :user_id ORDER BY created_at DESC";
        return $this->db->fetchAll($query, ['user_id' => $user_id]);
    }

    // Function to retrieve logs by action
    public function getLogsByAction($action) {
        $query = "SELECT * FROM logs WHERE action = :action ORDER BY created_at DESC";
        return $this->db->fetchAll($query, ['action' => $action]);
    }

    // Function to retrieve logs by date range
    public function getLogsByDateRange($start_date, $end_date) {
        $query = "SELECT * FROM logs WHERE created_at BETWEEN :start_date AND :end_date ORDER BY created_at DESC";
        return $this->db->fetchAll($query, [
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
    }
}

// Display logs for admin
session_start();
require_once 'includes/auth.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || $auth->getUserRole() !== 'admin') {
    header('Location: index.php');
    exit();
}

$logs = new Logs();
$allLogs = $logs->getAllLogs();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">myDressing</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-5">
        <h1>Logs</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allLogs as $log): ?>
                <tr>
                    <td><?php echo htmlspecialchars($log['id']); ?></td>
                    <td><?php echo htmlspecialchars($log['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($log['action']); ?></td>
                    <td><?php echo htmlspecialchars($log['details']); ?></td>
                    <td><?php echo htmlspecialchars($log['created_at']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
