<?php
$base_dir = __DIR__ . '/../';

if (file_exists($base_dir . './../db/conexao.php')) { $conexao = require_once $base_dir . './../db/conexao.php'; } 
elseif (file_exists('./db/conexao.php')) { $conexao = require_once './db/conexao.php'; }
else { die("Erro fatal: Arquivo de conexão 'conexao.php' não encontrado em nenhum caminho esperado."); }

if (!$conexao instanceof PDO) { die("Erro fatal: A conexão com o banco de dados falhou durante a inicialização."); }

class Usuario {
    private $pdo;

    public function __construct(PDO $pdo) { $this -> pdo = $pdo; }

    public function registrarUsuario(string $cpf, string $senha_pura, string $nome_completo, string $contato_celular, int $id_perfil, ?string $nome_social = null): int|bool {
        $senha_hash = password_hash($senha_pura, PASSWORD_BCRYPT); 

        $sql = "INSERT INTO usuarios (cpf, senha_hash, nome_completo, nome_social_apelido, contato_celular, id_perfil) 
                VALUES (:cpf, :hash, :nome_completo, :nome_social, :celular, :id_perfil)";
        
        try {
            $stmt = $this -> pdo -> prepare($sql);
            
            $stmt -> bindParam(':cpf', $cpf);
            $stmt -> bindParam(':hash', $senha_hash);
            $stmt -> bindParam(':nome_completo', $nome_completo);
            $stmt -> bindParam(':nome_social', $nome_social);
            $stmt -> bindParam(':celular', $contato_celular);
            $stmt -> bindParam(':id_perfil', $id_perfil, PDO::PARAM_INT);
            
            if ($stmt -> execute()) { return (int)$this -> pdo -> lastInsertId(); }

            return false;
        } catch (PDOException $e) { return false; }
    }
    
    public function autenticar(string $cpf, string $senha_pura): array|bool {
        $sql = "SELECT id_usuario, senha_hash, nome_completo, id_perfil FROM usuarios WHERE cpf = :cpf";

        $stmt = $this -> pdo -> prepare($sql);

        $stmt -> bindParam(':cpf', $cpf);
        $stmt -> execute();
        
        $usuario = $stmt -> fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) { return false; } 
        
        if (password_verify($senha_pura, $usuario['senha_hash'])) {
            unset($usuario['senha_hash']);
            return $usuario;
        } else { return false; }
    }
    
    public function buscarPorId(int $id_usuario): array|bool {
        $sql = "SELECT id_usuario, cpf, nome_completo, nome_social_apelido, contato_celular, id_perfil 
                FROM usuarios 
                WHERE id_usuario = :id";
        
        $stmt = $this -> pdo -> prepare($sql);

        $stmt -> bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt -> execute();
        
        return $stmt -> fetch(PDO::FETCH_ASSOC);
    }

    public function atualizarPerfil(int $id_perfil): bool {
        $sql = "UPDATE usuarios SET id_perfil = :id_perfil WHERE id_usuario = :id_usuario";
        
        $stmt = $this -> pdo -> prepare($sql);

        $stmt -> bindParam(':id_perfil', $id_perfil, PDO::PARAM_INT);
        $stmt -> bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        
        return $stmt -> execute();
    }

    public function deletarUsuario(int $id_perfil): bool {
        $sql = "DELETE FROM usuarios WHERE id_usuario = :id_usuario";
        
        $stmt = $this -> pdo -> prepare($sql);

        $stmt -> bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        
        return $stmt -> execute();
    }

    public function atualizarSenha(int $id_perfil): bool {
        $sql = "UPDATE usuarios SET senha_hash = :senha_hash WHERE id_usuario = :id_usuario";
        
        $stmt = $this -> pdo -> prepare($sql);

        $stmt -> bindParam(':senha_hash', $senha_hash);
        $stmt -> bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        
        return $stmt -> execute();
    }
}
