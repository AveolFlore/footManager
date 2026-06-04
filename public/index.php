<?php
// Cacher les warnings et erreurs
error_reporting(0);
ini_set('display_errors', 0);

// Configurer le dossier de session
$sessionPath = __DIR__ . '/../tmp';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}
session_save_path($sessionPath);

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "../autoload.php";
require('../core/router.php');
