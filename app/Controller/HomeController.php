<?php

namespace DudeGenuine\PHP\MVC\Controller;

use DudeGenuine\PHP\MVC\App\View;
use DudeGenuine\PHP\MVC\Config\Database;
use DudeGenuine\PHP\MVC\Repository\SessionRepository;
use DudeGenuine\PHP\MVC\Repository\UserRepository;
use DudeGenuine\PHP\MVC\Service\SessionService;

class HomeController
{
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }
    function view(): void
    {
        $signedInUser = $this->sessionService->current();
        if ($signedInUser == null) {

            $model = [
                "title" => "Home",
                "content" => "Guest screen"
            ];
            View::render('Home/index', $model);
            return;
        }
        $model = [
            "title" => "Dashboard",
            "content" => "Dashboard screen",
            "user" => [
                "id" => $signedInUser->id,
                "name" => $signedInUser->name,
            ]
        ];
        View::render('Home/dashboard', $model);
    }
}