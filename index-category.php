<?php
require_once("config.php");

try {
    $categories = $pdo->query("SELECT c.id, c.name, COUNT(p.id) as product_count
        FROM categories c
        LEFT JOIN products p ON p.category_id = c.id
        GROUP BY c.id, c.name
        ORDER BY c.name ASC")->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

$imageMap = [
    1 => "assets/img/category/5/access-toilette.png",
    2 => "assets/img/category/5/bien-etre.png",
    3 => "assets/img/category/5/category-3.jpg",
    4 => "assets/img/category/5/electronics.png",
    5 => "assets/img/category/5/6.png"
];

$bgColors = ["#E5EFE2", "#F5EFEC", "#F2E0E3", "#E6F1E0", "#F2E3D5"];
$textColors = ["#5C8C10", "#FA3737", "#F87117", "#5C8C10", "#FF7B02"];
?>

<section class="tp-category-area pt-110 pb-110">
  <div class="container">
    <div class="row">
      <div class="col-xl-12">
        <div class="tp-section-title-wrapper-5 mb-50 text-center">
          <span class="tp-section-title-pre-5">
            Découvre nos catégories
            <svg width="82" height="22" viewBox="0 0 82 22" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M81 14.5798C0.890564 -8.05914 -5.81154 0.0503902 5.00322 21" stroke="currentColor"
                stroke-opacity="0.3" stroke-width="2" stroke-miterlimit="3.8637" stroke-linecap="round" />
            </svg>
          </span>
          <h3 class="tp-section-title-5">Populaire sur la boutique UrDesire.</h3>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-12">
        <div class="tp-category-slider-5">
          <div class="tp-category-slider-active-5 swiper-container mb-50">
            <div class="swiper-wrapper">
              <?php foreach ($categories as $index => $cat): ?>
                <?php
                  $bgColor = $bgColors[$index % count($bgColors)];
                  $textColor = $textColors[$index % count($textColors)];
                  $image = $imageMap[$cat['id']] ?? "assets/img/category/5/default.png";
                ?>
                <div class="tp-category-item-5 p-relative z-index-1 fix swiper-slide"
                     data-bg-color="<?= $bgColor ?>">
                  <a href="shop.php?category=<?= $cat['id'] ?>">
                    <div class="tp-category-thumb-5 include-bg"
                         data-background="<?= $image ?>"></div>
                    <div class="tp-category-content-5">
                      <h3 class="tp-category-title-5"><?= htmlspecialchars($cat['name']) ?></h3>
                      <span data-text-color="<?= $textColor ?>"><?= $cat['product_count'] ?> Articles</span>
                    </div>
                  </a>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="tp-category-5-swiper-scrollbar tp-swiper-scrollbar"></div>
        </div>
      </div>
    </div>
  </div>
</section>