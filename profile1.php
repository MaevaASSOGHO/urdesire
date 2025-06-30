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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="utf-8">
   <meta http-equiv="x-ua-compatible" content="ie=edge">
   <title>UrDesire - Boutique pour adultes</title>
   <meta name="description" content="">
   <meta name="viewport" content="width=device-width, initial-scale=1">

   <!-- Place favicon.ico in the root directory -->
   <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/favicon-32x32.png">

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



<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2 class="fw-bold">Mon Profil</h2>
            <?php if (!empty($success)) echo "<div class='alert alert-success'>" . $success . "</div>"; ?>
            <?php if (!empty($erreur)) echo "<div class='alert alert-danger'>" . $erreur . "</div>"; ?>
        </div>
    </div>

    <div class="row">
        <!-- Avatar -->
        <div class="col-md-4 text-center">
            <img src="<?php echo htmlspecialchars($user['avatar'] ?? 'assets/img/users/default.jpg'); ?>" class="img-thumbnail rounded-circle mb-3" style="width:150px;height:150px;" alt="Avatar">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_avatar">
                <input type="file" name="avatar" class="form-control mb-2" accept="image/*" required>
                <button type="submit" class="btn btn-sm btn-primary">Changer l'avatar</button>
            </form>
        </div>

        <!-- Infos profil -->
        <div class="col-md-8">
            <form method="POST">
                <input type="hidden" name="action" value="update_profile">
                <div class="mb-3">
                    <label for="pseudo" class="form-label">Pseudonyme</label>
                    <input type="text" class="form-control" name="pseudo" value="<?php echo htmlspecialchars($user['pseudo']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="text" class="form-control" name="telephone" value="<?php echo htmlspecialchars($user['telephone'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn btn-success">Mettre à jour</button>
            </form>
        </div>
    </div>

    <hr class="my-5">

    <div class="row">
        <div class="col-md-6 offset-md-3">
            <h4>Changer de mot de passe</h4>
            <form method="POST">
                <input type="hidden" name="action" value="update_password">
                <div class="mb-3">
                    <label class="form-label">Mot de passe actuel</label>
                    <input type="password" name="ancien_motdepasse" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nouveau mot de passe</label>
                    <input type="password" name="nouveau_motdepasse" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-warning">Modifier</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
