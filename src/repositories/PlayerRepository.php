<?php

namespace repositories;

use core\Repository;
use PDO;
class PlayerRepository extends Repository
{
    protected string $tableName = 'players';

    /**
     * @param $id
     * @return array
     */
    public function getOne($id): array
    {
        $query = $this->db->prepare("SELECT * FROM {$this->tableName} WHERE id = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        return $query->fetch(PDO::FETCH_OBJ);
    }

    /**
     * @param $userId
     * @return array
     */
    public function getAllByUser($userId): array
    {
        $query = $this->db->prepare("SELECT p.id, p.name, ps.stats FROM {$this->tableName} p LEFT JOIN players_stats ps on (p.id=ps.player_id AND ps.user_id=:user_id) ORDER BY p.name");
        $query->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_OBJ);
    }
}