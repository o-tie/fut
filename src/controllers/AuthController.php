<?php

namespace controllers;

use core\BaseController;
use Exception;
use repositories\UserRepository;
use Throwable;

class AuthController extends BaseController
{
    protected UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    /**
     * @return void
     */
    public function index(): void
    {
        $this->render('login.index');
    }

    /**
     * @param $request
     * @return void
     */
    public function login($request): void
    {
        try {
            $message = null;
            $isNewUser = false;
            $success = false;

            $params = $request['params'] ?? null;

            if (empty($params['username']) || empty($params['password'])) {
                throw new Exception('Bad request!');
            }

            $user = $this->userRepo->getOne($params['username']);

            if (empty($user)) {
                $message = 'Схоже що ви новий користувач, створити акаунт?';
                $isNewUser = true;
            } else {
                $password = md5($params['password']);
                if ($user['password'] === $password) {
                    $_SESSION['user'] = $user['id'];
                    $success = true;
                } else {
                    $message = 'Невірний логін або пароль';
                }
            }
        } catch (Throwable $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }

        $this->jsonResponse(['success' => $success, 'message' => $message, 'isNewUser' => $isNewUser]);
    }

    public function create($request): void
    {
        try {
            $message = null;

            $params = $request['params'] ?? null;

            if (empty($params['username']) || empty($params['password'])) {
                throw new Exception('Bad request!');
            }
            $params['password'] = md5($params['password']);
            if ($this->userRepo->create($params)) {
                $user = $this->userRepo->getOne($params['username']);
                $_SESSION['user'] = $user['id'];
                $message = 'Акаунт успішно створений.';
            } else {
                $message = 'Щось пішло не так, акаунт не створений.';
            }
        } catch (Throwable $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }

        $this->jsonResponse(['success' => true, 'message' => $message]);
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        unset($_SESSION['user']);
        header("Location: /");
    }
}
