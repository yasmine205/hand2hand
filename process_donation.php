<?php
require 'auth.php';
require 'db_config.php';
require_role('user');

if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: donate.php"); exit(); }

$user_id        = $_SESSION['user_id'];
$item_name      = trim($_POST['item_name'] ?? '');
$category       = $_POST['category'] ?? 'Other';
$quantity       = trim($_POST['quantity'] ?? '');
$item_condition = $_POST['item_condition'] ?? 'Good';
$description    = trim($_POST['description'] ?? '');
$contact_phone  = trim($_POST['contact_phone'] ?? '');

if (!in_array($category, ['Food', 'Clothes', 'Medical', 'Other'], true))       $category = 'Other';
if (!in_array($item_condition, ['New', 'Like New', 'Good', 'Fair'], true))     $item_condition = 'Good';
if ($item_name === '' || $quantity === '' || $contact_phone === '') {
    header("Location: donate.php?error=invalid"); exit();
}

// --- Envoi de l'image (sécurisé) ---
$image_name = null;
$f = $_FILES['item_image'] ?? null;
if (!$f || $f['error'] !== UPLOAD_ERR_OK) {
    header("Location: donate.php?error=image"); exit();
}
if ($f['size'] > 5 * 1024 * 1024) {
    header("Location: donate.php?error=size"); exit();
}
$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
$mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
if (!isset($allowed[$mime])) {
    header("Location: donate.php?error=image"); exit();
}
$target_dir = __DIR__ . "/uploads/";
if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
$image_name = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];   // nom aléatoire, extension contrôlée
if (!move_uploaded_file($f['tmp_name'], $target_dir . $image_name)) {
    header("Location: donate.php?error=image"); exit();
}

try {
    $stmt = $pdo->prepare("INSERT INTO items (user_id, item_name, category, quantity, item_condition, item_image, description, contact_phone, status)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([$user_id, $item_name, $category, $quantity, $item_condition, $image_name, $description, $contact_phone]);
    header("Location: dashboard.php?donation=success");
    exit();
} catch (PDOException $e) {
    die("Erreur : " . htmlspecialchars($e->getMessage()));
}
