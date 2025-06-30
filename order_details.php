<?php
session_start();
require_once("config.php");

// Vérification de l'utilisateur et de la commande
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

// Récupération des articles de la commande
$stmt = $pdo->prepare("
    SELECT oi.*, p.name AS product_name, p.image_url 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcul du total
$subtotal = 0;
foreach ($items as $item) {
    $subtotal += $item['quantity'] * $item['price'];
}
$shipping_fee = 1500; // par exemple
$total = $subtotal + $shipping_fee;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails de la commande - Ur Desire</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .commande { max-width: 800px; margin: auto; }
        .article { border-bottom: 1px solid #ccc; padding: 10px 0; display: flex; align-items: center; }
        .article img { width: 80px; margin-right: 20px; }
        .total { font-weight: bold; margin-top: 20px; }
    </style>
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
   <?php include 'includes/header.php'; ?>

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
                        <input type="text" name="query" placeholder="Search for product...">
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

      <main>
         <section class="breadcrumb__area include-bg pt-95 pb-90">
      <div class="container">
         <div class="breadcrumb__content p-relative z-index-1">
            <h3 class="breadcrumb__title"> #<?= htmlspecialchars($order['order_number']) ?></h3>
            <div class="breadcrumb__list">
               <span><a href="orders.php">Tes Commandes</a></span>
            </div>
         </div>
      </div>
   </section>
   <section class="tp-order-area pb-160">
      <div class="container">
      <div class="row">
         <div class="col-lg-6">
            <div class="tp-order-details" data-bg-color="#4F3D97" style="background-color: rgb(79, 61, 151);">
               <div class="tp-order-details-top text-center mb-70">
                  <div class="tp-order-details-icon">
                     <span>
                        <svg width="52" height="52" viewBox="0 0 52 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M46 26V51H6V26" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                           <path d="M51 13.5H1V26H51V13.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                           <path d="M26 51V13.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                           <path d="M26 13.5H14.75C13.0924 13.5 11.5027 12.8415 10.3306 11.6694C9.15848 10.4973 8.5 8.9076 8.5 7.25C8.5 5.5924 9.15848 4.00269 10.3306 2.83058C11.5027 1.65848 13.0924 1 14.75 1C23.5 1 26 13.5 26 13.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                           <path d="M26 13.5H37.25C38.9076 13.5 40.4973 12.8415 41.6694 11.6694C42.8415 10.4973 43.5 8.9076 43.5 7.25C43.5 5.5924 42.8415 4.00269 41.6694 2.83058C40.4973 1.65848 38.9076 1 37.25 1C28.5 1 26 13.5 26 13.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                     </span>
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
         <div class="col-lg-6">
            <div class="tp-order-info-wrapper">
               <h4 class="tp-order-info-title">Articles :</h4>
               <?php foreach ($items as $item): ?>
               <div class="article">
                  <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="">
                  <div>
                     <p><strong><?= htmlspecialchars($item['product_name']) ?></strong></p>
                     <p>Quantité : <?= $item['quantity'] ?></p>
                     <p>Prix unitaire : <?= number_format($item['price'], 0, ',', ' ') ?> FCFA</p>
                     <p>Total : <?= number_format($item['quantity'] * $item['price'], 0, ',', ' ') ?> FCFA</p>
                  </div>
               </div>
               <?php endforeach; ?>
               <div class="total">
                  <p><strong>Total de la commande :</strong> <?= number_format($subtotal, 0, ',', ' ') ?> FCFA</p>
                  <p><strong>Frais de livraison :</strong> <?= number_format($shipping_fee, 0, ',', ' ') ?> FCFA</p>
                  <h4><strong>Total à payer :</strong> <?= number_format($total, 0, ',', ' ') ?> FCFA</h4>
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
