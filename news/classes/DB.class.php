<?php
/**
 * class DB
 * Содержит основные методы для работы с базой данных
 * Реализует интерфейс IDB
 */
class DB implements IDB{
    private const DB_NAME = '../news.db';
    private $_db;
    private $logger;

    /**
     * Соединие с базой, создание базы, создание экземпляра класса Logger
     *
     * @return void
     */
    function __construct(){
        $this->logger = Logger::getInstance();

        try {
            $this->_db = new PDO('sqlite:' . self::DB_NAME);
            $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (!file_exists(self::DB_NAME) || !filesize(self::DB_NAME)) {
                $sql = "CREATE TABLE msgs(
							id INTEGER PRIMARY KEY AUTOINCREMENT,
							title TEXT,
							category INTEGER,
							description TEXT,
							text TEXT,
							source TEXT,
							datetime INTEGER)";
                $this->_db->exec($sql);

                $sql = "CREATE TABLE category(
							id INTEGER PRIMARY KEY AUTOINCREMENT,
							name TEXT)";
                $this->_db->exec($sql);

                $sql = "INSERT INTO category(name)
							SELECT 'Политика' as name
							UNION SELECT 'Культура' as name
							UNION SELECT 'Спорт' as name";
                $this->_db->exec($sql);
            }
        }
        catch (PDOException $e){
            $error = "\"".implode(',', $this->_db->errorInfo())."\" in file ".$e->getFile()." on line ".$e->getLine();
            $this->logger->writeLog($error);
            die('Не удалось создать таблицы в базе данных');
        }
    }

    /**
     * Получить свойство класса
     *
     * @params string $name - имя свойства
     *
     * @throws Exception
     */
    function __get($name){
        throw new Exception("Ошибка доступа к свойству $name!");
    }

    /**
     * Установить свойство класса
     *
     * @params string $name - имя свойства
     * @params mixed $value - значение свойства
     *
     * @throws Exception
     */
    function __set($name, $value){
        throw new Exception("Ошибка доступа к свойству $name!");
    }

    /**
     * Удалить объект PDO
     *
     * @return void
     */
    function __destruct(){
        unset($this->_db);
    }

    /**
     * Выполнить запрос
     *
     * @param string $sql - запрос
     *
     * @return int $result - количество затронутых строк
     */
    function exec($sql){
        try {
            $result = $this->_db->exec($sql);
        }
        catch (PDOException $e){
            $error = "\"".implode(',', $this->_db->errorInfo())."\" in file ".$e->getFile()." on line ".$e->getLine();
            $this->logger->writeLog($error);
            die('Не удалось выполнить запрос');
        }

        return $result;
    }

    /**
     * Выполнить запрос
     *
     * @param string $sql - запрос
     *
     * @return PDOStatement $result - результирующий набор в виде объекта PDOStatement
     */
    function query($sql){
        try {
            $result = $this->_db->query($sql);
        }
        catch (PDOException $e){
            $error = "\"".implode(',', $this->_db->errorInfo())."\" in file ".$e->getFile()." on line ".$e->getLine();
            $this->logger->writeLog($error);
            die('Не удалось выполнить запрос');
        }

        return $result;
    }

    /**
     * Извлечь все строки из результирующего набора объекта PDOStatement в массив
     *
     * @param PDOStatement $data - выборка данных
     * @param int $type - константа, определяющая в каком виде следующая строка будет возвращена (например PDO::FETCH_ASSOC)
     *
     * @return mixed $result - результирующий набор в виде, которые зависит от передаваемого $type
     */
    function fetch($data, $type = PDO::FETCH_ASSOC){
        return $data->fetch($type);
    }

    /**
     * Подготавливает запрос к выполнению и возвращает связанный с этим запросом объект
     *
     * @param string $sql - запрос
     *
     * @return PDOStatement $result - связанный с подготовленным запросом объект
     */
    function prepare($sql){
        return $this->_db->prepare($sql);
    }

    /**
     * Подготавливает запрос к выполнению и возвращает связанный с этим запросом объект
     *
     * @param PDOStatement $data - результирующий набор запрос (объект класса PDOStatement)
     *
     * @return array $arr - массив с данными, пришедшими в объекте класса PDOStatement
     */
    function db2Arr(PDOStatement $data){
        $arr = array();
        while ($row = $data->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $row;
        }
        return $arr;
    }
}