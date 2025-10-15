<?php
$id_usuario = $_SESSION['usuario']['id_usuario'];
$nome_sindico = $_SESSION['usuario']['nome_completo'];

require 'func/morador_condominio.php';

require_once 'func/agendamento.php';
require_once 'func/condominio.php';
require_once 'func/saloes.php';

$vinculoManager = new MoradorCondominio($conexao); 
$agendamentoManager = new Agendamento($conexao);
$condominioManager = new Condominio($conexao);
$salaoManager = new Salao($conexao);

$condominioSindico = $condominioManager -> buscarPorSindico($id_usuario);
$id_condominio = $condominioSindico['id_condominio'] ?? null;
$nome_condominio = $condominioSindico['nome_condominio'] ?? 'N/D';

$reservasPendentes = [ ['id_agendamento' => 10, 'nome_salao' => 'Salão de Festas', 'data_evento' => '2025-11-20', 'morador_nome' => 'Maria Silva', 'valor_total' => 400.00] ];

$condominioSindico = $condominioManager -> buscarPorSindico($id_usuario);
$id_condominio = $condominioSindico['id_condominio'] ?? null;
$nome_condominio = $condominioSindico['nome_condominio'] ?? 'N/D';

$totalMoradores = 0;
$totalSaloes = 0;
$reservasPendentes = [];
$saloesCondominio = [];

if ($id_condominio) {
    $totalSaloes = $salaoManager -> contarSaloesPorCondominio($id_condominio);
    $saloesCondominio = $salaoManager -> buscarSaloesPorCondominio($id_condominio);
    $totalMoradores = $vinculoManager -> contarMoradoresPorCondominio($id_condominio);
    $reservasPendentes = $agendamentoManager -> listarPendentesPorCondominio($id_condominio);
}
?>

<section id="overview" class="mb-5 p-4 bg-success text-white rounded shadow-sm">
    <h1 class="mb-4"><i class="bi bi-person-badge me-2"></i> Painel do Síndico</h1>
    <p class="lead">Bem-vindo, Síndico <?php echo htmlspecialchars($nome_sindico); ?>! Gestão do condomínio: <?php echo htmlspecialchars($nome_condominio); ?></p>
    
    <div class="row g-4 mt-3">
        <div class="col-lg-4 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Moradores Vinculados</h5>
                    <p class="card-text fs-3 fw-bold"><?php echo $totalMoradores; ?></p> <p class="card-text">Total de usuários vinculados.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">Salões de Festa</h5>
                    <p class="card-text fs-3 fw-bold"><?php echo $totalSaloes; ?></p> <p class="card-text">Total de áreas comuns disponíveis.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Reservas Pendentes</h5>
                    <p class="card-text fs-3 fw-bold"><?php echo count($reservasPendentes); ?></p> <p class="card-text">Aguardando sua aprovação.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="aprovar" class="mb-5 p-4 bg-white rounded shadow">
    <h2 class="mb-4 text-warning"><i class="bi bi-check-square me-2"></i> Aprovação de Reservas Pendentes</h2>
    <div class="table-responsive">
        <table class="table table-hover">
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
    <h2 class="mb-4 text-success"><i class="bi bi-geo-alt me-2"></i> Gestão de Salões de Festa</h2>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-success">
                <tr>
                    <th>Salão</th>
                    <th>Preço Base</th>
                    <th>Capacidade Máxima</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($saloesCondominio)): ?>
                    <?php foreach ($saloesCondominio as $salao): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($salao['nome_salao']); ?></td>
                        <td>R$ <?php echo number_format($salao['preco_locacao_base'] ?? 0, 2, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($salao['capacidade_maxima'] ?? 'N/D'); ?></td>
                        <td>
                            <a href="editar_salao.php?id=<?php echo $salao['id_salao']; ?>" class="btn btn-sm btn-outline-success me-2">Editar</a>
                            <a href="deletar_salao.php?id=<?php echo $salao['id_salao']; ?>" class="btn btn-sm btn-outline-danger">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">Nenhum salão cadastrado para este condomínio.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
