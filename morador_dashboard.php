<?php
$id_usuario = $_SESSION['usuario']['id_usuario'];
$nome_display = $_SESSION['usuario']['nome_completo'];

require_once 'func/agendamento.php';
require_once 'func/morador_condominio.php';
require_once 'func/saloes.php';

$agendamentoManager = new Agendamento($conexao);
$vinculoManager = new MoradorCondominio($conexao);
$salaoManager = new Salao($conexao);

$condominios = $vinculoManager -> buscarCondominiosDoMorador($id_usuario);
$id_condominio_morador = $condominios[0]['id_condominio'] ?? null;
$nome_condominio = $condominios[0]['nome_condominio'] ?? 'N/D';
$minhasReservas = $agendamentoManager -> listarAgendamentosMorador($id_usuario);
$proximaReserva = array_filter($minhasReservas, function($res) { return $res['status'] == 'Confirmada' && strtotime($res['data_evento']) >= time(); });

usort($proximaReserva, function($a, $b) { return strtotime($a['data_evento']) - strtotime($b['data_evento']); });

$proximaReserva = $proximaReserva[0] ?? null;

$saloesDisponiveis = $id_condominio_morador ? $salaoManager -> buscarSaloesPorCondominio($id_condominio_morador) : [];
?>

<section id="overview" class="mb-5 p-4 bg-primary text-white rounded shadow-sm">
    <h1 class="mb-4"><i class="bi bi-house-door me-2"></i> Painel do Morador</h1>
    <p class="lead">Bem-vindo, **<?php echo htmlspecialchars($nome_display); ?>**! Seu condomínio atual: **<?php echo htmlspecialchars($nome_condominio); ?>**</p>
    
    <div class="row g-4">
        <div class="col-lg-4 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Minhas Reservas Ativas</h5>
                    <p class="card-text fs-3 fw-bold"><?php echo count($minhasReservas); ?></p>
                    <p class="card-text">Total de agendamentos solicitados.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Próxima Reserva Confirmada</h5>
                    <?php if ($proximaReserva): ?>
                        <p class="card-text fs-3 fw-bold">
                            <?php echo date('d/m/Y', strtotime($proximaReserva['data_evento'])); ?> - <?php echo htmlspecialchars($proximaReserva['nome_salao']); ?>
                        </p>
                        <p class="card-text">Status: <?php echo htmlspecialchars($proximaReserva['status']); ?></p>
                    <?php else: ?>
                        <p class="card-text fs-3 fw-bold">Nenhuma reserva futura.</p>
                        <p class="card-text">Que tal agendar um evento?</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="minhas_reservas" class="mb-5 p-4 bg-white rounded shadow">
    <h2 class="mb-4 text-success"><i class="bi bi-calendar-check me-2"></i> Histórico de Reservas</h2>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Salão</th>
                    <th>Data do Evento</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($minhasReservas)): ?>
                    <?php foreach ($minhasReservas as $reserva): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reserva['nome_salao']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($reserva['data_evento'])); ?></td>
                        <td>R$ <?php echo number_format($reserva['valor_total'], 2, ',', '.'); ?></td>
                        <td>
                            <span class="badge 
                                <?php echo $reserva['status'] == 'Confirmada' ? 'bg-success' : ''; ?>
                                <?php echo $reserva['status'] == 'Pendente' ? 'bg-warning text-dark' : ''; ?>
                                <?php echo in_array($reserva['status'], ['Rejeitada', 'Cancelada']) ? 'bg-danger' : ''; ?>
                            ">
                                <?php echo htmlspecialchars($reserva['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($reserva['status'] == 'Pendente' || $reserva['status'] == 'Confirmada'): ?>
                            <a href="processa_cancelamento.php?id=<?php echo $reserva['id_agendamento']; ?>" class="btn btn-sm btn-danger"><i class="bi bi-x-circle"></i> Cancelar</a>
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
            <div class="col-md-6">
                <label for="dataEvento" class="form-label">Data do Evento</label>
                <input type="date" class="form-control" id="dataEvento" name="data_evento" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="col-12">
                <label for="detalhes" class="form-label">Detalhes do Evento (Opcional)</label>
                <textarea class="form-control" id="detalhes" name="detalhes" rows="2" maxlength="255"></textarea>
            </div>
            <input type="hidden" name="valor_base" id="valorBaseHidden">
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-calendar-plus me-2"></i> Solicitar Reserva</button>
            </div>
        </div>
    </form>
</section>