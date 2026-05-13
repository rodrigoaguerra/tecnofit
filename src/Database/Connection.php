<?php
namespace App\Database;

use PDO;

class Connection {
    
    /**
     * Executa a conexão com o bando de dados MySQL por meio da extensão PDO.
     * @return PDO
     **/
    public static function get() {
        return new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=" . DB_PORT . ";charset=utf8", DB_USER, DB_PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }
}