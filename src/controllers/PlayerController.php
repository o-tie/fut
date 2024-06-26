<?php

namespace controllers;

use core\Controller;
use repositories\PlayerRepository;
use services\PlayerService;
use services\StatService;

class PlayerController extends Controller
{
    protected PlayerRepository $playerRepo;
    protected StatService $statService;
    protected PlayerService $playerService;
    public function __construct()
    {
        parent::__construct();

        $this->playerRepo = new PlayerRepository();
        $this->statService = new StatService();
        $this->playerService = new PlayerService();
    }

    /**
     * @return void
     */
    public function index(): void
    {
        $this->render('players.index');
    }

    /**
     * @param $params
     * @return void
     */
    public function getPlayers($params): void
    {
        try {
            if (isset($params['id'])) {
                $id = $params['id'];
                $players = $this->playerRepo->getOne($id);
            } else {
                $players = $this->playerRepo->getAllByUser($_SESSION['user']);
            }

            foreach ($players as &$player) {
                $player->votes = $this->statService->getVotes($player->id);
                $overall = $this->statService->getOverall($player->id);
                $player->overall = $overall['overall'];
                $player->overallStats = $overall['overallStats'];
                $player->overallUser = $this->statService->getOverallUser($player->stats);
                $player->corrections = $this->statService->getWeekCorrections($player->id);
                $player->lastCorrection = $this->statService->getLastCorrection($player->id);
                $player->updateStatus = $this->statService->getUpdateStatus($player->id);

                $this->playerService->setStats($player);
            }

            $this->jsonResponse(['success' => true, 'records' => $players]);
        } catch (\Throwable $e) {
            $this->jsonResponse(['success' => false, 'records' => [], 'message' => $e->getMessage()]);
        }
    }

    /**
     * @param $params
     * @return void
     */
    public function update($params): void
    {
        $errors = $this->statService->validateStats($params['stats']);

        if (!empty($errors)) {
            $this->jsonResponse(['success' => false, 'errors' => $errors]);
        }

        $result = $this->statService->updatePlayerStats($params);

        $this->jsonResponse(['success' => $result]);
    }
}
