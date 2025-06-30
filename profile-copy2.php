<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$orders = [];
// Récupération des informations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if ($_POST['action'] === 'update_profile') {
         $pseudo = $_POST['pseudo'];
         $telephone = $_POST['telephone'] ?? null;
         $stmt = $pdo->prepare("UPDATE users SET pseudo = ?, telephone = ? WHERE id = ?");
         $stmt->execute([$pseudo, $telephone, $user_id]);
         header("Location: profile.php?success=1");
         exit;
      }

    if ($_POST['action'] === 'update_password') {
        $ancien = $_POST['ancien_motdepasse'];
        $nouveau = $_POST['nouveau_motdepasse'];
        if (!password_verify($ancien, $user['password'])) {
            $erreur = "Mot de passe actuel incorrect.";
        } else {
            $new_hash = password_hash($nouveau, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([$new_hash, $user_id]);
            $success = "Mot de passe mis à jour.";
        }
    }

    if ($_POST['action'] === 'update_avatar' && isset($_FILES['avatar'])) {
        $file = $_FILES['avatar'];
        if ($file['error'] === 0) {
            $path = 'assets/img/users/' . time() . "_" . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $path)) {
                $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                $stmt->execute([$path, $user_id]);
                header("Location: profile.php?avatar=ok");
                exit;
            }
        }
    }
}
// Récupération des commandes de l'utilisateur
   if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Nombre de commandes - on garde la logique précédente si tu as une table orders
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_orders = $stmt->fetchColumn();

// Nombre d'articles dans la wishlist (wishlists)
$stmt = $pdo->prepare("SELECT SUM(quantity) FROM wishlists WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_wishlist = $stmt->fetchColumn() ?: 0;

// Nombre d'articles dans la boîte à cadeaux (cart_items)
$stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart_items WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_cart = $stmt->fetchColumn() ?: 0;

// Pseudo de l'utilisateur
$pseudo = $_SESSION['pseudo'] ?? 'Invité';
$phone = $address = $bio = '';

if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($profile) {
        $phone = htmlspecialchars($profile['phone_number']);
        $address = htmlspecialchars($profile['address']);
        $bio = htmlspecialchars($profile['bio']);
    }
}
// Compter les notifications non lues
$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->execute([$user_id]);
$unread_count = $stmt->fetchColumn();

// Récupération des adresses de facturation et de livraison
$billing_address = $shipping_address = [];

if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE user_id = ? AND type = 'billing'");
    $stmt->execute([$user_id]);
    $billing_address = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE user_id = ? AND type = 'shipping'");
    $stmt->execute([$user_id]);
    $shipping_address = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!doctype html>
<html class="no-js" lang="zxx">
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
   <div class="cartmini__area">
      <div class="cartmini__wrapper d-flex justify-content-between flex-column">
         <div class="cartmini__top-wrapper">
            <div class="cartmini__top p-relative">
               <div class="cartmini__top-title">
                  <h4>Shopping cart</h4>
               </div>
               <div class="cartmini__close">
                  <button type="button" class="cartmini__close-btn cartmini-close-btn"><i
                        class="fal fa-times"></i></button>
               </div>
            </div>
            <div class="cartmini__shipping">
               <p> Free Shipping for all orders over <span>$50</span></p>
               <div class="progress">
                  <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                     data-width="70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
               </div>
            </div>
            <div class="cartmini__widget">
               <div class="cartmini__widget-item">
                  <div class="cartmini__thumb">
                     <a href="product-details.html">
                        <img src="assets/img/product/product-1.jpg" alt="">
                     </a>
                  </div>
                  <div class="cartmini__content">
                     <h5 class="cartmini__title"><a href="product-details.html">Level Bolt Smart Lock</a></h5>
                     <div class="cartmini__price-wrapper">
                        <span class="cartmini__price">$46.00</span>
                        <span class="cartmini__quantity">x2</span>
                     </div>
                  </div>
                  <a href="#" class="cartmini__del"><i class="fa-regular fa-xmark"></i></a>
               </div>
            </div>
            <!-- for wp -->
            <!-- if no item in cart -->
            <div class="cartmini__empty text-center d-none">
               <img src="assets/img/product/cartmini/empty-cart.png" alt="">
               <p>Your Cart is empty</p>
               <a href="shop.php" class="tp-btn" style="background-color: #E44C76;" onmouseover="this.style.backgroundColor='#C50E40'" onmouseout="this.style.backgroundColor='#E44C76'"> Go to Shop</a>
            </div>
         </div>
         <div class="cartmini__checkout">
            <div class="cartmini__checkout-title mb-30">
               <h4>Subtotal:</h4>
               <span>$113.00</span>
            </div>
            <div class="cartmini__checkout-btn">
               <a href="cart.html" class="tp-btn mb-10 w-100" style="background-color: #E44C76;" onmouseover="this.style.backgroundColor='#C50E40'" onmouseout="this.style.backgroundColor='#E44C76'"> view cart</a>
               <a href="checkout.html" class="tp-btn tp-btn-border w-100" style="background-color: #E44C76;" onmouseover="this.style.backgroundColor='#C50E40'" onmouseout="this.style.backgroundColor='#E44C76'"> checkout</a>
            </div>
         </div>
      </div>
   </div>
   <!-- cart mini area end -->
<?php include 'includes/header.php'; ?>

      <main>

        <!-- profile area start -->
        <section class="profile__area pt-120 pb-120">
         <div class="container">
            <div class="profile__inner p-relative">
               <div class="profile__shape">
                  <img class="profile__shape-1" src="assets/img/login/laptop.png" alt="" >
                  <img class="profile__shape-2" src="assets/img/login/man.png" alt="" >
                  <img class="profile__shape-3" src="assets/img/login/shape-1.png" alt="" >
                  <img class="profile__shape-4" src="assets/img/login/shape-2.png" alt="" >
                  <img class="profile__shape-5" src="assets/img/login/shape-3.png" alt="" >
                  <img class="profile__shape-6" src="assets/img/login/shape-4.png" alt="" >
               </div>
               <div class="row">
                  <div class="col-xxl-4 col-lg-4">
                     <div class="profile__tab mr-40">
                        <nav>
                           <div class="nav nav-tabs tp-tab-menu flex-column" id="profile-tab" role="tablist">
                              <button class="nav-link active" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false"><span><i class="fa-regular fa-user-pen"></i></span>Profil</button>
                              <button class="nav-link" id="nav-information-tab" data-bs-toggle="tab" data-bs-target="#nav-information" type="button" role="tab" aria-controls="nav-information" aria-selected="false"><span><i class="fa-regular fa-circle-info"></i></span> Information</button>
                              <button class="nav-link" id="nav-address-tab" data-bs-toggle="tab" data-bs-target="#nav-address" type="button" role="tab" aria-controls="nav-address" aria-selected="false"><span><i class="fa-light fa-location-dot"></i></span> Adresse </button>
                              <button class="nav-link" id="nav-order-tab" data-bs-toggle="tab" data-bs-target="#nav-order" type="button" role="tab" aria-controls="nav-order" aria-selected="false"><span><i class="fa-light fa-clipboard-list-check"></i></span> Mes Commandes </button>
                              <button class="nav-link" id="nav-notification-tab" data-bs-toggle="tab" data-bs-target="#nav-notification" type="button" role="tab" aria-controls="nav-notification" aria-selected="false">
                                 <span><i class="fa-regular fa-bell"></i></span>
                                 Notifications
                                 <?php if (!empty($unread_count)): ?>
                                    <span class="badge bg-danger ms-2"><?= $unread_count ?></span>
                                 <?php endif; ?>
                              </button>                              
                              <button class="nav-link" id="nav-password-tab" data-bs-toggle="tab" data-bs-target="#nav-password" type="button" role="tab" aria-controls="nav-password" aria-selected="false"><span><i class="fa-regular fa-lock"></i></span> Changer le Mot de Passe</button>
                              <span id="marker-vertical" class="tp-tab-line d-none d-sm-inline-block"></span>
                           </div>
                        </nav>
                     </div>
                  </div>
                  <div class="col-xxl-8 col-lg-8">
                     <div class="profile__tab-content">
                        <div class="tab-content" id="profile-tabContent">
                           <div class="tab-pane fade show active" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                              <div class="profile__main">
                                 <div class="profile__main-top pb-80">
                                    <div class="row align-items-center">
                                    <div class="col-md-6">
                                       <div class="profile__main-inner d-flex flex-wrap align-items-center">
                                          <div class="profile__main-thumb">
                                          <img src="<?php echo htmlspecialchars($user['avatar'] ?? 'assets/img/users/default.jpg'); ?>" alt="Avatar">
                                          <div class="profile__main-thumb-edit">
                                             <input id="profile-thumb-input" class="profile-img-popup" type="file" disabled>
                                             <label for="profile-thumb-input"><i class="fa-light fa-camera"></i></label>
                                          </div>
                                          </div>
                                          <div class="profile__main-content">
                                          <h4 class="profile__main-title">Bienvenue <?php echo htmlspecialchars($user['pseudo']); ?> !</h4>
                                          <p>Vous avez <span><?php echo (int)($user['notifications'] ?? 0); ?></span> notifications</p>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-md-6">
                                       <div class="profile__main-logout text-sm-end">
                                          <a href="logout.php" class="tp-logout-btn">Déconnexion</a>
                                       </div>
                                    </div>
                                    </div>
                                 </div>

                                 <div class="profile__main-info">
                                    <div class="row gx-3">
                                    <!-- Commandes -->
                                    <div class="col-md-3 col-sm-6">
                                       <div class="profile__main-info-item">
                                          <div class="profile__main-info-icon">
                                             <span>
                                                <span class="profile-icon-count profile-order"><?php echo $total_orders; ?></span>
                                                <svg viewBox="0 0 512 512"><path d="M472.916,224H448.007a24.534,24.534,0,0,0-23.417-18H398V140.976a6.86,6.86,0,0,0-3.346-6.062L207.077,26.572a6.927,6.927,0,0,0-6.962,0L12.48,134.914A6.981,6.981,0,0,0,9,140.976V357.661a7,7,0,0,0,3.5,6.062L200.154,472.065a7,7,0,0,0,3.5.938,7.361,7.361,0,0,0,3.6-.938L306,415.108v41.174A29.642,29.642,0,0,0,335.891,486H472.916A29.807,29.807,0,0,0,503,456.282v-202.1A30.2,30.2,0,0,0,472.916,224Zm-48.077-4A10.161,10.161,0,0,1,435,230.161v.678A10.161,10.161,0,0,1,424.839,241H384.161A10.161,10.161,0,0,1,374,230.839v-.678A10.161,10.161,0,0,1,384.161,220ZM203.654,40.717l77.974,45.018L107.986,185.987,30.013,140.969ZM197,453.878,23,353.619V153.085L197,253.344Zm6.654-212.658-81.668-47.151L295.628,93.818,377.3,140.969ZM306,254.182V398.943l-95,54.935V253.344L384,153.085V206h.217A24.533,24.533,0,0,0,360.8,224H335.891A30.037,30.037,0,0,0,306,254.182Zm183,202.1A15.793,15.793,0,0,1,472.916,472H335.891A15.628,15.628,0,0,1,320,456.282v-202.1A16.022,16.022,0,0,1,335.891,238h25.182a23.944,23.944,0,0,0,23.144,17H424.59a23.942,23.942,0,0,0,23.143-17h25.183A16.186,16.186,0,0,1,489,254.182Z"/><path d="M343.949,325h7.327a7,7,0,1,0,0-14H351V292h19.307a6.739,6.739,0,0,0,6.655,4.727A7.019,7.019,0,0,0,384,289.743v-4.71A7.093,7.093,0,0,0,376.924,278H343.949A6.985,6.985,0,0,0,337,285.033v32.975A6.95,6.95,0,0,0,343.949,325Z"/><path d="M344,389h33a7,7,0,0,0,7-7V349a7,7,0,0,0-7-7H344a7,7,0,0,0-7,7v33A7,7,0,0,0,344,389Zm7-33h19v19H351Z"/><path d="M351.277,439H351V420h18.929a7.037,7.037,0,0,0,14.071.014v-6.745A7.3,7.3,0,0,0,376.924,406H343.949A7.191,7.191,0,0,0,337,413.269v32.975A6.752,6.752,0,0,0,343.949,453h7.328a7,7,0,1,0,0-14Z"/><path d="M393.041,286.592l-20.5,20.5-6.236-6.237a7,7,0,1,0-9.9,9.9l11.187,11.186a7,7,0,0,0,9.9,0l25.452-25.452a7,7,0,0,0-9.9-9.9Z"/><path d="M393.041,415.841l-20.5,20.5-6.236-6.237a7,7,0,1,0-9.9,9.9l11.187,11.186a7,7,0,0,0,9.9,0l25.452-25.452a7,7,0,0,0-9.9-9.9Z"/><path d="M464.857,295H420.891a7,7,0,0,0,0,14h43.966a7,7,0,0,0,0-14Z"/><path d="M464.857,359H420.891a7,7,0,0,0,0,14h43.966a7,7,0,0,0,0-14Z"/><path d="M464.857,423H420.891a7,7,0,0,0,0,14h43.966a7,7,0,0,0,0-14Z"/></svg>
                                             </span>
                                          </div>
                                          <h4 class="profile__main-info-title">Commandes</h4>
                                       </div>
                                    </div>

                                    <div class="col-md-3 col-sm-6">
                                       <div class="profile__main-info-item">
                                          <div class="profile__main-info-icon">
                                          <span>
                                             <span class="profile-icon-count profile-wishlist"><?php echo $total_wishlist; ?></span>
                                             <svg viewBox="0 -20 480 480" xmlns="http://www.w3.org/2000/svg"><path d="m348 0c-43 .0664062-83.28125 21.039062-108 56.222656-24.71875-35.183594-65-56.1562498-108-56.222656-70.320312 0-132 65.425781-132 140 0 72.679688 41.039062 147.535156 118.6875 216.480469 35.976562 31.882812 75.441406 59.597656 117.640625 82.625 2.304687 1.1875 5.039063 1.1875 7.34375 0 42.183594-23.027344 81.636719-50.746094 117.601563-82.625 77.6875-68.945313 118.726562-143.800781 118.726562-216.480469 0-74.574219-61.679688-140-132-140zm-108 422.902344c-29.382812-16.214844-224-129.496094-224-282.902344 0-66.054688 54.199219-124 116-124 41.867188.074219 80.460938 22.660156 101.03125 59.128906 1.539062 2.351563 4.160156 3.765625 6.96875 3.765625s5.429688-1.414062 6.96875-3.765625c20.570312-36.46875 59.164062-59.054687 101.03125-59.128906 61.800781 0 116 57.945312 116 124 0 153.40625-194.617188 266.6875-224 282.902344zm0 0"/></svg>
                                          </span>
                                          </div>
                                          <h4 class="profile__main-info-title">Souhaits</h4>
                                       </div>
                                    </div>

                                    <div class="col-md-3 col-sm-6">
                                       <div class="profile__main-info-item">
                                          <div class="profile__main-info-icon">
                                          <span>
                                             <span class="profile-icon-count profile-wishlist"><?php echo $total_cart; ?></span>
                                             <svg viewBox="0 0 512 512"><path d="m352.742 291.476h-263.963l228.58-145.334a6 6 0 0 0 1.844-8.284l-22.647-35.618a36.285 36.285 0 0 0 -50.033-11.14l-32.165 20.451 2.548-12.191a34.314 34.314 0 1 0 -66.987-14.914l-16.71 75.054-55.951-12.454a34.315 34.315 0 0 0 -21 65.026l-34.458 21.91a36.285 36.285 0 0 0 -11.14 50.032l22.647 35.619a6 6 0 0 0 8.283 1.845l21.08-13.4v151.888a36.285 36.285 0 0 0 36.246 36.244h223.584a36.285 36.285 0 0 0 36.244-36.244v-162.49a6 6 0 0 0 -6.002-6zm-99.78-190.248a24.084 24.084 0 0 1 12.961-3.794 24.481 24.481 0 0 1 5.316.587 24.09 24.09 0 0 1 15.19 10.658l19.428 30.555-94.5 60.086-32.436-51.014zm-91.33-14.173a22.314 22.314 0 1 1 43.545 9.775l-4.926 23.564-53.667 34.249zm7.16 67.69 32.436 51.014-54.76 34.816-32.435-51.014zm-117.821 37.768a22.314 22.314 0 0 1 23.679-33.754l48.485 10.794-53.822 33.739-4.362-.972a22.168 22.168 0 0 1 -13.98-9.807zm-10.755 115.619-19.427-30.556a24.272 24.272 0 0 1 7.45-33.467l75.667-48.109 32.435 51.014zm116.353 176.078h-57.653a24.272 24.272 0 0 1 -24.244-24.244v-156.49h81.9zm76.264 0h-64.264v-180.734h64.264zm113.909-24.244a24.272 24.272 0 0 1 -24.242 24.244h-77.667v-180.734h101.909z"/><path d="m419.833 236.971 2.87-16.735a6 6 0 0 0 -8.703-6.325l-15.028 7.9-15.029-7.9a6 6 0 0 0 -8.706 6.325l2.87 16.735-12.158 11.85a6 6 0 0 0 3.325 10.235l16.8 2.442 7.514 15.225a6 6 0 0 0 10.762 0l7.513-15.225 16.8-2.442a6 6 0 0 0 3.325-10.235zm-12.817 13.1a6 6 0 0 0 -4.518 3.282l-3.529 7.152-3.53-7.152a6 6 0 0 0 -4.517-3.282l-7.894-1.147 5.712-5.567a6 6 0 0 0 1.726-5.311l-1.349-7.862 7.06 3.711a5.994 5.994 0 0 0 5.584 0l7.059-3.711-1.348 7.862a6 6 0 0 0 1.725 5.311l5.712 5.567z"/><path d="m488.841 154.3-16.5-4.01-6.051-15.863a6 6 0 0 0 -10.714-1.012l-8.911 14.453-16.957.853a6 6 0 0 0 -4.272 9.876l10.991 12.941-4.427 16.39a6 6 0 0 0 8.073 7.115l15.7-6.454 14.227 9.277a6 6 0 0 0 9.261-5.479l-1.285-16.93 13.213-10.657a6 6 0 0 0 -2.348-10.5zm-20.856 13.8a6 6 0 0 0 -2.216 5.125l.6 7.953-6.681-4.359a6 6 0 0 0 -5.559-.524l-7.376 3.032 2.08-7.7a6 6 0 0 0 -1.219-5.449l-5.163-6.079 7.966-.4a6 6 0 0 0 4.807-2.842l4.185-6.789 2.842 7.452a6 6 0 0 0 4.189 3.691l7.751 1.884z"/><path d="m400.6 56.763-4.429 16.39a6 6 0 0 0 8.073 7.116l15.7-6.455 14.221 9.279a6 6 0 0 0 9.261-5.48l-1.285-16.93 13.216-10.658a6 6 0 0 0 -2.348-10.5l-16.5-4.009-6.05-15.864a6 6 0 0 0 -10.714-1.01l-8.91 14.452-16.958.852a6 6 0 0 0 -4.273 9.876zm13.991-11.844a6 6 0 0 0 4.806-2.843l4.186-6.789 2.842 7.452a6 6 0 0 0 4.189 3.692l7.75 1.883-6.208 5.006a6 6 0 0 0 -2.217 5.125l.6 7.954-6.681-4.359a6 6 0 0 0 -5.559-.524l-7.376 3.032 2.08-7.7a6 6 0 0 0 -1.219-5.45l-5.163-6.08z"/><path d="m291.746 237.835c-13.546 8.525-20.164 18.855-20.439 19.291a6 6 0 0 0 10.134 6.427c.9-1.4 22.609-34.215 69.86-22.527a6 6 0 0 0 2.883-11.648c-29.072-7.193-50.001.628-62.438 8.457z"/><path d="m405.6 174.293a6 6 0 0 0 4.6-11.084c-42.714-17.727-73.759-4.452-92.28 9.808a112.488 112.488 0 0 0 -29.868 35.246 6 6 0 1 0 10.748 5.337 101.191 101.191 0 0 1 26.44-31.075c23.288-17.925 50.325-20.697 80.36-8.232z"/><path d="m338.178 129.844a6 6 0 0 0 3.862 7.555 90.163 90.163 0 0 0 25.438 3.676c10.034 0 21.623-1.811 32.015-7.971 13.6-8.058 22.32-21.787 25.934-40.8a6 6 0 1 0 -11.789-2.24c-2.938 15.461-9.738 26.46-20.211 32.69-19.921 11.853-47.267 3.367-47.7 3.229a6 6 0 0 0 -7.549 3.861z"/><path d="m258.867 284.873a10.806 10.806 0 1 0 -10.805-10.806 10.819 10.819 0 0 0 10.805 10.806zm0-12a1.195 1.195 0 1 1 -1.194 1.194 1.2 1.2 0 0 1 1.194-1.194z"/><path d="m343.887 88.873a10.806 10.806 0 1 0 -10.806-10.806 10.818 10.818 0 0 0 10.806 10.806zm0-12a1.195 1.195 0 1 1 -1.195 1.194 1.2 1.2 0 0 1 1.195-1.194z"/><path d="m496.194 80.633a10.806 10.806 0 1 0 10.806 10.805 10.817 10.817 0 0 0 -10.806-10.805zm0 12a1.195 1.195 0 1 1 1.195-1.195 1.2 1.2 0 0 1 -1.195 1.195z"/><path d="m254.444 224.027a10.806 10.806 0 1 0 -10.8 10.806 10.817 10.817 0 0 0 10.8-10.806zm-10.8 1.2a1.195 1.195 0 1 1 1.194-1.2 1.2 1.2 0 0 1 -1.199 1.195z"/><path d="m478.4 230.812a10.806 10.806 0 1 0 10.806 10.806 10.818 10.818 0 0 0 -10.806-10.806zm0 12a1.194 1.194 0 1 1 1.195-1.194 1.2 1.2 0 0 1 -1.195 1.194z"/></svg>
                                          </span>
                                          </div>
                                          <h4 class="profile__main-info-title">Panier</h4>
                                       </div>
                                    </div>
                                    </div>
                                 </div>
                              </div>
                           </div>

                           <div class="tab-pane fade" id="nav-information" role="tabpanel" aria-labelledby="nav-information-tab">
                              <div class="profile__info">
                                 <h3 class="profile__info-title">Détails Personnels</h3>
                                 <div class="profile__info-content">
                                    <form action="includes/update_profile.php" method="POST">
                                       <div class="row">
                                          <div class="col-xxl-6 col-md-6">
                                             <div class="profile__input-box">
                                                <div class="profile__input">
                                                   <input type="text" name="pseudo" placeholder="Entrez votre nom d'utilisateur" value="<?php echo htmlspecialchars($pseudo); ?>">
                                                   <span>
                                                      <svg width="17" height="19" viewBox="0 0 17 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                         <path d="M9 9C11.2091 9 13 7.20914 13 5C13 2.79086 11.2091 1 9 1C6.79086 1 5 2.79086 5 5C5 7.20914 6.79086 9 9 9Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                         <path d="M15.5 17.6C15.5 14.504 12.3626 12 8.5 12C4.63737 12 1.5 14.504 1.5 17.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                      </svg>
                                                   </span>
                                                </div>
                                             </div>
                                          </div>
                                         
                                          <div class="col-xxl-6 col-md-6">
                                             <div class="profile__input-box">
                                                <div class="profile__input">
                                                   <input type="text" name="phone" placeholder="Entrez votre numéro" value="<?php echo htmlspecialchars($phone); ?>">
                                                   <span>
                                                      <svg width="15" height="18" viewBox="0 0 15 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                         <path d="M13.9148 5V13C13.9148 16.2 13.1076 17 9.87892 17H5.03587C1.80717 17 1 16.2 1 13V5C1 1.8 1.80717 1 5.03587 1H9.87892C13.1076 1 13.9148 1.8 13.9148 5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                         <path opacity="0.4" d="M9.08026 3.80054H5.85156" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                         <path opacity="0.4" d="M7.45425 14.6795C8.14522 14.6795 8.70537 14.1243 8.70537 13.4395C8.70537 12.7546 8.14522 12.1995 7.45425 12.1995C6.76327 12.1995 6.20312 12.7546 6.20312 13.4395C6.20312 14.1243 6.76327 14.6795 7.45425 14.6795Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                      </svg>                                                                                        
                                                   </span>
                                                </div>
                                             </div>
                                          </div>
                                          
                                          <div class="col-xxl-12">
                                             <div class="profile__input-box">
                                                <div class="profile__input">
                                                   <input type="text" name="address" placeholder="Entrez votre adresse" value="<?php echo htmlspecialchars($address); ?>">
                                                   <span>
                                                      <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                         <path d="M7.99377 10.1461C9.39262 10.1461 10.5266 9.0283 10.5266 7.64946C10.5266 6.27061 9.39262 5.15283 7.99377 5.15283C6.59493 5.15283 5.46094 6.27061 5.46094 7.64946C5.46094 9.0283 6.59493 10.1461 7.99377 10.1461Z" stroke="currentColor" stroke-width="1.5"/>
                                                         <path d="M1.19707 6.1933C2.79633 -0.736432 13.2118 -0.72843 14.803 6.2013C15.7365 10.2663 13.1712 13.7072 10.9225 15.8357C9.29079 17.3881 6.70924 17.3881 5.06939 15.8357C2.8288 13.7072 0.263493 10.2583 1.19707 6.1933Z" stroke="currentColor" stroke-width="1.5"/>
                                                      </svg>
                                                   </span>
                                                </div>
                                             </div>
                                          </div>

                                          <div class="col-xxl-12">
                                             <div class="profile__input-box">
                                                <div class="profile__input">
                                                   <textarea name="bio" placeholder="Entrez votre bio"><?php echo $bio; ?></textarea>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="col-xxl-12">
                                             <div class="profile__btn">
                                                <button type="submit" class="tp-btn" style="background-color: #E44C76;" onmouseover="this.style.backgroundColor='#C50E40'" onmouseout="this.style.backgroundColor='#E44C76'">Mettre à jour le profil</button>
                                             </div>
                                          </div>
                                       </div>
                                    </form>
                                 </div>
                              </div>
                           </div>
                           <div class="tab-pane fade" id="nav-password" role="tabpanel" aria-labelledby="nav-password-tab">
                              <div class="profile__password">
                                 <form action="includes/change_password.php" method="POST">
                                    <div class="row">
                                       <div class="col-xxl-12">
                                          <div class="tp-profile-input-box">
                                             <div class="tp-contact-input">
                                                <input name="old_pass" id="old_pass" type="password" required>
                                             </div>
                                             <div class="tp-profile-input-title">
                                                <label for="old_pass">Ancien Mot de Passe</label>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="col-xxl-6 col-md-6">
                                          <div class="tp-profile-input-box">
                                             <div class="tp-profile-input">
                                                <input name="new_pass" id="new_pass" type="password" required>
                                             </div>
                                             <div class="tp-profile-input-title">
                                                <label for="new_pass">Nouveau Mot de Passe</label>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="col-xxl-6 col-md-6">
                                          <div class="tp-profile-input-box">
                                             <div class="tp-profile-input">
                                                <input name="con_new_pass" id="con_new_pass" type="password" required>
                                             </div>
                                             <div class="tp-profile-input-title">
                                                <label for="con_new_pass">Confirmer le Mot de Passe</label>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="col-xxl-6 col-md-6">
                                          <div class="profile__btn">
                                             <button type="submit" class="tp-btn" style="background-color: #E44C76;" onmouseover="this.style.backgroundColor='#C50E40'" onmouseout="this.style.backgroundColor='#E44C76'">Mettre à jour</button>
                                          </div>
                                       </div>
                                    </div>
                                 </form>
                              </div>
                           </div>

                           <div class="tab-pane fade" id="nav-address" role="tabpanel" aria-labelledby="nav-address-tab">
                              <div class="profile__address">
                                 <div class="row">
                                    <div class="col-md-6">
                                       <div class="profile__address-item d-sm-flex align-items-start">
                                          <div class="profile__address-icon">
                                             <span>
                                                <svg enable-background="new 0 0 32 32" viewBox="0 0 32 32" ><g><path d="m31.494 23.128-.959-.844v-3.708c0-1.315-1.067-2.382-2.382-2.382-1.144 0-2.126.813-2.34 1.937l-.821-.721c-.954-.835-2.378-.835-3.332 0l-6.5 5.717c-.307.276-.332.748-.057 1.055.262.292.704.331 1.014.091v5.326c.001 1.187.963 2.149 2.15 2.15h10.119c1.187-.001 2.148-.963 2.149-2.15v-5.326c.323.257.793.204 1.05-.119.248-.311.208-.763-.091-1.026zm-4.227-4.552c-.016-.488.366-.897.854-.913s.897.366.913.854c.001.02.001.04 0 .059v2.389l-1.767-1.554zm-2.625 11.671h-2.5v-1.748c.001-.613.497-1.109 1.11-1.11h.285c.613.001 1.109.497 1.11 1.11zm4.393-.648c0 .171-.068.336-.189.457h-.004c-.122.123-.287.191-.46.191h-2.24v-1.748c-.002-1.441-1.169-2.608-2.61-2.61h-.285c-1.441.002-2.608 1.169-2.61 2.61v1.746h-2.373c-.359-.001-.649-.291-.65-.65v-6.63l5.035-4.428c.387-.339.965-.339 1.352 0l5.034 4.426z"/><path d="m21.106 22.318c0 1.226.993 2.219 2.219 2.219s2.219-.994 2.219-2.219v-.001c-.002-1.225-.994-2.217-2.219-2.218-1.226 0-2.219.993-2.219 2.219zm2.938-.001c-.002.396-.323.716-.719.717v.002c-.397 0-.719-.322-.719-.719s.322-.719.719-.719.719.322.719.719z"/><path d="m23.001 10.145c0-.414-.336-.75-.75-.75h-15.462c-.414 0-.75.336-.75.75s.336.75.75.75h15.463c.414-.001.749-.336.749-.75z"/><path d="m6.789 14.216c-.414 0-.75.336-.75.75s.336.75.75.75h10.572c.414 0 .75-.336.75-.75s-.336-.75-.75-.75z"/><path d="m12.075 19.039h-5.286c-.414 0-.75.336-.75.75s.336.75.75.75h5.286c.414 0 .75-.336.75-.75s-.336-.75-.75-.75z"/><path d="m11.556 30.247h-9.03c-.427-.001-.772-.346-.773-.773v-25.653c.001-.27.142-.52.372-.661l2.11-1.283c.268-.164.609-.148.862.039l1.404 1.037c.749.558 1.764.598 2.554.1l1.9-1.183c.26-.163.593-.156.846.018l1.629 1.111c.766.527 1.776.532 2.547.013l1.692-1.133c.255-.171.587-.175.846-.009l1.836 1.171c.783.504 1.796.476 2.55-.072l1.425-1.027c.265-.191.622-.195.891-.01l1.736 1.2c.21.144.335.382.335.637v8.089c0 .414.336.75.75.75s.75-.336.75-.75v-8.093c-.001-.748-.37-1.449-.987-1.872l-1.733-1.194c-.792-.544-1.839-.532-2.619.028l-1.425 1.025c-.256.186-.6.196-.867.025l-1.836-1.17c-.761-.485-1.736-.474-2.486.028l-1.692 1.133c-.262.177-.606.177-.868 0l-1.63-1.119c-.746-.509-1.722-.529-2.488-.05l-1.896 1.181c-.269.169-.614.155-.868-.034l-1.406-1.037c-.742-.55-1.744-.593-2.531-.11l-2.11 1.279c-.677.414-1.09 1.15-1.093 1.943v25.653c.001 1.255 1.018 2.272 2.273 2.273h9.03c.414 0 .75-.336.75-.75s-.336-.75-.75-.75z"/></g></svg>
                                             </span>
                                          </div>
                                          <div class="profile__address-content">
                                             <h3 class="profile__address-title">
                                                Adresse de Facturation
                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editBillingModal">Modifier</button>
                                             </h3>
                                             <p><span>Rue :</span> <?= htmlspecialchars($billing_address['street'] ?? '') ?></p>
                                             <p><span>Ville :</span> <?= htmlspecialchars($billing_address['city'] ?? '') ?></p>
                                             <p><span>Numéro de téléphone :</span> <?= htmlspecialchars($billing_address['phone'] ?? '') ?></p>
                                             <p><span>Code postal :</span> <?= htmlspecialchars($billing_address['postal_code'] ?? '') ?></p>
                                             <p><span>Indicatif téléphonique :</span> <?= htmlspecialchars($billing_address['country_code'] ?? '') ?></p>
                                             <p><span>Pays :</span> <?= htmlspecialchars($billing_address['country_name'] ?? '') ?></p>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-md-6">
                                       <div class="profile__address-item d-sm-flex align-items-start">
                                          <div class="profile__address-icon">
                                             <span>
                                                <svg viewBox="0 0 64 64"><g id="ad"><g><path d="m50 49c-1.654 0-3 1.346-3 3s1.346 3 3 3 3-1.346 3-3-1.346-3-3-3zm0 4c-.551 0-1-.448-1-1s.449-1 1-1 1 .448 1 1-.449 1-1 1z"/><path d="m13 55c1.654 0 3-1.346 3-3s-1.346-3-3-3-3 1.346-3 3 1.346 3 3 3zm0-4c.551 0 1 .448 1 1s-.449 1-1 1-1-.448-1-1 .449-1 1-1z"/><path d="m62 47.278v-8.653c0-.612-.184-1.203-.533-1.708l-7.452-10.763c-.933-1.349-2.47-2.154-4.111-2.154h-7.61l1.742-3.049c1.285-1.731 1.963-3.788 1.963-5.951 0-5.514-4.486-10-10-10-1.791 0-3.547.479-5.081 1.385-.476.281-.633.895-.352 1.37s.893.632 1.37.353c1.225-.725 2.63-1.107 4.063-1.107 4.411 0 8 3.589 8 8 0 1.748-.554 3.408-1.601 4.802-.025.033-.048.068-.069.104l-6.331 11.078-6.33-11.077c-.021-.036-.044-.071-.069-.104-1.047-1.394-1.601-3.055-1.601-4.803 0-1.897.676-3.736 1.902-5.179.358-.42.307-1.052-.114-1.409-.421-.358-1.052-.308-1.41.114-1.534 1.803-2.379 4.103-2.379 6.474 0 1.781.467 3.486 1.346 5h-23.343c-1.654 0-3 1.346-3 3v27c0 1.654 1.346 3 3 3h2.08c.488 3.386 3.401 6 6.92 6s6.432-2.614 6.92-6h9.223c.552 0 1-.447 1-1s-.448-1-1-1h-9.223c-.488-3.386-3.401-6-6.92-6s-6.432 2.614-6.92 6h-2.08c-.551 0-1-.448-1-1v-9h26c.552 0 1-.447 1-1s-.448-1-1-1h-26v-16.001c0-.552.449-1 1-1h24.563l6.569 11.496c.178.312.509.504.868.504s.69-.192.868-.504l1.132-1.981v7.485h-5c-.552 0-1 .447-1 1s.448 1 1 1h5v10h-5.5c-.552 0-1 .447-1 1s.448 1 1 1h10.58c.488 3.386 3.401 6 6.92 6s6.432-2.614 6.92-6h4.08c1.103 0 2-.897 2-2v-2c0-.737-.405-1.375-1-1.722zm-49-.278c2.757 0 5 2.243 5 5s-2.243 5-5 5-5-2.243-5-5 2.243-5 5-5zm46.784-9h-15.784v-8h10.245zm-18.632-12h8.753c.984 0 1.906.483 2.466 1.293l.49.707h-8.86c-1.103 0-2 .897-2 2v8c0 1.103.897 2 2 2h16v7h-5.111c-1.263-1.235-2.988-2-4.889-2s-3.627.765-4.889 2h-5.111v-18.985l1.152-2.015zm-1.152 23h3.685c-.297.622-.503 1.294-.605 2h-3.08zm10 8c-2.757 0-5-2.243-5-5s2.243-5 5-5 5 2.243 5 5-2.243 5-5 5zm11-6h-4.08c-.102-.706-.308-1.378-.605-2h4.685z"/><path d="m36 21c3.309 0 6-2.691 6-6s-2.691-6-6-6-6 2.691-6 6 2.691 6 6 6zm0-10c2.206 0 4 1.794 4 4s-1.794 4-4 4-4-1.794-4-4 1.794-4 4-4z"/><path d="m43 43h4c.552 0 1-.447 1-1s-.448-1-1-1h-4c-.552 0-1 .447-1 1s.448 1 1 1z"/></g></g></svg>
                                             </span>
                                          </div>
                                          <div class="profile__address-content">
                                             <h3 class="profile__address-title">
                                                Adresse de Livraison
                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editShippingModal">Modifier</button>
                                             </h3>
                                             <p><span>Rue :</span> <?= htmlspecialchars($shipping_address['street'] ?? '') ?></p>
                                             <p><span>Ville :</span> <?= htmlspecialchars($shipping_address['city'] ?? '') ?></p>
                                             <p><span>Numéro de téléphone :</span> <?= htmlspecialchars($shipping_address['phone'] ?? '') ?></p>
                                             <p><span>Code postal :</span> <?= htmlspecialchars($shipping_address['postal_code'] ?? '') ?></p>
                                             <p><span>Indicatif téléphonique :</span> <?= htmlspecialchars($shipping_address['country_code'] ?? '') ?></p>
                                             <p><span>Pays :</span> <?= htmlspecialchars($shipping_address['country_name'] ?? '') ?></p>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 
                              </div>

                           </div>
                           <div class="tab-pane fade" id="nav-order" role="tabpanel" aria-labelledby="nav-order-tab">
                              <div class="profile__ticket table-responsive">
                                 <table class="table">
                                    <thead>
                                       <tr>
                                          <th scope="col">Id de la Commande</th>
                                          <th scope="col">Titre du Produit</th>
                                          <th scope="col">Statut</th>
                                          <th scope="col">Voir</th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                       <?php if (!empty($orders)): ?>
                                          <?php foreach ($orders as $order): ?>
                                             <tr>
                                                <th scope="row">#<?= htmlspecialchars($order['order_number']) ?></th>
                                                <td>
                                                   <?php
                                                   // On récupère le premier titre de produit associé à la commande
                                                   $stmtItems = $pdo->prepare("
                                                      SELECT p.title FROM order_items oi
                                                      JOIN products p ON oi.product_id = p.id
                                                      WHERE oi.order_id = ?
                                                      LIMIT 1
                                                   ");
                                                   $stmtItems->execute([$order['id']]);
                                                   $product = $stmtItems->fetchColumn();
                                                   echo htmlspecialchars($product ?? 'Produit inconnu');
                                                   ?>
                                                </td>
                                                <td><?= ucfirst(htmlspecialchars($order['status'])) ?></td>
                                                <td>
                                                   <a href="facture.php?order=<?= $order['id'] ?>" class="tp-logout-btn">Voir</a>
                                                </td>
                                             </tr>
                                          <?php endforeach; ?>
                                       <?php else: ?>
                                          <tr>
                                             <td colspan="4">Aucune commande trouvée.</td>
                                          </tr>
                                       <?php endif; ?>
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                           <div class="tab-pane fade" id="nav-notification" role="tabpanel" aria-labelledby="nav-notification-tab">
                              <div class="profile__notification">
                                 <div class="profile__notification-top mb-30">
                                    <h3 class="profile__notification-title">Mes paramètres d'activité</h3>
                                    <p>Restez informé des notifications concernant les activités vous impliquant, y compris les mentions, les messages, les réponses à vos offres, les nouveaux articles, les ventes et les mises à jour administratives.</p>
                                 </div>
                                 <div class="profile__notification-list">
                                    <?php
                                       // Inclure le fichier de notification
                                       require_once 'notif.php';
                                    ?>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
     </section>
     <!-- profile area end -->

   <?php
      if (isset($_GET['success']) || isset($_GET['error'])) {
         $notifType = isset($_GET['success']) ? 'success' : 'error';
         $messages = [
            'password_updated' => '✅ Votre mot de passe a été modifié avec succès.',
            'unauthenticated' => 'Vous devez être connecté pour changer votre mot de passe.',
            'missing_fields' => 'Veuillez remplir tous les champs.',
            'nomatch' => '❌ Les nouveaux mots de passe ne correspondent pas.',
            'wrong_old' => '❌ L’ancien mot de passe est incorrect.'
         ];
         $key = $_GET['success'] ?? $_GET['error'];
         $msg = $messages[$key] ?? 'Notification inconnue.';
   ?>
      <!-- MODALE BOOTSTRAP -->
      <div class="modal fade" id="passwordFeedback" tabindex="-1" aria-labelledby="passwordFeedbackLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header <?= $notifType === 'success' ? 'bg-success text-white' : 'bg-danger text-white' ?>">
            <h5 class="modal-title" id="passwordFeedbackLabel">Notification</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
            <?= htmlspecialchars($msg) ?>
            </div>
         </div>
      </div>
      </div>

      <script>
      document.addEventListener("DOMContentLoaded", function () {
         var modal = new bootstrap.Modal(document.getElementById('passwordFeedback'));
         modal.show();
      });
      </script>
   <?php } ?>

      <!-- MODALE FACTURATION -->
      <div class="modal fade" id="editBillingModal" tabindex="-1" aria-labelledby="editBillingModalLabel" aria-hidden="true">
      <div class="modal-dialog">
         <div class="modal-content">
            <form id="billingForm">
            <div class="modal-header">
               <h5 class="modal-title">Modifier l'adresse de facturation</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
               <div class="mb-2">
                  <label>Rue</label>
                  <input type="text" class="form-control" name="billing_street" value="<?= htmlspecialchars($billing_address['street'] ?? '') ?>">
               </div>
               <div class="mb-2">
                  <label>Ville</label>
                  <input type="text" class="form-control" name="billing_city" value="<?= htmlspecialchars($billing_address['city'] ?? '') ?>">
               </div>
               <div class="mb-2">
                  <label>Téléphone</label>
                  <input type="text" class="form-control" name="billing_phone" value="<?= htmlspecialchars($billing_address['phone'] ?? '') ?>">
               </div>
               <div class="mb-2">
                  <label>Code postal</label>
                  <input type="text" class="form-control" name="billing_postal_code" value="<?= htmlspecialchars($billing_address['postal_code'] ?? '') ?>">
               </div>
               <div class="mb-2">
                  <label>Code pays</label>
                  <input type="text" class="form-control" name="billing_country_code" value="<?= htmlspecialchars($billing_address['country_code'] ?? '') ?>">
               </div>
               <div class="mb-2">
                  <label>Pays</label>
                  <input type="text" class="form-control" name="billing_country_name" value="<?= htmlspecialchars($billing_address['country_name'] ?? '') ?>">
               </div>
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
            </form>
         </div>
      </div>
      </div>


      <!-- MODALE LIVRAISON -->
      <div class="modal fade" id="editShippingModal" tabindex="-1" aria-labelledby="editShippingModalLabel" aria-hidden="true">
      <div class="modal-dialog">
         <div class="modal-content">
            <form id="shippingForm">
            <div class="modal-header">
               <h5 class="modal-title" id="editShippingModalLabel">Modifier l'adresse de livraison</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
               <!-- Champs -->
               <div class="mb-2">
                  <label for="shipping_street">Rue</label>
                  <input type="text" class="form-control" name="shipping_street" id="shipping_street" value="<?= htmlspecialchars($shipping_address['street'] ?? '') ?>" required>
               </div>
               <div class="mb-2">
                  <label for="shipping_city">Ville</label>
                  <input type="text" class="form-control" name="shipping_city" id="shipping_city" value="<?= htmlspecialchars($shipping_address['city'] ?? '') ?>" required>
               </div>
               <div class="mb-2">
                  <label for="shipping_phone">Téléphone</label>
                  <input type="text" class="form-control" name="shipping_phone" id="shipping_phone" value="<?= htmlspecialchars($shipping_address['phone'] ?? '') ?>" required>
               </div>
               <div class="mb-2">
                  <label for="shipping_postal_code">Code postal</label>
                  <input type="text" class="form-control" name="shipping_postal_code" id="shipping_postal_code" value="<?= htmlspecialchars($shipping_address['postal_code'] ?? '') ?>" required>
               </div>
               <div class="mb-2">
                  <label for="shipping_country_code">Code pays</label>
                  <input type="text" class="form-control" name="shipping_country_code" id="shipping_country_code" value="<?= htmlspecialchars($shipping_address['country_code'] ?? '') ?>" required>
               </div>
               <div class="mb-2">
                  <label for="shipping_country_name">Pays</label>
                  <input type="text" class="form-control" name="shipping_country_name" id="shipping_country_name" value="<?= htmlspecialchars($shipping_address['country_name'] ?? '') ?>" required>
               </div>
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
            </form>
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
                              <li><a href="#">Suivre les commandes</a></li>
                              <li><a href="#">Liste de souhaits</a></li>
                              <li><a href="#">Mon compte</a></li>
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

      <script>
      function handleAddressForm(formId, type) {
      document.getElementById(formId).addEventListener('submit', function(e) {
         e.preventDefault();
         const formData = new FormData(this);

         fetch('includes/update_address.php?type=' + type, {
            method: 'POST',
            body: formData
         })
         .then(res => {
            if (!res.ok) throw new Error("Erreur serveur : " + res.status);
            return res.json();
         })
         .then(data => {
            if (data.success) {
            alert("Adresse mise à jour !");
            location.reload();
            } else {
            alert("Erreur : " + data.message);
            }
         })
         .catch(err => {
            alert("Une erreur s'est produite.");
            console.error(err);
         });
      });
      }

      handleAddressForm('billingForm', 'billing');
      handleAddressForm('shippingForm', 'shipping');
      </script>

   </body>

<!-- Mirrored from html.weblearnbd.net/shofy-prv/shofy/profile.php by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 15 Oct 2023 08:15:25 GMT -->
</html>
