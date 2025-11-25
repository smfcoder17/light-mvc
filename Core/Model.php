<?php

declare(strict_types=1);

namespace Core;

use \PDO;

abstract class Model
{
    protected static ?PDO $db = null;
    protected static string $dbHost = '';
    protected static string $dbPort = '';
    protected static string $dbName = '';
    protected static string $dbUser = '';
    protected static string $dbPassword = '';

    protected static function getDB(): PDO
    {
        if (self::$db === null) {
            $dbName = self::$dbName;
            $dbHost = self::$dbHost;
            $dbPort = self::$dbPort;
            $dbCharset = 'utf8mb4';

            $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;port=$dbPort;charset=$dbCharset", self::$dbUser, self::$dbPassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$db = $pdo;
        }
        return self::$db;
    }

    public static function setDbParams(string $dbHost = '', string $dbPort = '', string $dbName = '', string $dbUser = '', string $dbPassword = ''): void
    {
        self::$dbHost = $dbHost;
        self::$dbPort = $dbPort;
        self::$dbName = $dbName;
        self::$dbUser = $dbUser;
        self::$dbPassword = $dbPassword;
    }
}
