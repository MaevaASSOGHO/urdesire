<?php
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

<body>

  

<main>
   <!-- Breadcrumb -->
   <section class="breadcrumb__area include-bg pt-95 pb-50">
      <div class="container">
         <div class="row">
            <div class="col-xxl-12">
               <div class="breadcrumb__content">
                  <h3 class="profile__notification-title">Mes Commandes</h3>

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
