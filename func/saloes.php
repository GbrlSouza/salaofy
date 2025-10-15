<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$base_dir = __DIR__ . '/../';

if (file_exists($base_dir . './../db/conexao.php')) { $conexao = require $base_dir . './../db/conexao.php'; } 
elseif (file_exists('./db/conexao.php')) { $conexao = require './db/conexao.php'; }
else { die("Erro fatal: Arquivo de conexão 'conexao.php' não encontrado em nenhum caminho esperado."); }

if (!$conexao instanceof PDO) { die("Erro fatal: A conexão com o banco de dados falhou durante a inicialização."); }

class Salao
{
    private $pdo;

    public function __construct(PDO $pdo) { $this -> pdo = $pdo; }

    public function criarSalao(string $nome, int $id_condominio, ?string $regras = null, ?float $preco_base = null, ?int $capacidade = null): int|bool {
        $sql = "INSERT INTO saloes (nome_salao, regras_uso, preco_locacao_base, capacidade_maxima, id_condominio) 
                VALUES (:nome, :regras, :preco, :capacidade, :condominio)";
        
        $stmt = $this -> pdo -> prepare($sql);
        
        $stmt -> bindParam(':nome', $nome);
        $stmt -> bindParam(':regras', $regras);
        $stmt -> bindParam(':preco', $preco_base);
        $stmt -> bindParam(':capacidade', $capacidade, PDO::PARAM_INT);
        $stmt -> bindParam(':condominio', $id_condominio, PDO::PARAM_INT);
        
        if ($stmt -> execute()) { return (int)$this -> pdo -> lastInsertId(); } else { return false; }
    }
    
    public function buscarSaloesPorCondominio(int $id_condominio): array {
        $sql = "SELECT id_salao, nome_salao, preco_locacao_base, capacidade_maxima 
                FROM saloes 
                WHERE id_condominio = :condominio 
                ORDER BY nome_salao";
        
        $stmt = $this -> pdo -> prepare($sql);

        $stmt -> bindParam(':condominio', $id_condominio, PDO::PARAM_INT);
        $stmt -> execute();
        
        return $stmt -> fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarSalao(int $id_salao): array|bool {
        $sql = "SELECT id_salao, nome_salao, regras_uso, preco_locacao_base, capacidade_maxima, id_condominio 
                FROM saloes 
                WHERE id_salao = :id";
        
        $stmt = $this -> pdo -> prepare($sql);

        $stmt -> bindParam(':id', $id_salao, PDO::PARAM_INT);
        $stmt -> execute();
        
        return $stmt -> fetch(PDO::FETCH_ASSOC);
    }
    
    public function atualizaSalao(int $id_salao): bool {
        $sql = "UPDATE saloes 
                SET nome_salao = :nome, regras_uso = :regras, preco_locacao_base = :preco, capacidade_maxima = :capacidade 
                WHERE id_salao = :id";
        
        $stmt = $this -> pdo -> prepare($sql);
        
        $stmt -> bindParam(':id', $id_salao, PDO::PARAM_INT);
        
        return $stmt -> execute();
    }

    public function deletaSalao(int $id_salao): bool { 
        $sql = "DELETE FROM saloes WHERE id_salao = :id";
        
        $stmt = $this -> pdo -> prepare($sql);
        
        $stmt -> bindParam(':id', $id_salao, PDO::PARAM_INT);
        
        return $stmt -> execute();
    }
}
