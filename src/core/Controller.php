<?php

namespace core;

class Controller extends BaseController
{
    public function __construct()
    {
        if (!isset($_SESSION['user'])) {
            // Redirect to login page
            header('Location: /login');
            exit();
        }
    }
}
