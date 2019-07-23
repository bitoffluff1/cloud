<?php

namespace App\models\repositories;

use App\models\entities\User;

class UserRepository extends Repository
{
    public function getTableName(): string
    {
        return "users";
    }

    protected function getEntityClass()
    {
        return User::class;
    }

    public function getUser($login)
    {
        $table = $this->getTableName();
        $sql = "SELECT * FROM {$table} WHERE login= :login";
        return $this->db->getObject($sql, $this->getEntityClass(), [":login" => $login]);
    }
}