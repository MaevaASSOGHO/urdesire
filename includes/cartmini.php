<?php
require_once("config.php");

$user_id = $_SESSION['user_id'] ?? null;
$subtotal = 0;
$items = [];

if ($user_id) {
    $stmt = $pdo->prepare("
        SELECT ci.id AS cart_id, ci.quantity, p.*
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        WHERE ci.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
}
?>

<style>
.cartmini__wrapper {
  height: 100%;
  max-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.cartmini__top-wrapper {
  overflow-y: auto;
  flex-grow: 1;
  padding-right: 5px;
}

.cartmini__checkout {
  flex-shrink: 0;
  background: #fff;
  padding: 20px;
  box-shadow: 0 -1px 5px rgba(0,0,0,0.1);
}
</style>

<div class="cartmini__area">
    <div class="cartmini__wrapper d-flex justify-content-between flex-column">
        <div class="cartmini__top-wrapper">
            <div class="cartmini__top p-relative">
                <div class="cartmini__top-title">
                    <h4>Panier</h4>
                </div>
                <div class="cartmini__close">
                    <button type="button" class="cartmini__close-btn cartmini-close-btn"><i class="fal fa-times"></i></button>
                </div>
            </div>
            <div class="cartmini__shipping">
                <p>Livraison gratuite pour toutes les commandes de plus de <span>15.000 FCFA</span></p>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                         style="width: <?= min(100, ($subtotal / 15000) * 100) ?>%"
                         aria-valuenow="<?= min(100, ($subtotal / 15000) * 100) ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <?php if (empty($items)): ?>
                <div class="cartmini__empty text-center">
                    <img src="assets/img/product/cartmini/empty-cart.png" alt="">
                    <p>Votre panier est vide.</p>
                    <a href="shop.php" class="tp-btn">Aller au Store</a>
                </div>
            <?php else: ?>
                <div class="cartmini__widget">
                    <?php foreach ($items as $item): ?>
                        <div class="cartmini__widget-item">
                            <div class="cartmini__thumb">
                                <a href="product-details.php?id=<?= $item['id'] ?>">
                                    <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                </a>
                            </div>
                            <div class="cartmini__content">
                                <h5 class="cartmini__title">
                                    <a href="product-details.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></a>
                                </h5>
                                <div class="cartmini__price-wrapper">
                                    <span class="cartmini__price"><?= number_format($item['price'], 0, ',', ' ') ?> FCFA</span>
                                    <span class="cartmini__quantity">x<?= (int)$item['quantity'] ?></span>
                                </div>
                            </div>
                            <form action="remove_from_cart.php" method="post" class="cartmini__del">
                                <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                <button type="submit" class="cartmini__del"><i class="fa-regular fa-xmark"></i></button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="cartmini__checkout">
            <div class="cartmini__checkout-title mb-30">
                <h4>Sous-total :</h4>
                <span><?= number_format($subtotal, 0, ',', ' ') ?> FCFA</span>
            </div>
            <div class="cartmini__checkout-btn">
                <a href="cart.php" class="tp-btn mb-10 w-100">Voir le Panier</a>
                <a href="checkout.php" class="tp-btn tp-btn-border w-100">Passer à la Caisse</a>
            </div>
        </div>
    </div>
</div>