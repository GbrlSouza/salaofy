<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

$pageTitle = "Saindo | Salaofy"; 

require './inc/header.php'; 
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="text-center">
        <h1 class="text-success display-4 mb-4"><i class="bi bi-box-arrow-right"></i> Saiu do Sistema</h1>
        <p class="lead">Sua sessão foi encerrada com segurança. Você será redirecionado para a página de login em instantes.</p>
        <p class="mt-4">Se não for redirecionado automaticamente, clique no link abaixo:</p>
        <a href="login.php" class="btn btn-primary btn-lg"><i class="bi bi-person-fill"></i> Ir para o Login</a>
    </div>
</div>

<?php require './inc/footer.php'; ?>

<script> setTimeout(() => { window.location.href = 'login.php'; }, 3000); </script>
