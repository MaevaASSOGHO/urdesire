<?php
require_once("config.php");
try {
    $categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}
?>

<div class="offcanvas__area offcanvas__style-green">
  <div class="offcanvas__wrapper">
    <div class="offcanvas__close">
      <button class="offcanvas__close-btn offcanvas-close-btn">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M11 1L1 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M1 1L11 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
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
        <button class="tp-offcanvas-category-toggle" style="background-color: #E44C76;">
          <i class="fa-solid fa-bars"></i>
          Catégories
        </button>
        <div class="tp-category-mobile-menu mt-3">
          <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
              <a href="shop.php?category=<?= $cat['id'] ?>" class="d-block mb-2 text-dark tp-category-link" style="pointer-events:auto;">
                <i class="fa-solid fa-angle-right me-2"></i> <?= htmlspecialchars($cat['name']) ?>
              </a>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="text-muted">Aucune catégorie trouvée.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="tp-main-menu-mobile fix d-lg-none mb-40"></div>

      <div class="offcanvas__contact align-items-center d-none">
        <div class="offcanvas__contact-icon mr-20">
          <span><img src="assets/img/icon/contact.png" alt=""></span>
        </div>
        <div class="offcanvas__contact-content">
          <h3 class="offcanvas__contact-title"><a href="tel:098-852-987">004524865</a></h3>
        </div>
      </div>

      <div class="offcanvas__btn">
        <a href="contact.php" class="tp-btn-2 tp-btn-border-2">Contact</a>
      </div>
    </div>

    <div class="offcanvas__bottom">
      <div class="offcanvas__footer d-flex align-items-center justify-content-between">
        <div class="offcanvas__currency-wrapper currency">
          <span class="offcanvas__currency-selected-currency tp-currency-toggle" id="tp-offcanvas-currency-toggle">Currency : USD</span>
          <ul class="offcanvas__currency-list tp-currency-list">
            <li>XOF</li>
            <li>EUR</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="body-overlay"></div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll('.tp-category-link').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      const target = this.getAttribute('href');
      window.location.href = target;
    });
  });
});
</script>