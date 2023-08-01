<?php

namespace DudeGenuine\PHP\MVC\Service;

use DudeGenuine\PHP\MVC\Config\Database;
use DudeGenuine\PHP\MVC\Exception\ValidationException;
use DudeGenuine\PHP\MVC\Model\UserRegisterRequest;
use DudeGenuine\PHP\MVC\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private UserService $userService;

    protected function setUp(): void
    {
        $userRepository = new UserRepository(Database::getConnection());
        $this->userService = new UserService($userRepository);
        $userRepository->deleteAll();
    }

    function testUserRegisterSuccess()
    {
        $userRequest = new UserRegisterRequest(
            id: "utifmd", name: "Utif Milkedori", password: "121212"
        );
        $userResponse = $this->userService->register($userRequest);

        self::assertEquals($userResponse->id, $userRequest->id);
        self::assertEquals($userResponse->name, $userRequest->name);
        self::assertNotEquals($userResponse->password, $userRequest->password);

        self::assertTrue(password_verify($userRequest->password, $userResponse->password));
    }

    function testRegisterFailed()
    {
        $this->expectException(\Exception::class);

        $userRequest = new UserRegisterRequest(
            id: "", name: "", password: ""
        );
        $this->userService->register($userRequest);
    }

    function testRegisterFailedDuplicate()
    {
        $userRequest1 = new UserRegisterRequest(
            id: "utifmd", name: "Utif Milkedori", password: "121212"
        );
        $userRequest2 = new UserRegisterRequest(
            id: "utifmd", name: "Utif Milkedori", password: "121212"
        );
        $this->userService->register($userRequest1);

        $this->expectException(ValidationException::class);

        $this->userService->register($userRequest2);
    }
}
