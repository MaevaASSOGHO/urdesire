<?php
/*-------------------------------------------------
 | CONFIGURATION DE LA BASE DE DONNÉES
 *------------------------------------------------*/
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'toge4652_urdesire');
if (!defined('DB_USER')) define('DB_USER', 'toge4652_supa_admin');
if (!defined('DB_PASS')) define('DB_PASS', '8abe-VKUE-4vF#');


/*-------------------------------------------------
 | CONNEXION PDO
 *------------------------------------------------*/
try {
    $dsn  = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo  = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die('Connexion échouée : ' . $e->getMessage());
}
