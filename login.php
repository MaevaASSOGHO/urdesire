<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = trim($_POST['pseudo'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($pseudo) || empty($password)) {
        echo "Veuillez remplir tous les champs.";
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE pseudo = ?");
        $stmt->execute([$pseudo]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Connexion réussie
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['pseudo'] = $pseudo;

            // Redirection ou réponse
            header("Location: index.php");;
        } else {
            echo "Pseudo ou mot de passe incorrect.";
        }

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
} else {
    echo "Méthode non autorisée.";
}
?>
