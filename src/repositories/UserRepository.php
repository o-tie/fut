<?php

namespace repositories;

use core\Repository;
use PDO;

class UserRepository extends Repository
{
    protected string $tableName = 'users';

    /**
     * @param string $username
     * @return array|bool
     */
    public function getOne(string $username): array|bool
    {
        $query = $this->db->prepare("SELECT * FROM {$this->tableName} WHERE username = :username");
        $query->bindParam(':username', $username);
        $query->execute();

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        $query = $this->db->prepare("SELECT * FROM {$this->tableName}");
        $query->execute();

        return $query->fetchAll();
    }

    /**
     * @param $params
     * @return bool
     */
    public function create($params): bool
    {
        $username = trim(htmlspecialchars($params['username']));
        $password = trim(htmlspecialchars($params['password']));

        $query = $this->db->prepare("INSERT INTO {$this->tableName} (username, password) VALUES (:username, :pass)");
        $query->bindParam(':username', $username);
        $query->bindParam(':pass', $password);

        return $query->execute();
    }
}