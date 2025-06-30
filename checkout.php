<?php
session_start();
require_once("config.php");

$user_id = $_SESSION['user_id'] ?? null;
$cart_items = [];
$subtotal = 0;
$livraison = null;

if ($user_id) {
    // Adresses de livraison
    $stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE user_id = ? AND type = 'livraison' LIMIT 1");
    $stmt->execute([$user_id]);
    $livraison = $stmt->fetch(PDO::FETCH_ASSOC);

    // Articles du panier
    $stmt = $pdo->prepare("SELECT ci.quantity, p.name, p.price FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.user_id = ?");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cart_items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Passer à la caisse - UrDesire</title>
  <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CSS here -->
      <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/favicon-32x32.png">
      <link rel="stylesheet" href="assets/css/bootstrap.css">
      <link rel="stylesheet" href="assets/css/animate.css">
      <link rel="stylesheet" href="assets/css/swiper-bundle.css">
      <link rel="stylesheet" href="assets/css/slick.css">
      <link rel="stylesheet" href="assets/css/magnific-popup.css">
      <link rel="stylesheet" href="assets/css/font-awesome-pro.css">
      <link rel="stylesheet" href="assets/css/flaticon_shofy.css">
      <link rel="stylesheet" href="assets/css/spacing.css">
      <link rel="stylesheet" href="assets/css/main.css">

</head>
<body>

   <!-- back to top start -->
   <div class="back-to-top-wrapper">
      <button id="back_to_top" type="button" class="back-to-top-btn">
         <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M11 6L6 1L1 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
               stroke-linejoin="round" />
         </svg>
      </button>
   </div>
   <!-- back to top end -->

   <!-- offcanvas area start -->
      <?php include 'includes/offcanvas.php'; ?>
   <!-- offcanvas area end -->

   <!-- mobile menu area start -->
   <div id="tp-bottom-menu-sticky" class="tp-mobile-menu d-lg-none">
      <div class="container">
         <div class="row row-cols-5">
            <div class="col">
               <div class="tp-mobile-item text-center">
                  <a href="shop.php" class="tp-mobile-item-btn">
                     <i class="flaticon-store"></i>
                     <span>Store</span>
                  </a>
               </div>
            </div>
            <div class="col">
               <div class="tp-mobile-item text-center">
                  <button class="tp-mobile-item-btn tp-search-open-btn">
                     <i class="flaticon-search-1"></i>
                     <span>Chercher</span>
                  </button>
               </div>
            </div>
            <div class="col">
               <div class="tp-mobile-item text-center">
                  <a href="wishlist.php" class="tp-mobile-item-btn">
                     <i class="flaticon-love"></i>
                     <span>Wishlist</span>
                  </a>
               </div>
            </div>
            <div class="col">
               <div class="tp-mobile-item text-center">
                  <a href="profile.php" class="tp-mobile-item-btn">
                     <i class="flaticon-user"></i>
                     <span>Compte</span>
                  </a>
               </div>
            </div>
            <div class="col">
               <div class="tp-mobile-item text-center">
                  <button class="tp-mobile-item-btn tp-offcanvas-open-btn">
                     <i class="flaticon-menu-1"></i>
                     <span>Menu</span>
                  </button>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- mobile menu area end -->

   <!-- search area start -->
   <section class="tp-search-area tp-search-style-green">
      <div class="container">
         <div class="row">
            <div class="col-xl-12">
               <div class="tp-search-form">
                  <div class="tp-search-close text-center mb-20">
                     <button class="tp-search-close-btn tp-search-close-btn"></button>
                  </div>
                  <form action="search.php" method="get">
                    <div class="tp-search-input mb-10">
                        <input type="text" name="q" placeholder="Recherche un produit...">
                        <button type="submit"><i class="flaticon-search-1"></i></button>
                    </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </section>
   <!-- search area end -->
<?php include 'includes/header.php'; ?>

<main>
  <!-- breadcrumb area start -->
         <section class="breadcrumb__area include-bg pt-95 pb-50" data-bg-color="#EFF1F5">
            <div class="container">
               <div class="row">
                  <div class="col-xxl-12">
                     <div class="breadcrumb__content p-relative z-index-1">
                        <h3 class="breadcrumb__title">Paiement</h3>
                        <div class="breadcrumb__list">
                           <!-- <span>Checkout</span> -->
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </section>
         <!-- breadcrumb area end -->
  <section class="checkout-area pb-120"data-bg-color="#EFF1F5">
    <div class="container">
      <form action="place_order.php" method="post">
        <div class="row">
          <div class="col-lg-7">
            <div class="checkout-form">
              <h3 class="title">Adresse de livraison</h3>
              <div class="row">
                <div class="col-md-12">
                  <label>Rue *</label>
                  <input type="text" name="street_livraison" class="form-control" required value="<?= htmlspecialchars($livraison['street'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label>Ville *</label>
                  <input type="text" name="city_livraison" class="form-control" required value="<?= htmlspecialchars($livraison['city'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label>Code postal *</label>
                  <input type="text" name="postal_livraison" class="form-control" required value="<?= htmlspecialchars($livraison['postal_code'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label>Pays *</label>
                  <input type="text" name="country_livraison" class="form-control" required value="<?= htmlspecialchars($livraison['country_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label>Téléphone *</label>
                  <input type="text" name="phone_livraison" class="form-control" required value="<?= htmlspecialchars($livraison['phone'] ?? '') ?>">
                </div>
              </div>

              <div class="form-check mt-4">
                <input type="checkbox" id="facturationCheck" class="form-check-input">
                <label for="facturationCheck" class="form-check-label">Utiliser une adresse de facturation différente</label>
              </div>

              <div id="facturationFields" class="mt-4" style="display: none;">
                <h3>Adresse de facturation</h3>
                <div class="row">
                  <div class="col-md-12">
                    <label>Rue *</label>
                    <input type="text" name="street_facturation" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Ville *</label>
                    <input type="text" name="city_facturation" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Code postal *</label>
                    <input type="text" name="postal_facturation" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Pays *</label>
                    <input type="text" name="country_facturation" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Téléphone *</label>
                    <input type="text" name="phone_facturation" class="form-control">
                  </div>
                </div>
              </div>

              <script>
                document.addEventListener("DOMContentLoaded", function () {
                  const checkbox = document.getElementById('facturationCheck');
                  const fields = document.getElementById('facturationFields');
                  checkbox.addEventListener('change', function () {
                    fields.style.display = this.checked ? 'block' : 'none';
                  });
                });
              </script>

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
                <button type="submit" class="btn btn-green w-100" style="color:white; "background-color: #E44C76;" onmouseover="this.style.backgroundColor='#C50E40'" onmouseout="this.style.backgroundColor='#E44C76'">Confirmer la commande</button>
              </div>
            </div>
          </div>

        </div>
      </form>
    </div>
  </section>
</main>
<?php include 'includes/footer.php'; ?>

 <script src="assets/js/vendor/jquery.js"></script>
      <script src="assets/js/vendor/waypoints.js"></script>
      <script src="assets/js/bootstrap-bundle.js"></script>
      <script src="assets/js/meanmenu.js"></script>
      <script src="assets/js/swiper-bundle.js"></script>
      <script src="assets/js/slick.js"></script>
      <script src="assets/js/range-slider.js"></script>
      <script src="assets/js/magnific-popup.js"></script>
      <script src="assets/js/nice-select.js"></script>
      <script src="assets/js/purecounter.js"></script>
      <script src="assets/js/countdown.js"></script>
      <script src="assets/js/wow.js"></script>
      <script src="assets/js/isotope-pkgd.js"></script>
      <script src="assets/js/imagesloaded-pkgd.js"></script>
      <script src="assets/js/ajax-form.js"></script>
      <script src="https://unpkg.com/infinite-ajax-scroll@3/dist/infinite-ajax-scroll.min.js"></script>
      <script src="assets/js/main.js"></script>

</body>
</html>