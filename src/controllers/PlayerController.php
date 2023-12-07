<?php

namespace controllers;

use core\Controller;
use repositories\PlayerRepository;
use services\StatService;

class PlayerController extends Controller
{
    protected PlayerRepository $playerRepo;
    protected StatService $statService;
    public function __construct()
    {
        parent::__construct();

        $this->playerRepo = new PlayerRepository();
        $this->statService = new StatService();
    }

    /**
     * @return void
     */
    public function index(): void
    {
        $this->render('players.index');
    }

    public function getPlayers($params)
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
                $player->overall = $this->statService->getOverall($player->id);
                $player->overallUser = $this->statService->getOverallUser($player->stats);
            }

            $this->jsonResponse(['success' => true, 'records' => $players]);
        } catch (\Throwable $e) {
            $this->jsonResponse(['success' => false, 'records' => [], 'message' => $e->getMessage()]);
        }
    }

    public function update($params)
    {
        $errors = $this->statService->validateStats($params['stats']);

        if (!empty($errors)) {
            $this->jsonResponse(['success' => false, 'errors' => $errors]);
        }

        $result = $this->statService->updatePlayerStats($params);


        $this->jsonResponse(['success' => $result]);
    }
}