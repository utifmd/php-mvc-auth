<?php

namespace DudeGenuine\PHP\MVC\Controller;

use DudeGenuine\PHP\MVC\App\View;
use DudeGenuine\PHP\MVC\Config\Database;
use DudeGenuine\PHP\MVC\Exception\ValidationException;
use DudeGenuine\PHP\MVC\Model\UserLoginRequest;
use DudeGenuine\PHP\MVC\Model\UserRegisterRequest;
use DudeGenuine\PHP\MVC\Repository\UserRepository;
use DudeGenuine\PHP\MVC\Service\UserService;

class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $userRepository = new UserRepository(Database::getConnection());
        $this->userService = new UserService($userRepository);
    }

    function viewRegister(): void
    {
        $model = [
            "title" => "Register",
            "content" => "Register screen"
        ];
        View::render('User/register', $model);
    }

    function submitRegister(): void
    {
        try {
            $userRegisterRequest = new UserRegisterRequest(
                id: $_POST['id'], name: $_POST['name'], password: $_POST['password']
            );
            $this->userService->register($userRegisterRequest);
            View::redirect('/users/login');

        } catch (\Exception $exception) {

            $model = [
                "title" => "Register",
                "error" => $exception->getMessage()
            ];
            View::render('User/register', $model);
        }
    }

    function viewLogin(): void
    {
        $model = [
            "title" => "Login",
            "error" => "Login screen"
        ];
        View::render('User/login', $model);
    }

    function submitLogin(): void
    {
        try {
            $userLoginRequest = new UserLoginRequest(
                id: $_POST['id'], password: $_POST['password']
            );
            $this->userService->login($userLoginRequest);
            View::redirect('/');

        } catch (ValidationException $exception) {
            View::render('User/login', [
                "title" => "Login",
                "error" => $exception->getMessage()
            ]);
        }
    }
}