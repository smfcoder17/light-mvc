<?php
namespace Core;
use \PDO;

abstract class Model
{
    protected static $db = null;
    protected static $dbHost = '';
    protected static $dbPort = '';
    protected static $dbName = '';
    protected static $dbUser = '';
    protected static $dbPassword = '';

    protected static function getDB()
    {
        if (self::$db == null) {
            $dbName = self::$dbName;
            $dbHost = self::$dbHost;
            $dbPort = self::$dbPort;
            $dbCharset = 'utf8';
            
            $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;port=$dbPort;charset=$dbCharset", self::$dbUser, self::$dbPassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$db = $pdo;
        }
        return self::$db;
    }

    public static function setDbParams($dbHost = '', $dbPort = '', $dbName = '', $dbUser = '', $dbPassword = '')
    {
        self::$dbHost = $dbHost;
        self::$dbPort = $dbPort;
        self::$dbName = $dbName;
        self::$dbUser = $dbUser;
        self::$dbPassword = $dbPassword;
    }
}