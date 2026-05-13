<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use App\Core\Container;

header("Content-Type: application/json; charset=UTF-8");

// 🔒 CORS seguro
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, ALLOWED_ORIGINS)) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Vary: Origin");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// 🔒 Metodos permitidos
$method = $_SERVER['REQUEST_METHOD'];

if (!in_array($method, ['GET', 'OPTIONS'])) {
    http_response_code(405);
    echo json_encode(
        ["error" => "Método não permitido"],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

// 🔒 Preflight 
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// 🔍 Roteamento simples para tratar a URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = array_values(array_filter(explode('/', $uri)));

// Verifica se a URI corresponde ao formato esperado para ranking
if (($parts[0] === 'ranking' && isset($parts[1])) || isset($_GET['id']) || isset($_GET['name'])) {
    // Permite tanto /ranking/{id_ou_nome} quanto /ranking?id={id_ou_nome}
    $uri_param = urldecode($parts[1] ?? $_GET['id'] ?? $_GET['name']); 

    // instancia o Controller por meio do Container para obter o ranking do movimento solicitado
    $controller = Container::movementController();

    // chama o método do Controller para obter o ranking e exibe a resposta JSON formatada ou mensagem de erro
    echo $controller->getRanking($uri_param);
} else {
    // se a URI for raiz mostramos uma mensagem de boas-vindas e instruções de uso
    if ($uri === '/' || $uri === '') {
        echo json_encode([
            "app" => APP_NAME,
            "message" => "Bem-vindo à API de Movimentos! Use /ranking/{id_ou_nome} ou /ranking?id={id} ou /ranking?name={nome} para obter o ranking de um movimento específico."
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        // se a URI não corresponder ao formato esperado, retorna um erro de parâmetro inválido com instruções de uso
        http_response_code(400);
        echo json_encode([ 
            "app" => APP_NAME, 
            "error" => "Parâmetro inválido",
            "usage" => "Use /ranking/{id_ou_nome} ou /ranking?id={id} ou /ranking?name={nome} para obter o ranking de um movimento específico"
          ], 
          JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }
}
