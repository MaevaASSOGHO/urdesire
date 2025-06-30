<?php
require_once("config.php");
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

// Compter total notifications
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ?");
$countStmt->execute([$user_id]);
$total = $countStmt->fetchColumn();
$pages = ceil($total / $limit);

// Récupérer notifications paginées
$stmt = $pdo->prepare("SELECT id, message, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->execute([$user_id, $limit, $offset]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="profile__notification-list">
  <?php foreach ($notifications as $notif): ?>
    <div class="notification-item d-flex justify-content-between align-items-start p-3 mb-3 border rounded <?php echo $notif['is_read'] ? 'bg-light' : 'bg-warning-subtle'; ?>">
      <div>
        <strong class="<?php echo $notif['is_read'] ? 'text-muted' : 'text-dark'; ?>">
          <?= htmlspecialchars($notif['message']) ?>
        </strong>
        <div class="small text-muted"><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?></div>
      </div>
      <?php if (!$notif['is_read']): ?>
        <form method="POST" action="includes/mark_read.php">
          <input type="hidden" name="notif_id" value="<?= $notif['id'] ?>">
          <button type="submit" class="btn btn-sm btn-outline-success">Marquer comme lue</button>
        </form>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>

  <!-- Pagination -->
  <nav>
    <ul class="pagination">
      <?php for ($i = 1; $i <= $pages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>#nav-notification"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>
