<?php


namespace App\models\repositories;


interface IRepository
{
    /**
     * Данный метод должен вернуть имя таблицы
     *
     * @return string
     */
    public function getTableName(): string;
}