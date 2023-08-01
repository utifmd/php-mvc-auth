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
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
    }

    function testSaveSuccess()
    {
        $user = new User();
        $user->id = "utifmd";
        $user->name = "Utif Milkedori";
        $user->password = "121212";

        $this->userRepository->save($user);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
    }

    function testFindByIdIsNull()
    {
        $result = $this->userRepository->findById("notFound");
        self::assertNull($result);
    }
}
