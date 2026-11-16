<?php
// COPIEZ ce fichier en "db_config.php" puis adaptez les identifiants si besoin.
$host    = 'localhost';
$db      = 'hand2hand';
$user    = 'root'; // Utilisateur par défaut de XAMPP (à adapter)
$pass    = '';     // Mot de passe par défaut de XAMPP (à adapter)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    die("Connexion à la base de données impossible. Vérifiez que MySQL est démarré dans XAMPP "
      . "et que la base '$db' a été importée (database.sql).<br><small>" . htmlspecialchars($e->getMessage()) . "</small>");
}
