<?php
require_once __DIR__ . '/inc/bootstrap.php'; 

require_once __DIR__ . '/func/usuarios.php';
require_once __DIR__ . '/func/condominio.php';
require_once __DIR__ . '/func/morador_condominio.php';

$mensagem_sucesso = '';
$mensagem_erro = '';
$id_perfil = 3;

$usuarioManager = new Usuario($conexao);
$condominioManager = new Condominio($conexao);
$vinculoManager = new MoradorCondominio($conexao);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_NUMBER_INT);
    $celular = filter_input(INPUT_POST, 'celular', FILTER_SANITIZE_NUMBER_INT);
    $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirma_senha = filter_input(INPUT_POST, 'confirma_senha', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $id_perfil = filter_input(INPUT_POST, 'perfil', FILTER_VALIDATE_INT);

    $nome_condominio = filter_input(INPUT_POST, 'nome_condominio', FILTER_SANITIZE_FULL_SPECIAL_CHARS); 
    $cep = filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_NUMBER_INT);

    $usuarioManager = new Usuario($conexao);
    $condominioManager = new Condominio($conexao);
    $vinculoManager = new MoradorCondominio($conexao);
    
    if (empty($nome) || empty($cpf) || empty($celular) || empty($id_perfil) || empty($senha) || empty($confirma_senha)) {
        $mensagem_erro = 'Por favor, preencha todos os campos obrigatórios.';
    } elseif ($senha !== $confirma_senha) {
        $mensagem_erro = 'A senha e a confirmação de senha não coincidem.';
    } elseif (strlen($cpf) !== 11) {
        $mensagem_erro = 'O CPF deve ter 11 dígitos.';
    } elseif ($usuarioManager->cpfJaExiste($cpf)) {
        $mensagem_erro = 'Este CPF já está cadastrado em nosso sistema.';
    } else {
        $nome_condominio = filter_input(INPUT_POST, 'nome_condominio', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $cep = filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_NUMBER_INT); 

        if ($id_perfil == 2) {
            if (empty($nome_condominio) || empty($cep) || strlen($cep) !== 8) {
                $mensagem_erro = 'Por favor, preencha o Nome do Condomínio e um CEP válido (8 dígitos) para criá-lo.';
            } else {
                $conexao->beginTransaction();
                
                try {
                    $id_novo_sindico = $usuarioManager->registrarUsuario($cpf, $senha, $nome, $celular, 2); // Perfil ID 2 (Síndico)

                    if ($id_novo_sindico) {
                        $id_novo_condominio = $condominioManager->criarCondominioComSindico($nome_condominio, $cep, $id_novo_sindico);

                        if ($id_novo_condominio) {
                            $vinculo_sucesso = $vinculoManager->adicionarVinculo($id_novo_sindico, $id_novo_condominio);

                            if ($vinculo_sucesso) {
                                $conexao->commit();
                                $mensagem_sucesso = "Condomínio **" . htmlspecialchars($nome_condominio) . "** criado com sucesso! Você já pode fazer login como Síndico.";
                                $_POST = [];
                            } else {
                                $conexao->rollBack();
                                $mensagem_erro = 'Erro: Falha na vinculação do síndico como morador.';
                            }
                        } else {
                            $conexao->rollBack();
                            $mensagem_erro = 'Erro: Falha ao criar o Condomínio.';
                        }
                    } else {
                        $conexao->rollBack();
                        $mensagem_erro = 'Erro: Falha ao registrar o usuário Síndico.';
                    }
                } catch (Exception $e) {
                    $conexao->rollBack();
                    error_log("Erro durante o cadastro de Síndico: " . $e->getMessage());
                    $mensagem_erro = 'Erro interno do sistema. Tente novamente.';
                }
            }
        
        } elseif ($id_perfil == 3) {
            if (empty($cep) || strlen($cep) !== 8) { $mensagem_erro = 'O CEP é obrigatório e deve ter 8 dígitos para Morador.'; }
            else {
                $condominio = $condominioManager->buscarCondominioPorCep($cep);
                
                if (!$condominio) { 
                    $mensagem_erro = 'Condomínio não encontrado. Verifique o CEP ou contate a administração do seu condomínio.';
                } else {
                    $id_perfil_morador = 3;
                    $id_novo_usuario = $usuarioManager->registrarUsuario($cpf, $senha, $nome, $celular, $id_perfil_morador);

                    if ($id_novo_usuario) {
                        $id_condominio = $condominio['id_condominio'];
                        $vinculo_sucesso = $vinculoManager->adicionarVinculo($id_novo_usuario, $id_condominio);

                        if ($vinculo_sucesso) {
                            $mensagem_sucesso = "Cadastro concluído! Você já pode fazer login como Morador do **" . htmlspecialchars($condominio['nome_condominio']) . "**.";
                            $_POST = [];
                        } else {  $mensagem_erro = 'Cadastro realizado, mas falha na vinculação ao condomínio. Contate o suporte.'; }
                    } else { $mensagem_erro = 'Erro ao registrar o usuário Morador.'; }
                }
            }
        } else { $mensagem_erro = 'Selecione um perfil válido (Síndico ou Morador).'; }
    }
}

require './inc/header.php'; 
?>

<div class="container d-flex justify-content-center align-items-center vh-100-minus-nav">
    <div class="card shadow-lg p-4" style="max-width: 600px; width: 100%;">
        <div class="card-header text-center bg-white border-0">
            <h2 class="text-success"><i class="bi bi-person-plus-fill me-2"></i> Solicitar Cadastro</h2>
            <p class="text-muted">Preencha seus dados para começar a usar o SalaoFy.</p>
        </div>
        <div class="card-body pt-0">
            
            <?php 
            // Exibir mensagens de erro ou sucesso
            if (!empty($mensagem_erro)) { echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($mensagem_erro) . '</div>'; }
            if (!empty($mensagem_sucesso)) { echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($mensagem_sucesso) . '</div>'; }
            ?>

            <form action="cadastrar.php" method="POST">
                
                <div class="mb-3">
                    <label for="perfil" class="form-label">Eu serei:</label>
                    <select id="perfil" name="perfil" class="form-select" required onchange="ajustarCampos()">
                        <option value="">Selecione...</option>
                        <option value="2" <?php echo (($_POST['perfil'] ?? '') == 2) ? 'selected' : ''; ?>>Síndico (Criar Condomínio)</option>
                        <option value="3" <?php echo (($_POST['perfil'] ?? '') == 3) ? 'selected' : ''; ?>>Morador (Vincular a Condomínio)</option>
                    </select>
                </div>
                
                <hr>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nome" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="cpf" class="form-label">CPF (Apenas números)</label>
                        <input type="text" class="form-control" id="cpf" name="cpf" maxlength="11" value="<?php echo htmlspecialchars($_POST['cpf'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="celular" class="form-label">Contato Celular (Ex: 11999998888)</label>
                    <input type="text" class="form-control" id="celular" name="celular" value="<?php echo htmlspecialchars($_POST['celular'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="cep" class="form-label">CEP do Condomínio</label>
                    <input type="text" class="form-control" id="cep" name="cep" maxlength="8" value="<?php echo htmlspecialchars($_POST['cep'] ?? ''); ?>" required>
                    <div class="form-text" id="help_text_cep"></div> 
                </div>


                <div id="campo_condominio" class="mb-3" style="display: <?php echo (($_POST['perfil'] ?? '') == 2) ? 'block' : 'none'; ?>;">
                    <label for="nome_condominio" class="form-label">Nome do Condomínio a Ser Criado</label>
                    <input type="text" class="form-control" id="nome_condominio" name="nome_condominio" value="<?php echo htmlspecialchars($_POST['nome_condominio'] ?? ''); ?>">
                </div>

                <hr>

                <div id="campo_cep" class="mb-3" style="display: <?php echo (($_POST['perfil'] ?? '') == 3) ? 'block' : 'none'; ?>;">
                    <label for="cep" class="form-label">CEP do Condomínio</label>
                    <input type="text" class="form-control" id="cep" name="cep" maxlength="8" value="<?php echo htmlspecialchars($_POST['cep'] ?? ''); ?>">
                    <div class="form-text">Morador: Usado para vincular você ao condomínio.</div>
                </div>
                
                <hr>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="confirma_senha" class="form-label">Confirmar Senha</label>
                        <input type="password" class="form-control" id="confirma_senha" name="confirma_senha" required>
                    </div>
                </div>
                
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-person-plus me-2"></i> Solicitar Cadastro</button>
                </div>
                
                <div class="text-center mt-3">
                    Já tem conta? <a href="login.php">Fazer Login</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require './inc/footer.php'; ?>

<script>
    function ajustarCampos() {
        const perfil = document.getElementById('perfil').value;
        const campoCondominio = document.getElementById('campo_condominio');
        const inputNomeCondominio = document.getElementById('nome_condominio');
        const helpTextCep = document.getElementById('help_text_cep');

        if (perfil == '2') {
            campoCondominio.style.display = 'block';
            inputNomeCondominio.required = true;
            helpTextCep.innerHTML = 'Síndico: Este CEP será usado para o **novo** condomínio que você está criando.';
        } else if (perfil == '3') {
            campoCondominio.style.display = 'none';
            inputNomeCondominio.required = false;
            helpTextCep.innerHTML = 'Morador: Usado para buscar o seu condomínio e fazer o vínculo.';
        } else {
            campoCondominio.style.display = 'none';
            inputNomeCondominio.required = false;
            helpTextCep.innerHTML = '';
        }
    }

    window.onload = ajustarCampos;
</script>
