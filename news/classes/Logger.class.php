<?php
/**
 * class Logger
 * Пишет ошибки в файл логов
 * Реализует шаблон Singleton
 */
class Logger{
    private const LOG_FILE = '../logs/sql_errors.log';
    static private $instance = null;

    private function __construct(){}

    private function __clone(){}

    /**
     *	Получить сущность класса Logger (если объекта еще не существует, то создать его)
     *
     *	@return Logger $obj
     */
    public static function getInstance(){
        if (self::$instance == null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     *	Записать ошибку в файл логов
     *
     *	@param string $error - описание ошибки
     *
     *	@return void
     */
    public function writeLog($error){
        $log_error = date('d.m.Y H:i:s', time())." ";
        $log_error .= $error;
        $log_error .= "\n";
        file_put_contents(self::LOG_FILE, $log_error, FILE_APPEND);
    }
}