<?php
require_once("config.php");
session_start();

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    echo "Produit non spécifié.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Produit introuvable.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($product['name']) ?> - UrDesire</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container py-5">
  <div class="row">
    <!-- Image du produit -->
    <div class="col-md-6">
      <div class="card shadow-sm">
        <img src="<?= htmlspecialchars($product['image_url']) ?>" class="card-img-top img-fluid" alt="<?= htmlspecialchars($product['name']) ?>">
      </div>
    </div>

    <!-- Détails du produit -->
    <div class="col-md-6">
      <h1 class="mb-3"><?= htmlspecialchars($product['name']) ?></h1>
      <h3 class="text-primary mb-3"><?= number_format($product['price'], 0, ',', ' ') ?> FCFA</h3>
      <p class="text-muted">Note : <?= number_format($product['rating'], 1) ?>/5</p>
      <p class="<?= $product['stock'] > 0 ? 'text-success' : 'text-danger' ?>">
        <?= $product['stock'] > 0 ? 'En stock' : 'Rupture de stock' ?>
      </p>

      <div class="mb-4">
        <h5>Description :</h5>
        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
      </div>

      <?php if ($product['stock'] > 0): ?>
      <form action="add_to_cart.php" method="post" class="mt-4">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
        <div class="mb-3 row">
          <label for="quantity" class="col-sm-3 col-form-label">Quantité :</label>
          <div class="col-sm-4">
            <input type="number" class="form-control" name="quantity" id="quantity" min="1" max="<?= $product['stock'] ?>" value="1">
          </div>
        </div>
        <button type="submit" class="btn btn-success">Ajouter au panier</button>
      </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>