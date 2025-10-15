<?php
require_once './inc/bootstrap.php'; 
checarAutenticacao();

require './inc/header.php'; 

$id_perfil = $_SESSION['usuario']['id_perfil'] ?? 0;

if ($id_perfil == 1) { 
    require './admin_dashboard.php'; 
} elseif ($id_perfil == 2) {  
    require './sindico_dashboard.php'; 
} elseif ($id_perfil == 3) { 
    require './morador_dashboard.php'; 
} else { 
    header('Location: logout.php');
    exit;
}

require './inc/footer.php';