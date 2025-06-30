<?php
require_once("config.php");
session_start();

$search = $_GET['q'] ?? '';
$results = [];

if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
    $stmt->execute(["%$search%", "%$search%"]);
    $results = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Recherche - UrDesire</title>
  <meta name="description" content="">
   <meta name="viewport" content="width=device-width, initial-scale=1">

   <!-- Place favicon.ico in the root directory -->
   <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/favicon-32x32.png">

   <!-- CSS here -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
      integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
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

   <!-- cart mini area start -->
         <?php include 'includes/cartmini.php'; ?>
   <!-- cart mini area end -->

  <?php include 'includes/header.php'; ?>

  <main>
    <section class="breadcrumb__area include-bg pt-95 pb-50">
      <div class="container">
        <h3>Résultats pour « <?= htmlspecialchars($search) ?> »</h3>
      </div>
    </section>

    <section class="tp-shop-area pb-120">
      <div class="container">
        <div class="row">
          <?php if (empty($results)): ?>
            <div class="col-12"><p>Aucun produit trouvé.</p></div>
          <?php else: ?>
            <?php foreach ($results as $product): ?>
              <div class="col-lg-4 col-md-6">
                <div class="tp-product-item-2 mb-40">
                  <div class="tp-product-thumb-2">
                    <a href="product-details.php?id=<?= $product['id'] ?>">
                      <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    </a>
                  </div>
                  <div class="tp-product-content-2 pt-15">
                    <h4><a href="product-details.php?id=<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></a></h4>
                    <div class="tp-product-price-wrapper-2">
                      <span class="tp-product-price-2"><?= number_format($product['price'], 0, ',', ' ') ?> FCFA</span>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </section>
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
      <script src="assets/js/main.js"></script>
</body>
</html>