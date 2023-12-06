<?php

namespace controllers;

use core\Controller;
use Exception;
use repositories\UserRepository;
use Throwable;

class UserController extends Controller
{
    protected $userRepo;

    public function __construct()
    {
        parent::__construct();

        $this->userRepo = new UserRepository();
    }
}
