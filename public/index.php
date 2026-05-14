<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/headers.php';

use App\Core\Container;

// 🔍 Roteamento simples para tratar a URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = array_values(array_filter(explode('/', $uri)));

$route = $parts[0] ?? '';  // Ex: 'ranking'
$parms = $parts[1] ?? null; // Permite pegar o ID ou Nome diretamente da URI, ex: /ranking/1 ou /ranking/squat
$query = $_GET['id'] ?? $_GET['name'] ?? null; // Permite tanto ?id= quanto ?name= para flexibilidade
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // número da página, padrão 1
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // registros por página

// Funções handlers para cada rota
function handleRanking($method, $resource, $controller) {
    if ($method !== 'GET') {
        http_response_code(405);
        echo json_encode(["error" => "Método não permitido para ranking"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return;
    }

    $identifier = $resource ?? ($_GET['id'] ?? $_GET['name'] ?? null);
    if (!$identifier) {
        http_response_code(400);
        echo json_encode(["error" => "Identificador do movimento é obrigatório"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return;
    }

    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 10;

    echo $controller->getRanking($identifier, $page, $limit);
}

// Instancia o Controller
$movementController = Container::movementController();

// Roteamento baseado na rota e método
switch ($route) {
    case 'ranking':
        handleRanking($method, urldecode($parms ?? $query), $movementController);
        break;

    default:
        if ($uri === '/' || $uri === '') {
            echo json_encode([
                "api" => APP_NAME,
                "version" => "1.0",
                "message" => "Bem-vindo à API de Movimentos!",
                "endpoints" => [
                    "GET /ranking/{id}?page=1&limit=10" => "Obter ranking de um movimento pelo ID",
                    "GET /ranking?id={id}&page=1&limit=10" => "Obter ranking de um movimento pelo ID",
                    "GET /ranking/{name}?page=1&limit=10" => "Obter ranking de um movimento pelo Nome",
                    "GET /ranking?name={name}&page=1&limit=10" => "Obter ranking de um movimento pelo Nome"
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode([
                "error" => "Rota não encontrada",
                "usage" => "Consulte / para ver os endpoints disponíveis"
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        break;
}
