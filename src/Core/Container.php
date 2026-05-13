<?php
namespace App\Core;

use App\Controllers\MovementController;
use App\Models\MovementModel;
use App\Database\Connection;

class Container {
    public static function movementController() {
        $connection = Connection::get();
        
        $model = new MovementModel($connection);

        return new MovementController($model);
    }
}