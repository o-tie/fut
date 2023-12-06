<?php

namespace controllers;

use core\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $this->render('index.index');
    }
}
