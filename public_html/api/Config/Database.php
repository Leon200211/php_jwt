<?php


/**
 * Класс для подключения к Базе данных MySQL
 */
class Database
{
    // Учётные данные базы данных
    private $_host = "127.0.0.1:3307";
    private $_db_name = "php_jwt";
    private $_username = "root";
    private $_password = "root";
    private $_conn;

    /**
     * Получаем соединение с базой данных
     * @return object
     */
    public function getConnection(): object
    {
        $this->_conn = null;

        try {
            $this->_conn = new PDO("mysql:host=" . $this->_host . ";dbname=" . $this->_db_name, $this->_username, $this->_password);
        } catch (PDOException $exception) {
            echo "Ошибка соединения с БД: " . $exception->getMessage();
        }

        return $this->_conn;
    }
}