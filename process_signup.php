<?php
require 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: SignUp.html");
    exit();
}

function back(string $err) { header("Location: SignUp.html?error=" . $err); exit(); }

$type       = ($_POST['account_type'] ?? 'user') === 'association' ? 'association' : 'user';
$first_name = trim($_POST['first_name'] ?? '');
$last_name  = trim($_POST['last_name'] ?? '');
$email      = trim($_POST['email'] ?? '');
$rawPass    = $_POST['password'] ?? '';
$phone      = trim($_POST['phone'] ?? '');
$city       = trim($_POST['city'] ?? '');
$address    = trim($_POST['address'] ?? '');

// Validation côté serveur (celle du navigateur peut être contournée)
if ($first_name === '' || $last_name === '')               back('invalid');
if (!filter_var($email, FILTER_VALIDATE_EMAIL))             back('email');
if (strlen($rawPass) < 6)                                   back('password');
if (!preg_match('/^[0-9]{8}$/', $phone))                    back('phone');

$password = password_hash($rawPass, PASSWORD_BCRYPT);
$table    = $type === 'association' ? 'associations' : 'users';

try {
    // Email déjà utilisé ?
    $check = $pdo->prepare("SELECT id FROM $table WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) back('exists');

    if ($type === 'association') {
        $assoc_name = trim($_POST['assoc_name'] ?? '');
        $reg_number = trim($_POST['reg_number'] ?? '');
        if ($assoc_name === '' || $reg_number === '') back('assoc');

        $stmt = $pdo->prepare("INSERT INTO associations (assoc_name, reg_number, first_name, last_name, email, password, phone, city, address)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$assoc_name, $reg_number, $first_name, $last_name, $email, $password, $phone, $city, $address]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, phone, city, address)
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $email, $password, $phone, $city, $address]);
    }

    header("Location: login.html?signup=success");
    exit();

} catch (PDOException $e) {
    die("Erreur d'inscription : " . htmlspecialchars($e->getMessage()));
}
