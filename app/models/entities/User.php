<?php

namespace App\models\entities;

/**
 * Class User
 * @package App\models\entities
 *
 * @property $id
 * @property $login
 * @property $password
 * @property $fio
 */
class User extends Entity
{
    public $columns = [
        "id" => "",
        "login" => "",
        "password" => "",
        "fio" => ""
    ];
}