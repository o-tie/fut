<?php

namespace controllers;

use core\Controller;

class SquadController extends Controller
{
    /**
     * @return void
     */
    public function index(): void
    {
        $this->render('squads.index');
    }
}
