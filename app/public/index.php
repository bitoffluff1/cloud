<?php

const PUBLIC_DIR = __DIR__;
include($_SERVER['DOCUMENT_ROOT'] . '/../../vendor/autoload.php');

$config = include($_SERVER['DOCUMENT_ROOT'] . '/../main/config.php');
\App\main\App::call()->run($config);
