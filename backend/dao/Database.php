<?php

/**
 * Creates one shared PDO connection.
 * Credentials come from backend/config/config.php (not committed).
 */
class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $configPath = __DIR__ . '/../config/config.php';
            if (!file_exists($configPath)) {
                throw new RuntimeException(
                    'Missing backend/config/config.php. Copy config.example.php to config.php and fill in your values.'
                );
            }

            $config = require $configPath;
            $db = $config['db'];
            $dsn = 'mysql:host=' . $db['host'] . ';dbname=' . $db['name'] . ';charset=' . $db['charset'];

            self::$connection = new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                // update() and delete() report matched rows, even if nothing changed
                PDO::MYSQL_ATTR_FOUND_ROWS   => true,
            ]);
        }

        return self::$connection;
    }
}
