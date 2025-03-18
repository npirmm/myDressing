<?php
require_once 'db.php';

class Upload {
    private $db;
    private $uploadDir = '/media/pictures/';

    public function __construct() {
        $this->db = new Database();
    }

    // Function to handle photo uploads
    public function uploadPhoto($article_id, $file) {
        $timestamp = date('ymdHis');
        $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = $timestamp . '_' . $originalName . '.' . $extension;
        $targetPath = $_SERVER['DOCUMENT_ROOT'] . $this->uploadDir . $newFileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $query = "INSERT INTO article_photos (article_id, photo_name, created_at) VALUES (:article_id, :photo_name, NOW())";
            return $this->db->executeQuery($query, [
                'article_id' => $article_id,
                'photo_name' => $newFileName
            ]);
        }

        return false;
    }
}
?>
