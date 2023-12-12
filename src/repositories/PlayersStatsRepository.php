<?php

namespace repositories;

use core\Repository;
use DateTime;
use PDO;

class PlayersStatsRepository extends Repository
{
    protected string $tableName = 'players_stats';

    public function updateOrCreate($data) {
        if ($this->getPlayerStats($data)) {
            return $this->updateStats($data);
        } else {
            return $this->createStats($data);
        }
    }

    public function createStats($data)
    {
        $query = $this->db->prepare("INSERT INTO {$this->tableName} (user_id, player_id, stats) VALUES (:user_id, :player_id, :stats)");
        $query->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $query->bindParam(':player_id', $data['player_id'], PDO::PARAM_INT);
        $query->bindParam(':stats', $data['stats']);

        return $query->execute();

    }

    public function updateStats($data)
    {
        $query = $this->db->prepare("UPDATE {$this->tableName} SET stats = :stats WHERE user_id=:user_id AND player_id=:player_id");
        $query->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $query->bindParam(':player_id', $data['player_id'], PDO::PARAM_INT);
        $query->bindParam(':stats', $data['stats']);

        return $query->execute();
    }

    public function getPlayerStats($data)
    {
        $query = $this->db->prepare("SELECT * FROM {$this->tableName} WHERE user_id = :user_id AND player_id=:player_id");
        $query->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $query->bindParam(':player_id', $data['player_id'], PDO::PARAM_INT);
        $query->execute();

        return $query->fetch(PDO::FETCH_OBJ);
    }

    /**
     * @param $playerId
     * @return array|false
     */
    public function getAllPlayerStats($playerId)
    {
        $query = $this->db->prepare("SELECT * FROM {$this->tableName} WHERE player_id=:player_id");
        $query->bindParam(':player_id', $playerId, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $playerId
     * @return mixed
     */
    public function getPlayerVotes(int $playerId)
    {
        $query = $this->db->prepare("SELECT count(player_id) as votes FROM {$this->tableName} WHERE player_id = :player_id");
        $query->bindParam(':player_id', $playerId, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchColumn();
    }

    /**
     * @param int $playerId
     * @return int
     */
    public function getWeekCorrections(int $playerId): int
    {
        $query = $this->db->prepare(
            "SELECT COUNT(player_id) as corrections
        FROM players_stats
        WHERE player_id = :player_id
          AND updated_at >= 
            CASE 
              WHEN DAYOFWEEK(NOW()) = 7 THEN
                CONCAT(CURDATE(), ' 09:00:00')
              ELSE
                CONVERT_TZ(CURDATE() - INTERVAL (DAYOFWEEK(NOW()) + 5) % 7 DAY + INTERVAL 9 HOUR, 'UTC', 'Europe/Berlin')
            END
        ");
        $query->bindParam(':player_id', $playerId, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchColumn();
    }
}
