<?php
require 'auth.php';
require 'db_config.php';
require_role('user');

$item_id = (int)($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

try {
    // On récupère l'image pour supprimer aussi le fichier
    $sel = $pdo->prepare("SELECT item_image FROM items WHERE id = ? AND user_id = ? AND status = 'pending'");
    $sel->execute([$item_id, $user_id]);
    $item = $sel->fetch();

    if ($item) {
        $del = $pdo->prepare("DELETE FROM items WHERE id = ? AND user_id = ? AND status = 'pending'");
        $del->execute([$item_id, $user_id]);
        if (!empty($item['item_image'])) {
            $path = __DIR__ . '/uploads/' . basename($item['item_image']);
            if (is_file($path)) @unlink($path);
        }
        header("Location: dashboard.php?deleted=1");
    } else {
        header("Location: dashboard.php");
    }
} catch (PDOException $e) {
    die("Erreur de suppression");
}
exit();
