<?php

header("Content-Type: application/json; charset=UTF-8");

// 🔒 CORS seguro
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, ALLOWED_ORIGINS)) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Vary: Origin");
header("Access-Control-Allow-Methods: " . implode(', ', METHODS_ALLOWED));
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// 🔒 Metodos permitidos
$method = $_SERVER['REQUEST_METHOD'];

if (!in_array($method, METHODS_ALLOWED)) {
    http_response_code(405);
    echo json_encode(
        ["error" => "Método não permitido"],
        JSON_PRETTY_PRINT |
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

// 🔒 Preflight para requisições front-end 
if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// 🔒 Headers de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');