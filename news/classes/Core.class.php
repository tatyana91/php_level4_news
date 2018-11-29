<?php
/**
 * class Core
 * Содержит основные статические методы
 */
class Core{
    /**
     * Привести переменную к целому положительному числу
     *
     * @param mixed $value - переменная
     *
     * @return int $result - переменная, приведенная к целому положительному числу
     */
    static function clearInt($value){
		return abs((int)$value);
	}

    /**
     * Очистить строку от пробелов в начале и конце
     *
     * @param string $value - строка
     *
     * @return string $result - строка с удаленными пробелами в начале и конце
     */
    static function clearStr($value){
        return trim($value);
    }
}