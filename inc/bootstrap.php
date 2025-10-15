<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_PATH', __DIR__ . '/..');
define('DB_PATH', ROOT_PATH . '/db/conexao.php');

if (!isset($conexao) || !$conexao instanceof PDO) {
    $conexao = @require_once DB_PATH; 

    if (!$conexao instanceof PDO) {
        error_log("Erro fatal: Falha ao carregar a conexão PDO. Verifique db/conexao.php.");
        http_response_code(500);
        die("Erro fatal: O sistema está temporariamente indisponível.");
    }
}

function checarAutenticacao(string $redirecionarPara = 'login.php') {
    if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario']) || !isset($_SESSION['usuario']['id_perfil'])) {
        header("Location: $redirecionarPara");
        exit;
    }
}