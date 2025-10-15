<?php
require_once './inc/bootstrap.php'; 
require './func/usuarios.php'; 

$mensagem_erro = '';
$cpf_digitado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    $cpf_digitado = htmlspecialchars($cpf); 

    if (!empty($cpf) && !empty($senha) && isset($conexao)) {
        $usuarioManager = new Usuario($conexao);
        $dados_usuario = $usuarioManager -> autenticar($cpf, $senha);
        
        if ($dados_usuario) {
            $_SESSION['usuario'] = $dados_usuario;
            header('Location: home.php');
            exit;
            
        } else { $mensagem_erro = 'CPF ou senha incorretos.'; }
    } else { $mensagem_erro = 'Por favor, preencha todos os campos.'; }
}

require './inc/header.php'; 
?>

<div class="container d-flex justify-content-center align-items-center vh-100-minus-nav">
    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
        <div class="card-header bg-white text-center border-0">
            <h2 class="mb-0 text-primary"><i class="bi bi-building-fill me-2"></i> Salaofy Login</h2>
            <p class="text-muted small">Acesso exclusivo para usuários cadastrados</p>
        </div>
        <div class="card-body">
            
            <?php if ($mensagem_erro): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($mensagem_erro); ?>
            </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                
                <div class="mb-3">
                    <label for="cpf" class="form-label">CPF</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="number" class="form-control" id="cpf" name="cpf" placeholder="Seu CPF (apenas números)" required maxlength="11" value="<?php echo $cpf_digitado; ?>">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="senha" class="form-label">Senha</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="senha" name="senha" placeholder="Sua senha" required>
                    </div>
                </div>
                
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Entrar
                    </button>
                </div>
                
                <div class="text-center mt-3">
                    <a href="#" class="text-muted small">Esqueceu sua senha?</a>
                </div>
            </form>

        </div>
        <div class="card-footer text-center bg-white border-0">
            <p class="small mb-0">Novo por aqui? <a href="cadastrar.php">Solicitar Cadastro</a></p>
        </div>
    </div>
</div>

<?php require './inc/footer.php'; ?>