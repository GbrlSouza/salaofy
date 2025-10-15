<?php
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

        } catch (PDOException $e) {
            error_log("Erro ao registrar usuário: " . $e -> getMessage());
            return false;
        }
    }

    public function autenticar(string $cpf, string $senha_pura): array|bool {
        $sql = "SELECT id_usuario, cpf, senha_hash, nome_completo, id_perfil FROM usuarios WHERE cpf = :cpf";
        
        $stmt = $this -> pdo -> prepare($sql);
        
        $stmt -> bindParam(':cpf', $cpf);
        $stmt -> execute();
        
        $usuario = $stmt -> fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha_pura, $usuario['senha_hash'])) {
            unset($usuario['senha_hash']); 
            return $usuario;
        }

        return false;
    }

    public function buscarUsuarioPorId(int $id_usuario): array|bool {
        $sql = "SELECT id_usuario, cpf, nome_completo, contato_celular, id_perfil FROM usuarios WHERE id_usuario = :id";
        
        $stmt = $this -> pdo -> prepare($sql);
        
        $stmt -> bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt -> execute();
        
        return $stmt -> fetch(PDO::FETCH_ASSOC);
    }
    
    public function atualizarPerfil(int $id_usuario, int $novo_id_perfil): bool {
        $sql = "UPDATE usuarios SET id_perfil = :id_perfil WHERE id_usuario = :id_usuario";
        
        $stmt = $this -> pdo -> prepare($sql);

        $stmt -> bindParam(':id_perfil', $novo_id_perfil, PDO::PARAM_INT);
        $stmt -> bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        
        return $stmt -> execute();
    }

    public function deletarUsuario(int $id_usuario): bool {
        $sql = "DELETE FROM usuarios WHERE id_usuario = :id_usuario";
        
        $stmt = $this -> pdo -> prepare($sql);

        $stmt -> bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        
        return $stmt -> execute();
    }

    public function atualizarSenha(int $id_usuario, string $nova_senha_pura): bool {
        $nova_senha_hash = password_hash($nova_senha_pura, PASSWORD_BCRYPT);
        
        $sql = "UPDATE usuarios SET senha_hash = :senha_hash WHERE id_usuario = :id_usuario";
        
        $stmt = $this -> pdo -> prepare($sql);

        $stmt -> bindParam(':senha_hash', $nova_senha_hash);
        $stmt -> bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        
        return $stmt -> execute();
    }

    public function cpfJaExiste(string $cpf): bool {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE cpf = :cpf";
        
        $stmt = $this -> pdo -> prepare($sql);
        
        $stmt -> bindParam(':cpf', $cpf);
        $stmt -> execute();
        
        return $stmt -> fetchColumn() > 0;
    }

    public function contarTotalUsuarios(): int {
        $sql = "SELECT COUNT(*) FROM usuarios";
        
        try {
            $stmt = $this -> pdo -> query($sql);
            return (int)$stmt -> fetchColumn();
        } catch (PDOException $e) {
            error_log("Erro ao contar total de usuários: " . $e -> getMessage());
            return 0;
        }
    }

    public function contarUsuariosPorPerfil(int $id_perfil): int {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE id_perfil = :id_perfil";
        
        try {
            $stmt = $this -> pdo -> prepare($sql);
            $stmt -> bindParam(':id_perfil', $id_perfil, PDO::PARAM_INT);
            $stmt -> execute();
            return (int)$stmt -> fetchColumn();
        } catch (PDOException $e) {
            error_log("Erro ao contar usuários por perfil: " . $e -> getMessage());
            return 0;
        }
    }
}