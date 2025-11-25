<?php

declare(strict_types=1);

namespace Core;

use \PDO;

/**
 * Model abstract class - Base model for database operations
 * 
 * Provides database connection management with lazy initialization.
 * All application models should extend this class.
 * 
 * @package Core
 * @author Light-MVC
 * @version 2.0.0
 */
abstract class Model
{
    /** @var PDO|null Singleton PDO database connection */
    protected static ?PDO $db = null;
    
    /** @var string Database host */
    protected static string $dbHost = '';
    
    /** @var string Database port */
    protected static string $dbPort = '';
    
    /** @var string Database name */
    protected static string $dbName = '';
    
    /** @var string Database username */
    protected static string $dbUser = '';
    
    /** @var string Database password */
    protected static string $dbPassword = '';

    /**
     * Get database connection (singleton pattern)
     * Creates connection on first call with configured parameters
     * 
     * @return PDO Database connection instance
     * @throws \PDOException If connection fails
     */
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

    /**
     * Set database connection parameters
     * Should be called during application initialization
     * 
     * @param string $dbHost Database host (default: '')
     * @param string $dbPort Database port (default: '')
     * @param string $dbName Database name (default: '')
     * @param string $dbUser Database username (default: '')
     * @param string $dbPassword Database password (default: '')
     * @return void
     */
    public static function setDbParams(string $dbHost = '', string $dbPort = '', string $dbName = '', string $dbUser = '', string $dbPassword = ''): void
    {
        self::$dbHost = $dbHost;
        self::$dbPort = $dbPort;
        self::$dbName = $dbName;
        self::$dbUser = $dbUser;
        self::$dbPassword = $dbPassword;
    }
}
