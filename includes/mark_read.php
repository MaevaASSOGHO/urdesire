<?php
require_once(__DIR__ . '/../config.php');
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$notif_id = $_POST['notif_id'] ?? null;

if ($user_id && $notif_id) {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([$notif_id, $user_id]);
}

header("Location: ../profile.php#nav-notification");
exit;
