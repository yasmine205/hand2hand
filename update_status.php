<?php
require 'auth.php';
require 'db_config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { die("Unauthorized"); }

$id     = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';
$type   = $_GET['type'] ?? 'item';

if ($id && in_array($action, ['approved', 'refused'], true)) {
    $table = ($type === 'job') ? 'job_offers' : 'items';
    $stmt = $pdo->prepare("UPDATE $table SET status = ? WHERE id = ? AND status = 'pending'");
    $stmt->execute([$action, $id]);
    header("Location: admin_panel.php?status=updated");
    exit();
}

header("Location: admin_panel.php");
exit();
