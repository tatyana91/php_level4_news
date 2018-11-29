<?php
/**
 * Интерфейс для работы с базой данных
 */
interface IDB{
    /**
     * Выполнить запрос
     *
     * @param string $sql - запрос
     */
    function exec($sql);

    /**
     * Выполнить запрос
     *
     * @param string $sql - запрос
     */
    function query($sql);

    /**
     * Извлечь все строки из результирующего набора объекта PDOStatement в массив
     *
     * @param PDOStatement $data - выборка данных
     * @param int $type - константа, определяющая в каком виде следующая строка будет возвращена (например PDO::FETCH_ASSOC)
     */
    function fetch($data, $type = PDO::FETCH_ASSOC);

    /**
     * Подготавливает запрос к выполнению и возвращает связанный с этим запросом объект
     *
     * @param string $sql - запрос
     */
    function prepare($sql);
}
