<?php

namespace DudeGenuine\PHP\MVC\Service;

require_once __DIR__ . '/../Helper/helper.php';
use DudeGenuine\PHP\MVC\Config\Database;
use DudeGenuine\PHP\MVC\Domain\User;
use DudeGenuine\PHP\MVC\Repository\SessionRepository;
use DudeGenuine\PHP\MVC\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class SessionServiceTest extends TestCase
{
    private SessionService $sessionService;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService(
            $this->sessionRepository, $userRepository
        );
        $this->sessionRepository->deleteAll();
        $userRepository->deleteAll();

        $user = new User(
            id: "utifmd", name: "Utif Milkedori", password: "121212"
        );
        $userRepository->save($user);
    }

    public function testCreateSuccess()
    {
        $session = $this->sessionService->create("utifmd");
        $this->expectOutputRegex("[". SessionService::COOKIE_NAME .": $session->id]");
    }

    public function testDestroySuccess()
    {
        $session = $this->sessionService->create("utifmd");

        $_COOKIE[SessionService::COOKIE_NAME] = $session->id;

        $this->sessionService->destroy();

        $this->expectOutputRegex("[". SessionService::COOKIE_NAME .": ]");

        $result = $this->sessionRepository->findById($session->id);

        self::assertNull($result);
    }

    public function testCurrentSuccess()
    {
        $session = $this->sessionService->create("utifmd");

        $_COOKIE[SessionService::COOKIE_NAME] = $session->id;

        $user = $this->sessionService->current();

        self::assertEquals($session->userId, $user->id);
    }
}
