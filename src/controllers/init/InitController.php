<?php

namespace controllers\init;

use core\DB;
use PDO;
use Throwable;

class InitController
{
    protected string $initFile;
    protected array $migrations;
    protected array $players;
    protected PDO $db;

    public function __construct()
    {
        $this->initFile = __DIR__ . '/../../../init';
        $this->migrations = require_once(__DIR__ . '/../../migrations/init_migrations.php');
        $this->players = require_once(__DIR__ . '/../../configs/players.php');
        $this->db = DB::getInstance();
    }

    /**
     * @return void
     */
    public function index(): void
    {
        if (file_exists($this->initFile)) {
            return;
        }
        try {
            $this->db->beginTransaction();
            foreach ($this->migrations as $migration) {
                $this->db->exec($migration);
            }
            foreach ($this->players as $player) {
                $stmt = $this->db->prepare("INSERT INTO players (name) VALUES (:name)");
                $stmt->bindParam(':name', $player);
                $stmt->execute();
            }
            if ($this->db->inTransaction()) {
                $this->db->commit();
            }
            file_put_contents($this->initFile, '');
            echo 'Initialized successfully';
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            echo $e->getMessage();
        }
    }
}
