<?php

namespace DudeGenuine\PHP\MVC\Repository;

use DudeGenuine\PHP\MVC\Domain\Session;

class SessionRepository
{
    private \PDO $connection;

    public function __construct(\PDO $pdo)
    {
        $this->connection = $pdo;
    }

    function save(Session $session): Session
    {
        $statement = $this->connection->prepare("INSERT INTO sessions(id, user_id) VALUES (?, ?)");
        $statement->execute([
            $session->id, $session->userId
        ]);
        return $session;
    }

    function findById(string $id): ?Session
    {
        $statement = $this->connection->prepare("SELECT id, user_id FROM sessions WHERE id = ?");
        $statement->execute([$id]);
        try {
            if ($row = $statement->fetch()) {
                return new Session(
                    id: $row['id'], userId: $row['user_id']
                );
            }
            return null;
        } finally {
            $statement->closeCursor();
        }
    }

    function deleteById(string $id): void
    {
        $statement = $this->connection->prepare("DELETE FROM sessions WHERE id = ?");
        $statement->execute([$id]);
    }

    function deleteAll(): void
    {
        $this->connection->exec("DELETE FROM sessions");
    }
}