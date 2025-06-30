<?php
include 'config.php';

$category_id = $_GET['category'] ?? null;

if ($category_id) {
    $stmt = $pdo->prepare("
        SELECT p.*, c.name AS category_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.category_id = ?
        ORDER BY p.id DESC
    ");
    $stmt->execute([$category_id]);
} else {
    $stmt = $pdo->query("
        SELECT p.*, c.name AS category_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        ORDER BY p.id DESC
    ");
}
$produits = $stmt->fetchAll();

?>


<div class="tp-shop-items-wrapper tp-shop-item-primary">
  <div class="tab-content" id="productTabContent">
    <div class="tab-pane fade show active" id="grid-tab-pane" role="tabpanel" aria-labelledby="grid-tab" tabindex="0">
      <div class="row infinite-container">
        <?php if (!empty($produits)): ?>
          <?php foreach ($produits as $produit): ?>
            <div class="col-xl-4 col-md-6 col-sm-6 infinite-item">
              <div class="tp-product-item-2 mb-40">
                <div class="tp-product-thumb-2 p-relative z-index-1 fix w-img">
                  <a href="product-details.php?id=<?= $produit['id'] ?>">
                    <img src="<?= htmlspecialchars($produit['image_url']) ?>" alt="<?= htmlspecialchars($produit['name']) ?>">
                  </a>
                  <div class="tp-product-action-2 tp-product-action-blackStyle">
                    <div class="tp-product-action-item-2 d-flex flex-column">
                      <button type="button" class="tp-product-action-btn-2 tp-product-add-cart-btn">
                        <i class="fa-solid fa-cart-shopping"></i>
                      </button>
                      <button class="tp-product-action-btn-2 tp-product-wishlist-btn add-to-wishlist" data-id="<?= $produit['id'] ?>">
                      <i class="fa-regular fa-heart"></i>
                    </button>

                    </div>
                  </div>
                </div>
                <div class="tp-product-content-2 pt-15">
                  <div class="tp-product-tag-2">
                    <a href="#"> <?= htmlspecialchars($produit['category_name']) ?> </a>
                  </div>

                  <h3 class="tp-product-title-2">
                    <a href="product-details.php?id=<?= $produit['id'] ?>"><?= htmlspecialchars($produit['name']) ?></a>
                  </h3>
                  <div class="tp-product-price-wrapper-2">
                    <span class="tp-product-price-2"><?= number_format($produit['price'], 0, ',', '.') ?> FCFA</span>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12">
            <p class="text-muted">Aucun produit disponible.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".add-to-wishlist").forEach(button => {
    button.addEventListener("click", function (e) {
      e.preventDefault();
      const productId = this.getAttribute("data-id");

      fetch(`add_to_wishlist.php?id=${productId}`)
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            // Ajoute une classe visuelle ou remplace l'icône par cœur plein
            this.querySelector("i").classList.remove("fa-regular");
            this.querySelector("i").classList.add("fa-solid");
            // Optionnel : met à jour le compteur wishlist
            const countSpan = document.querySelector('.wishlist-count');
            if (countSpan) countSpan.textContent = data.total;
          } else {
            alert("Erreur : " + data.message);
          }
        })
        .catch(err => {
          console.error("Erreur AJAX Wishlist :", err);
        });
    });
  });
});
</script>

