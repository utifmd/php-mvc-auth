<?php

namespace DudeGenuine\PHP\MVC\Repository;

use DudeGenuine\PHP\MVC\Config\Database;
use DudeGenuine\PHP\MVC\Domain\User;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $connection = Database::getConnection();

        $sessionRepository = new SessionRepository($connection);
        $sessionRepository->deleteAll();

        $this->userRepository = new UserRepository($connection);
        $this->userRepository->deleteAll();
    }

    function testSaveSuccess()
    {
        $user = new User(
            id: "utifmd",
            name: "Utif Milkedori",
            password: "121212"
        );
        $this->userRepository->save($user);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
    }

    public function testUpdateSuccess()
    {
        $user = new User(
            id: "utifmd", name: "Utif Milkedori", password: password_hash("121212", PASSWORD_BCRYPT)
        );
        $response = $this->userRepository->save($user);

        $newUser = new User(
            id: "utifmd", name: "Tom Cruise", password: password_hash("131313", PASSWORD_BCRYPT)
        );
        $updatedResponse = $this->userRepository->update($newUser);

        self::assertEquals($newUser->id, $updatedResponse->id);
        self::assertEquals($newUser->name, $updatedResponse->name);
        self::assertEquals($newUser->password, $updatedResponse->password);
    }

    function testFindByIdIsNull()
    {
        $result = $this->userRepository->findById("notFound");
        self::assertNull($result);
    }
}
