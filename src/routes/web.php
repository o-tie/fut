<?php

namespace routes;

use controllers\AuthController;
use controllers\IndexController;
use controllers\init\InitController;
use controllers\PlayerController;

return [
    'GET' => [
        '/' => [IndexController::class, 'index'],
        '/init' => [InitController::class, 'index'],
        '/logout' => [AuthController::class, 'logout'],
        '/login' => [AuthController::class, 'index'],
        '/players' => [PlayerController::class, 'index'],

        '/api/players' => [PlayerController::class, 'getPlayers'],
    ],
    'POST' => [
        '/login' => [AuthController::class, 'login'],
        '/api/create-user' => [AuthController::class, 'create'],
        '/api/players' => [PlayerController::class, 'update'],
    ],
];
