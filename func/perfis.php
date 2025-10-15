<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$base_dir = __DIR__ . '/../';

if (file_exists($base_dir . './../db/conexao.php')) { $conexao = require $base_dir . './../db/conexao.php'; } 
elseif (file_exists('./db/conexao.php')) { $conexao = require './db/conexao.php'; }
else { die("Erro fatal: Arquivo de conexão 'conexao.php' não encontrado em nenhum caminho esperado."); }

if (!$conexao instanceof PDO) { die("Erro fatal: A conexão com o banco de dados falhou durante a inicialização."); }

class Perfil {
    private $pdo;

    public function __construct(PDO $pdo) { $this -> pdo = $pdo; }

    public function buscarTodos(): array {
        $sql = "SELECT id_perfil, nome_perfil FROM perfis ORDER BY id_perfil ASC";
        
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute();
        
        return $stmt -> fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function buscarNomePorId(int $id_perfil): string|bool {
        $sql = "SELECT nome_perfil FROM perfis WHERE id_perfil = :id";
        
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> bindParam(':id', $id_perfil, PDO::PARAM_INT);
        $stmt -> execute();
        
        $resultado = $stmt -> fetch(PDO::FETCH_ASSOC);
        
        return $resultado ? $resultado['nome_perfil'] : false;
    }

    public function buscarIdPorNome(string $nome_perfil): int|bool {
        $sql = "SELECT id_perfil FROM perfis WHERE nome_perfil = :nome";
        
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> bindParam(':nome', $nome_perfil);
        $stmt -> execute();
        
        $resultado = $stmt -> fetch(PDO::FETCH_ASSOC);
        
        return $resultado ? (int)$resultado['id_perfil'] : false;
    }

    public function buscarNomePorPerfil(string $nome_perfil): int|bool {
        $sql = "SELECT id_perfil FROM perfis WHERE nome_perfil = :nome";
        
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> bindParam(':nome', $nome_perfil);
        $stmt -> execute();
        
        $resultado = $stmt -> fetch(PDO::FETCH_ASSOC);

        return $resultado ? (int)$resultado['nome_perfil'] : false;
    }
}
