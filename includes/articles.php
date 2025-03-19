<?php
require_once 'db.php';

// Ajouter un article
function addArticle($data) {
    global $pdo;
    $sql = "INSERT INTO articles (name, short_description, long_description, category_id, brand_id, size, weight, status, condition, storage_location_id, storage_shelf, storage_id, purchase_date, purchase_price, supplier_id, clean_instructions, notes, rating, favorite, created_by, owner_id) 
            VALUES (:name, :short_description, :long_description, :category_id, :brand_id, :size, :weight, :status, :condition, :storage_location_id, :storage_shelf, :storage_id, :purchase_date, :purchase_price, :supplier_id, :clean_instructions, :notes, :rating, :favorite, :created_by, :owner_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
    return $pdo->lastInsertId();
}

// Récupérer tous les articles
function getArticles() {
    global $pdo;
    $sql = "SELECT * FROM articles";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Supprimer un article
function deleteArticle($id) {
    global $pdo;
    $sql = "DELETE FROM articles WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
}
?>
