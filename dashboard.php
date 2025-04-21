<?php
session_start(); // Start the session to manage user authentication and data.
require_once 'includes/auth.php'; // Include the authentication class.
require_once 'includes/logs.php'; // Include the logging functionality.

// Check if user is logged in
$auth = new Auth(); // Create an instance of the Auth class.
if (!$auth->isLoggedIn()) { // Redirect to the login page if the user is not logged in.
    header('Location: index.php');
    exit();
}

// Get user role
$userRole = $auth->getUserRole(); // Retrieve the role of the logged-in user.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Include custom styles and Bootstrap for styling -->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">myDressing</a> <!-- Brand name -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a> <!-- Link to the profile page -->
                </li>
                <?php if ($userRole === 'admin'): ?> <!-- Show logs link only for admin users -->
                <li class="nav-item">
                    <a class="nav-link" href="logs.php">Logs</a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a> <!-- Link to log out -->
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-5">
        <h1>Welcome to the Dashboard</h1>
        <p>Your role: <?php echo ucfirst($userRole); ?></p> <!-- Display the user's role -->
        <div class="row">
            <!-- Card for managing articles -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Manage Articles</h5>
                        <p class="card-text">Add, edit, and delete articles in your wardrobe.</p>
                        <a href="articles.php" class="btn btn-primary">Go to Articles</a>
                    </div>
                </div>
            </div>
            <!-- Card for managing events -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Manage Events</h5>
                        <p class="card-text">Create and manage events related to your wardrobe.</p>
                        <a href="#" class="btn btn-primary">Go to Events</a>
                    </div>
                </div>
            </div>
            <?php if ($userRole === 'admin'): ?> <!-- Show user management card only for admin users -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Manage Users</h5>
                        <p class="card-text">Add, edit, and delete users and their permissions.</p>
                        <a href="#" class="btn btn-primary">Go to Users</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Include JavaScript libraries for Bootstrap functionality -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>