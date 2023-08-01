<?php

function getDatabaseConfig(): array
{
    return [
        "database" => [
            "test" => [
                "url" => "pgsql:host=0.0.0.0;port=5432;dbname=php_mvc_dev_db_test",
                "username" => "postgres",
                "password" => "9809poiiop"
            ],
            "prod" => [
                "url" => "pgsql:host=0.0.0.0;port=5432;dbname=php_mvc_dev_db",
                "username" => "postgres",
                "password" => "9809poiiop"
            ]
        ]
    ];
}
