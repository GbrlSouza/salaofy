<?php
require_once './func/condominio.php'; 

$condominioManager = new Condominio($conexao);
$idNovoCondominio = $condominioManager->criarCondominio("Residencial Salaofy", "12345678");

if ($idNovoCondominio) {
    echo "Condomínio criado com ID: " . $idNovoCondominio;

    $sucesso = $condominioManager->associarSindico($idNovoCondominio, 2);
    
    if ($sucesso) { echo "Síndico associado com sucesso."; }
} else { echo "Falha ao criar condomínio."; }

$dadosCondominio = $condominioManager->buscarCondominio(1); 
