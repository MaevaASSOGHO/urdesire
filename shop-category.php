<?php
require_once("config.php");

try {
    $categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) {
    die("Erreur lors de la récupération des catégories : " . $e->getMessage());
}
// Mapping images personnalisées
$imageMap = [
    1 => "assets/img/category/accessoire.png",
    2 => "assets/img/category/bien-etre.png",
    3 => "assets/img/category/comestibles.png",
    4 => "assets/img/category/electronics.png",
    5 => "assets/img/category/lingerie.png"
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catégories - UrDesire</title>
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

       <!-- header area start -->
   <?php include 'includes/header.php'; ?>
   
   <!-- header area end -->
<main>
    <section class="breadcrumb__area include-bg pt-100 pb-50">
        <div class="container">
            <div class="breadcrumb__content text-center">
                <h3 class="breadcrumb__title">Nos Catégories</h3>
                <div class="breadcrumb__list">
                    <span><a href="index.php">Accueil</a></span>
                    <span>Catégories</span>
                </div>
            </div>
        </div>
    </section>

    <section class="tp-category-area pb-120">
        <div class="container">
            <div class="row">
                <?php foreach ($categories as $cat): ?>
                    <?php
                        $image = $imageMap[$cat['id']] ?? 'assets/img/cat/default.jpg';
                    ?>
                    <div class="col-lg-4 col-sm-6">
                        <div class="tp-category-main-box mb-25 p-relative fix" style="background-color: #F3F5F7;">
                            <div class="tp-category-main-thumb include-bg transition-3"
                                 style="background-image: url('<?= $image ?>'); height: 200px; background-size: cover;"></div>
                            <div class="tp-category-main-content text-center">
                                <h3 class="tp-category-main-title">
                                    <a href="shop.php?category=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a>
                                </h3>
                                <span class="tp-category-main-item">Voir les produits</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>
</body>
</html>
