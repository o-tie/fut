<?php

namespace services;

class PlayerService
{
    /**
     * @param $player
     * @return mixed
     */
    public function setStats(&$player): object
    {
        $stats = $player->overallStats;
        foreach ($stats as $stat => $value) {
            $player->$stat = $value;
        }

        return $player;
    }
}