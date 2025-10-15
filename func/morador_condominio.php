<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$base_dir = __DIR__ . '/../';

if (file_exists($base_dir . './../db/conexao.php')) { $conexao = require $base_dir . './../db/conexao.php'; } 
elseif (file_exists('./db/conexao.php')) { $conexao = require './db/conexao.php'; }
else { die("Erro fatal: Arquivo de conexão 'conexao.php' não encontrado em nenhum caminho esperado."); }

if (!$conexao instanceof PDO) { die("Erro fatal: A conexão com o banco de dados falhou durante a inicialização."); }

class MoradorCondominio {
    private $pdo;

    public function __construct(PDO $pdo) { $this -> pdo = $pdo; }

    public function adicionarVinculo(int $id_morador, int $id_condominio): bool {
        $sql = "INSERT INTO morador_condominio (id_morador, id_condominio) 
                VALUES (:morador, :condominio)";
        
        try {
            $stmt = $this -> pdo -> prepare($sql);
            
            $stmt -> bindParam(':morador', $id_morador, PDO::PARAM_INT);
            $stmt -> bindParam(':condominio', $id_condominio, PDO::PARAM_INT);
            
            return $stmt -> execute();
        } catch (PDOException $e) {
            error_log("Erro ao adicionar vínculo morador-condomínio: " . $e -> getMessage());
            return false;
        }
    }

    public function removerVinculo(int $id_morador, int $id_condominio): bool {
        $sql = "DELETE FROM morador_condominio 
                WHERE id_morador = :morador AND id_condominio = :condominio";
        
        $stmt = $this -> pdo -> prepare($sql);
        
        $stmt -> bindParam(':morador', $id_morador, PDO::PARAM_INT);
        $stmt -> bindParam(':condominio', $id_condominio, PDO::PARAM_INT);
        
        return $stmt -> execute();
    }

    public function buscarCondominiosDoMorador(int $id_morador): array {
        $sql = "SELECT c.id_condominio, c.nome_condominio, c.cep 
                FROM morador_condominio mc
                JOIN condominios c ON mc.id_condominio = c.id_condominio
                WHERE mc.id_morador = :morador";
        
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> bindParam(':morador', $id_morador, PDO::PARAM_INT);
        $stmt -> execute();
        
        return $stmt -> fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function isVinculado(int $id_morador, int $id_condominio): bool {
        $sql = "SELECT id_vinculo FROM morador_condominio 
                WHERE id_morador = :morador AND id_condominio = :condominio";
        
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> bindParam(':morador', $id_morador, PDO::PARAM_INT);
        $stmt -> bindParam(':condominio', $id_condominio, PDO::PARAM_INT);
        $stmt -> execute();
        
        return $stmt -> rowCount() > 0;
    }
}
