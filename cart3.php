<?php
session_start();
require_once("config.php");

$user_id = $_SESSION['user_id'] ?? null;
?>

<!doctype html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <title>Mon Panier - UrDesire</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
      .qty-btn {
        border: 1px solid #ccc;
        background: none;
        padding: 5px 10px;
        cursor: pointer;
      }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<main>
  <section class="breadcrumb__area include-bg pt-95 pb-50">
    <div class="container">
      <h3>Panier</h3>
    </div>
  </section>

  <section class="tp-cart-area pb-120">
    <div class="container">
      <div class="row">
        <div class="col-xl-9 col-lg-8">
          <div class="tp-cart-list mb-25 mr-30">
            <table class="table">
              <thead>
                <tr>
                  <th colspan="2">Produit</th>
                  <th>Prix</th>
                  <th>Quantité</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
<?php
if (!$user_id) {
    echo '<tr><td colspan="5">Veuillez vous connecter.</td></tr>';
} else {
    $stmt = $pdo->prepare("
        SELECT ci.id AS cart_id, ci.quantity, p.*
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        WHERE ci.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cart_items)) {
        echo '<tr><td colspan="5">Votre panier est vide.</td></tr>';
    } else {
        foreach ($cart_items as $item):
?>
<tr>
  <td><img src="<?= htmlspecialchars($item['image_url']) ?>" alt="" width="60"></td>
  <td><?= htmlspecialchars($item['name']) ?></td>
  <td><?= number_format($item['price'], 0, ',', ' ') ?> FCFA</td>
  <td>
    <div class="tp-product-quantity mt-10 mb-10 d-flex align-items-center">
      <button class="qty-btn minus" data-id="<?= $item['cart_id'] ?>">-</button>
      <input class="tp-cart-input mx-2 text-center" type="text" value="<?= (int)$item['quantity'] ?>" readonly style="width: 60px;">
      <button class="qty-btn plus" data-id="<?= $item['cart_id'] ?>">+</button>
    </div>
  </td>
  <td>
    <form action="includes/remove_from_cart.php" method="post">
      <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
      <button type="submit">Supprimer</button>
    </form>
  </td>
</tr>
<?php
        endforeach;
    }
}
?>
              </tbody>
            </table>
          </div>
        </div>
        <div><?php include 'cart-summary.php'; ?></div>
      </div>
    </div>
  </section>
</main>

<script>
document.querySelectorAll('.qty-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    const cartId = this.dataset.id;
    const action = this.classList.contains('plus') ? 'increment' : 'decrement';
    const input = this.parentElement.querySelector('input');

    fetch('includes/update_cart_quantity.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `cart_id=${cartId}&action=${action}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        input.value = data.quantity;
        location.reload();
      }
    });
  });
});
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
