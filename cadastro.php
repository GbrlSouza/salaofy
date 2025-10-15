<?php
require_once './func/perfis.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once './func/usuarios.php';
require_once './func/condominio.php';
require_once './func/morador_condominio.php';

$mensagem_sucesso = '';
$mensagem_erro = '';

global $conexao; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_NUMBER_INT);
    $celular = filter_input(INPUT_POST, 'celular', FILTER_SANITIZE_NUMBER_INT);
    $cep = filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_NUMBER_INT);
    $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);
    $confirma_senha = filter_input(INPUT_POST, 'confirma_senha', FILTER_SANITIZE_STRING);

    $usuarioManager = new Usuario($conexao);
    $condominioManager = new Condominio($conexao);
    $vinculoManager = new MoradorCondominio($conexao);

    if (empty($nome) || empty($cpf) || empty($celular) || empty($cep) || empty($senha) || empty($confirma_senha)) { $mensagem_erro = 'Por favor, preencha todos os campos obrigatórios.'; }
    elseif ($senha !== $confirma_senha) { $mensagem_erro = 'A senha e a confirmação de senha não coincidem.'; }
    elseif (strlen($cpf) !== 11) { $mensagem_erro = 'O CPF deve ter 11 dígitos.'; }
    elseif ($usuarioManager -> cpfJaExiste($cpf)) { $mensagem_erro = 'Este CPF já está cadastrado em nosso sistema.'; }
    else {
        $condominio = $condominioManager -> buscarCondominioPorCep($cep);
        
        if (!$condominio) { $mensagem_erro = 'Condomínio não encontrado. Verifique o CEP ou contate a administração.';}
        else {
            $id_novo_usuario = $usuarioManager -> registrarUsuario($cpf, $senha, $nome, $celular, $id_perfil);

            if ($id_novo_usuario) {
                $id_condominio = $condominio['id_condominio'];
                $vinculo_sucesso = $vinculoManager -> adicionarVinculo($id_novo_usuario, $id_condominio);

                if ($vinculo_sucesso) {
                    $mensagem_sucesso = "Cadastro concluído! Você já pode fazer login.";
                    $_POST = [];
                } else { $mensagem_erro = 'Cadastro realizado, mas falha na vinculação ao condomínio. Contate a administração.'; }
            } else { $mensagem_erro = 'Erro desconhecido ao registrar o usuário no banco de dados.'; }
        }
    }
}
