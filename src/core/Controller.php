<?php

namespace core;

class Controller extends BaseController
{
    public function __construct()
    {
        if (!isset($_SESSION['user'])) {
            // Перенаправление на страницу логина
            header('Location: /login');
            exit();
        }
    }
}
