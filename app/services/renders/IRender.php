<?php


namespace App\services\renders;


interface IRender
{
    public function render($template, $params);
}