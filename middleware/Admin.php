<?php

/**
 * Middleware pour sécuriser les routes réservées aux administrateurs.
 * Vérifie si l'utilisateur est connecté et possède le rôle 'admin' ou 'president'.
 */
function requireAdmin()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'president'])) {
        // Rediriger vers la page de connexion si l'utilisateur n'est pas admin
        header("Location: /page-login?msg=Accès refusé : réservé aux administrateurs");
        exit;
    }
}
