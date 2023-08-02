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
    }

    public function testSaveSuccess()
    {
        $user = new User(
            id: "utifmd", name: "Utif Milkedori", password: "121212"
        );
        $this->userRepository->save($user);

        $session = new Session(
            id: "session_utifmd", user_id: "utifmd"
        );
        $result = $this->sessionRepository->save($session);

        self::assertEquals($result->id, $session->id);
        self::assertEquals($result->user_id, $session->user_id);
    }

    public function testFailedSaveIncorrectFKUserId()
    {
        $user = new User(
            id: "utifmd", name: "Utif Milkedori", password: "121212"
        );
        $this->userRepository->save($user);

        $this->expectException(\PDOException::class);

        $session = new Session(
            id: "session_utifmd", user_id: "utid"
        );
        $result = $this->sessionRepository->save($session);
    }

    public function testDeleteByIdSuccess()
    {
        $user = new User(
            id: "utifmd", name: "Utif Milkedori", password: "121212"
        );
        $this->userRepository->save($user);

        $session = new Session(
            id: "session_utifmd", user_id: "utifmd"
        );
        $response = $this->sessionRepository->save($session);

        $this->sessionRepository->deleteById($response->id);

        $result = $this->sessionRepository->findById($response->id);

        self::assertNull($result);
    }

    public function testFindByIdNotFound()
    {
        $user = new User(
            id: "utifmd", name: "Utif Milkedori", password: "121212"
        );
        $this->userRepository->save($user);

        $session = new Session(
            id: "session_utifmd", user_id: "utifmd"
        );
        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findById("notFound");

        self::assertNull($result);
    }

}
