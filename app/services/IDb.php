<?php


namespace App\services;


interface IDb
{
    /**
     * Поиск конкретной записи
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function find(string $sql, array $params = []);

    /**
     * Поиск всех записей
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function findAll(string $sql, array $params = []) :array;
    public function execute(string $sql, array $params = []) :void;
}