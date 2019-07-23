<?php

//пространство имен - это способ инкапсуляции элементов
namespace App\models\repositories;

use App\main\App;
use App\models\entities\Entity;

abstract class Repository implements IRepository//abstract от этого класса нельзя создать объект
{
    protected $db;

    public function __construct()
    {
        $this->db = App::call()->db;
    }

    //указан, чтобы обязательно был у всех наследников, абстрактный потому что нет тела функции
    abstract protected function getEntityClass();

    /**
     * Получение конкретной записи
     *
     * @param $id
     * @return object
     */
    public function getOne($id)
    {
        $table = $this->getTableName();
        $sql = "SELECT * FROM {$table} WHERE id = :id";
        return $this->db->getObject($sql, $this->getEntityClass(), [":id" => $id]);
    }

    /**
     * Получение всех записей
     *
     * @return array
     */
    public function getAll($id, string $sql = "")
    {
        if (empty($sql)) {
            $table = $this->getTableName();
            $sql = "SELECT * FROM {$table} WHERE id_user = :id";
        }

        return $this->db->getObjects($sql, $this->getEntityClass(), [":id" => $id]);
    }

    public function delete(Entity $entity)
    {
        $table = $this->getTableName();
        $sql = "DELETE FROM {$table} WHERE id = :id";
        $this->db->execute($sql, [":id" => $entity->id]);
    }

    protected function insert(Entity $entity)
    {
        $columns = [];
        $params = [];
        foreach ($entity->columns as $key => $value) {
            if (!empty($value)) {
                $columns[] = $key;
                $params[":{$key}"] = $value;
            }
        }
        $columns = implode(", ", $columns);
        $placeholders = implode(", ", array_keys($params));

        $table = $this->getTableName();
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->db->execute($sql, $params);
        $entity->id = (integer)$this->db->getLastId();
    }

    protected function update(Entity $entity)
    {
        $array = [];
        foreach ($entity->columns as $key => $value) {
            if ($key === "id") {
                continue;
            }
            if (empty($value)) {
                continue;
            }
            $str = "{$key} = '{$value}'";
            $array[] = $str;
        }
        $str = implode(",", $array);
        $table = $this->getTableName();

        $sql = "UPDATE {$table} SET {$str} WHERE id = :id";
        $this->db->execute($sql, [":id" => $entity->id]);
    }

    public function save(Entity $entity)
    {
        $entity->id ? $this->update($entity) : $this->insert($entity);
    }

    public function increaseViews($id)
    {
        $sql = "UPDATE gallery SET count = count + 1 WHERE id = :id";
        $this->db->execute($sql, [":id" => $id]);
    }

    public function newEntity(array $data)
    {
        $className = $this->getEntityClass();
        $entity = new $className();

        foreach ($data as $key => $value) {
            $entity->$key = $value;
        }

        return $entity;
    }
}