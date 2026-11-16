<?php
require 'auth.php';
require 'db_config.php';
require_role('user');

if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: dashboard.php"); exit(); }

$item_id        = (int)($_POST['item_id'] ?? 0);
$user_id        = $_SESSION['user_id'];
$item_name      = trim($_POST['item_name'] ?? '');
$category       = $_POST['category'] ?? 'Other';
$quantity       = trim($_POST['quantity'] ?? '');
$item_condition = $_POST['item_condition'] ?? 'Good';
$description    = trim($_POST['description'] ?? '');
$contact_phone  = trim($_POST['contact_phone'] ?? '');

if (!in_array($category, ['Food', 'Clothes', 'Medical', 'Other'], true))   $category = 'Other';
if (!in_array($item_condition, ['New', 'Like New', 'Good', 'Fair'], true)) $item_condition = 'Good';
if ($item_name === '' || $quantity === '' || $contact_phone === '') {
    header("Location: edit_donation.php?id=$item_id"); exit();
}

try {
    // Modifiable uniquement par son propriétaire et tant que la donation est « pending »
    $stmt = $pdo->prepare("UPDATE items SET item_name = ?, category = ?, quantity = ?, item_condition = ?,
                           description = ?, contact_phone = ?
                           WHERE id = ? AND user_id = ? AND status = 'pending'");
    $stmt->execute([$item_name, $category, $quantity, $item_condition, $description, $contact_phone, $item_id, $user_id]);
    header("Location: dashboard.php?update=success");
    exit();
} catch (PDOException $e) {
    die("Erreur : " . htmlspecialchars($e->getMessage()));
}
