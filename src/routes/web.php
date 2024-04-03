<?php

namespace routes;

use controllers\{AuthController, CalendarController, IndexController, PlayerController, SquadController};
use controllers\init\InitController;

return [
    'GET' => [
        '/' => [IndexController::class, 'index'],
        '/init' => [InitController::class, 'index'],
        '/logout' => [AuthController::class, 'logout'],
        '/login' => [AuthController::class, 'index'],
        '/players' => [PlayerController::class, 'index'],
        '/squads' => [SquadController::class, 'index'],
        '/calendar' => [CalendarController::class, 'index'],

        '/api/players' => [PlayerController::class, 'getPlayers'],
    ],
    'POST' => [
        '/login' => [AuthController::class, 'login'],
        '/api/create-user' => [AuthController::class, 'create'],
        '/api/players' => [PlayerController::class, 'update'],
    ],
];
