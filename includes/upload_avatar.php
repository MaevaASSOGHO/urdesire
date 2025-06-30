<?php
require_once 'config.php';
session_start();

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Vérifie si un fichier a été envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];

    // Vérification d'erreur
    if ($file['error'] === 0) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array(strtolower($ext), $allowed)) {
            // Crée un nom de fichier unique
            $filename = time() . '_' . uniqid() . '.' . $ext;
            $destination = 'assets/img/users/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Met à jour le champ avatar dans la BDD
                $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                $stmt->execute([$destination, $user_id]);

                header("Location: profile.php?avatar=ok");
                exit;
            } else {
                $error = "Erreur lors du déplacement du fichier.";
            }
        } else {
            $error = "Extension non autorisée. (jpg, jpeg, png, webp)";
        }
    } else {
        $error = "Erreur lors du téléchargement.";
    }
} else {
    $error = "Aucun fichier envoyé.";
}

// En cas d'erreur, redirige avec message
header("Location: profile.php?avatar=error&message=" . urlencode($error));
exit;
