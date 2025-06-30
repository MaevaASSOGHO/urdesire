<?php
session_start();
require_once("config.php");

$user_id = $_SESSION['user_id'] ?? null;
$cart_items = [];
$subtotal = 0;

if ($user_id) {
    $stmt = $pdo->prepare("SELECT ci.quantity, p.name, p.price FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.user_id = ?");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cart_items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
}
$livraison = null;

if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE user_id = ? AND type = 'livraison' LIMIT 1");
    $stmt->execute([$user_id]);
    $livraison = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Passer à la caisse</title>
  <link rel="stylesheet" href="assets/css/bootstrap.css">
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

<main>
  <section class="checkout-area pb-120">
    <div class="container">
      <form action="place_order.php" method="post">
        <div class="row">
          <div class="col-lg-7">
            <div class="checkout-form">
              <h3 class="title">Informations de facturation</h3>
              <div class="row">
                <div class="col-md-6">
                  <label>Prénom *</label>
                  <input type="text" name="first_name" required>
                </div>
                <div class="col-md-6">
                  <label>Nom *</label>
                  <input type="text" name="last_name" required>
                </div>
                <div class="col-md-12">
                  <label>Adresse *</label>
                  <input type="text" name="address" required>
                </div>
                <div class="col-md-6">
                  <label>Email *</label>
                  <input type="email" name="email" required>
                </div>
                <div class="col-md-6">
                  <label>Téléphone *</label>
                  <input type="text" name="phone" required>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-5">
            <div class="your-order">
              <h3 class="title">Votre commande</h3>
              <div class="order-table table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Produit</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody>
<?php if (empty($cart_items)): ?>
  <tr><td colspan="2">Aucun produit dans votre panier.</td></tr>
<?php else: ?>
  <?php foreach ($cart_items as $item): ?>
    <tr>
      <td><?= htmlspecialchars($item['name']) ?> x <?= (int)$item['quantity'] ?></td>
      <td><?= number_format($item['price'] * $item['quantity'], 0, ',', ' ') ?> FCFA</td>
    </tr>
  <?php endforeach; ?>
<?php endif; ?>
<tr>
  <td><strong>Sous-total</strong></td>
  <td><?= number_format($subtotal, 0, ',', ' ') ?> FCFA</td>
</tr>
<tr>
  <td><strong>Livraison</strong></td>
  <td>1 500 FCFA</td>
</tr>
<tr>
  <td><strong>Total</strong></td>
  <td><strong><?= number_format($subtotal + 1500, 0, ',', ' ') ?> FCFA</strong></td>
</tr>
                  </tbody>
                </table>
              </div>

              <input type="hidden" name="total" value="<?= $subtotal + 1500 ?>">

              <div class="place-order mt-20">
                <button type="submit" class="btn btn-primary w-100">Passer la commande</button>
              </div>
            </div>
          </div>

        </div>
      </form>
    </div>
  </section>
</main>

</body>
</html>