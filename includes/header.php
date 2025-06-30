<?php
session_start();
require_once 'config.php'; // connexion PDO

$user_id = $_SESSION['user_id'] ?? null;
$pseudo = $_SESSION['pseudo'] ?? 'Invité';

// Valeurs par défaut
$total_wishlist = 0;
$total_cart = 0;

// Si utilisateur connecté, on calcule les totaux
if ($user_id) {
    // Total wishlist
    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM wishlists WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $total_wishlist = (int)($stmt->fetchColumn() ?? 0);

    // Total panier
    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart_items WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $total_cart = (int)($stmt->fetchColumn() ?? 0);
}

// try {
//     $categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();
// } catch (PDOException $e) {
//     $categories = [];
// }
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
 <header>
      <!-- header top start  -->
            <div class="tp-header-top-2 p-relative z-index-11 tp-header-top-border d-none d-md-block">
               <div class="container">
                  <div class="row align-items-center">
                     <div class="col-md-6">
                        <div class="tp-header-info d-flex align-items-center">
                           <div class="tp-header-info-item">
                              <a href="#">
                                 <span>
                                    <svg width="8" height="15" viewBox="0 0 8 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                       <path d="M8 0H5.81818C4.85376 0 3.92883 0.383116 3.24688 1.06507C2.56493 1.74702 2.18182 2.67194 2.18182 3.63636V5.81818H0V8.72727H2.18182V14.5455H5.09091V8.72727H7.27273L8 5.81818H5.09091V3.63636C5.09091 3.44348 5.16753 3.25849 5.30392 3.1221C5.44031 2.98571 5.6253 2.90909 5.81818 2.90909H8V0Z" fill="currentColor"/>
                                    </svg>                                    
                                 </span> 7500k Followers
                              </a>
                           </div>
                           <div class="tp-header-info-item">
                              <a href="tel:402-763-282-46">
                                 <span>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                       <path fill-rule="evenodd" clip-rule="evenodd" d="M1.359 2.73916C1.59079 2.35465 2.86862 0.958795 3.7792 1.00093C4.05162 1.02426 4.29244 1.1883 4.4881 1.37943H4.48885C4.93737 1.81888 6.22423 3.47735 6.29648 3.8265C6.47483 4.68282 5.45362 5.17645 5.76593 6.03954C6.56213 7.98771 7.93402 9.35948 9.88313 10.1549C10.7455 10.4679 11.2392 9.44752 12.0956 9.62511C12.4448 9.6981 14.1042 10.9841 14.5429 11.4333V11.4333C14.7333 11.6282 14.8989 11.8698 14.9214 12.1422C14.9553 13.1016 13.4728 14.3966 13.1838 14.5621C12.502 15.0505 11.6125 15.0415 10.5281 14.5373C7.50206 13.2784 2.66618 8.53401 1.38384 5.39391C0.893174 4.31561 0.860062 3.42016 1.359 2.73916Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                       <path d="M9.84082 1.18318C12.5534 1.48434 14.6952 3.62393 15 6.3358" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                       <path d="M9.84082 3.77927C11.1378 4.03207 12.1511 5.04544 12.4039 6.34239" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>                                   
                                 </span> +(225) 763 282 46
                              </a>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="tp-header-top-right tp-header-top-black d-flex align-items-center justify-content-end">
                           <div class="tp-header-top-menu d-flex align-items-center justify-content-end">
                              <!-- <div class="tp-header-top-menu-item tp-header-lang">
                                 <span class="tp-header-lang-toggle" id="tp-header-lang-toggle">English</span>
                                 <ul>
                                    <li>
                                       <a href="#">Spanish</a>
                                    </li>
                                    <li>
                                       <a href="#">Russian</a>
                                    </li>
                                    <li>
                                       <a href="#">Portuguese</a>
                                    </li>
                                 </ul>
                              </div>
                              <div class="tp-header-top-menu-item tp-header-currency">
                                 <span class="tp-header-currency-toggle" id="tp-header-currency-toggle">USD</span>
                                 <ul>
                                    <li>
                                       <a href="#">EUR</a>
                                    </li>
                                    <li>
                                       <a href="#">CHF</a>
                                    </li>
                                    <li>
                                       <a href="#">GBP</a>
                                    </li>
                                    <li>
                                       <a href="#">KWD</a>
                                    </li>
                                 </ul>
                              </div> -->
                              <div class="tp-header-top-menu-item tp-header-setting">
                                 <span class="tp-header-setting-toggle" id="tp-header-setting-toggle">Paramètres</span>
                                 <ul>
                                    <li>
                                       <a href="profile.php">Profil</a>
                                    </li>
                                    <li>
                                       <a href="wishlist.php">Wishlist</a>
                                    </li>
                                    <li>
                                       <a href="cart.php">Panier</a>
                                    </li>
                                    <li>
                                       <a href="login.html">Déconnexion</a>
                                    </li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <!-- header bottom start -->

      <div id="header-sticky" class="tp-header-area p-relative tp-header-sticky tp-header-height" style="background-color: #E44C76;">
         <div class="tp-header-5 pl-25 pr-25" data-bg-color="#E44C76">
            <div class="container-fluid">
               <div class="row align-items-center">
                  <div class="col-xl-2 col-lg-6 col-md-6 col-sm-5 col-8">
                     <div class="tp-header-left-5 d-flex align-items-center">
                        <div class="tp-header-hamburger-5 mr-15 d-none d-lg-block">
                           <button class="tp-hamburger-btn-2 tp-hamburger-toggle">
                              <span></span>
                              <span></span>
                              <span></span>
                           </button>
                        </div>
                        <div class="tp-header-hamburger-5 mr-15 d-lg-none">
                           <button class="tp-hamburger-btn-2 tp-offcanvas-open-btn">
                              <span></span>
                              <span></span>
                              <span></span>
                           </button>
                        </div>
                        <div class="logo">
                           <a href="index.php">
                              <img src="assets/img/logo/logo-centered.png" alt="logo" style="height: 20%; width: 20%;">
                           </a>
                        </div>
                     </div>
                  </div>
                  <div class="col-xl-8 col-lg-6 d-none d-xl-block">
                     <div class="main-menu d-none">
                        <nav id="mobile-menu">
                           <ul>
                              <li class="has-dropdown">
                                 <a href="#">Home</a>
                                 <ul class="tp-submenu">
                                    <li><a href="#">Home Style 1</a></li>
                                    <li><a href="#">Home Style 1</a></li>
                                    <li><a href="#">Home Style 1</a></li>
                                    <li><a href="#">Home Style 1</a></li>
                                 </ul>
                              </li>
                              <li><a href="#">Shop</a></li>
                              <li><a href="#">Products</a></li>
                              <li><a href="#">Categories</a></li>
                              <li><a href="#">Pages</a></li>
                              <li><a href="#">Contact</a></li>
                           </ul>
                        </nav>
                     </div>
                     <div class="tp-header-search-5">
                        <form action="#">
                           <div class="tp-header-search-input-box-5">
                              <div class="tp-header-search-input-5">
                                 <input type="text" placeholder="Recherche un produit...">
                                 <span class="tp-header-search-icon-5">
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                       xmlns="http://www.w3.org/2000/svg">
                                       <path
                                          d="M8.11111 15.2222C12.0385 15.2222 15.2222 12.0385 15.2222 8.11111C15.2222 4.18375 12.0385 1 8.11111 1C4.18375 1 1 4.18375 1 8.11111C1 12.0385 4.18375 15.2222 8.11111 15.2222Z"
                                          stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                          stroke-linejoin="round" />
                                       <path d="M16.9995 17L13.1328 13.1333" stroke="currentColor" stroke-width="2"
                                          stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                 </span>
                              </div>
                              <button type="submit">Recherche</button>
                           </div>
                        </form>
                     </div>
                  </div>
                  <div class="col-xl-2 col-lg-6 col-md-6 col-sm-7 col-4">
                     <div class="tp-header-right-5 d-flex align-items-center justify-content-end">
                        <div class="tp-header-login-5 d-none d-md-block">
                           <a href="profile.php" class="d-flex align-items-center">
                              <div class="tp-header-login-icon-5">
                                 <span>
                                    <svg width="16" height="18" viewBox="0 0 16 18" fill="none"
                                       xmlns="http://www.w3.org/2000/svg">
                                       <path
                                          d="M8.00029 9C10.2506 9 12.0748 7.20914 12.0748 5C12.0748 2.79086 10.2506 1 8.00029 1C5.75 1 3.92578 2.79086 3.92578 5C3.92578 7.20914 5.75 9 8.00029 9Z"
                                          stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                          stroke-linejoin="round" />
                                       <path d="M15 17C15 13.904 11.8626 11.4 8 11.4C4.13737 11.4 1 13.904 1 17"
                                          stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                          stroke-linejoin="round" />
                                    </svg>
                                 </span>
                              </div>
                              <div class="tp-header-login-content-5">
                                 <p><span>Bienvenue</span> <br> <?php echo htmlspecialchars($pseudo); ?></p>
                              </div>
                           </a>
                        </div>
                        <div class="tp-header-action-5 d-flex align-items-center ml-20">
                           <div class="tp-header-action-item-5 d-none d-sm-block">
                              <a href="wishlist.php">
                                 <svg width="18" height="17" viewBox="0 0 18 17" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M9.20125 16.0348C11.0291 14.9098 12.7296 13.5858 14.2722 12.0865C15.3567 11.0067 16.1823 9.69033 16.6858 8.23822C17.5919 5.42131 16.5335 2.19649 13.5717 1.24212C12.0151 0.740998 10.315 1.02741 9.00329 2.01177C7.69109 1.02861 5.99161 0.742297 4.43489 1.24212C1.47305 2.19649 0.40709 5.42131 1.31316 8.23822C1.81666 9.69033 2.64228 11.0067 3.72679 12.0865C5.26938 13.5858 6.96983 14.9098 8.79771 16.0348L8.99568 16.1579L9.20125 16.0348Z"
                                       stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                       stroke-linejoin="round" />
                                    <path d="M5.85156 4.41306C4.95446 4.69963 4.31705 5.50502 4.2374 6.45262"
                                       stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                       stroke-linejoin="round" />
                                 </svg>
                                 <span class="tp-header-action-badge-5"><?php echo $total_wishlist; ?></span>
                              </a>
                           </div>
                           <div class="tp-header-action-item-5">
                              <button type="button" class="cartmini-open-btn">
                                 <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M5.31165 17H12.6964C15.4091 17 17.4901 16.0781 16.899 12.3676L16.2107 7.33907C15.8463 5.48764 14.5912 4.77907 13.49 4.77907H4.48572C3.36828 4.77907 2.18607 5.54097 1.76501 7.33907L1.07673 12.3676C0.574694 15.659 2.59903 17 5.31165 17Z"
                                       stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                       stroke-linejoin="round" />
                                    <path
                                       d="M5.19048 4.59622C5.19048 2.6101 6.90163 1.00003 9.01244 1.00003V1.00003C10.0289 0.99598 11.0052 1.37307 11.7254 2.04793C12.4457 2.72278 12.8506 3.6398 12.8506 4.59622V4.59622"
                                       stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                       stroke-linejoin="round" />
                                    <path d="M6.38837 8.34478H6.42885" stroke="currentColor" stroke-width="1.5"
                                       stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M11.5466 8.34478H11.5871" stroke="currentColor" stroke-width="1.5"
                                       stroke-linecap="round" stroke-linejoin="round" />
                                 </svg>
                                 <span class="tp-header-action-badge-5"><?php echo $total_cart; ?></span>
                              </button>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="tp-header-side-menu tp-side-menu-5">
            <nav class="tp-category-menu-content">
               <ul>
                  <li>
                     <a href="shop.php">
                        <i class="fa-light fa-gear"></i>
                        Accessoires
                     </a>
                  </li>
                  <li>
                     <a href="shop.php">
                        <i class="fas fa-spa"></i>
                        Bien-Être
                     </a>
                  </li>
                  <li class="has-dropdown">
                     <a href="shop.php">
                        <i class="flaticon-apple"></i>
                        Comestibles
                     </a>
                  </li>
                  <li>
                     <a href="shop.php">
                        <i class="fas fa-microchip"></i>
                        Électroniques

                     </a>
                  </li>
                  <li>
                     <a href="shop.php">
                        <i class="fas fa-heart"></i>
                        Lingerie
                     </a>
                  </li>
               </ul>
            </nav>
         </div>
         
      </div>
   </header>