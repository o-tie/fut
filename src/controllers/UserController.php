<?php

namespace controllers;

use core\Controller;
use repositories\UserRepository;

class UserController extends Controller
{
    protected UserRepository $userRepo;

    public function __construct()
    {
        parent::__construct();

        $this->userRepo = new UserRepository();
    }
}
