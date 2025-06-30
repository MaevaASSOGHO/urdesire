<?php
require_once("config.php");

try {
    $categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) {
    die("Erreur lors de la récupération des catégories : " . $e->getMessage());
}

// Mapping images personnalisées
$imageMap = [
    1 => "assets/img/cat/accessoires.jpg",
    2 => "assets/img/cat/bien-etre.jpg",
    3 => "assets/img/cat/comestibles.jpg",
    4 => "assets/img/cat/electronique.jpg",
    5 => "assets/img/cat/lingerie.jpg"
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catégories - UrDesire</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
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