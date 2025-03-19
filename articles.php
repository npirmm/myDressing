<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/articles.php';

// Check if user is logged in
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Fetch all articles
$articles = getArticles();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Articles</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="dashboard.php">myDressing</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-5">
        <h1 class="mb-4">Manage Articles</h1>
        <a href="add-article.php" class="btn btn-primary mb-3">Add New Article</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Thumbnail</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><?= htmlspecialchars($article['id']) ?></td>
                        <td>
                            <?php if ($article['thumbnail']): ?>
                                <img src="media/pictures/<?= htmlspecialchars($article['thumbnail']) ?>" alt="Thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                            <?php else: ?>
                                <span>No Image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($article['name']) ?></td>
                        <td><?= htmlspecialchars($article['category_id']) ?></td>
                        <td><?= htmlspecialchars($article['status']) ?></td>
                        <td>
                            <a href="edit-article.php?id=<?= $article['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="manage-photos.php?id=<?= $article['id'] ?>" class="btn btn-info btn-sm">Manage Photos</a>
                            <a href="delete-article.php?id=<?= $article['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this article?')">Delete</a>
                        </td>
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
