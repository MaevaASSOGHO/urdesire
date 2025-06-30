<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config.php'; // fichier avec ta connexion PDO ($pdo)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = trim($_POST['pseudo'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (strlen($pseudo) < 3 || strlen($mot_de_passe) < 6) {
        echo "Pseudo ou mot de passe invalide.";
        exit;
    }

    // Vérifie si le pseudo existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE pseudo = :pseudo");
    $stmt->execute(['pseudo' => $pseudo]);
    if ($stmt->fetch()) {
        echo "Ce pseudo est déjà utilisé.";
        exit;
    }

    // Hash du mot de passe
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Générer un token aléatoire
    $token = bin2hex(random_bytes(32));

    // Insertion en base de données
    $stmt = $pdo->prepare("INSERT INTO users (pseudo, password, remember_token) VALUES (:pseudo, :password, :remember_token)");
    $stmt->execute([
        'pseudo' => $pseudo,
        'password' => $mot_de_passe_hash,
        'remember_token' => $token
    ]);

    $_SESSION['pseudo'] = $pseudo;
    $_SESSION['token'] = $token;

    header("Location: index.php"); 
    exit;
}
?>
