<?php
require_once("../config.php");
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$cart_id = $_POST['cart_id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$user_id || !$cart_id || !in_array($action, ['increment', 'decrement'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
    exit;
}

$stmt = $pdo->prepare("SELECT quantity FROM cart_items WHERE id = ? AND user_id = ?");
$stmt->execute([$cart_id, $user_id]);
$current = $stmt->fetch();

if (!$current) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Article introuvable.']);
    exit;
}

$new_quantity = $action === 'increment' ? $current['quantity'] + 1 : max(1, $current['quantity'] - 1);

$stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND user_id = ?");
$stmt->execute([$new_quantity, $cart_id, $user_id]);

echo json_encode(['success' => true, 'quantity' => $new_quantity]);
?>