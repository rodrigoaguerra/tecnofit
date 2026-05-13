<?php
namespace App\Core;

use App\Controllers\MovementController;
use App\Models\MovementModel;
use App\Database\Connection;

class Container {

    /**
    * Método estático para criar e retornar uma instância do MovementController.
    * Ele gerencia a criação da conexão com o banco de dados e a injeção da dependência da Model no Controller.
    * @return MovementController
    */
    public static function movementController() {
        $connection = Connection::get();
        
        $model = new MovementModel($connection);

        return new MovementController($model);
    }
}