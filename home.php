<?php
require './inc/header.php'; 

$id_perfil = $id_perfil_logado;

if ($id_perfil == 1) { require 'admin_dashboard.php'; }
elseif ($id_perfil == 2) {  require 'sindico_dashboard.php'; }
elseif ($id_perfil == 3) { require 'morador_dashboard.php'; }
else { echo '<div class="alert alert-danger">Erro de autenticação ou perfil não reconhecido.</div>'; }

require 'inc/footer.php';
