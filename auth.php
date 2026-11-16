<?php
// Fonctions communes : session, contrôle des rôles, échappement HTML
if (session_status() === PHP_SESSION_NONE) session_start();

function require_role(string $role): void {
    if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== $role) {
        header("Location: login.html");
        exit();
    }
}

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
