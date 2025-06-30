<?php
require_once 'config.php';

try {
    $stmt = $pdo->prepare("
        SELECT c.id, c.name, COUNT(p.id) AS total
        FROM categories c
        LEFT JOIN products p ON p.category_id = c.id
        GROUP BY c.id, c.name
        ORDER BY c.name ASC
    ");
    $stmt->execute();
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
}
