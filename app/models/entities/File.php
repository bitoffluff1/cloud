<?php

namespace App\models\entities;


class File extends Entity
{
    public $columns = [
        "id" => "",
        "name" => "",
        "filename_extension" =>"",
        "id_user" => "",
        "path" =>"",
        "id_folder" => "",
        "mark" => ""
    ];
}