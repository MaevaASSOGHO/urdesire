<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once(__DIR__ . '/../config.php');
// Vérification de l'utilisateur connecté
$user_id = $_SESSION['user_id'] ?? null;
$wishlist_id = $_POST['wishlist_id'] ?? $_GET['id'] ?? null;

if (!$user_id || !$wishlist_id) {
    if (!empty($_GET)) {
        echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
        exit;
    } else {
        header("Location: wishlist.php");
        exit;
    }
}

// Supprimer de la wishlist
$stmt = $pdo->prepare("DELETE FROM wishlists WHERE id = ? AND user_id = ?");
$stmt->execute([$wishlist_id, $user_id]);

// Recompter pour le header
$stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id = ?");
$stmt->execute([$user_id]);
$total = $stmt->fetchColumn();

// Réponse AJAX si GET
if (!empty($_GET)) {
    echo json_encode(['success' => true, 'total' => $total]);
    exit;
}

// Redirection si suppression classique
header("Location: ../wishlist.php");
exit;

