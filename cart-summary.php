<?php
// Résumé panier dynamique
$subtotal = 0;

if ($user_id) {
    $stmt = $pdo->prepare("
        SELECT SUM(p.price * c.quantity) AS total
        FROM cart_items c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $subtotal = (int) $stmt->fetchColumn();
}

$shipping_options = [
    'Abidjan'     => 1500,
    'Hors Abidjan'  => 2000,
    'Retrait en magasin' => 0
];

$selected_shipping = $_GET['shipping'] ?? 'Abidjan';
$shipping_cost = $shipping_options[$selected_shipping] ?? 0;

$total = $subtotal + $shipping_cost;
?>

<div class="col-xl-3 col-lg-4 col-md-6">
  <div class="tp-cart-checkout-wrapper">
    <div class="tp-cart-checkout-top d-flex align-items-center justify-content-between">
      <span class="tp-cart-checkout-top-title">Sous-total</span>
      <span class="tp-cart-checkout-top-price"><?= number_format($subtotal, 0, ',', ' ') ?> FCFA</span>
    </div>

    <div class="tp-cart-checkout-shipping">
      <h4 class="tp-cart-checkout-shipping-title">Livraison</h4>
      <form method="get" action="">
        <div class="tp-cart-checkout-shipping-option-wrapper">
          <?php foreach ($shipping_options as $option => $cost): ?>
            <div class="tp-cart-checkout-shipping-option">
              <input id="<?= $option ?>" type="radio" name="shipping" value="<?= $option ?>"
                <?= $selected_shipping === $option ? 'checked' : '' ?>
                onchange="this.form.submit()">
              <label for="<?= $option ?>">
                <?= ucfirst(str_replace('_', ' ', $option)) ?> :
                <?= $cost > 0 ? '<span>' . number_format($cost, 0, ',', ' ') . ' FCFA</span>' : 'Gratuit' ?>
              </label>
            </div>
          <?php endforeach; ?>
        </div>
      </form>
    </div>

    <div class="tp-cart-checkout-total d-flex align-items-center justify-content-between">
      <span>Total</span>
      <span><?= number_format($total, 0, ',', ' ') ?> FCFA</span>
    </div>

    <div class="tp-cart-checkout-proceed">
      <a href="checkout.php" class="tp-cart-checkout-btn w-100" style="background-color: #E44C76; color: white;" onmouseover="this.style.backgroundColor='#C50E40'" onmouseout="this.style.backgroundColor='#E44C76'">Passer à la caisse</a>
    </div>
  </div>
</div>