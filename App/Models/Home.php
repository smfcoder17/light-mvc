<?php
namespace App\Models;

use Core\Model;
use \PDO;

class Home extends Model
{
    protected $table = 'email-list';

    public static function saveUserContact()
    {
        $db = self::getDB();
        // $query = "INSERT INTO";
        $stmt = $db->query("SELECT * FROM tests");
        $res = null;
        if ($stmt->rowCount() > 0) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $res;
    }

    public static function userContactExist(...$checkers)
    {
        if (isset($checkers)) {
            $query = 
        }
    }
}