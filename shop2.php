<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Démarrage de la session
session_start();

// Redirection si l'utilisateur n'est pas connecté
if (!isset($_SESSION['pseudo'])) {
    header('Location: login.html');
    exit;
}

// Déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.html');
    exit;
}
?>
<?php
require_once 'config.php';

$category_id = $_GET['category'] ?? null;

$minPrice = $_GET['min_price'] ?? 0;
$maxPrice = $_GET['max_price'] ?? 999999;
$stockFilter = $_GET['stock'] ?? null;
$categorie = $_GET['categorie'] ?? null;

$sql = "SELECT * FROM products WHERE price BETWEEN ? AND ?";
$params = [$minPrice, $maxPrice];

if ($stockFilter === 'in') {
    $sql .= " AND stock > 0";
} elseif ($stockFilter === 'out') {
    $sql .= " AND stock <= 0";
}

if ($categorie) {
    $sql .= " AND category_id = ?";
    $params[] = $categorie;
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $produits = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur base de données : " . $e->getMessage());
}
?>
<?php
require_once("config.php");

$category_id = $_GET['category'] ?? null;
$category_name = "Tous Nos Produits";

if ($category_id) {
    $stmtCat = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $stmtCat->execute([$category_id]);
    $category_name = $stmtCat->fetchColumn() ?: $category_name;
}

$sql = "SELECT * FROM products WHERE 1";
$params = [];

if ($category_id) {
    $sql .= " AND category_id = ?";
    $params[] = $category_id;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produits = $stmt->fetchAll();
?>

<!doctype html>
<html class="no-js" lang="zxx">

<!-- Mirrored from html.weblearnbd.net/shofy-prv/shofy/index-5.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 15 Oct 2023 08:17:47 GMT -->

<head>
   <meta charset="utf-8">
   <meta http-equiv="x-ua-compatible" content="ie=edge">
   <title>UrDesire - Boutique pour adultes</title>
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
   <!--[if lte IE 9]>
      <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
      <![endif]-->


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
                  <form action="#">
                     <div class="tp-search-input mb-10">
                        <input type="text" placeholder="Recherche un produit...">
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

       <!-- header area start -->
   <?php include 'includes/header.php'; ?>
   
   <!-- header area end -->



      <!-- filter offcanvas area start -->
      <div class="tp-filter-offcanvas-area">
         <div class="tp-filter-offcanvas-wrapper">
            <div class="tp-filter-offcanvas-close">
               <button type="button" class="tp-filter-offcanvas-close-btn filter-close-btn">
                  <i class="fa-solid fa-xmark"></i>
                  Fermer
               </button>
            </div>
            <div class="tp-shop-sidebar">
               <!-- filter -->
               <div class="tp-shop-widget mb-35">
                  <h3 class="tp-shop-widget-title no-border">Filtre par Prix</h3>

                  <div class="tp-shop-widget-content">
                     <div class="tp-shop-widget-filter">
                        <div id="slider-range-offcanvas" class="mb-10"></div>
                        <div class="tp-shop-widget-filter-info d-flex align-items-center justify-content-between">
                           <span class="input-range">
                              <input type="text" id="amount-offcanvas" readonly>
                           </span>
                           <button class="tp-shop-widget-filter-btn" type="button">Filtrer</button>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- status -->
               <div class="tp-shop-widget mb-50">
                  <h3 class="tp-shop-widget-title">Statut du Produit</h3>

                  <div class="tp-shop-widget-content">
                     <div class="tp-shop-widget-checkbox">
                        <ul class="filter-items filter-checkbox">
                           <li class="filter-item checkbox">
                              <input id="on_sale2" type="checkbox">
                              <label for="on_sale2">En promotion</label>
                           </li>
                           <li class="filter-item checkbox">
                              <input id="in_stock2" type="checkbox">
                              <label for="in_stock2">En stock</label>
                           </li>
                        </ul><!-- .filter-items -->
                     </div>
                  </div>
               </div>
               <!-- categories -->
               <div class="tp-shop-widget mb-50">
                  <h3 class="tp-shop-widget-title">Catégories</h3>

                  <div class="tp-shop-widget-content">
                     <div class="tp-shop-widget-categories">
                        <ul>
                           <li><a href="#">Accessoires <span>10</span></a></li>
                           <li><a href="#">Bien-Être <span>18</span></a></li>
                           <li><a href="#">Comestibles<span>22</span></a></li>
                           <li><a href="#">Électroniques<span>17</span></a></li>
                           <li><a href="#">Lingerie <span>22</span></a></li>
                        </ul>
                     </div>
                  </div>
               </div>
              
               <!-- product rating -->
               <?php include 'top-rated-products.php'; ?>
               <!-- brand -->
               <!-- <div class="tp-shop-widget mb-50">
                  <h3 class="tp-shop-widget-title">Popular Brands</h3>

                  <div class="tp-shop-widget-content ">
                     <div class="tp-shop-widget-brand-list d-flex align-items-center justify-content-between flex-wrap">
                        <div class="tp-shop-widget-brand-item">
                           <a href="#">
                              <img src="assets/img/product/shop/brand/logo_01.png" alt="">
                           </a>
                        </div>
                        <div class="tp-shop-widget-brand-item">
                           <a href="#">
                              <img src="assets/img/product/shop/brand/logo_02.png" alt="">
                           </a>
                        </div>
                        <div class="tp-shop-widget-brand-item">
                           <a href="#">
                              <img src="assets/img/product/shop/brand/logo_03.png" alt="">
                           </a>
                        </div>
                        <div class="tp-shop-widget-brand-item">
                           <a href="#">
                              <img src="assets/img/product/shop/brand/logo_04.png" alt="">
                           </a>
                        </div>
                        <div class="tp-shop-widget-brand-item">
                           <a href="#">
                              <img src="assets/img/product/shop/brand/logo_05.png" alt="">
                           </a>
                        </div>
                        <div class="tp-shop-widget-brand-item">
                           <a href="#">
                              <img src="assets/img/product/shop/brand/logo_06.png" alt="">
                           </a>
                        </div>
                        <div class="tp-shop-widget-brand-item">
                           <a href="#">
                              <img src="assets/img/product/shop/brand/logo_07.png" alt="">
                           </a>
                        </div>
                        <div class="tp-shop-widget-brand-item">
                           <a href="#">
                              <img src="assets/img/product/shop/brand/logo_08.png" alt="">
                           </a>
                        </div>
                     </div>
                  </div>
               </div> -->
            </div>
         </div>
      </div>
      <!-- filter offcanvas area end -->

      <main>

         <!-- breadcrumb area start -->
         <section class="breadcrumb__area include-bg pt-100 pb-50">
            <div class="container">
               <div class="row">
                  <div class="col-xxl-12">
                     <div class="breadcrumb__content p-relative z-index-1">
                        <h3 class="breadcrumb__title">Tous Nos Produits</h3>
                        <div class="breadcrumb__list">
                           <span><a href="index.php">Accueil</a></span>
                           <span>Tous Nos Produits</span>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </section>
         <!-- breadcrumb area end -->

         <!-- shop area start -->
         <section class="tp-shop-area pb-120">
            <div class="container">
               <div class="row">
                  <div class="col-xl-3 col-lg-4">
                     <div class="tp-shop-sidebar mr-10">
                        <!-- filter -->
                        <div class="tp-shop-widget mb-35">
                           <h3 class="tp-shop-widget-title no-border">Filtre de Prix</h3>

                           <div class="tp-shop-widget-content">
                              <div class="tp-shop-widget-filter">
                                 <div id="slider-range" class="mb-10"></div>
                                 <div class="tp-shop-widget-filter-info d-flex align-items-center justify-content-between">
                                    <span class="input-range">
                                       <input type="text" id="amount" readonly>
                                    </span>
                                    <button class="tp-shop-widget-filter-btn" type="button">Filtrer</button>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <!-- status -->
                        <div class="tp-shop-widget mb-50">
                           <h3 class="tp-shop-widget-title">Statut du Produit</h3>

                           <div class="tp-shop-widget-content">
                              <div class="tp-shop-widget-checkbox">
                                 <ul class="filter-items filter-checkbox">
                                    <li class="filter-item checkbox">
                                       <input id="on_sale" type="checkbox" <?= (isset($_GET['stock']) && $_GET['stock'] === 'out') ? 'checked' : '' ?>>
                                       <label for="on_sale">En rupture</label>
                                    </li>
                                    <li class="filter-item checkbox">
                                       <input id="in_stock" type="checkbox" <?= (isset($_GET['stock']) && $_GET['stock'] === 'in') ? 'checked' : '' ?>>
                                       <label for="in_stock">En stock</label>
                                    </li>
                                 </ul><!-- .filter-items -->
                              </div>
                           </div>
                        </div>
                        <!-- categories -->
                        <div class="tp-shop-widget mb-50">
                           <?php include 'categories-widget.php'; ?>
                           <h3 class="tp-shop-widget-title">Catégories</h3>
                           <div class="tp-shop-widget-content">
                              <div class="tp-shop-widget-categories">
                                 <ul>
                                 <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                       <li>
                                       <a href="shop.php?categorie=<?= urlencode($cat['id']) ?>">
                                          <?= htmlspecialchars($cat['name']) ?>
                                          <span><?= $cat['total'] ?></span>
                                       </a>
                                       </li>
                                    <?php endforeach; ?>
                                 <?php else: ?>
                                    <li><em>Aucune catégorie disponible</em></li>
                                 <?php endif; ?>
                                 </ul>
                              </div>
                           </div>
                        </div>


                        <!-- color -->
                        <!-- <div class="tp-shop-widget mb-50">
                           <h3 class="tp-shop-widget-title">Filter by Color</h3>

                           <div class="tp-shop-widget-content">
                              <div class="tp-shop-widget-checkbox-circle-list">
                                 <ul>
                                    <li>
                                       <div class="tp-shop-widget-checkbox-circle">
                                          <input type="checkbox" id="red">
                                          <label for="red">Red</label>
                                          <span data-bg-color="#FF401F" class="tp-shop-widget-checkbox-circle-self"></span>
                                       </div>
                                       <span class="tp-shop-widget-checkbox-circle-number">8</span>
                                    </li>
                                    <li>
                                       <div class="tp-shop-widget-checkbox-circle">
                                          <input type="checkbox" id="dark_blue">
                                          <label for="dark_blue">Dark Blue</label>
                                          <span data-bg-color="#4666FF" class="tp-shop-widget-checkbox-circle-self"></span>
                                       </div>
                                       <span class="tp-shop-widget-checkbox-circle-number">14</span>
                                    </li>
                                    <li>
                                       <div class="tp-shop-widget-checkbox-circle">
                                          <input type="checkbox" id="oragnge">
                                          <label for="oragnge">Orange</label>
                                          <span data-bg-color="#FF9E2C" class="tp-shop-widget-checkbox-circle-self"></span>
                                       </div>
                                       <span class="tp-shop-widget-checkbox-circle-number">18</span>
                                    </li>
                                    <li>
                                       <div class="tp-shop-widget-checkbox-circle">
                                          <input type="checkbox" id="purple">
                                          <label for="purple">Purple</label>
                                          <span data-bg-color="#B615FD" class="tp-shop-widget-checkbox-circle-self"></span>
                                       </div>
                                       <span class="tp-shop-widget-checkbox-circle-number">23</span>
                                    </li>
                                    <li>
                                       <div class="tp-shop-widget-checkbox-circle">
                                          <input type="checkbox" id="yellow">
                                          <label for="yellow">Yellow</label>
                                          <span data-bg-color="#FFD747" class="tp-shop-widget-checkbox-circle-self"></span>
                                       </div>
                                       <span class="tp-shop-widget-checkbox-circle-number">17</span>
                                    </li>
                                    <li>
                                       <div class="tp-shop-widget-checkbox-circle">
                                          <input type="checkbox" id="green">
                                          <label for="green">Green</label>
                                          <span data-bg-color="#41CF0F" class="tp-shop-widget-checkbox-circle-self"></span>
                                       </div>
                                       <span class="tp-shop-widget-checkbox-circle-number">15</span>
                                    </li>
                                 </ul>
                              </div>
                           </div>
                        </div> -->
                        <!-- product rating -->
                                       <?php include 'top-rated-products.php'; ?>

                        <!-- brand -->
                        <div class="tp-shop-widget mb-50">
                           <h3 class="tp-shop-widget-title">Popular Brands</h3>

                           <div class="tp-shop-widget-content ">
                              <div class="tp-shop-widget-brand-list d-flex align-items-center justify-content-between flex-wrap">
                                 <div class="tp-shop-widget-brand-item">
                                    <a href="#">
                                       <img src="assets/img/product/shop/brand/logo_01.png" alt="">
                                    </a>
                                 </div>
                                 <div class="tp-shop-widget-brand-item">
                                    <a href="#">
                                       <img src="assets/img/product/shop/brand/logo_02.png" alt="">
                                    </a>
                                 </div>
                                 <div class="tp-shop-widget-brand-item">
                                    <a href="#">
                                       <img src="assets/img/product/shop/brand/logo_03.png" alt="">
                                    </a>
                                 </div>
                                 <div class="tp-shop-widget-brand-item">
                                    <a href="#">
                                       <img src="assets/img/product/shop/brand/logo_04.png" alt="">
                                    </a>
                                 </div>
                                 <div class="tp-shop-widget-brand-item">
                                    <a href="#">
                                       <img src="assets/img/product/shop/brand/logo_05.png" alt="">
                                    </a>
                                 </div>
                                 <div class="tp-shop-widget-brand-item">
                                    <a href="#">
                                       <img src="assets/img/product/shop/brand/logo_06.png" alt="">
                                    </a>
                                 </div>
                                 <div class="tp-shop-widget-brand-item">
                                    <a href="#">
                                       <img src="assets/img/product/shop/brand/logo_07.png" alt="">
                                    </a>
                                 </div>
                                 <div class="tp-shop-widget-brand-item">
                                    <a href="#">
                                       <img src="assets/img/product/shop/brand/logo_08.png" alt="">
                                    </a>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-xl-9 col-lg-8">
                     <?php include 'products-grid.php'; ?>
                  </div>
               </div>
            </div>
         </section>
         
         <!-- shop area end -->

         <div class="modal fade tp-product-modal" id="producQuickViewModal" tabindex="-1" aria-labelledby="producQuickViewModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
               <div class="modal-content">
                  <div class="tp-product-modal-content d-lg-flex align-items-start">
                     <button type="button" class="tp-product-modal-close-btn" data-bs-toggle="modal" data-bs-target="#producQuickViewModal"><i class="fa-regular fa-xmark"></i></button>
                     <div class="tp-product-details-thumb-wrapper tp-tab d-sm-flex">
                        <nav>
                           <div class="nav nav-tabs flex-sm-column " id="productDetailsNavThumb" role="tablist">
                              <button class="nav-link active" id="nav-1-tab" data-bs-toggle="tab" data-bs-target="#nav-1" type="button" role="tab" aria-controls="nav-1" aria-selected="true">
                                 <img src="assets/img/product/details/nav/product-details-nav-1.jpg" alt="">
                              </button>
                              <button class="nav-link" id="nav-2-tab" data-bs-toggle="tab" data-bs-target="#nav-2" type="button" role="tab" aria-controls="nav-2" aria-selected="false">
                                 <img src="assets/img/product/details/nav/product-details-nav-2.jpg" alt="">
                              </button>
                              <button class="nav-link" id="nav-3-tab" data-bs-toggle="tab" data-bs-target="#nav-3" type="button" role="tab" aria-controls="nav-3" aria-selected="false">
                                 <img src="assets/img/product/details/nav/product-details-nav-3.jpg" alt="">
                              </button>
                              <button class="nav-link" id="nav-4-tab" data-bs-toggle="tab" data-bs-target="#nav-4" type="button" role="tab" aria-controls="nav-4" aria-selected="false">
                                 <img src="assets/img/product/details/nav/product-details-nav-4.jpg" alt="">
                              </button>
                           </div>
                        </nav>
                        <div class="tab-content m-img" id="productDetailsNavContent">
                           <div class="tab-pane fade show active" id="nav-1" role="tabpanel" aria-labelledby="nav-1-tab" tabindex="0">
                              <div class="tp-product-details-nav-main-thumb">
                                 <img src="assets/img/product/details/main/product-details-main-1.jpg" alt="">
                              </div>
                           </div>
                           <div class="tab-pane fade" id="nav-2" role="tabpanel" aria-labelledby="nav-2-tab" tabindex="0">
                              <div class="tp-product-details-nav-main-thumb">
                                 <img src="assets/img/product/details/main/product-details-main-2.jpg" alt="">
                              </div>
                           </div>
                           <div class="tab-pane fade" id="nav-3" role="tabpanel" aria-labelledby="nav-3-tab" tabindex="0">
                              <div class="tp-product-details-nav-main-thumb">
                                 <img src="assets/img/product/details/main/product-details-main-3.jpg" alt="">
                              </div>
                           </div>
                           <div class="tab-pane fade" id="nav-4" role="tabpanel" aria-labelledby="nav-4-tab" tabindex="0">
                              <div class="tp-product-details-nav-main-thumb">
                                 <img src="assets/img/product/details/main/product-details-main-4.jpg" alt="">
                              </div>
                           </div>
                         </div>
                     </div>
                     <div class="tp-product-details-wrapper">
                        <div class="tp-product-details-category">
                           <span>Computers & Tablets</span>
                        </div>
                        <h3 class="tp-product-details-title">Samsung galaxy A8 tablet</h3>
   
                        <!-- inventory details -->
                        <div class="tp-product-details-inventory d-flex align-items-center mb-10">
                           <div class="tp-product-details-stock mb-10">
                              <span>In Stock</span>
                           </div>
                           <div class="tp-product-details-rating-wrapper d-flex align-items-center mb-10">
                              <div class="tp-product-details-rating">
                                 <span><i class="fa-solid fa-star"></i></span>
                                 <span><i class="fa-solid fa-star"></i></span>
                                 <span><i class="fa-solid fa-star"></i></span>
                                 <span><i class="fa-solid fa-star"></i></span>
                                 <span><i class="fa-solid fa-star"></i></span>
                              </div>
                              <div class="tp-product-details-reviews">
                                 <span>(36 Reviews)</span>
                              </div>
                           </div>
                        </div>
                        <p>A Screen Everyone Will Love: Whether your family is streaming or video chatting with friends tablet A8... <span>See more</span></p>
   
                        <!-- price -->
                        <div class="tp-product-details-price-wrapper mb-20">
                           <span class="tp-product-details-price old-price">$320.00</span>
                           <span class="tp-product-details-price new-price">$236.00</span>
                        </div>
   
                        <!-- variations -->
                        <div class="tp-product-details-variation">
                           <!-- single item -->
                           <div class="tp-product-details-variation-item">
                              <h4 class="tp-product-details-variation-title">Color :</h4>
                              <div class="tp-product-details-variation-list">
                                 <button type="button" class="color tp-color-variation-btn" >
                                    <span data-bg-color="#F8B655"></span>
                                    <span class="tp-color-variation-tootltip">Yellow</span>
                                 </button>
                                 <button type="button" class="color tp-color-variation-btn active" >
                                    <span data-bg-color="#CBCBCB"></span>
                                    <span class="tp-color-variation-tootltip">Gray</span>
                                 </button>
                                 <button type="button" class="color tp-color-variation-btn" >
                                    <span data-bg-color="#494E52"></span>
                                    <span class="tp-color-variation-tootltip">Black</span>
                                 </button>
                                 <button type="button" class="color tp-color-variation-btn" >
                                    <span data-bg-color="#B4505A"></span>
                                    <span class="tp-color-variation-tootltip">Brown</span>
                                 </button>
                              </div>
                           </div>
                        </div>
   
                        <!-- actions -->
                        <div class="tp-product-details-action-wrapper">
                           <h3 class="tp-product-details-action-title">Quantity</h3>
                           <div class="tp-product-details-action-item-wrapper d-flex align-items-center">
                              <div class="tp-product-details-quantity">
                                 <div class="tp-product-quantity mb-15 mr-15">
                                    <span class="tp-cart-minus">
                                       <svg width="11" height="2" viewBox="0 0 11 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M1 1H10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                       </svg>                                                            
                                    </span>
                                    <input class="tp-cart-input" type="text" value="1">
                                    <span class="tp-cart-plus">
                                       <svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M1 6H10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                          <path d="M5.5 10.5V1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                       </svg>
                                    </span>
                                 </div>
                              </div>
                              <div class="tp-product-details-add-to-cart mb-15 w-100">
                                 <button class="tp-product-details-add-to-cart-btn w-100">Add To Cart</button>
                              </div>
                           </div>
                           <button class="tp-product-details-buy-now-btn w-100">Buy Now</button>
                        </div>
                        <div class="tp-product-details-action-sm">
                           <button type="button" class="tp-product-details-action-sm-btn">
                              <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                 <path d="M1 3.16431H10.8622C12.0451 3.16431 12.9999 4.08839 12.9999 5.23315V7.52268" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                 <path d="M3.25177 0.985168L1 3.16433L3.25177 5.34354" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                 <path d="M12.9999 12.5983H3.13775C1.95486 12.5983 1 11.6742 1 10.5295V8.23993" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                 <path d="M10.748 14.7774L12.9998 12.5983L10.748 10.4191" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                              </svg>
                              Compare
                           </button>
                           <button type="button" class="tp-product-details-action-sm-btn">
                              <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                 <path fill-rule="evenodd" clip-rule="evenodd" d="M2.33541 7.54172C3.36263 10.6766 7.42094 13.2113 8.49945 13.8387C9.58162 13.2048 13.6692 10.6421 14.6635 7.5446C15.3163 5.54239 14.7104 3.00621 12.3028 2.24514C11.1364 1.8779 9.77578 2.1014 8.83648 2.81432C8.64012 2.96237 8.36757 2.96524 8.16974 2.81863C7.17476 2.08487 5.87499 1.86999 4.69024 2.24514C2.28632 3.00549 1.68259 5.54167 2.33541 7.54172ZM8.50115 15C8.4103 15 8.32018 14.9784 8.23812 14.9346C8.00879 14.8117 2.60674 11.891 1.29011 7.87081C1.28938 7.87081 1.28938 7.8701 1.28938 7.8701C0.462913 5.33895 1.38316 2.15812 4.35418 1.21882C5.7492 0.776121 7.26952 0.97088 8.49895 1.73195C9.69029 0.993159 11.2729 0.789057 12.6401 1.21882C15.614 2.15956 16.5372 5.33966 15.7115 7.8701C14.4373 11.8443 8.99571 14.8088 8.76492 14.9332C8.68286 14.9777 8.592 15 8.50115 15Z" fill="currentColor"/>
                                 <path d="M8.49945 13.8387L8.42402 13.9683L8.49971 14.0124L8.57526 13.9681L8.49945 13.8387ZM14.6635 7.5446L14.5209 7.4981L14.5207 7.49875L14.6635 7.5446ZM12.3028 2.24514L12.348 2.10211L12.3478 2.10206L12.3028 2.24514ZM8.83648 2.81432L8.92678 2.93409L8.92717 2.9338L8.83648 2.81432ZM8.16974 2.81863L8.25906 2.69812L8.25877 2.69791L8.16974 2.81863ZM4.69024 2.24514L4.73548 2.38815L4.73552 2.38814L4.69024 2.24514ZM8.23812 14.9346L8.16727 15.0668L8.16744 15.0669L8.23812 14.9346ZM1.29011 7.87081L1.43266 7.82413L1.39882 7.72081H1.29011V7.87081ZM1.28938 7.8701L1.43938 7.87009L1.43938 7.84623L1.43197 7.82354L1.28938 7.8701ZM4.35418 1.21882L4.3994 1.36184L4.39955 1.36179L4.35418 1.21882ZM8.49895 1.73195L8.42 1.85949L8.49902 1.90841L8.57801 1.85943L8.49895 1.73195ZM12.6401 1.21882L12.6853 1.0758L12.685 1.07572L12.6401 1.21882ZM15.7115 7.8701L15.5689 7.82356L15.5686 7.8243L15.7115 7.8701ZM8.76492 14.9332L8.69378 14.8011L8.69334 14.8013L8.76492 14.9332ZM2.19287 7.58843C2.71935 9.19514 4.01596 10.6345 5.30013 11.744C6.58766 12.8564 7.88057 13.6522 8.42402 13.9683L8.57487 13.709C8.03982 13.3978 6.76432 12.6125 5.49626 11.517C4.22484 10.4185 2.97868 9.02313 2.47795 7.49501L2.19287 7.58843ZM8.57526 13.9681C9.12037 13.6488 10.4214 12.8444 11.7125 11.729C12.9999 10.6167 14.2963 9.17932 14.8063 7.59044L14.5207 7.49875C14.0364 9.00733 12.7919 10.4 11.5164 11.502C10.2446 12.6008 8.9607 13.3947 8.42364 13.7093L8.57526 13.9681ZM14.8061 7.59109C15.1419 6.5613 15.1554 5.39131 14.7711 4.37633C14.3853 3.35729 13.5989 2.49754 12.348 2.10211L12.2576 2.38816C13.4143 2.75381 14.1347 3.54267 14.4905 4.48255C14.8479 5.42648 14.8379 6.52568 14.5209 7.4981L14.8061 7.59109ZM12.3478 2.10206C11.137 1.72085 9.72549 1.95125 8.7458 2.69484L8.92717 2.9338C9.82606 2.25155 11.1357 2.03494 12.2577 2.38821L12.3478 2.10206ZM8.74618 2.69455C8.60221 2.8031 8.40275 2.80462 8.25906 2.69812L8.08043 2.93915C8.33238 3.12587 8.67804 3.12163 8.92678 2.93409L8.74618 2.69455ZM8.25877 2.69791C7.225 1.93554 5.87527 1.71256 4.64496 2.10213L4.73552 2.38814C5.87471 2.02742 7.12452 2.2342 8.08071 2.93936L8.25877 2.69791ZM4.64501 2.10212C3.39586 2.49722 2.61099 3.35688 2.22622 4.37554C1.84299 5.39014 1.85704 6.55957 2.19281 7.58826L2.478 7.49518C2.16095 6.52382 2.15046 5.42513 2.50687 4.48154C2.86175 3.542 3.58071 2.7534 4.73548 2.38815L4.64501 2.10212ZM8.50115 14.85C8.43415 14.85 8.36841 14.8341 8.3088 14.8023L8.16744 15.0669C8.27195 15.1227 8.38645 15.15 8.50115 15.15V14.85ZM8.30897 14.8024C8.19831 14.7431 6.7996 13.9873 5.26616 12.7476C3.72872 11.5046 2.07716 9.79208 1.43266 7.82413L1.14756 7.9175C1.81968 9.96978 3.52747 11.7277 5.07755 12.9809C6.63162 14.2373 8.0486 15.0032 8.16727 15.0668L8.30897 14.8024ZM1.29011 7.72081C1.31557 7.72081 1.34468 7.72745 1.37175 7.74514C1.39802 7.76231 1.41394 7.78437 1.42309 7.8023C1.43191 7.81958 1.43557 7.8351 1.43727 7.84507C1.43817 7.8504 1.43869 7.85518 1.43898 7.85922C1.43913 7.86127 1.43923 7.8632 1.43929 7.865C1.43932 7.86591 1.43934 7.86678 1.43936 7.86763C1.43936 7.86805 1.43937 7.86847 1.43937 7.86888C1.43937 7.86909 1.43937 7.86929 1.43938 7.86949C1.43938 7.86959 1.43938 7.86969 1.43938 7.86979C1.43938 7.86984 1.43938 7.86992 1.43938 7.86994C1.43938 7.87002 1.43938 7.87009 1.28938 7.8701C1.13938 7.8701 1.13938 7.87017 1.13938 7.87025C1.13938 7.87027 1.13938 7.87035 1.13938 7.8704C1.13938 7.8705 1.13938 7.8706 1.13938 7.8707C1.13938 7.8709 1.13938 7.87111 1.13938 7.87131C1.13939 7.87173 1.13939 7.87214 1.1394 7.87257C1.13941 7.87342 1.13943 7.8743 1.13946 7.8752C1.13953 7.87701 1.13962 7.87896 1.13978 7.88103C1.14007 7.88512 1.14059 7.88995 1.14151 7.89535C1.14323 7.90545 1.14694 7.92115 1.15585 7.93861C1.16508 7.95672 1.18114 7.97896 1.20762 7.99626C1.2349 8.01409 1.26428 8.02081 1.29011 8.02081V7.72081ZM1.43197 7.82354C0.623164 5.34647 1.53102 2.26869 4.3994 1.36184L4.30896 1.0758C1.23531 2.04755 0.302663 5.33142 1.14679 7.91665L1.43197 7.82354ZM4.39955 1.36179C5.7527 0.932384 7.22762 1.12136 8.42 1.85949L8.57791 1.60441C7.31141 0.820401 5.74571 0.619858 4.30881 1.07585L4.39955 1.36179ZM8.57801 1.85943C9.73213 1.14371 11.2694 0.945205 12.5951 1.36192L12.685 1.07572C11.2763 0.632908 9.64845 0.842602 8.4199 1.60447L8.57801 1.85943ZM12.5948 1.36184C15.4664 2.27018 16.3769 5.34745 15.5689 7.82356L15.8541 7.91663C16.6975 5.33188 15.7617 2.04893 12.6853 1.07581L12.5948 1.36184ZM15.5686 7.8243C14.9453 9.76841 13.2952 11.4801 11.7526 12.7288C10.2142 13.974 8.80513 14.7411 8.69378 14.8011L8.83606 15.0652C8.9555 15.0009 10.3826 14.2236 11.9413 12.9619C13.4957 11.7037 15.2034 9.94602 15.8543 7.91589L15.5686 7.8243ZM8.69334 14.8013C8.6337 14.8337 8.56752 14.85 8.50115 14.85V15.15C8.61648 15.15 8.73201 15.1217 8.83649 15.065L8.69334 14.8013Z" fill="currentColor"/>
                                 <path fill-rule="evenodd" clip-rule="evenodd" d="M12.8384 6.93209C12.5548 6.93209 12.3145 6.71865 12.2911 6.43693C12.2427 5.84618 11.8397 5.34743 11.266 5.1656C10.9766 5.07361 10.8184 4.76962 10.9114 4.48718C11.0059 4.20402 11.3129 4.05023 11.6031 4.13934C12.6017 4.45628 13.3014 5.32371 13.3872 6.34925C13.4113 6.64606 13.1864 6.90622 12.8838 6.92993C12.8684 6.93137 12.8538 6.93209 12.8384 6.93209Z" fill="currentColor"/>
                                 <path d="M12.8384 6.93209C12.5548 6.93209 12.3145 6.71865 12.2911 6.43693C12.2427 5.84618 11.8397 5.34743 11.266 5.1656C10.9766 5.07361 10.8184 4.76962 10.9114 4.48718C11.0059 4.20402 11.3129 4.05023 11.6031 4.13934C12.6017 4.45628 13.3014 5.32371 13.3872 6.34925C13.4113 6.64606 13.1864 6.90622 12.8838 6.92993C12.8684 6.93137 12.8538 6.93209 12.8384 6.93209" stroke="currentColor" stroke-width="0.3"/>
                              </svg>
                              Add Wishlist
                           </button>
                           <button type="button" class="tp-product-details-action-sm-btn">
                              <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                 <path d="M8.575 12.6927C8.775 12.6927 8.94375 12.6249 9.08125 12.4895C9.21875 12.354 9.2875 12.1878 9.2875 11.9907C9.2875 11.7937 9.21875 11.6275 9.08125 11.492C8.94375 11.3565 8.775 11.2888 8.575 11.2888C8.375 11.2888 8.20625 11.3565 8.06875 11.492C7.93125 11.6275 7.8625 11.7937 7.8625 11.9907C7.8625 12.1878 7.93125 12.354 8.06875 12.4895C8.20625 12.6249 8.375 12.6927 8.575 12.6927ZM8.55625 5.0638C8.98125 5.0638 9.325 5.17771 9.5875 5.40553C9.85 5.63335 9.98125 5.92582 9.98125 6.28294C9.98125 6.52924 9.90625 6.77245 9.75625 7.01258C9.60625 7.25272 9.3625 7.5144 9.025 7.79763C8.7 8.08087 8.44063 8.3795 8.24688 8.69352C8.05313 9.00754 7.95625 9.29385 7.95625 9.55246C7.95625 9.68792 8.00938 9.79567 8.11563 9.87572C8.22188 9.95576 8.34375 9.99578 8.48125 9.99578C8.63125 9.99578 8.75625 9.94653 8.85625 9.84801C8.95625 9.74949 9.01875 9.62635 9.04375 9.47857C9.08125 9.23228 9.16562 9.0137 9.29688 8.82282C9.42813 8.63195 9.63125 8.42568 9.90625 8.20402C10.2812 7.89615 10.5531 7.58829 10.7219 7.28042C10.8906 6.97256 10.975 6.62775 10.975 6.246C10.975 5.59333 10.7594 5.06996 10.3281 4.67589C9.89688 4.28183 9.325 4.0848 8.6125 4.0848C8.1375 4.0848 7.7 4.17716 7.3 4.36187C6.9 4.54659 6.56875 4.81751 6.30625 5.17463C6.20625 5.31009 6.16563 5.44863 6.18438 5.59025C6.20313 5.73187 6.2625 5.83962 6.3625 5.91351C6.5 6.01202 6.64688 6.04281 6.80313 6.00587C6.95937 5.96892 7.0875 5.88272 7.1875 5.74726C7.35 5.5256 7.54688 5.35627 7.77813 5.23929C8.00938 5.1223 8.26875 5.0638 8.55625 5.0638ZM8.5 15.7775C7.45 15.7775 6.46875 15.5897 5.55625 15.2141C4.64375 14.8385 3.85 14.3182 3.175 13.6532C2.5 12.9882 1.96875 12.2062 1.58125 11.3073C1.19375 10.4083 1 9.43547 1 8.38873C1 7.35431 1.19375 6.38762 1.58125 5.48866C1.96875 4.58969 2.5 3.80772 3.175 3.14273C3.85 2.47775 4.64375 1.95438 5.55625 1.57263C6.46875 1.19088 7.45 1 8.5 1C9.5375 1 10.5125 1.19088 11.425 1.57263C12.3375 1.95438 13.1313 2.47775 13.8063 3.14273C14.4813 3.80772 15.0156 4.58969 15.4094 5.48866C15.8031 6.38762 16 7.35431 16 8.38873C16 9.43547 15.8031 10.4083 15.4094 11.3073C15.0156 12.2062 14.4813 12.9882 13.8063 13.6532C13.1313 14.3182 12.3375 14.8385 11.425 15.2141C10.5125 15.5897 9.5375 15.7775 8.5 15.7775ZM8.5 14.6692C10.2625 14.6692 11.7656 14.0534 13.0094 12.822C14.2531 11.5905 14.875 10.1128 14.875 8.38873C14.875 6.6647 14.2531 5.18695 13.0094 3.95549C11.7656 2.72404 10.2625 2.10831 8.5 2.10831C6.7125 2.10831 5.20312 2.72404 3.97188 3.95549C2.74063 5.18695 2.125 6.6647 2.125 8.38873C2.125 10.1128 2.74063 11.5905 3.97188 12.822C5.20312 14.0534 6.7125 14.6692 8.5 14.6692Z" fill="currentColor" stroke="currentColor" stroke-width="0.3"/>
                              </svg>
                              Ask a question
                           </button>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </main>
      

      <!-- footer area start -->
         <footer>
      <div class="tp-footer-area tp-footer-style-2 tp-footer-style-5" data-bg-color="#FFFFFF">
         <div class="tp-footer-top pt-95 pb-45">
            <div class="container">
               <div class="row">
                  <div class="col-xl-4 col-lg-3 col-md-4 col-sm-6">
                     <div class="tp-footer-widget footer-col-1 mb-50">
                        <div class="tp-footer-widget-content">
                           <div class="tp-footer-logo">
                              <a href="index.php">
                                 <img src="assets/img/logo/logo/logo-noground.png" alt="logo" style="height: 100px; width: 100px;">
                              </a>
                           </div>
                           <p class="tp-footer-desc">Nous sommes une équipe d'épicuriens qui créent des sensations de haute qualité.</p>
                           <div class="tp-footer-social">
                              <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                              <a href="#"><i class="fa-brands fa-twitter"></i></a>
                              <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                              <a href="#"><i class="fa-brands fa-vimeo-v"></i></a>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                     <div class="tp-footer-widget footer-col-2 mb-50">
                        <h4 class="tp-footer-widget-title">Mon compte</h4>
                        <div class="tp-footer-widget-content">
                           <ul>
                              <li><a href="orders.php">Suivre les commandes</a></li>
                              <li><a href="wishlist.php">Liste de souhaits</a></li>
                              <li><a href="profile.php">Mon compte</a></li>
                              <li><a href="#">Historique des commandes</a></li>
                              <li><a href="#">Retours</a></li>
                           </ul>
                        </div>
                     </div>
                  </div>
                  <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                     <div class="tp-footer-widget footer-col-3 mb-50">
                        <h4 class="tp-footer-widget-title">Infomation</h4>
                        <div class="tp-footer-widget-content">
                           <ul>
                              <li><a href="#">Notre histoire</a></li>
                              <li><a href="#">Carrières</a></li>
                              <li><a href="#">Politique de confidentialité</a></li>
                              <li><a href="#">Conditions générales</a></li>
                              <li><a href="#">Dernières nouvelles</a></li>
                              <li><a href="#">Contactez-nous</a></li>
                           </ul>
                        </div>
                     </div>
                  </div>
                  <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                     <div class="tp-footer-widget footer-col-4 mb-50">
                        <h4 class="tp-footer-widget-title">Contactez-Nous</h4>
                        <div class="tp-footer-widget-content">
                           <div class="tp-footer-talk mb-20">
                              <span>Des questions? Appelez-nous</span>
                              <h4><a href="tel:670-413-90-762">+225 413 90 762</a></h4>
                           </div>
                           <div class="tp-footer-contact">
                              <div class="tp-footer-contact-item d-flex align-items-start">
                                 <div class="tp-footer-contact-icon">
                                    <span>
                                       <svg width="18" height="16" viewBox="0 0 18 16" fill="none"
                                          xmlns="http://www.w3.org/2000/svg">
                                          <path
                                             d="M1 5C1 2.2 2.6 1 5 1H13C15.4 1 17 2.2 17 5V10.6C17 13.4 15.4 14.6 13 14.6H5"
                                             stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10"
                                             stroke-linecap="round" stroke-linejoin="round" />
                                          <path
                                             d="M13 5.40039L10.496 7.40039C9.672 8.05639 8.32 8.05639 7.496 7.40039L5 5.40039"
                                             stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10"
                                             stroke-linecap="round" stroke-linejoin="round" />
                                          <path d="M1 11.4004H5.8" stroke="currentColor" stroke-width="1.5"
                                             stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                          <path d="M1 8.19922H3.4" stroke="currentColor" stroke-width="1.5"
                                             stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                       </svg>
                                    </span>
                                 </div>
                                 <div class="tp-footer-contact-content">
                                    <p><a
                                          href="https://html.weblearnbd.net/cdn-cgi/l/email-protection#84f7ecebe2fdc4e3e9e5ede8aae7ebe9"><span
                                             class="__cf_email__"
                                             data-cfemail="493a21262f30092e24282025672a2624">[email&#160;protected]</span></a>
                                    </p>
                                 </div>
                              </div>
                              <div class="tp-footer-contact-item d-flex align-items-start">
                                 <div class="tp-footer-contact-icon">
                                    <span>
                                       <svg width="17" height="20" viewBox="0 0 17 20" fill="none"
                                          xmlns="http://www.w3.org/2000/svg">
                                          <path
                                             d="M8.50001 10.9417C9.99877 10.9417 11.2138 9.72668 11.2138 8.22791C11.2138 6.72915 9.99877 5.51416 8.50001 5.51416C7.00124 5.51416 5.78625 6.72915 5.78625 8.22791C5.78625 9.72668 7.00124 10.9417 8.50001 10.9417Z"
                                             stroke="currentColor" stroke-width="1.5" />
                                          <path
                                             d="M1.21115 6.64496C2.92464 -0.887449 14.0841 -0.878751 15.7889 6.65366C16.7891 11.0722 14.0406 14.8123 11.6313 17.126C9.88298 18.8134 7.11704 18.8134 5.36006 17.126C2.95943 14.8123 0.210885 11.0635 1.21115 6.64496Z"
                                             stroke="currentColor" stroke-width="1.5" />
                                       </svg>
                                    </span>
                                 </div>
                                 <div class="tp-footer-contact-content">
                                    <p><a href="https://www.google.com/maps/place/Sleepy+Hollow+Rd,+Gouverneur,+NY+13642,+USA/@44.3304966,-75.4552367,17z/data=!3m1!4b1!4m6!3m5!1s0x4cccddac8972c5eb:0x56286024afff537a!8m2!3d44.3304928!4d-75.453048!16s%2Fg%2F1tdsjdj4"
                                          target="_blank">Riviera Palmeraie <br> Abidjan, Côte d'Ivoire</a></p>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="tp-footer-bottom">
            <div class="container">
               <div class="tp-footer-bottom-wrapper">
                  <div class="row align-items-center">
                     <div class="col-md-6">
                        <div class="tp-footer-copyright">
                           <p>© 2025 Tous Droits Réservés.</p>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="tp-footer-payment text-md-end">
                           <p>
                              <img src="assets/img/footer/footer-pay-2.png" alt="">
                           </p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </footer>
      <!-- footer area end -->

      <script>
          $('#in_stock').on('change', function () {
               window.location.href = 'shop.php?stock=in';
               });
               $('#on_sale').on('change', function () {
               window.location.href = 'shop.php?stock=out';
               });

      </script>

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

<!-- Mirrored from html.weblearnbd.net/shofy-prv/shofy/shop.php by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 15 Oct 2023 08:15:21 GMT -->
</html>
