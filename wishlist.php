<?php
session_start();
require_once("config.php");

$user_id = $_SESSION['user_id'] ?? null;
?>
<!doctype html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Wishlist - UrDesire</title>
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
               <path d="M11 6L6 1L1 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
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
                        <span>Recherche</span>
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
    <section class="tp-cart-area pb-120">
        <div class="container">
            <section class="breadcrumb__area include-bg pt-95 pb-50">
            <div class="container">
               <div class="row">
                  <div class="col-xxl-12">
                     <div class="breadcrumb__content p-relative z-index-1">
                        <h3 class="breadcrumb__title">Ma wishlist</h3>
                     </div>
                  </div>
               </div>
            </div>
         </section>
            <div class="tp-cart-list mb-45 mr-30">
                <table class="table">
                    <thead>
                    <tr>
                        <th colspan="2">Produit</th>
                        <th>Prix</th>
                        <th>Quantité</th>
                        <th>Action</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (!$user_id) {
                        echo '<tr><td colspan="6">Veuillez vous connecter pour voir votre wishlist.</td></tr>';
                    } else {
                        $stmt = $pdo->prepare("
                            SELECT w.id AS wishlist_id, w.quantity, p.*
                            FROM wishlists w
                            JOIN products p ON w.product_id = p.id
                            WHERE w.user_id = ?
                        ");
                        $stmt->execute([$user_id]);
                        $wishlist = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (empty($wishlist)) {
                            echo '<tr><td colspan="6">Votre liste de souhaits est vide.</td></tr>';
                        } else {
                            foreach ($wishlist as $item):
                                ?>
                                <tr>
                                    <td class="tp-cart-img">
                                        <a href="product-details.php?id=<?= $item['id'] ?>">
                                            <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                        </a>
                                    </td>
                                    <td class="tp-cart-title"><a href="product-details.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></a></td>
                                    <td class="tp-cart-price"><?= number_format($item['price'], 0, ',', ' ') ?> FCFA</td>
                                    <td><?= (int)$item['quantity'] ?></td>
                                    <td>
                                        <form action="add_to_cart.php" method="post">
                                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                            <input type="hidden" name="quantity" value="<?= $item['quantity'] ?>">
                                            <button type="submit" class="tp-btn tp-btn-2 tp-btn" style="background-color: #E44C76;" onmouseover="this.style.backgroundColor='#C50E40'" onmouseout="this.style.backgroundColor='#E44C76'">Ajouter au panier</button>
                                        </form>
                                    </td>
                                    <td>
                                        <form action="includes/remove_from_wishlist.php" method="post">
                                            <input type="hidden" name="wishlist_id" value="<?= $item['wishlist_id'] ?>">
                                            <button type="submit" class="tp-cart-action-btn">Supprimer</button>
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