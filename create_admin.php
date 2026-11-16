<?php
// Création du compte administrateur.
// - Utilisable UNIQUEMENT tant qu'aucun admin n'existe.
// - À SUPPRIMER du serveur une fois l'admin créé.
require 'db_config.php';

$message = '';
$ok = false;

$count = (int)$pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
$locked = $count > 0;

if (!$locked && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Adresse email invalide.";
    } elseif (strlen($password) < 8) {
        $message = "Le mot de passe doit contenir au moins 8 caractères.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO admins (username, email, password) VALUES ('Administrator', ?, ?)");
        $stmt->execute([$email, password_hash($password, PASSWORD_BCRYPT)]);
        $ok = true;
        $locked = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer l'admin | Hand2Hand</title>
    <style>
        body { font-family: Arial, sans-serif; background:#0a0f1c; color:#fff; display:flex; justify-content:center; padding:60px 20px; }
        .box { background:#111827; border:1px solid #ffffff22; border-radius:20px; padding:32px; width:100%; max-width:420px; }
        input { width:100%; padding:12px; margin:8px 0; border-radius:10px; border:1px solid #ffffff22; background:#ffffff0d; color:#fff; box-sizing:border-box; }
        button { width:100%; padding:12px; margin-top:8px; border:0; border-radius:30px; background:#10b981; color:#fff; font-weight:bold; cursor:pointer; }
        .ok { color:#34d399; } .err { color:#f87171; } .warn { color:#fbbf24; }
    </style>
</head>
<body>
<div class="box">
    <h2>Créer le compte admin</h2>
    <?php if ($ok): ?>
        <p class="ok">Compte admin créé avec succès.</p>
        <p class="warn"><strong>Supprimez maintenant le fichier create_admin.php.</strong></p>
        <p><a style="color:#34d399" href="login.html">Aller à la connexion</a></p>
    <?php elseif ($locked): ?>
        <p class="warn">Un administrateur existe déjà : cette page est verrouillée.</p>
        <p>Supprimez le fichier <code>create_admin.php</code>.</p>
    <?php else: ?>
        <?php if ($message): ?><p class="err"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email de l'admin" required>
            <input type="password" name="password" placeholder="Mot de passe (8 caractères min.)" required minlength="8">
            <button type="submit">Créer l'admin</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
