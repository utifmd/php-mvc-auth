<?php

namespace DudeGenuine\PHP\MVC\Service;

use DudeGenuine\PHP\MVC\Config\Database;
use DudeGenuine\PHP\MVC\Domain\User;
use DudeGenuine\PHP\MVC\Exception\ValidationException;
use DudeGenuine\PHP\MVC\Model\UserChangePasswordRequest;
use DudeGenuine\PHP\MVC\Model\UserLoginRequest;
use DudeGenuine\PHP\MVC\Model\UserRegisterRequest;
use DudeGenuine\PHP\MVC\Model\UserResponse;
use DudeGenuine\PHP\MVC\Model\UserUpdateRequest;
use DudeGenuine\PHP\MVC\Repository\SessionRepository;
use DudeGenuine\PHP\MVC\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private UserService $userService;

    protected function setUp(): void
    {
        $connection = Database::getConnection();

        $sessionRepository = new SessionRepository($connection);
        $sessionRepository->deleteAll();

        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);
        $userRepository->deleteAll();
    }

    private function setUpRegister(): array
    {
        $userRequest = new UserRegisterRequest(
            id: "utifmd", name: "Utif Milkedori", password: "121212"
        );
        $userResponse = $this->userService->register($userRequest);
        return [
            "request" => $userRequest, "response" => $userResponse
        ];
    }

    function testUserRegisterSuccess()
    {
        $registered = $this->setUpRegister();
        $userRequest = $registered["request"];
        $userResponse = $registered["response"];

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
        $this->setUpRegister();

        $userRequest2 = new UserRegisterRequest(
            id: "utifmd", name: "Utif Milkedori", password: "121212"
        );
        $this->expectException(ValidationException::class);

        $this->userService->register($userRequest2);
    }

    function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);

        $userLoginRequest = new UserLoginRequest(id: "utifmd", password: "121212");

        $this->userService->login($userLoginRequest);
    }

    function testLoginWrongPassword()
    {
        $this->setUpRegister();

        $userLoginRequest = new UserLoginRequest(
            id: "utifmd",
            password: "wrongPassword"
        );
        $this->expectException(ValidationException::class);

        $userResponse = $this->userService->login($userLoginRequest);

        self::assertFalse(password_verify($userLoginRequest->password, $userResponse->password));
    }

    function testLoginSuccess()
    {
        $this->setUpRegister();

        $userLoginRequest = new UserLoginRequest(
            id: "utifmd",
            password: "121212"
        );
        $userResponse = $this->userService->login($userLoginRequest);

        self::assertEquals($userLoginRequest->id, $userResponse->id);
        self::assertTrue(password_verify($userLoginRequest->password, $userResponse->password));
    }

    public function testUpdateSuccess()
    {
        $response = $this->setUpRegister()["response"];
        $user = new UserUpdateRequest(
            id: $response->id, name: "Brad pitt", password: "131313"
        );
        $userResponse = $this->userService->update($user);

        self::assertEquals($userResponse->id, $user->id);
        self::assertEquals($userResponse->name, $user->name);
        self::assertEquals($userResponse->password, $user->password);
    }

    public function testUpdateFailed()
    {
        $this->setUpRegister();

        $this->expectException(ValidationException::class);

        $user = new UserUpdateRequest(
            id: "utif", name: "Tom Cruise", password: "151515"
        );
        $userResponse = $this->userService->update($user);

        self::assertEquals($userResponse->id, $user->id);
        self::assertEquals($userResponse->name, $user->name);
        self::assertEquals($userResponse->password, $user->password);
    }

    public function testChangeUserPasswordSuccess()
    {
        $setUpRegister = $this->setUpRegister();
        $response = $setUpRegister["response"];

        $changePasswordRequest = new UserChangePasswordRequest(
            id: $response->id, oldPassword: "121212", newPassword: "414141"
        );
        $userResponse = $this->userService->changePassword($changePasswordRequest);

        self::assertTrue(password_verify('414141', $userResponse->password));
    }

    public function testChangeUserOldPasswordWrongFailed()
    {
        $setUpRegister = $this->setUpRegister();
        $response = $setUpRegister["response"];

        $this->expectException(ValidationException::class);

        $changePasswordRequest = new UserChangePasswordRequest(
            id: $response->id, oldPassword: "121211", newPassword: "414141"
        );
        $this->userService->changePassword($changePasswordRequest);
    }

    public function testInvalidInputUpdate()
    {
        $registered = $this->setUpRegister();
        $userResponse = $registered['response'];

        $this->expectException(ValidationException::class);

        $user = new UserUpdateRequest(
            id: $userResponse->id, name: "", password: ""
        );
        $this->userService->update($user);
    }
}
