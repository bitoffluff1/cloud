<?php


namespace App\traits;


trait TSingleton
{
    //используем паттерн "singleton"
    //Статические методы и свойства принадлежат классу, а не его экземплярам
    private static $item;

    //закрыли конструктор, не даем пользователю создать объект
    protected function __construct(){}
    protected function __clone(){}
    protected function __wakeup(){}

    public static function getInstance()
    {
        if (empty(static::$item)) {
            static::$item = new static();//создаем новый экземпляр класса
        }
        return static::$item;//возвращаем новый экземпляр класса
    }
}