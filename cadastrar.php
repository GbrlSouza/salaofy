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
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_NUMBER_INT);
    $celular = filter_input(INPUT_POST, 'celular', FILTER_SANITIZE_NUMBER_INT);
    $cep = filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_NUMBER_INT);
    $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);
    $confirma_senha = filter_input(INPUT_POST, 'confirma_senha', FILTER_SANITIZE_STRING);
    $nome_social = filter_input(INPUT_POST, 'nome_social', FILTER_SANITIZE_STRING);

    if (empty($nome) || empty($cpf) || empty($celular) || empty($cep) || empty($senha) || empty($confirma_senha)) { 
        $mensagem_erro = 'Por favor, preencha todos os campos obrigatórios.'; 
    } elseif ($senha !== $confirma_senha) { 
        $mensagem_erro = 'A senha e a confirmação de senha não coincidem.'; 
    } elseif (strlen($cpf) !== 11) { 
        $mensagem_erro = 'O CPF deve ter 11 dígitos.'; 
    } elseif ($usuarioManager->cpfJaExiste($cpf)) { 
        $mensagem_erro = 'Este CPF já está cadastrado em nosso sistema.'; 
    } else {
        $condominio = $condominioManager->buscarCondominioPorCep($cep);
        
        if (!$condominio) { 
            $mensagem_erro = 'Condomínio não encontrado. Verifique o CEP ou contate a administração.';
        } else {
            $id_novo_usuario = $usuarioManager->registrarUsuario($cpf, $senha, $nome, $celular, $id_perfil, $nome_social);

            if ($id_novo_usuario) {
                $id_condominio = $condominio['id_condominio'];
                $vinculo_sucesso = $vinculoManager->adicionarVinculo($id_novo_usuario, $id_condominio);

                if ($vinculo_sucesso) {
                    $mensagem_sucesso = "Cadastro concluído! Você já pode fazer login.";
                    $_POST = [];
                } else { 
                    $mensagem_erro = 'Cadastro realizado, mas falha na vinculação ao condomínio. Contate o administrador.';
                }
            } else {
                $mensagem_erro = 'Falha ao registrar usuário no sistema. Tente novamente.';
            }
        }
    }
}

require './inc/header.php'; 
?>

<div class="container d-flex justify-content-center align-items-center vh-100-minus-nav">
    <div class="card shadow-lg p-4" style="max-width: 600px; width: 100%;">
        <div class="card-header bg-white text-center border-0">
            <h2 class="mb-0 text-success"><i class="bi bi-person-plus-fill me-2"></i> Solicitar Cadastro</h2>
            <p class="text-muted small">Crie sua conta para acessar os recursos de agendamento.</p>
        </div>
        <div class="card-body">
            
            <?php if ($mensagem_erro): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($mensagem_erro); ?>
            </div>
            <?php endif; ?>

            <?php if ($mensagem_sucesso): ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($mensagem_sucesso); ?>
            </div>
            <?php endif; ?>

            <form action="cadastrar.php" method="POST">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="nome_social" class="form-label">Nome Social/Apelido (Opcional)</label>
                    <input type="text" class="form-control" id="nome_social" name="nome_social" value="<?php echo htmlspecialchars($_POST['nome_social'] ?? ''); ?>">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cpf" class="form-label">CPF <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="cpf" name="cpf" maxlength="11" value="<?php echo htmlspecialchars($_POST['cpf'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="celular" class="form-label">Celular/Contato <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="celular" name="celular" maxlength="15" value="<?php echo htmlspecialchars($_POST['celular'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="cep" class="form-label">CEP do Condomínio <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="cep" name="cep" maxlength="8" value="<?php echo htmlspecialchars($_POST['cep'] ?? ''); ?>" required>
                    <div class="form-text">Usado para vincular você ao condomínio correto.</div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="senha" class="form-label">Senha <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="confirma_senha" class="form-label">Confirmar Senha <span class="text-danger">*</span></label>
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