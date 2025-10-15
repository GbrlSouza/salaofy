<?php
require_once './inc/header.php'; 

$id_perfil = $id_perfil_logado;

if ($id_perfil == 1) { require_once 'admin_dashboard.php'; }
elseif ($id_perfil == 2) {  require_once 'sindico_dashboard.php'; }
elseif ($id_perfil == 3) { require_once 'morador_dashboard.php'; }
else { echo '<div class="alert alert-danger">Erro de autenticação ou perfil não reconhecido.</div>'; }

require_once 'inc/footer.php';
