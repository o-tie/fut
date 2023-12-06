<?php

namespace controllers;

use core\BaseController;
use core\Controller;
use repositories\UserRepository;
use Exception;
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
     */
    public function login($request)
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
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }

        return $this->jsonResponse(['success' => $success, 'message' => $message, 'isNewUser' => $isNewUser]);
    }

    public function create($request)
    {
        try {
            $message = null;
            $success = false;

            $params = $request['params'] ?? null;

            if (empty($params['username']) || empty($params['password'])) {
                throw new Exception('Bad request!');
            }
            $params['password'] = md5($params['password']);
            if ($this->userRepo->create($params)) {
                $user = $this->userRepo->getOne($params['username']);
                $success = true;
                $_SESSION['user'] = $user['id'];
                $message = 'Акаунт успішно створений.';
            } else {
                $message = 'Щось пішло не так, акаунт не створений.';
            }
        } catch (Throwable $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }

        return $this->jsonResponse(['success' => $success, 'message' => $message]);
    }

    public function logout()
    {
        unset($_SESSION['user']);
        $this->render('login.index');
    }
}
