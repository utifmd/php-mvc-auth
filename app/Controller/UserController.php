<?php

namespace DudeGenuine\PHP\MVC\Controller;

use DudeGenuine\PHP\MVC\App\View;
use DudeGenuine\PHP\MVC\Config\Database;
use DudeGenuine\PHP\MVC\Exception\ValidationException;
use DudeGenuine\PHP\MVC\Model\UserLoginRequest;
use DudeGenuine\PHP\MVC\Model\UserRegisterRequest;
use DudeGenuine\PHP\MVC\Model\UserUpdateRequest;
use DudeGenuine\PHP\MVC\Repository\SessionRepository;
use DudeGenuine\PHP\MVC\Repository\UserRepository;
use DudeGenuine\PHP\MVC\Service\SessionService;
use DudeGenuine\PHP\MVC\Service\UserService;

class UserController
{
    private UserService $userService;
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);

        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
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
            "content" => "Login screen"
        ];
        View::render('User/login', $model);
    }

    function submitLogin(): void
    {
        try {
            $userLoginRequest = new UserLoginRequest(
                id: $_POST['id'], password: $_POST['password']
            );
            $userResponse = $this->userService->login($userLoginRequest);

            $this->sessionService->create($userResponse->id);

            View::redirect('/');

        } catch (ValidationException $exception) {
            View::render('User/login', [
                "title" => "Login",
                "error" => $exception->getMessage()
            ]);
        }
    }

    function viewProfile(): void
    {
        $user = $this->sessionService->current();
        if ($user == null) {
            View::redirect('/users/login');
        }
        $model = [
            "title" => "Profile",
            "content" => "Profile screen",
            "user" => $user,
        ];
        View::render('User/profile', $model);
    }

    function updateProfile(): void
    {
        try {
            $user = $this->sessionService->current();
            $request = new UserUpdateRequest(
                id: $user->id, name: $_POST['name'], password: $user->password
            );
            $request->password = $user->password;

            $this->userService->update($request);

            View::redirect('/');
        } catch (\Exception $exception) {

            $model = [
                "title" => "Change Profile",
                "content" => "Change Profile Screen",
                "error" => $exception->getMessage()
            ];
            View::render('User/profile', $model);
        }
    }

    function logout(): void
    {
        $this->sessionService->destroy();
        View::redirect('/');
    }
}