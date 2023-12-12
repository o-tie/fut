<?php

namespace services;

class PlayerService
{
    public function setStats(&$player)
    {
        $stats = $player->overallStats;
        foreach ($stats as $stat => $value) {
            $player->$stat = $value;
        }
        return $player;
    }
}