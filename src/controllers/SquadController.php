<?php

namespace controllers;

use core\Controller;

class SquadController extends Controller
{
    public function index(): void
    {
        $this->render('squads.index');
    }
}
