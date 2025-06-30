<?php
session_start();
require_once("../config.php"); // car fichier dans /includes

$user_id = $_SESSION['user_id'] ?? null;
$cart_id = $_POST['cart_id'] ?? $_GET['id'] ?? null;

if (!$user_id || !$cart_id) {
    if (!empty($_GET)) {
        echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
        exit;
    } else {
        header("Location: ../cart.php");
        exit;
    }
}

// Supprimer l'article du panier
$stmt = $pdo->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
$stmt->execute([$cart_id, $user_id]);

// Recalculer le total
$stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart_items WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_items = $stmt->fetchColumn() ?: 0;

// Réponse AJAX
if (!empty($_GET)) {
    echo json_encode(['success' => true, 'count' => $total_items]);
    exit;
}

// Redirection normale vers le panier
header("Location: ../cart.php");
exit;