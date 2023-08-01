<?php

namespace DudeGenuine\PHP\MVC\Config;

class Database
{
    public static ?\PDO $pdo = null;
    public static function getConnection(string $env = "test"): \PDO {
        if (self::$pdo == null) {
            require_once __DIR__ . '/../../config/database.php';
            $config = getDatabaseConfig();

            self::$pdo = new \PDO(
                $config['database'][$env]['url'],
                $config['database'][$env]['username'],
                $config['database'][$env]['password'],
            );
        }
        return self::$pdo;
    }
}