<?php
session_start();
require_once("config.php");

$user_id = $_SESSION['user_id'] ?? null;

// Priorité au POST (formulaire), sinon fallback GET (AJAX index)
$product_id = $_POST['product_id'] ?? $_GET['id'] ?? null;
$quantity = $_POST['quantity'] ?? 1;

if (!$user_id || !$product_id) {
    if (!empty($_GET)) {
        // Appel AJAX (index) — renvoyer erreur
        echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
        exit;
    } else {
        // Appel formulaire classique — rediriger proprement
        header("Location: index.php");
        exit;
    }
}

// Insert ou update dans la base
$stmt = $pdo->prepare("
    INSERT INTO cart_items (user_id, product_id, quantity)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
");
$stmt->execute([$user_id, $product_id, $quantity]);

// Compte total pour le header
$stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart_items WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_items = $stmt->fetchColumn() ?: 0;

// Réponse AJAX
if (!empty($_GET)) {
    echo json_encode(['success' => true, 'count' => $total_items]);
    exit;
}

// Redirection classique
header("Location: cart.php");
exit;