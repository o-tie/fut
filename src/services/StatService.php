<?php

namespace services;

use repositories\PlayerRepository;
use repositories\PlayersStatsRepository;

class StatService
{
    public const PACE = 0.15;
    public const DRIBBLING = 0.2;
    public const SHOOTING = 0.12;
    public const PASSING = 0.15;
    public const VISION = 0.15;
    public const DEFENDING = 0.1;
    public const POSITIONING = 0.05;
    public const STAMINA = 0.08;

    public const PACE_NAME = 'pac';
    public const DRIBBLING_NAME = 'dri';
    public const SHOOTING_NAME = 'sho';
    public const PASSING_NAME = 'pas';
    public const VISION_NAME = 'vis';
    public const DEFENDING_NAME = 'def';
    public const POSITIONING_NAME = 'pos';
    public const STAMINA_NAME = 'phy';

    protected PlayerRepository $playerRepo;
    protected PlayersStatsRepository $playerStatsRepo;

    public function __construct()
    {
        $this->playerRepo = new PlayerRepository();
        $this->playerStatsRepo = new PlayersStatsRepository();
    }

    /**
     * @param $data
     * @return int|null
     */
    public function getOverallUser($data): ?int
    {
        if (empty($data)) {
            return null;
        }
        $overall = 0;
        $stats = json_decode($data, true);

        return $this->calcOverallRate($stats);
    }

    /**
     * @param $playerId
     * @return int|null
     */
    public function getOverall($playerId): ?int
    {
        $data = $this->playerStatsRepo->getAllPlayerStats($playerId);
        if (empty($data)) {
            return null;
        }

        $sum[self::PACE_NAME] = $sum[self::DRIBBLING_NAME] = $sum[self::SHOOTING_NAME] = $sum[self::PASSING_NAME]
            = $sum[self::VISION_NAME] = $sum[self::DEFENDING_NAME] = $sum[self::POSITIONING_NAME] = $sum[self::STAMINA_NAME] = 0;

        $count = count($data);
        foreach ($data as $item) {
            $stats = json_decode($item['stats'], true);
            foreach ($stats as $stat => $value) {
                $sum[$stat] += $value;
            }
        }

        $avg= [];
        foreach ($sum as $stat => $value) {
            $avg[$stat] = round($value/$count);
        }

        return $this->calcOverallRate($avg);
    }

    /**
     * @param $stat
     * @param $value
     * @return float|null
     */
    protected function getStatValue($stat, $value): ?float
    {
        switch ($stat) {
            case self::PACE_NAME;
                $result = $value * self::PACE;
                break;
            case self::DRIBBLING_NAME;
                $result = $value * self::DRIBBLING;
                break;
            case self::SHOOTING_NAME;
                $result = $value * self::SHOOTING;
                break;
            case self::PASSING_NAME;
                $result = $value * self::PASSING;
                break;
            case self::VISION_NAME;
                $result = $value * self::VISION;
                break;
            case self::DEFENDING_NAME;
                $result = $value * self::DEFENDING;
                break;
            case self::POSITIONING_NAME;
                $result = $value * self::POSITIONING;
                break;
            case self::STAMINA_NAME;
                $result = $value * self::STAMINA;
                break;
            default:
                return null;
        }
        return $result;
    }

    /**
     * @param array $stats
     * @return int
     */
    protected function calcOverallRate(array $stats): int
    {
        $overall = 0;

        foreach ($stats as $stat => $value) {
            $overall += (float) $this->getStatValue($stat, $value);
        }

        return round($overall);
    }

    /**
     * @param $stats
     * @return array
     */
    public function validateStats($stats): array
    {
        $errors = [];
        foreach ($stats as $stat => $value) {
            if (!is_int($value) || $value < 1 || $value > 100) {
                $errors[] = $stat;
            }
        }

        return $errors;
    }

    /**
     * @param $params
     * @return bool
     */
    public function updatePlayerStats($params): bool
    {
        $player = $params['player'];
        $data['user_id'] = $_SESSION['user'];
        $data['player_id'] = $player['id'];
        $data['stats'] = json_encode($params['stats']);

        return $this->playerStatsRepo->updateOrCreate($data);
    }
}

