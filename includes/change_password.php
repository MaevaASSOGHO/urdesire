<?php
require_once("../config.php");
session_start();
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => "Utilisateur non connecté."]);
    exit;
}

$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (empty($current) || empty($new) || empty($confirm)) {
    echo json_encode(['success' => false, 'message' => "Tous les champs sont requis."]);
    exit;
}

$stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user || !password_verify($current, $user['password'])) {
    echo json_encode(['success' => false, 'message' => "Mot de passe actuel incorrect."]);
    exit;
}

if (strlen($new) < 6) {
    echo json_encode(['success' => false, 'message' => "Le nouveau mot de passe est trop court (min. 6 caractères)."]);
    exit;
}

if ($new !== $confirm) {
    echo json_encode(['success' => false, 'message' => "Les mots de passe ne correspondent pas."]);
    exit;
}

$new_hash = password_hash($new, PASSWORD_DEFAULT);
$pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$new_hash, $user_id]);

$pdo->prepare("INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, NOW())")
    ->execute([$user_id, "Votre mot de passe a été modifié."]);

echo json_encode(['success' => true, 'message' => "Mot de passe changé avec succès."]);
?>
