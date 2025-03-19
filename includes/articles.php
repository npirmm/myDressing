<?php
require_once 'db.php';

$db = new Database();

// Ajouter un article
function addArticle($data) {
    global $db;
    $sql = "INSERT INTO articles (name, short_description, long_description, category_id, brand_id, size, weight, status, `condition`, storage_location_id, storage_shelf, storage_id, purchase_date, purchase_price, supplier_id, clean_instructions, notes, rating, favorite, created_by, owner_id) 
            VALUES (:name, :short_description, :long_description, :category_id, :brand_id, :size, :weight, :status, :condition, :storage_location_id, :storage_shelf, :storage_id, :purchase_date, :purchase_price, :supplier_id, :clean_instructions, :notes, :rating, :favorite, :created_by, :owner_id)";
    $db->executeQuery($sql, $data);
    return $db->fetch("SELECT LAST_INSERT_ID() AS id")['id'];
}

// Récupérer tous les articles
function getArticles() {
    global $db;
    $sql = "SELECT * FROM articles";
    return $db->fetchAll($sql);
}

// Supprimer un article
function deleteArticle($id) {
    global $db;
    $sql = "DELETE FROM articles WHERE id = :id";
    $db->executeQuery($sql, ['id' => $id]);
}
?>
