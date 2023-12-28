<?php

namespace services;

use Carbon\Carbon;
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

    public const TOLERANCE = 0.25;

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
     * @return array|null
     */
    public function getOverall($playerId): ?array
    {
        $playerStats = $this->playerStatsRepo->getAllPlayerStats($playerId);

        if (empty($playerStats)) {
            return null;
        }

        $statsArr = [];
        $avg = [];
        foreach ($playerStats as $playerStat) {
            $stats = json_decode($playerStat['stats'], true);
            foreach ($stats as $stat => $value) {
                $statsArr[$stat][] = $value;
            }
        }

        foreach ($statsArr as $statName => &$statValue) {
            $countStat = count($statValue);
            if ($countStat > 2) {
                /* Check min-max values. If difference min or max and avg the left rates is 25% or more value will correlate */
                $minValue = min($statValue);
                $maxValue = max($statValue);
                sort($statValue);
                array_shift($statValue);
                array_pop($statValue);
                $averageSmooth = array_sum($statValue)/($countStat - 2);
                if ($this->isTolerance($minValue, $averageSmooth)) {
                    $statValue[] = $minValue;
                } else {
                    $statValue[] = round(($minValue + $averageSmooth)/2);
                }
                if ($this->isTolerance($maxValue, $averageSmooth)) {
                    $statValue[] = $maxValue;
                } else {
                    $statValue[] = round(($maxValue + $averageSmooth)/2);
                }
            }
            $avg[$statName] = round(array_sum($statValue)/$countStat);
        }

        $result['overallStats'] = $avg;
        $result['overall'] = $this->calcOverallRate($avg);

        return $result;
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

    /**
     * Returns ['corrections', 'last_update']
     * @param $playerId
     * @return string
     */
    public function getWeekCorrections($playerId): string
    {
        return $this->playerStatsRepo->getWeekCorrections($playerId);
    }

    public function getLastCorrection($playerId): string
    {
        $date = $this->playerStatsRepo->getLastCorrection($playerId);

        if (!empty($date)) {
            return Carbon::parse($date)->format('d-m-Y H:i:s');
        } else {
            return 'not set';
        }
    }

    /**
     * @param int $playerId
     * @return int
     */
    public function getVotes(int $playerId): int
    {
        return $this->playerStatsRepo->getPlayerVotes($playerId) ?? 0;
    }

    protected function isTolerance($num1, $num2): bool
    {
        // Вычисляем разницу между числами
        $difference = abs($num1 - $num2);
        // Вычисляем порог для сравнения (25% от меньшего числа)
        $threshold = min($num1, $num2) * self::TOLERANCE;
        // Проверяем, превышает ли разница порог
        return $difference < $threshold;
    }
}

