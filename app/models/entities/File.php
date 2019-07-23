<?php

namespace App\models\entities;

/**
 * Class File
 * @package App\models\entities
 *
 * @property $id
 * @property $name
 * @property $id_user
 */
class File extends Entity
{
    public $columns = [
        "id" => "",
        "name" => "",
        "filename_extension" =>"",
        "id_user" => ""
    ];
}