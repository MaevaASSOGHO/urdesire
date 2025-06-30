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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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

   <!-- cart mini area start -->
         <?php include 'includes/cartmini.php'; ?>
   <!-- cart mini area end -->
    <!-- header area start -->
   <?php include 'includes/header.php'; ?>
   <!-- header area end -->
<main>
  
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
      <h3 class="mb-3"><?= htmlspecialchars($product['name']) ?></h3>
      <h5 class="text mb-3" style="color: #C50E40;"><?= number_format($product['price'], 0, ',', ' ') ?> FCFA</h5>
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
        <button type="submit" class="btn btn" style="color:white;background-color: #E44C76;" onmouseover="this.style.backgroundColor='#C50E40'" onmouseout="this.style.backgroundColor='#E44C76'">Ajouter au panier</button>
      </form>
      <?php endif; ?>
    </div>
  </div>
</div>
 <?php include 'includes/footer.php'; ?>

   </main>

    <!-- JS here -->
      
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