<?php

function requireLogin()
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user'])) {
        header("Location:/page-login");
        exit;
    }
}

function requireRole($role)
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== $role) {
        header("Location:/page-login");
        exit;
    }
}