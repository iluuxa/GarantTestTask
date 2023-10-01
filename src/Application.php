<?php

namespace App;

use PDO;

class Application
{
    private static PDO $connection;

    public static function getConnection(): PDO
    {
        return self::$connection;
    }

    public static function setConnection(PDO $connection): void
    {
        self::$connection = $connection;
    }
}