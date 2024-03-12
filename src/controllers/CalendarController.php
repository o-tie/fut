<?php

namespace controllers;

use core\BaseController;

class CalendarController extends BaseController
{
    /**
     * @return void
     */
    public function index(): void
    {
        $this->render('calendar.index');
    }
}
