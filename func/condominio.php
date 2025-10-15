<?php
class Condominio {
    private $pdo;

    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function criarCondominio(string $nome, string $cep): int|bool {
        $sql = "INSERT INTO condominios (nome_condominio, cep) VALUES (:nome, :cep)";
        
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cep', $cep);
        
        if ($stmt->execute()) { return (int)$this->pdo->lastInsertId(); }

        return false;
    }

    public function buscarCondominio(int $id): array|bool {
        $sql = "SELECT id_condominio, nome_condominio, cep, id_sindico FROM condominios WHERE id_condominio = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    public function buscarCondominioPorCep(string $cep): array|bool {
        $sql = "SELECT id_condominio, nome_condominio, cep, id_sindico FROM condominios WHERE cep = :cep";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':cep', $cep);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    public function listarTodos(): array {
        $sql = "SELECT c.*, u.nome_completo as nome_sindico 
                FROM condominios c 
                LEFT JOIN usuarios u ON c.id_sindico = u.id_usuario 
                ORDER BY nome_condominio";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function editarCondominio(int $id, string $nome, string $cep): bool {
        $sql = "UPDATE condominios SET nome_condominio = :nome, cep = :cep WHERE id_condominio = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cep', $cep);
        
        return $stmt->execute(); 
    }

    public function deletarCondominio (int $id): bool {
        $sql = "DELETE FROM condominios WHERE id_condominio = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute(); 
    }

    public function associarSindico(int $id_condominio, int $id_sindico): bool {
        $sql = "UPDATE condominios SET id_sindico = :id_sindico WHERE id_condominio = :id_condominio";
        
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->bindParam(':id_sindico', $id_sindico, PDO::PARAM_INT);
        $stmt->bindParam(':id_condominio', $id_condominio, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    public function buscarPorSindico(int $id_sindico): array|bool {
        $sql = "SELECT id_condominio, nome_condominio, cep FROM condominios WHERE id_sindico = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':id', $id_sindico, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function contarTotalCondominios(): int {
        $sql = "SELECT COUNT(*) FROM condominios";
        
        try {
            $stmt = $this->pdo->query($sql);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erro ao contar total de condomínios: " . $e->getMessage());
            return 0;
        }
    }
}