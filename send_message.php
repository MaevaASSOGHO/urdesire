<?php
require 'db.php'; // ta connexion PDO

$data = json_decode(file_get_contents('php://input'), true);
$sender_id = $data['sender_id'];
$receiver_id = $data['receiver_id'];
$encrypted_message = $data['encrypted_message'];

$stmt = $pdo->prepare("INSERT INTO rsa_messages (sender_id, receiver_id, encrypted_message) VALUES (?, ?, ?)");
$stmt->execute([$sender_id, $receiver_id, $encrypted_message]);

echo json_encode(["success" => true]);
