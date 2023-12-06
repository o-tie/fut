<?php

namespace core;

use PDO;

class Repository
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }
}
