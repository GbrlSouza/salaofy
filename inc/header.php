<?php
$id_perfil_logado = $_SESSION['usuario']['id_perfil'] ?? 0; 
$nome_usuario = $_SESSION['usuario']['nome_completo'] ?? 'Visitante';
$pageTitle = $pageTitle ?? "Salaofy | Gestão Condominial"; 

$menuLinks = [];

switch ($id_perfil_logado) {
    case 1: 
        $menuLinks = [
            'Dashboard' => ['href' => 'admin.php#overview', 'icon' => 'bi-house-door'],
            'Gestão de Usuários' => ['href' => 'admin.php#usuarios', 'icon' => 'bi-people'],
            'Configurações Globais' => ['href' => 'admin.php#config', 'icon' => 'bi-gear'],
            'Salões (Todos)' => ['href' => 'admin.php#saloes', 'icon' => 'bi-building'],
        ];

        $perfilNome = "Administrador";
        break;

    case 2: 
        $menuLinks = [
            'Dashboard' => ['href' => 'sindico.php#overview', 'icon' => 'bi-house-door'],
            'Aprovar Reservas' => ['href' => 'sindico.php#aprovar', 'icon' => 'bi-check-square'],
            'Lista de Moradores' => ['href' => 'sindico.php#moradores', 'icon' => 'bi-people'],
            'Gestão de Salões' => ['href' => 'sindico.php#saloes', 'icon' => 'bi-building'],
        ];

        $perfilNome = "Síndico";
        break;

    case 3: 
        $menuLinks = [
            'Home' => ['href' => 'home.php', 'icon' => 'bi-house-door'],
            'Minhas Reservas' => ['href' => 'morador.php#minhas_reservas', 'icon' => 'bi-calendar-check'],
            'Novo Agendamento' => ['href' => 'morador.php#agendar', 'icon' => 'bi-plus-square'],
        ];

        $perfilNome = "Morador";
        break;
    
    default:
        $menuLinks = [
            'Home' => ['href' => 'index.php', 'icon' => 'bi-house-door'],
            'Login' => ['href' => 'login.php', 'icon' => 'bi-box-arrow-in-right'],
            'Cadastrar' => ['href' => 'cadastrar.php', 'icon' => 'bi-person-plus'],
        ];

        $perfilNome = "Visitante";
        break;
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .vh-100-minus-nav {
            min-height: calc(100vh - 56px); 
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="bi bi-building-fill me-2"></i> Salaofy</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php
                foreach ($menuLinks as $label => $prop) {
                    $active = (basename($_SERVER['PHP_SELF']) == basename($prop['href'])) ? 'active' : '';
                    
                    echo '<li class="nav-item">';
                    echo '<a class="nav-link ' . $active . '" href="' . $prop['href'] . '">';
                    echo '<i class="bi ' . $prop['icon'] . '"></i> ' . $label;
                    echo '</a>';
                    echo '</li>';
                }
                ?>
            </ul>
            
            <?php if ($id_perfil_logado > 0): ?>
            <span class="navbar-text me-3">Olá, <?php echo htmlspecialchars($nome_usuario); ?>!</span>
            <div class="dropdown">
                <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle fs-4"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><span class="dropdown-item">Acesso: <?php echo $perfilNome; ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">Meu Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php">Sair</a></li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
</nav>