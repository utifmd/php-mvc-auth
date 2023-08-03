<?php

namespace DudeGenuine\PHP\MVC\Controller;

use DudeGenuine\PHP\MVC\Config\Database;
use DudeGenuine\PHP\MVC\Domain\Session;
use DudeGenuine\PHP\MVC\Domain\User;
use DudeGenuine\PHP\MVC\Repository\SessionRepository;
use DudeGenuine\PHP\MVC\Repository\UserRepository;
use DudeGenuine\PHP\MVC\Service\SessionService;
use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase
{
    private HomeController $homeController;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->homeController = new HomeController();
        $connection = Database::getConnection();
        $this->sessionRepository = new SessionRepository($connection);
        $this->userRepository = new UserRepository($connection);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testViewGuest()
    {
        $this->homeController->view();

        $this->expectOutputRegex("[Login Management]");
        $this->expectOutputRegex("[Utif Milkedori]");
        $this->expectOutputRegex("[Login]");
        $this->expectOutputRegex("[Register]");
    }
    public function testViewDashboard()
    {
        $user = new User(
            id: "utifmd", name: "Utif Milkedori", password: "121212"
        );
        $this->userRepository->save($user);

        $session = new Session(
            id: uniqid(), userId: $user->id
        );
        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::COOKIE_NAME] = $session->id;

        $this->homeController->view();

//        $this->expectOutputRegex("[Dashboard]");
        $this->expectOutputRegex("[Hello]");
//        $this->expectOutputRegex("[Profile]");
//        $this->expectOutputRegex("[Password]");
//        $this->expectOutputRegex("[Utif Milkedori]");
    }
}
