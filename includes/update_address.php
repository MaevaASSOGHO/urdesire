<?php
file_put_contents("debug.log", print_r($_POST, true));

ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once(__DIR__ . '/../config.php');

// Récupération type
$type = $_GET['type'] ?? '';
if (!in_array($type, ['billing', 'shipping'])) {
    echo json_encode(['success' => false, 'message' => 'Type invalide']);
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

$data = [
    'street' => $_POST[$type . '_street'] ?? '',
    'city' => $_POST[$type . '_city'] ?? '',
    'phone' => $_POST[$type . '_phone'] ?? '',
    'postal_code' => $_POST[$type . '_postal_code'] ?? '',
    'country_code' => $_POST[$type . '_country_code'] ?? '',
    'country_name' => $_POST[$type . '_country_name'] ?? ''
];

// Vérification rapide
foreach ($data as $field => $value) {
    if (empty($value)) {
        echo json_encode(['success' => false, 'message' => "Champ $field vide"]);
        exit;
    }
}

try {
    // Supprimer l'ancienne adresse
    $pdo->prepare("DELETE FROM user_addresses WHERE user_id = ? AND type = ?")->execute([$user_id, $type]);

    // Insérer la nouvelle
    $stmt = $pdo->prepare("INSERT INTO user_addresses (user_id, type, street, city, phone, postal_code, country_code, country_name)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $type, $data['street'], $data['city'], $data['phone'], $data['postal_code'], $data['country_code'], $data['country_name']]);

    // Notification
    $notif = $pdo->prepare("INSERT INTO notifications (user_id, message, type) VALUES (?, ?, 'info')");
    $notif->execute([$user_id, "Votre adresse de type $type a été mise à jour avec succès."]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur BDD : ' . $e->getMessage()]);
}
?>
