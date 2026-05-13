<?php

namespace App\Controllers;

use App\Models\MovementModel; 

class MovementController {
    private MovementModel $model;

    public function __construct(MovementModel $model) {
        $this->model = $model;
    }

    /**
     * Retorna o ranking de um movimento específico, formatado conforme os requisitos.
     * @param string $identifier ID ou Nome do movimento.
     * @return string JSON formatado ou mensagem de erro.
     */
    public function getRanking(string $identifier) {
        try {
            // O Controller apenas pede os dados para a Model
            $data = $this->model->getRankingByIdentifier($identifier);

            // O Controller verifica se o ranking foi encontrado
            if (!$data) {
                http_response_code(404);
                return json_encode(["error" => "Ranking não encontrado"], JSON_UNESCAPED_UNICODE);
            }

            // O Controller formata a resposta final conforme os requisitos
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

        }  catch (\InvalidArgumentException $e) {
            // Tratamento de erro
            http_response_code(400);
            echo json_encode(["error" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            // Tratamento de erro 
            http_response_code(500);
            error_log($e->getMessage()); // loga internamente
            echo json_encode(["error" => "Erro interno"], JSON_UNESCAPED_UNICODE); // não expõe detalhes
        }
    }
}
