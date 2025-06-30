<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../config.php');
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$old_pass = $_POST['old_pass'] ?? '';
$new_pass = $_POST['new_pass'] ?? '';
$con_new_pass = $_POST['con_new_pass'] ?? '';

if (!$user_id) {
    header("Location: ../profile.php?error=unauthenticated");
    exit;
}

if (empty($old_pass) || empty($new_pass) || empty($con_new_pass)) {
    header("Location: ../profile.php?error=missing_fields");
    exit;
}

if ($new_pass !== $con_new_pass) {
    header("Location: ../profile.php?error=nomatch");
    exit;
}

$stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$stored = $stmt->fetchColumn();

if (!$stored || !password_verify($old_pass, $stored)) {
    header("Location: ../profile.php?error=wrong_old");
    exit;
}

$hashed = password_hash($new_pass, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->execute([$hashed, $user_id]);

$notif = $pdo->prepare("INSERT INTO notifications (user_id, message, type) VALUES (?, ?, ?)");
$notif->execute([$user_id, 'Votre mot de passe a été changé avec succès.', 'success']);

header("Location: ../profile.php?success=password_updated");
exit;
