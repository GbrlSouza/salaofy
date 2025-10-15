<?php
global $conexao;

$id_usuario = $_SESSION['usuario']['id_usuario'];
$nome_sindico = $_SESSION['usuario']['nome_completo'];

require_once 'func/agendamento.php';
require_once 'func/condominio.php';
require_once 'func/saloes.php';

$agendamentoManager = new Agendamento($conexao);
$condominioManager = new Condominio($conexao);
$salaoManager = new Salao($conexao);

$condominioSindico = $condominioManager->buscarPorSindico($id_usuario);
$id_condominio = $condominioSindico['id_condominio'] ?? null;
$nome_condominio = $condominioSindico['nome_condominio'] ?? 'N/D';


$reservasPendentes = [
    ['id_agendamento' => 10, 'nome_salao' => 'Salão de Festas', 'data_evento' => '2025-11-20', 'morador_nome' => 'Maria Silva', 'valor_total' => 400.00]
];
?>

<section id="overview" class="mb-5 p-4 bg-success text-white rounded shadow-sm">
    <h1 class="mb-4"><i class="bi bi-person-badge me-2"></i> Painel do Síndico</h1>
    <p class="lead">Bem-vindo, Síndico **<?php echo htmlspecialchars($nome_sindico); ?>** do condomínio **<?php echo htmlspecialchars($nome_condominio); ?>**.</p>
    
    <div class="row g-4">
        <div class="col-lg-4 col-md-6">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Reservas Pendentes</h5>
                    <p class="card-text fs-3 fw-bold"><?php echo count($reservasPendentes); ?></p>
                    <p class="card-text">Aguardando sua aprovação.</p>
                </div>
            </div>
        </div>
        </div>
</section>

<section id="aprovar" class="mb-5 p-4 bg-white rounded shadow">
    <h2 class="mb-4 text-success"><i class="bi bi-check-square me-2"></i> Solicitações de Reserva</h2>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Morador</th>
                    <th>Salão</th>
                    <th>Data</th>
                    <th>Valor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reservasPendentes)): ?>
                    <?php foreach ($reservasPendentes as $reserva): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reserva['morador_nome']); ?></td>
                        <td><?php echo htmlspecialchars($reserva['nome_salao']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($reserva['data_evento'])); ?></td>
                        <td>R$ <?php echo number_format($reserva['valor_total'], 2, ',', '.'); ?></td>
                        <td>
                            <a href="processa_status.php?id=<?php echo $reserva['id_agendamento']; ?>&status=Confirmada" class="btn btn-sm btn-success">Aprovar</a>
                            <a href="processa_status.php?id=<?php echo $reserva['id_agendamento']; ?>&status=Rejeitada" class="btn btn-sm btn-danger">Rejeitar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">Nenhuma reserva pendente no momento.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section id="saloes" class="mb-5 p-4 bg-light rounded shadow">
    <h2 class="mb-4 text-success"><i class="bi bi-geo-alt me-2"></i> Meus Salões</h2>
</section>