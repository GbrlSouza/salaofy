<?php
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
            error_log("Erro ao adicionar vínculo: " . $e -> getMessage());
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

    public function contarMoradoresPorCondominio(int $id_condominio): int {
        $sql = "SELECT COUNT(id_morador) FROM morador_condominio WHERE id_condominio = :id_condominio";
        
        try {
            $stmt = $this -> pdo -> prepare($sql);
            $stmt -> bindParam(':id_condominio', $id_condominio, PDO::PARAM_INT);
            $stmt -> execute();

            return (int)$stmt -> fetchColumn();
        } catch (PDOException $e) {
            error_log("Erro ao contar moradores por condomínio: " . $e -> getMessage());
            return 0;
        }
    }
}
