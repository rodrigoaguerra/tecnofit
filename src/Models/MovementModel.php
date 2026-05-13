<?php

namespace App\Models;

use PDO;

class MovementModel {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Retorna o ranking de um movimento específico.
     * @param string|int $identifier ID ou Nome do movimento.
     * @return array|false
     */
    public function getRankingByIdentifier(string | int $identifier) {
        $sql = "
            -- CTE para calcular o ranking e pegar o recorde mais recente de cada usuário
            WITH ranking AS (
                SELECT 
                    m.id AS movement_id,
                    m.name AS movement_name,
                    u.id AS user_id,
                    u.name AS user_name,
                    MAX(pr.value) AS personal_record -- pega o MAIOR valor de cada usuário
                FROM personal_record pr
                JOIN user u ON pr.user_id = u.id -- liga o registro ao usuário
                JOIN movement m ON pr.movement_id = m.id -- liga o registro ao movimento
                WHERE m.id = :id OR m.name = :name -- filtra por um movimento específico
                GROUP BY m.id, m.name, u.id, u.name -- agrupa por usuário+movimento
            )

            -- Query principal para ordenar o ranking e pegar a data do recorde mais recente
            SELECT 
                r.movement_name,
                r.user_name,
                r.personal_record,
                -- Calcula a posição no ranking baseado no maior PR (DENSE_RANK para lidar com empates)
                DENSE_RANK() OVER (ORDER BY r.personal_record DESC) AS ranking_position,
                -- Busca a data em que aquele valor máximo foi atingido
                (
                    SELECT pr2.date
                    FROM personal_record pr2
                    WHERE pr2.user_id = r.user_id
                    AND pr2.movement_id = r.movement_id
                    AND pr2.value = r.personal_record -- casa exatamente com o maior valor
                    ORDER BY pr2.date DESC
                    LIMIT 1 -- pega a data mais recente caso haja múltiplos registros com o mesmo value
                ) AS record_date
            FROM ranking r
            ORDER BY r.personal_record DESC
        ";

        $stmt = $this->db->prepare($sql);
        
        // Mesmo identificador para ambos os filtros (ID ou Nome)
        $stmt->bindValue(':id', is_numeric($identifier) ? (int)$identifier : 0, PDO::PARAM_INT);
        $stmt->bindValue(':name', $identifier, PDO::PARAM_STR);
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}