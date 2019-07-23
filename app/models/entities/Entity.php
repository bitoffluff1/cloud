<?php


namespace App\models\entities;

/**
 * Class Entity
 * @package App\models\entities
 *
 * @property $id
 */
abstract class Entity
{
    public $columns = [];

    public function __get($name)
    {
        if (array_key_exists($name, $this->columns)) {
            return $this->columns[$name];
        }
        return false;
    }

    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->columns)) {
            $this->columns[$name] = $value;
        }
    }
}