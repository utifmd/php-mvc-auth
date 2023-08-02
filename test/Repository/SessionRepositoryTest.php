<?php

namespace DudeGenuine\PHP\MVC\Repository;

use DudeGenuine\PHP\MVC\Config\Database;
use DudeGenuine\PHP\MVC\Domain\Session;
use DudeGenuine\PHP\MVC\Domain\User;
use PHPUnit\Framework\TestCase;

class SessionRepositoryTest extends TestCase
{
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    protected function setUp(): void
    {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->sessionRepository = new SessionRepository($connection);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User(
            id: "utifmd", name: "Utif Milkedori", password: "121212"
        );
        $this->userRepository->save($user);
    }

    public function testSaveSuccess()
    {

        $session = new Session(
            id: "session_utifmd", userId: "utifmd"
        );
        $result = $this->sessionRepository->save($session);

        self::assertEquals($result->id, $session->id);
        self::assertEquals($result->userId, $session->userId);
    }

    public function testFailedSaveIncorrectFKUserId()
    {

        $this->expectException(\PDOException::class);

        $session = new Session(
            id: "session_utifmd", userId: "utid"
        );
        $result = $this->sessionRepository->save($session);
    }

    public function testDeleteByIdSuccess()
    {

        $session = new Session(
            id: "session_utifmd", userId: "utifmd"
        );
        $response = $this->sessionRepository->save($session);

        $this->sessionRepository->deleteById($response->id);

        $result = $this->sessionRepository->findById($response->id);

        self::assertNull($result);
    }

    public function testFindByIdNotFound()
    {

        $session = new Session(
            id: "session_utifmd", userId: "utifmd"
        );
        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findById("notFound");

        self::assertNull($result);
    }

}
