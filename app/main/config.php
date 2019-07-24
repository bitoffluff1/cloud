<?php

return [
    "name" => "Cloud",
    "defaultAction" => "auth",

    "components" => [
        "db" => [
            "class" => \App\services\Db::class,
            "config" => [
                "driver" => "mysql",
                "db" => "cloud",
                "host" => "localhost",
                "user" => "root",
                "password" => "",
                "charset" => "utf8"
            ],
        ],
        "render" => [
            "class" => \App\services\renders\TwigRender::class,
        ],
        "fileServices" => [
            "class" => \App\services\FileServices::class,
        ],
        "userRepository" => [
            "class" => \App\models\repositories\UserRepository::class,
        ],
        "fileRepository" => [
            "class" => \App\models\repositories\FileRepository::class,
        ],
        "folderRepository" => [
            "class" => \App\models\repositories\FolderRepository::class,
        ],
        "folderServices" => [
            "class" => \App\services\FolderServices::class,
        ],
        "authServices" => [
            "class" => \App\services\AuthServices::class,
        ],
    ],
];
