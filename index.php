<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (isset($_SESSION['usuario']) && is_array($_SESSION['usuario'])) {
    header('Location: home.php');
    exit;
} else {
    header('Location: login.php');
    exit;
}
