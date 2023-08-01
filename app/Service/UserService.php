<?php

namespace DudeGenuine\PHP\MVC\Service;

use DudeGenuine\PHP\MVC\Config\Database;
use DudeGenuine\PHP\MVC\Domain\User;
use DudeGenuine\PHP\MVC\Exception\ValidationException;
use DudeGenuine\PHP\MVC\Model\UserRegisterRequest;
use DudeGenuine\PHP\MVC\Model\UserRegisterResponse;
use DudeGenuine\PHP\MVC\Repository\UserRepository;

class UserService
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws ValidationException
     */
    function register(UserRegisterRequest $request): UserRegisterResponse
    {
        $this->validateRequest($request);
        try {
            Database::beginTransaction();
            $isUserExist = $this->repository->findById($request->id);
            if ($isUserExist != null) throw new ValidationException("User $request->id already exist.");

            $user = new User(
                id: $request->id,
                name: $request->name,
                password: password_hash($request->password, PASSWORD_BCRYPT)
            );
            $result = $this->repository->save($user);
            Database::commitTransaction();

            return new UserRegisterResponse(
                id: $result->id,
                name: $result->name,
                password: $result->password,
            );
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }
    private function validateRequest(UserRegisterRequest $request): void
    {
        if (trim($request->id) == "" || trim($request->name) == "" || trim($request->password) == ""){
            throw new ValidationException("Invalid input format");
        }
    }
}