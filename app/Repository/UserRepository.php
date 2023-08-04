<?php

namespace DudeGenuine\PHP\MVC\Repository;

use DudeGenuine\PHP\MVC\Domain\User;
use DudeGenuine\PHP\MVC\Model\UserResponse;
use DudeGenuine\PHP\MVC\Model\UserChangePasswordRequest;

class UserRepository
{
    private \PDO $connection;

    function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    function save(User $user): User
    {
        $statement = $this->connection->prepare("INSERT INTO users(id, name, password) VALUES (?, ?, ?)");
        $statement->execute([
            $user->id, $user->name, $user->password
        ]);
        return $user;
    }

    function update(User $user): User
    {
        $statement = $this->connection->prepare("UPDATE users SET name = ?, password = ? WHERE id = ?");
        $statement->execute([
            $user->name, $user->password, $user->id
        ]);
        return $user;
    }

    function findById(string $id): ?User
    {
        $statement = $this->connection->prepare("SELECT id, name, password FROM users WHERE id = ?");
        $statement->execute([$id]);
        try {
            if ($row = $statement->fetch()) {
                return new User(
                    id: $row['id'],
                    name: $row['name'],
                    password: $row['password']
                );
            }
            return null;
        } finally {
            $statement->closeCursor();
        }
    }

    function changePassword(User $request): UserResponse
    {
        $statement = $this->connection->prepare("UPDATE users SET password = ? WHERE id = ?");
        $statement->execute([
            $request->password, $request->id
        ]);
        return new UserResponse(
            id: $request->id, name: $request->name, password: $request->password
        );
    }

    function deleteAll(): void
    {
        $this->connection->exec("DELETE FROM users");
    }
}