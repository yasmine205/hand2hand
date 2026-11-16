<?php
session_start();
require 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.html");
    exit();
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';   // pas de trim : il doit rester identique à celui de l'inscription
$type     = $_POST['account_type'] ?? 'user';

// Liste blanche des tables autorisées (évite toute injection via $type)
$tables = ['admin' => 'admins', 'association' => 'associations', 'user' => 'users'];
if (!isset($tables[$type])) { $type = 'user'; }
$table = $tables[$type];

try {
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $type;

        if ($type === 'admin') {
            $_SESSION['display_name'] = $user['username'] ?? "Administrator";
        } elseif ($type === 'association') {
            $_SESSION['display_name'] = $user['assoc_name'];
        } else {
            $_SESSION['display_name'] = $user['first_name'] . " " . $user['last_name'];
        }

        switch ($type) {
            case 'admin':       header("Location: admin_panel.php"); break;
            case 'association': header("Location: association_dashboard.php"); break;
            default:            header("Location: dashboard.php"); break;
        }
        exit();
    }

    header("Location: login.html?error=1");
    exit();

} catch (PDOException $e) {
    die("Erreur base de données : " . htmlspecialchars($e->getMessage()));
}
