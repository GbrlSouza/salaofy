<?php
class Agendamento
{
    private $pdo;

    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function verificarDisponibilidade(int $id_salao, string $data_evento): bool {
        $sql = "SELECT COUNT(*) FROM agendamentos 
                WHERE id_salao = :salao 
                AND data_evento = :data 
                AND status IN ('Pendente', 'Confirmada')";
        
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':salao', $id_salao, PDO::PARAM_INT);
        $stmt->bindParam(':data', $data_evento);
        $stmt->execute();
        
        return $stmt->fetchColumn() == 0;
    }

    public function solicitarAgendamento(int $id_salao, int $id_morador, string $data_evento, float $valor_total, ?string $detalhes = null): int|bool {
        $sql = "INSERT INTO agendamentos (id_salao, id_morador, data_evento, valor_total, detalhes_evento) 
                VALUES (:salao, :morador, :data, :valor, :detalhes)";
        
        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':salao', $id_salao, PDO::PARAM_INT);
            $stmt->bindParam(':morador', $id_morador, PDO::PARAM_INT);
            $stmt->bindParam(':data', $data_evento);
            $stmt->bindParam(':valor', $valor_total);
            $stmt->bindParam(':detalhes', $detalhes);
            
            if ($stmt->execute()) { return (int)$this->pdo->lastInsertId(); }

            return false;
        } catch (PDOException $e) {
            error_log("Erro ao solicitar agendamento: " . $e->getMessage());
            return false;
        }
    }

    public function atualizarStatus(int $id_agendamento, string $novo_status): bool {
        $status_validos = ['Confirmada', 'Rejeitada', 'Cancelada'];

        if (!in_array($novo_status, $status_validos)) { return false; }

        $sql = "UPDATE agendamentos SET status = :status WHERE id_agendamento = :id";
        
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':status', $novo_status);
        $stmt->bindParam(':id', $id_agendamento, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    public function listarAgendamentosMorador(int $id_morador): array {
        $sql = "SELECT a.*, s.nome_salao 
                FROM agendamentos a
                JOIN saloes s ON a.id_salao = s.id_salao
                WHERE a.id_morador = :morador";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':morador', $id_morador, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPendentesPorCondominio(int $id_condominio): array {
        $sql = "SELECT a.id_agendamento, s.nome_salao, a.data_evento, a.valor_total, u.nome_completo as morador_nome
                FROM agendamentos a
                JOIN saloes s ON a.id_salao = s.id_salao
                JOIN condominios c ON s.id_condominio = c.id_condominio
                JOIN usuarios u ON a.id_morador = u.id_usuario
                WHERE a.status = 'Pendente' AND c.id_condominio = :id_condominio
                ORDER BY a.data_evento ASC";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_condominio', $id_condominio, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar agendamentos pendentes por condomínio: " . $e->getMessage());
            return [];
        }
    }
}