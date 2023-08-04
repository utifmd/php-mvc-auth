<?php

namespace DudeGenuine\PHP\MVC\Service;

use DudeGenuine\PHP\MVC\Config\Database;
use DudeGenuine\PHP\MVC\Domain\User;
use DudeGenuine\PHP\MVC\Exception\ValidationException;
use DudeGenuine\PHP\MVC\Model\UserLoginRequest;
use DudeGenuine\PHP\MVC\Model\UserRegisterRequest;
use DudeGenuine\PHP\MVC\Model\UserResponse;
use DudeGenuine\PHP\MVC\Model\UserChangePasswordRequest;
use DudeGenuine\PHP\MVC\Model\UserUpdateRequest;
use DudeGenuine\PHP\MVC\Repository\UserRepository;

class UserService
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    function register(UserRegisterRequest $request): UserResponse
    {
        $this->validateRegisterRequest($request);
        try {
            Database::beginTransaction();
            $isUserExist = $this->repository->findById($request->id);

            if ($isUserExist != null)
                throw new ValidationException("User $request->id already exist.");

            $user = new User(
                id: $request->id,
                name: $request->name,
                password: password_hash($request->password, PASSWORD_BCRYPT)
            );
            $result = $this->repository->save($user);
            Database::commitTransaction();

            return new UserResponse(
                id: $result->id,
                name: $result->name,
                password: $result->password,
            );
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateRegisterRequest(UserRegisterRequest $request): void
    {
        if (trim($request->id) == "" || trim($request->name) == "" || trim($request->password) == "") {
            throw new ValidationException("Invalid input format");
        }
    }

    function login(UserLoginRequest $request): UserResponse
    {
        $this->validateLoginRequest($request);
        $user = $this->repository->findById($request->id);

        if ($user == null)
            throw new ValidationException('Login failed, user not found');

        if (!password_verify($request->password, $user->password))
            throw new ValidationException("Login failed, id or username does not match");

        return new UserResponse(
            id: $user->id, name: $user->name, password: $user->password
        );
    }

    private function validateLoginRequest(UserLoginRequest $request): void
    {
        if (trim($request->id) == "" || trim($request->password) == "") {
            throw new ValidationException("Invalid input format");
        }
    }

    function update(UserUpdateRequest $request): UserResponse
    {
        $this->validateUpdateRequest($request);
        try {
            Database::beginTransaction();
            $isUserExist = $this->repository->findById($request->id);

            if (!$isUserExist) {
                throw new ValidationException("Update failed, user not found.");
            }
            $user = new User(
                id: $request->id, name: $request->name, password: $request->password
            );
            $response = $this->repository->update($user);
            Database::commitTransaction();

            return new UserResponse(
                id: $response->id, name: $response->name, password: $response->password
            );
        } catch (\Exception $exception) {
            throw new ValidationException($exception->getMessage());
        }
    }

    private function validateUpdateRequest(UserUpdateRequest $request)
    {
        if (trim($request->id) == "" || trim($request->name) == "" || trim($request->password) == "") {
            throw new ValidationException("Invalid input format");
        }
    }

    function changePassword(UserChangePasswordRequest $request): UserResponse
    {
        $this->validateChangePassword($request);
        try {
            Database::beginTransaction();
            $user = $this->repository->findById($request->id);
            if ($user == null) {
                throw new ValidationException("User not found");
            }
            if (!password_verify($request->oldPassword, $user->password)) {
                throw new ValidationException("Old password is wrong");
            }
            $user->password = $request->newPassword;
            $this->repository->update($user);
            Database::commitTransaction();

            return new UserResponse(
                id: $request->id, name: $user->name, password: $user->password
            );
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw new ValidationException($exception->getMessage());
        }
    }

    private function validateChangePassword(UserChangePasswordRequest $request)
    {
        if (trim($request->id) == "" ||
            trim($request->newPassword) == "" ||
            trim($request->oldPassword) == "") {
            throw new ValidationException("Invalid input format");
        }
    }
}