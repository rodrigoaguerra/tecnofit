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
            SELECT 
                m.name AS movement_name,
                u.name AS user_name,
                MAX(pr.value) AS personal_record,
                DENSE_RANK() OVER (ORDER BY MAX(pr.value) DESC) AS ranking_position,
                MAX(pr.date) AS record_date
            FROM personal_record pr
            JOIN user u ON pr.user_id = u.id
            JOIN movement m ON pr.movement_id = m.id
            WHERE m.id = :id OR m.name = :name
            GROUP BY u.id, u.name, m.name
            ORDER BY ranking_position ASC, record_date DESC
        ";

        $stmt = $this->db->prepare($sql);
        
        // Mesmo identificador para ambos os filtros (ID ou Nome)
        $stmt->bindValue(':id', is_numeric($identifier) ? (int)$identifier : 0, PDO::PARAM_INT);
        $stmt->bindValue(':name', $identifier, PDO::PARAM_STR);
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}