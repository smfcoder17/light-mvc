<?php
namespace Core\Database;

use \PDO;

class DB {

    private $dbName;
    private $dbUser;
    private $dbPassword;
    private $dbHost;
    private $pdo;

    public function __construct(string $dbName, string $dbUser = 'root', string $dbPassword = 'mysql', string $dbHost = '127.0.0.1')
    {
        $this->dbName = $dbName;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
        $this->dbHost = $dbHost;
    }

    /**
     * @return PDO : the instance of pdo
     */
    private function getPDO() : PDO {
        if ($this->pdo === null) {
            $pdo = new PDO("mysql:dbname=". $this->dbName .";host=". $this->dbHost, $this->dbUser, $this->dbPassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            $this->pdo = $pdo;
        }
        return $this->pdo;
    }

    /**
     * Execute a query
     * @param string $statement : the query to be executed
     * @param string $class_name : the name of the class which the results will be return in
     * @param bool $fetchOnlyOne : set if the result should be only one object. default false
     * @return array|$class_name : one or an array of $class_name from the database 
     */
    public function query(string $statement, string $class_name, bool $fetchOnlyOne = false) {
        $query = $this->getPDO()->query($statement);
        $query->setFetchMode(PDO::FETCH_CLASS, $class_name);
        if (!$fetchOnlyOne)
            $res = $query->fetchAll();
        else
            $res = $query->fetch();
        return $res;
    }

    /**
     * Prepare and execute a query
     * @param string $statement : the query to be prepared & execute
     * @param array $attributes : list of attributes
     * @param string $class_name : the name of the class which the results will be return in
     * @param bool $fetchOnlyOne : set if the result should be only one object. default false
     * @return array|$class_name : one or an array of $class_name from the database 
     */
    public function prepare(string $statement,array $attributes, string $class_name, bool $fetchOnlyOne = false)
    {
        $query = $this->getPDO()->prepare($statement);
        $query->execute($attributes);
        $query->setFetchMode(PDO::FETCH_CLASS, $class_name);
        if (!$fetchOnlyOne)
            $res = $query->fetchAll();
        else
            $res = $query->fetch();
        return $res;
    }

}


?>