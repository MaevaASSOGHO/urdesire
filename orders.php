<?php
session_start();
require_once 'config.php'; // Fichier contenant la connexion PDO ($pdo)

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit();
}

// Récupérer les commandes de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mes Commandes - Ur Desire</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
<?php include 'includes/header.php'; ?>

<main>
   <!-- Breadcrumb -->
   <section class="breadcrumb__area include-bg pt-95 pb-50">
      <div class="container">
         <div class="row">
            <div class="col-xxl-12">
               <div class="breadcrumb__content">
                  <h3 class="breadcrumb__title">Mes Commandes</h3>
                  <div class="breadcrumb__list">
                     <span><a href="index.php">Accueil</a></span>
                     <span>Commandes</span>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>

   <!-- Orders List -->
   <section class="tp-cart-area pb-120">
      <div class="container">
         <div class="row">
            <div class="col-xl-12">
               <div class="tp-cart-list mb-45">
                  <table class="table">
                     <thead>
                        <tr>
                           <th>Commande #</th>
                           <th>Date</th>
                           <th>Montant</th>
                           <th>Statut</th>
                           <th>Détails</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php if (count($orders) > 0): ?>
                           <?php foreach ($orders as $order): ?>
                              <tr>
                                 <td>#<?= htmlspecialchars($order['order_number']) ?></td>
                                 <td><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                                 <td><?= number_format($order['total_amount'], 2, '.', ' ') ?> XOF</td>
                                 <td><span class="badge bg-<?= $order['status'] === 'completed' ? 'success' : 'warning' ?>">
                                    <?= ucfirst($order['status']) ?>
                                 </span></td>
                                 <td><a href="order_details.php?id=<?= $order['id'] ?>" class="tp-btn tp-btn-blue">Voir</a></td>
                              </tr>
                           <?php endforeach; ?>
                        <?php else: ?>
                           <tr>
                              <td colspan="5" class="text-center">Aucune commande trouvée.</td>
                           </tr>
                        <?php endif; ?>
                     </tbody>
                  </table>
               </div>
            </div>
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
      <script src="https://unpkg.com/infinite-ajax-scroll@3/dist/infinite-ajax-scroll.min.js"></script>
      <script src="assets/js/main.js"></script>
</body>
</html>
