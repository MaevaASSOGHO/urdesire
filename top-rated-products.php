<?php
// Connexion à la base de données
require_once 'config.php'; // adapte le chemin si nécessaire

// Récupérer les 5 produits les mieux notés
$stmt = $pdo->prepare("SELECT id, name, description, price, image_url, rating FROM products ORDER BY rating DESC LIMIT 5");
$stmt->execute();
$produits = $stmt->fetchAll();
?>

<!-- Produits les mieux notés -->
<div class="tp-shop-widget mb-50">
  <h3 class="tp-shop-widget-title">Les mieux notés</h3>
  <div class="tp-shop-widget-content">
    <div class="tp-shop-widget-product">
      <?php foreach ($produits as $produit): ?>
        <div class="tp-shop-widget-product-item d-flex align-items-center">
          <div class="tp-shop-widget-product-thumb">
            <a href="product-details.php?id=<?= $produit['id'] ?>">
              <img src="<?= htmlspecialchars($produit['image_url']) ?>" alt="<?= htmlspecialchars($produit['name']) ?>">
            </a>
          </div>
          <div class="tp-shop-widget-product-content">
            <div class="tp-shop-widget-product-rating-wrapper d-flex align-items-center">
              <div class="tp-shop-widget-product-rating">
                <?php
                  $note = floatval($produit['rating']);
                  $fullStars = floor($note);
                  $hasHalfStar = ($note - $fullStars >= 0.5);
                  $totalStars = 5;

                  for ($i = 1; $i <= $fullStars; $i++) {
                    echo '<span>
                      <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 0L7.854 3.756L12 4.362L9 7.284L9.708 11.412L6 9.462L2.292 11.412L3 7.284L0 4.362L4.146 3.756L6 0Z" fill="currentColor"/>
                      </svg>
                    </span>';
                  }

                  if ($hasHalfStar) {
                    echo '<span>
                      <svg width="12" height="12" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                          <linearGradient id="halfGrad">
                            <stop offset="50%" stop-color="currentColor"/>
                            <stop offset="50%" stop-color="#ddd"/>
                          </linearGradient>
                        </defs>
                        <path d="M6 0L7.854 3.756L12 4.362L9 7.284L9.708 11.412L6 9.462L2.292 11.412L3 7.284L0 4.362L4.146 3.756L6 0Z" fill="url(#halfGrad)"/>
                      </svg>
                    </span>';
                    $fullStars++;
                  }

                  for ($i = $fullStars + 1; $i <= $totalStars; $i++) {
                    echo '<span>
                      <svg width="12" height="12" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 0L7.854 3.756L12 4.362L9 7.284L9.708 11.412L6 9.462L2.292 11.412L3 7.284L0 4.362L4.146 3.756L6 0Z" fill="#ddd"/>
                      </svg>
                    </span>';
                  }
                ?>
              </div>
              <div class="tp-shop-widget-product-rating-number">
                <span>(<?= number_format($produit['rating'], 1) ?>)</span>
              </div>
            </div>
            <h4 class="tp-shop-widget-product-title">
              <a href="product-details.php?id=<?= $produit['id'] ?>"><?= htmlspecialchars($produit['name']) ?></a>
            </h4>
            <div class="tp-shop-widget-product-price-wrapper">
              <span class="tp-shop-widget-product-price">
                <?= number_format($produit['price'], 0, ',', '.') ?> FCFA
              </span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
