<?php
require_once 'db.php';

class Upload {
    public static function uploadPhoto($file, $articleId) {
        $targetDir = __DIR__ . '/../media/pictures/';
        $timestamp = date('ymdHis');
        $originalName = basename($file['name']);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $newName = "{$timestamp}_{$originalName}";
        $targetFile = $targetDir . $newName;

        // Check file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($extension), $allowedTypes)) {
            throw new Exception("Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.");
        }

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            $db = new Database();
            $query = "INSERT INTO article_photos (article_id, photo_name, created_at) VALUES (:articleId, :photoName, NOW())";
            $db->executeQuery($query, [
                'articleId' => $articleId,
                'photoName' => $newName
            ]);
            return $newName;
        }

        throw new Exception("Failed to upload photo.");
    }
}
?>
