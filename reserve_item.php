<?php
// Réservation d'une donation par une association (lien direct ?id=...)
require 'auth.php';
require 'db_config.php';
require_role('association');

$item_id  = (int)($_GET['id'] ?? 0);
$assoc_id = $_SESSION['user_id'];

try {
    // Réservation possible uniquement si la donation est « approved »
    $stmt = $pdo->prepare("UPDATE items SET status = 'reserved', assoc_id = ? WHERE id = ? AND status = 'approved'");
    $stmt->execute([$assoc_id, $item_id]);
    header("Location: association_dashboard.php?success=1");
    exit();
} catch (PDOException $e) {
    die("Réservation impossible.");
}
