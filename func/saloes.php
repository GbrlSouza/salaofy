<?php
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
        
        if ($stmt -> execute()) { return (int)$this -> pdo -> lastInsertId(); }

        return false;
    }

    public function buscarSaloesPorCondominio(int $id_condominio): array {
        $sql = "SELECT id_salao, nome_salao, preco_locacao_base FROM saloes 
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
    
    public function atualizaSalao(int $id_salao, string $nome, ?string $regras = null, ?float $preco_base = null, ?int $capacidade = null): bool {
        $sql = "UPDATE saloes 
                SET nome_salao = :nome, regras_uso = :regras, preco_locacao_base = :preco, capacidade_maxima = :capacidade 
                WHERE id_salao = :id";
        
        $stmt = $this -> pdo -> prepare($sql);
        
        $stmt -> bindParam(':id', $id_salao, PDO::PARAM_INT);
        $stmt -> bindParam(':nome', $nome);
        $stmt -> bindParam(':regras', $regras);
        $stmt -> bindParam(':preco', $preco_base);
        $stmt -> bindParam(':capacidade', $capacidade, PDO::PARAM_INT);
        
        return $stmt -> execute();
    }
    
    public function deletarSalao(int $id_salao): bool {
        $sql = "DELETE FROM saloes WHERE id_salao = :id";

        $stmt = $this -> pdo -> prepare($sql);

        $stmt -> bindParam(':id', $id_salao, PDO::PARAM_INT);
        
        return $stmt -> execute();
    }
    public function contarSaloesPorCondominio(int $id_condominio): int {
        $sql = "SELECT COUNT(*) FROM saloes WHERE id_condominio = :id_condominio";
        
        try {
            $stmt = $this -> pdo -> prepare($sql);
            $stmt -> bindParam(':id_condominio', $id_condominio, PDO::PARAM_INT);
            $stmt -> execute();
            return (int)$stmt -> fetchColumn();
        } catch (PDOException $e) {
            error_log("Erro ao contar salões por condomínio: " . $e -> getMessage());
            return 0;
        }
    }
}