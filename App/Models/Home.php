<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;
use PDO;

class Home extends Model
{
    protected string $table = 'email-list';

    public static function saveUserContact(): ?array
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

    public static function userContactExist(mixed ...$checkers): bool
    {
        // TODO: Implement this method
        return false;
    }
}
