<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once(__DIR__ . '/../config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupération des champs du formulaire
$pseudo = trim($_POST['pseudo'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$bio = trim($_POST['bio'] ?? '');

// Sécurité basique
$pseudo = htmlspecialchars($pseudo);
$phone = htmlspecialchars($phone);
$address = htmlspecialchars($address);
$bio = htmlspecialchars($bio);

// 1. Mise à jour du pseudo dans la table `users`
$stmtUser = $pdo->prepare("UPDATE users SET pseudo = ? WHERE id = ?");
$stmtUser->execute([$pseudo, $user_id]);

// 2. Vérifier si le profil existe déjà
$stmtCheck = $pdo->prepare("SELECT id FROM user_profiles WHERE user_id = ?");
$stmtCheck->execute([$user_id]);

if ($stmtCheck->rowCount() > 0) {
    // 2a. Mise à jour du profil existant
    $stmtUpdate = $pdo->prepare("UPDATE user_profiles SET phone_number = ?, address = ?, bio = ? WHERE user_id = ?");
    $stmtUpdate->execute([$phone, $address, $bio, $user_id]);
} else {
    // 2b. Création d’un nouveau profil si inexistant
    $stmtInsert = $pdo->prepare("INSERT INTO user_profiles (user_id, phone_number, address, bio) VALUES (?, ?, ?, ?)");
    $stmtInsert->execute([$user_id, $phone, $address, $bio]);
}

// Redirection avec message de succès (optionnel)
header("Location: ../profile.php?updated=1"); // après mise à jour
exit;
?>
