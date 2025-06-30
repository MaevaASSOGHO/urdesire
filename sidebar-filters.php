<div class="tp-shop-sidebar mr-10">
  <!-- Filtre de prix -->
  <div class="tp-shop-widget mb-35">
    <h3 class="tp-shop-widget-title no-border">Filtre de Prix</h3>
    <div class="tp-shop-widget-content">
      <div class="tp-shop-widget-filter">
        <div id="slider-range" class="mb-10"></div>
        <div class="tp-shop-widget-filter-info d-flex align-items-center justify-content-between">
          <span class="input-range">
            <input type="text" id="amount" readonly>
          </span>
          <button id="price-filter-btn" class="tp-shop-widget-filter-btn" type="button">Filtrer</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Statut du produit -->
  <div class="tp-shop-widget mb-50">
    <h3 class="tp-shop-widget-title">Statut du Produit</h3>
    <div class="tp-shop-widget-content">
      <div class="tp-shop-widget-checkbox">
        <ul class="filter-items filter-checkbox">
          <li class="filter-item checkbox">
            <input id="on_sale" type="checkbox" value="out">
            <label for="on_sale">En rupture</label>
          </li>
          <li class="filter-item checkbox">
            <input id="in_stock" type="checkbox" value="in">
            <label for="in_stock">En stock</label>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Catégories -->
  <div class="tp-shop-widget mb-50">
    <h3 class="tp-shop-widget-title">Catégories</h3>
    <div class="tp-shop-widget-content">
      <div class="tp-shop-widget-categories">
        <ul id="category-filter">
          <?php foreach ($categories as $cat): ?>
            <li>
              <a href="#" data-id="<?= $cat['id'] ?>">
                <?= htmlspecialchars($cat['name']) ?> <span><?= $cat['total'] ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
<script>
$(function () {
  $("#slider-range").slider({
    range: true,
    min: 5000,
    max: 15000,
    values: [5000, 15000],
    slide: function (event, ui) {
      $("#amount").val(ui.values[0] + " FCFA - " + ui.values[1] + " FCFA");
    }
  });
  $("#amount").val($("#slider-range").slider("values", 0) + " FCFA - " + $("#slider-range").slider("values", 1) + " FCFA");

  // Filtrage AJAX
  function applyFilters() {
    const min = $("#slider-range").slider("values", 0);
    const max = $("#slider-range").slider("values", 1);
    const stock = $("#in_stock").is(":checked") ? 'in' : ($("#on_sale").is(":checked") ? 'out' : '');
    const categorie = $("#category-filter a.active").data("id") || '';

    $.get("shop.php", {
      min_price: min,
      max_price: max,
      stock: stock,
      categorie: categorie
    }, function (data) {
      const content = $(data).find(".tp-shop-items-wrapper").html();
      $(".tp-shop-items-wrapper").html(content);
    });
  }

  $('#price-filter-btn').on('click', applyFilters);
  $('#in_stock, #on_sale').on('change', applyFilters);

  $('#category-filter a').on('click', function (e) {
    e.preventDefault();
    $('#category-filter a').removeClass('active');
    $(this).addClass('active');
    applyFilters();
  });
});
</script>

