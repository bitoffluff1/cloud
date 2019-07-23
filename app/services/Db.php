<?php

namespace App\services;

class Db implements IDb
{
    /**
     * @var \PDO
     */
    protected $connection = null;

    private $config = [];

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Подключение к базе данных
     *
     * @return \PDO
     */
    private function getConnection()
    {
        if (empty($this->connection)) {
            $this->connection = new \PDO(
                $this->getDsn(),
                $this->config["user"],
                $this->config["password"]
            );
            $this->connection->setAttribute(
                \PDO::ATTR_DEFAULT_FETCH_MODE,
                \PDO::FETCH_ASSOC
            );
        }
        return $this->connection;
    }

    private function getDsn()
    {
        return sprintf("%s:host=%s;dbname=%s;charset=%s",
            $this->config["driver"],
            $this->config["host"],
            $this->config["db"],
            $this->config["charset"]
        );
    }

    /**
     * Выполнение запроса к БД
     *
     * @param string $sql Пример: SELECT * FROM users WHERE id = :id
     * @param array $params Пример: [":id" => 2]
     * @return bool|\PDOStatement
     */
    private function query(string $sql, array $params = [])
    {
        //Подготавливает запрос к выполнению и возвращает связанный с этим запросом объект
        $PDOStatement = $this->getConnection()->prepare($sql);
        //Выполнение подготовленного запроса с передачей массива входных значений
        $PDOStatement->execute($params);
        return $PDOStatement;
    }

    /**
     * Поиск конкретной записи
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function find(string $sql, array $params = [])
    {
        //Извлекает следующую строку из результирующего набора объекта PDOStatement
        return $this->query($sql, $params)->fetch();
    }

    public function getObject(string $sql, string $class, array $params = [])
    {
        $PDOStatement = $this->query($sql, $params);
        //Устанавливаем режим выборки по умолчанию для объекта запроса
        $PDOStatement->setFetchMode(\PDO::FETCH_CLASS, $class);
        //Извлечение следующей строки из результирующего набора
        return $PDOStatement->fetch();
    }

    /**
     * Поиск всех записей
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function findAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function getObjects(string $sql, string $class, array $params = [])
    {
        $PDOStatement = $this->query($sql, $params);
        $PDOStatement->setFetchMode(\PDO::FETCH_CLASS, $class);
        return $PDOStatement->fetchAll();
    }

    /**
     *
     *
     * @param string $sql
     * @param array $params
     */

    //void означает что функция не должна ничего возвращать
    public function execute(string $sql, array $params = []): void
    {
        $this->query($sql, $params);
    }

    /**
     * Возвращает ID последней вставленной строки в БД
     *
     * @return string
     */
    public function getLastId()
    {
        return $this->getConnection()->lastInsertId();
    }
}