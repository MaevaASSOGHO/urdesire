<?php
require_once("config.php");
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
  echo "Non autorisé";
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$notifs = $stmt->fetchAll();

// Marquer toutes comme lues
$pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0")->execute([$user_id]);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mes notifications</title>
  <link rel="stylesheet" href="assets/css/bootstrap.css">
</head>
<body>
  <div class="container mt-5">
    <h2>Mes notifications</h2>
    <ul class="list-group">
      <?php foreach ($notifs as $n): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <?= htmlspecialchars($n['message']) ?>
          <small><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></small>
        </li>
      <?php endforeach; ?>
      <?php if (count($notifs) === 0): ?>
        <li class="list-group-item text-muted">Aucune notification pour l’instant.</li>
      <?php endif; ?>
    </ul>
  </div>
</body>
</html>
