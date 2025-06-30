<?php
session_start();
$id = (int)($_GET['id'] ?? 0);

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
// on compte les quantités : cart[id] = qty
$_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;

echo json_encode([
  'success' => true,
  'count'   => array_sum($_SESSION['cart']) // total articles
]);
$user_id = $_SESSION['user_id'] ?? null;
$product_id = $_POST['product_id'] ?? null;
$quantity = $_POST['quantity'] ?? 1;

if ($user_id && $product_id) {
    // Insère dans le panier
    $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?) 
                           ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)");
    $stmt->execute([$user_id, $product_id, $quantity]);
}

header("Location: cart.php");
exit;