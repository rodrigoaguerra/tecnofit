<?php

namespace App\Controllers;

use App\Models\MovementModel; 

class MovementController {
    private $model;

    // Recebe a Model via Injeção de Dependência no construtor
    public function __construct(MovementModel $model) {
        $this->model = $model;
    }

    public function getRanking(string $identifier) {
        try {
            // O Controller apenas pede os dados para a Model [cite: 5]
            $data = $this->model->getRankingByIdentifier($identifier);

            if (!$data) {
                http_response_code(404);
                return json_encode(["error" => "Ranking não encontrado"], JSON_UNESCAPED_UNICODE);
            }

            // O Controller formata a resposta final conforme os requisitos [cite: 6, 7]
            return json_encode([
                "movimento" => $data[0]['movement_name'],
                "ranking" => array_map(function($row) {
                    return [
                        "usuario" => $row['user_name'],
                        "recorde" => (float)$row['personal_record'],
                        "posicao" => (int)$row['ranking_position'],
                        "data" => $row['record_date']
                    ];
                }, $data)
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            // Tratamento de erro para manter o código pronto para produção [cite: 18]
            http_response_code(500);
            return json_encode(["error" => "Erro interno no servidor"], JSON_UNESCAPED_UNICODE);
        }
    }
}
