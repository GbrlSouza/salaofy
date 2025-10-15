<?php
global $conexao;

$nome_admin = $_SESSION['usuario']['nome_completo'];

require 'func/usuario.php';
require 'func/condominio.php';

$usuarioManager = new Usuario($conexao);
$condominioManager = new Condominio($conexao);

$totalUsuarios = 50;
$totalCondominios = 5;
$novosCadastros = 3;
?>

<section id="overview" class="mb-5 p-4 bg-primary text-white rounded shadow-sm">
    <h1 class="mb-4"><i class="bi bi-speedometer me-2"></i> Painel do Administrador</h1>
    <p class="lead">Bem-vindo, **<?php echo htmlspecialchars($nome_admin); ?>**! Visão geral global do sistema.</p>
    
    <div class="row g-4">
        <div class="col-lg-4 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Total de Usuários</h5>
                    <p class="card-text fs-3 fw-bold"><?php echo $totalUsuarios; ?></p>
                    <p class="card-text">Entre moradores, síndicos e admins.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">Condomínios Ativos</h5>
                    <p class="card-text fs-3 fw-bold"><?php echo $totalCondominios; ?></p>
                    <p class="card-text">Total de condomínios cadastrados.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Novos Cadastros</h5>
                    <p class="card-text fs-3 fw-bold"><?php echo $novosCadastros; ?></p>
                    <p class="card-text">Usuários aguardando aprovação.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="usuarios" class="mb-5 p-4 bg-white rounded shadow">
    <h2 class="mb-4 text-primary"><i class="bi bi-people me-2"></i> Gestão de Usuários</h2>
    </section>

<section id="config" class="mb-5 p-4 bg-light rounded shadow">
    <h2 class="mb-4 text-primary"><i class="bi bi-gear me-2"></i> Configurações Globais</h2>
</section>