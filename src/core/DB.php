<?php

namespace core;

use Dotenv\Dotenv;
use PDO;
use Throwable;

class DB
{
    private static ?self $instance = null;
    private static ?PDO $pdo = null;
    private function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
        $dotenv->load();

        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_DATABASE'];
        $port = $_ENV['DB_PORT'];
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];

        try {
            self::$pdo = new PDO("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->exec("SET NAMES 'utf8'");
            self::$pdo->exec("SET CHARACTER SET utf8");
        } catch (Throwable $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    /**
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$pdo;
    }
}
