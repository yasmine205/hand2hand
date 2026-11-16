<?php
require 'auth.php';
require 'db_config.php';
require_role('user');

if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: offretravail.php"); exit(); }

$title   = trim($_POST['job_title'] ?? '');
$company = trim($_POST['company'] ?? '');
$loc     = trim($_POST['location'] ?? '');
$desc    = trim($_POST['description'] ?? '');

if ($title === '' || $company === '' || $loc === '') {
    header("Location: offretravail.php?error=invalid"); exit();
}

$stmt = $pdo->prepare("INSERT INTO job_offers (user_id, job_title, company, location, description, status)
                       VALUES (?, ?, ?, ?, ?, 'pending')");
$stmt->execute([$_SESSION['user_id'], $title, $company, $loc, $desc]);

header("Location: dashboard.php?success=job_posted");
exit();
