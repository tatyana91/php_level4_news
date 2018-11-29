<?php
/**
 * class Logger
 * Пишет ошибки в файл логов
 * Реализует шаблон Singleton
 */
class Logger{
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
     *	@param string $log_file - файл логов
     *
     *	@return void
     */
    public function writeLog($error, $log_file){
        $log_error = date('d.m.Y H:i:s', time())." ";
        $log_error .= $error;
        $log_error .= "\n";
        file_put_contents($log_file, $log_error, FILE_APPEND);
    }
}