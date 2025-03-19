<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/logs.php';

session_start();

$auth = new Auth();
if (!$auth->isLoggedIn() || $auth->getUserRole() !== 'admin') {
    header('Location: index.php');
    exit();
}

$logs = new Logs();
$filters = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET['user_id'])) {
        $filters['user_id'] = $_GET['user_id'];
    }
    if (!empty($_GET['action'])) {
        $filters['action'] = $_GET['action'];
    }
    if (!empty($_GET['date_from'])) {
        $filters['date_from'] = $_GET['date_from'];
    }
    if (!empty($_GET['date_to'])) {
        $filters['date_to'] = $_GET['date_to'];
    }
}

$logEntries = $logs->getLogs($filters);
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
        <h1 class="mb-4">Logs</h1>
        <form method="GET" class="mb-4">
            <div class="form-row">
                <div class="col">
                    <input type="text" name="user_id" class="form-control" placeholder="User ID" value="<?php echo htmlspecialchars($_GET['user_id'] ?? ''); ?>">
                </div>
                <div class="col">
                    <select name="action" class="form-control">
                        <option value="">All Actions</option>
                        <option value="login" <?php echo ($_GET['action'] ?? '') === 'login' ? 'selected' : ''; ?>>Login</option>
                        <option value="logout" <?php echo ($_GET['action'] ?? '') === 'logout' ? 'selected' : ''; ?>>Logout</option>
                        <option value="add" <?php echo ($_GET['action'] ?? '') === 'add' ? 'selected' : ''; ?>>Add</option>
                        <option value="modify" <?php echo ($_GET['action'] ?? '') === 'modify' ? 'selected' : ''; ?>>Modify</option>
                        <option value="delete" <?php echo ($_GET['action'] ?? '') === 'delete' ? 'selected' : ''; ?>>Delete</option>
                    </select>
                </div>
                <div class="col">
                    <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>">
                </div>
                <div class="col">
                    <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>">
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logEntries as $log): ?>
                    <tr>
                        <td><?php echo $log['id']; ?></td>
                        <td><?php echo htmlspecialchars($log['username'] ?? 'Unknown'); ?></td>
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
