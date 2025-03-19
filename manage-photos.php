<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/articles.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$articleId = $_GET['id'];
$photos = $db->fetchAll("SELECT * FROM article_photos WHERE article_id = :articleId", ['articleId' => $articleId]);
$article = $db->fetch("SELECT * FROM articles WHERE id = :id", ['id' => $articleId]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mainPhotoId = $_POST['main_photo_id'];
    updateMainPhoto($articleId, $mainPhotoId);
    header("Location: articles.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Photos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Manage Photos for <?= htmlspecialchars($article['name']) ?></h1>
        <form method="POST">
            <div class="row">
                <?php foreach ($photos as $photo): ?>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <img src="media/pictures/<?= htmlspecialchars($photo['photo_name']) ?>" class="card-img-top" alt="Photo">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="main_photo_id" value="<?= $photo['id'] ?>" <?= $article['main_photo_id'] == $photo['id'] ? 'checked' : '' ?>>
                                    <label class="form-check-label">Set as Main Photo</label>
                                </div>
                                <p class="card-text"><?= htmlspecialchars($photo['label']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="articles.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
