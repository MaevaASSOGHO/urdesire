<?php
session_start();
require_once("config.php");

$user_id = $_SESSION['user_id'] ?? null;
$order_id = $_GET['id'] ?? null;

if (!$user_id || !$order_id) {
    echo "Commande non trouvée.";
    exit;
}

// Récupération des infos de la commande
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Commande introuvable.";
    exit;
}

// Récupération des articles
$stmt = $pdo->prepare("SELECT oi.*, p.title FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcul du total si besoin
$subtotal = 0;
foreach ($items as $item) {
    $subtotal += $item['quantity'] * $item['price'];
}
$shipping_fee = 2000; // Exemple en FCFA
$total = $subtotal + $shipping_fee;
?>
<head>
   <meta charset="utf-8">
   <meta http-equiv="x-ua-compatible" content="ie=edge">
   <title>UrDesire - Boutique pour adultes</title>
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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>
     <!-- pre loader area start -->
   <div id="loading">
      <div id="loading-center">
         <div id="loading-center-absolute">
            <!-- loading content here -->
            <div class="tp-preloader-circle"
               style="position: relative; width: 190px; height: 190px; background-color: black; border-radius: 50%;">

               <!-- Cercle SVG décoratif par-dessus -->
               <svg width="190" height="190" viewBox="0 0 380 380" fill="none" xmlns="http://www.w3.org/2000/svg"
                  style="position: absolute; top: 0; left: 0; z-index: 2;">
                  <circle stroke="#D9D9D9" cx="190" cy="190" r="180" stroke-width="6" stroke-linecap="round" />
                  <circle stroke="red" cx="190" cy="190" r="180" stroke-width="6" stroke-linecap="round" />
               </svg>

               <!-- Image centrée -->
               <img src="assets/img/logo/preloader/preloader-icon-centered.svg" alt="logo" style="
                  position: absolute;
                  top: 50%;
                  left: 50%;
                  transform: translate(-50%, -50%);
                  width: 120px;
                  height: 120px;
                  z-index: 1;">
            </div>

            <h3 class="tp-preloader-title">UrDesire</h3>
            <p class="tp-preloader-subtitle">Loading</p>
         </div>
      </div>
   </div>
   </div>
   <!-- pre loader area end -->

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
   <div class="offcanvas__area offcanvas__style-green">
      <div class="offcanvas__wrapper">
         <div class="offcanvas__close">
            <button class="offcanvas__close-btn offcanvas-close-btn">
               <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M11 1L1 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                     stroke-linejoin="round" />
                  <path d="M1 1L11 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                     stroke-linejoin="round" />
               </svg>
            </button>
         </div>
         <div class="offcanvas__content">
            <div class="offcanvas__top mb-70 d-flex justify-content-between align-items-center">
               <div class="offcanvas__logo logo">
                  <a href="index.php">
                     <img src="assets/img/logo/logo/logo-noground.png" alt="logo">
                  </a>
               </div>
            </div>
            <div class="offcanvas__category pb-40">
               <button class="tp-offcanvas-category-toggle" style="background-color: #E44C76;" >
                  <i class="fa-solid fa-bars"></i>
                  Catégories
               </button>
               <div class="tp-category-mobile-menu">

               </div>
            </div>
            <div class="tp-main-menu-mobile fix d-lg-none mb-40"></div>

            <div class="offcanvas__contact align-items-center d-none">
               <div class="offcanvas__contact-icon mr-20">
                  <span>
                     <img src="assets/img/icon/contact.png" alt="">
                  </span>
               </div>
               <div class="offcanvas__contact-content">
                  <h3 class="offcanvas__contact-title">
                     <a href="tel:098-852-987">004524865</a>
                  </h3>
               </div>
            </div>
            <div class="offcanvas__btn">
               <a href="contact.html" class="tp-btn-2 tp-btn-border-2">Contact Us</a>
            </div>
         </div>
         <div class="offcanvas__bottom">
            <div class="offcanvas__footer d-flex align-items-center justify-content-between">
               <div class="offcanvas__currency-wrapper currency">
                  <span class="offcanvas__currency-selected-currency tp-currency-toggle"
                     id="tp-offcanvas-currency-toggle">Currency : USD</span>
                  <ul class="offcanvas__currency-list tp-currency-list">
                     <li>XOF</li>
                     <li>EUR</li>
                  </ul>
               </div>
               <!-- <div class="offcanvas__select language">
                     <div class="offcanvas__lang d-flex align-items-center justify-content-md-end">
                        <div class="offcanvas__lang-img mr-15">
                           <img src="assets/img/icon/language-flag.png" alt="">
                        </div>
                        <div class="offcanvas__lang-wrapper">
                           <span class="offcanvas__lang-selected-lang tp-lang-toggle" id="tp-offcanvas-lang-toggle">English</span>
                           <ul class="offcanvas__lang-list tp-lang-list">
                              <li>Spanish</li>
                              <li>Portugese</li>
                              <li>American</li>
                              <li>Canada</li>
                           </ul>
                        </div>
                     </div>
                  </div> -->
            </div>
         </div>
      </div>
   </div>
   <div class="body-overlay"></div>
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
   <section class="tp-search-area">
      <div class="container">
         <div class="row">
            <div class="col-xl-12">
               <div class="tp-search-form">
                  <div class="tp-search-close text-center mb-20">
                     <button class="tp-search-close-btn tp-search-close-btn"></button>
                  </div>
                  <form action="profile.php" method="get">
                     <div class="tp-search-input mb-10">
                        <input type="text" name="query" placeholder="Recherche un produit...">
                        <button type="submit"><i class="flaticon-search-1"></i></button>
                     </div>
                     <div class="tp-search-category">
                        <span>Rechercher par : </span>
                        <a href="#">Accessoires, </a>
                        <a href="#">Bien-Être, </a>
                        <a href="#">Comestibles, </a>
                        <a href="#">Électronique, </a>
                        <a href="#">Lingerie, </a>
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
   <!-- breadcrumb -->
   <section class="breadcrumb__area include-bg pt-95 pb-90">
      <div class="container">
         <div class="breadcrumb__content p-relative z-index-1">
            <h3 class="breadcrumb__title">Commande #<?= htmlspecialchars($order['order_number']) ?></h3>
            <div class="breadcrumb__list">
               <span><a href="index.php">Accueil</a></span>
               <span>Commande</span>
            </div>
         </div>
      </div>
   </section>

   <!-- order -->
   <section class="tp-order-area pb-160">
      <div class="container">
         <div class="tp-order-inner">
            <div class="row gx-0">
               <!-- Infos générales -->
               <div class="col-lg-6">
                  <div class="tp-order-details" data-bg-color="#4F3D97">
                     <div class="tp-order-details-top text-center mb-70">
                        <div class="tp-order-details-icon">
                           <span>📦</span>
                        </div>
                        <div class="tp-order-details-content">
                           <h3 class="tp-order-details-title">Commande confirmée</h3>
                           <p>Vous recevrez une confirmation par email dès l’expédition</p>
                        </div>
                     </div>

                     <div class="tp-order-details-item-wrapper">
                        <div class="row">
                           <div class="col-sm-6"><div class="tp-order-details-item"><h4>Date :</h4><p><?= date("d/m/Y", strtotime($order['created_at'])) ?></p></div></div>
                           <div class="col-sm-6"><div class="tp-order-details-item"><h4>Livraison prévue :</h4><p><?= date("d/m/Y", strtotime("+6 days", strtotime($order['created_at']))) ?></p></div></div>
                           <div class="col-sm-6"><div class="tp-order-details-item"><h4>N° Commande :</h4><p>#<?= htmlspecialchars($order['order_number']) ?></p></div></div>
                           <div class="col-sm-6"><div class="tp-order-details-item"><h4>Statut :</h4><p><?= ucfirst($order['status']) ?></p></div></div>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Détails produits -->
               <div class="col-lg-6">
                  <div class="tp-order-info-wrapper">
                     <h4 class="tp-order-info-title">Détails de la commande</h4>
                     <div class="tp-order-info-list">
                        <ul>
                           <li class="tp-order-info-list-header"><h4>Produit</h4><h4>Total</h4></li>

                           <?php foreach ($items as $item): ?>
                              <li class="tp-order-info-list-desc">
                                 <p><?= htmlspecialchars($item['title']) ?> <span>x <?= $item['quantity'] ?></span></p>
                                 <span><?= number_format($item['price'] * $item['quantity'], 0, ',', ' ') ?> FCFA</span>
                              </li>
                           <?php endforeach; ?>

                           <li class="tp-order-info-list-subtotal"><span>Sous-total</span><span><?= number_format($subtotal, 0, ',', ' ') ?> FCFA</span></li>
                           <li class="tp-order-info-list-shipping"><span>Livraison</span><span><?= number_format($shipping_fee, 0, ',', ' ') ?> FCFA</span></li>
                           <li class="tp-order-info-list-total"><span>Total</span><span><?= number_format($total, 0, ',', ' ') ?> FCFA</span></li>
                        </ul>
                     </div>
                  </div>
               </div>

            </div>
         </div>
      </div>
   </section>
</main>

<?php include 'includes/footer.php'; ?>


      <!-- JS here -->
      <!-- <script data-cfasync="false" src="../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="assets/js/vendor/jquery.js"></script> -->
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
