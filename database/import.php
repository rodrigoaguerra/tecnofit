<?php

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../config.php';

use App\Database\Connection;

$pdo = Connection::get();

// limpa o banco
$pdo->exec('DROP DATABASE IF EXISTS ' . DB_NAME);
$pdo->exec('CREATE DATABASE ' . DB_NAME);
$pdo->exec('USE ' . DB_NAME);

// lê arquivo SQL
$sql = file_get_contents(__DIR__ . '/database.sql');

// executa
$pdo->exec($sql);

echo "Banco importado com sucesso!\n";