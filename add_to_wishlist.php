<?php
session_start();
require_once("config.php");

$user_id = $_SESSION['user_id'] ?? null;
$product_id = $_POST['product_id'] ?? $_GET['id'] ?? null;

if (!$user_id || !$product_id) {
    if (!empty($_GET)) {
        echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
        exit;
    } else {
        header("Location: index.php");
        exit;
    }
}

// Ajouter à la wishlist si non existant
$stmt = $pdo->prepare("SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);
$exists = $stmt->fetch();

if (!$exists) {
    $stmt = $pdo->prepare("INSERT INTO wishlists (user_id, product_id, quantity) VALUES (?, ?, 1)");
    $stmt->execute([$user_id, $product_id]);
}

// Nombre total pour l'affichage dans le header
$stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id = ?");
$stmt->execute([$user_id]);
$total = $stmt->fetchColumn();

// Réponse JSON si AJAX
if (!empty($_GET)) {
    echo json_encode(['success' => true, 'total' => $total]);
    exit;
}

// Redirection classique sinon
header("Location: wishlist.php");
exit;