<?php
global $conexao;

$id_usuario = $_SESSION['usuario']['id_usuario'];
$nome_display = $_SESSION['usuario']['nome_completo'];

require_once 'func/agendamento.php';
require_once 'func/morador_condominio.php';
require_once 'func/saloes.php';

$agendamentoManager = new Agendamento($conexao);
$vinculoManager = new MoradorCondominio($conexao);
$salaoManager = new Salao($conexao);

$condominios = $vinculoManager->buscarCondominiosDoMorador($id_usuario);
$id_condominio_morador = $condominios[0]['id_condominio'] ?? null;
$nome_condominio = $condominios[0]['nome_condominio'] ?? 'N/D';
$minhasReservas = $agendamentoManager->listarAgendamentosMorador($id_usuario);
$proximaReserva = array_filter($minhasReservas, function($res) { return $res['status'] == 'Confirmada' && strtotime($res['data_evento']) >= time(); });

usort($proximaReserva, function($a, $b) { return strtotime($a['data_evento']) - strtotime($b['data_evento']); });

$proximaReserva = $proximaReserva[0] ?? null;

$saloesDisponiveis = $id_condominio_morador ? $salaoManager->buscarSaloesPorCondominio($id_condominio_morador) : [];
?>

<section id="overview" class="mb-5 p-4 bg-white rounded shadow-sm">
    <h1 class="mb-4 text-primary"><i class="bi bi-house-door-fill me-2"></i> Dashboard</h1>
    <p class="lead">Bem-vindo, **<?php echo htmlspecialchars($nome_display); ?>**! Você está no condomínio: **<?php echo htmlspecialchars($nome_condominio); ?>**.</p>

    <div class="row g-4">
        <div class="col-lg-4 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Próxima Reserva</h5>
                    <?php if ($proximaReserva): ?>
                        <p class="card-text fs-3 fw-bold"><?php echo htmlspecialchars($proximaReserva['nome_salao']); ?></p>
                        <p class="card-text"><?php echo date('d/m/Y', strtotime($proximaReserva['data_evento'])); ?> (Confirmada)</p>
                    <?php else: ?>
                        <p class="card-text fs-3 fw-bold">Nenhuma</p>
                        <p class="card-text">Sem reservas futuras confirmadas.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </div>
</section>

<section id="reservas" class="mb-5 p-4 bg-white rounded shadow">
    <h2 class="mb-4 text-primary"><i class="bi bi-calendar-check me-2"></i> Minhas Reservas</h2>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Salão</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Valor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($minhasReservas)): ?>
                    <?php foreach ($minhasReservas as $reserva): 
                        $badgeClass = match($reserva['status']) {
                            'Confirmada' => 'bg-success',
                            'Pendente' => 'bg-warning text-dark',
                            default => 'bg-danger'
                        };
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reserva['nome_salao']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($reserva['data_evento'])); ?></td>
                        <td><span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($reserva['status']); ?></span></td>
                        <td>R$ <?php echo number_format($reserva['valor_total'], 2, ',', '.'); ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i> Detalhes</button>
                            <?php if ($reserva['status'] == 'Pendente' || $reserva['status'] == 'Confirmada'): ?>
                                <a href="cancelar_reserva.php?id=<?php echo $reserva['id_agendamento']; ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle"></i> Cancelar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">Você não tem reservas registradas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section id="agendar" class="mb-5 p-4 bg-light rounded shadow-sm">
    <h2 class="mb-4 text-primary"><i class="bi bi-plus-square me-2"></i> Agendar Novo Salão</h2>
    
    <form action="processa_agendamento.php" method="POST">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="salaoSelect" class="form-label">Escolha o Salão</label>
                <select id="salaoSelect" name="id_salao" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($saloesDisponiveis as $salao): ?>
                        <option value="<?php echo $salao['id_salao']; ?>">
                            <?php echo htmlspecialchars($salao['nome_salao']); ?> (R$ <?php echo number_format($salao['preco_locacao_base'], 2, ',', '.'); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="hidden" name="id_morador" value="<?php echo $id_usuario; ?>">
        </div>
    </form>
</section>
