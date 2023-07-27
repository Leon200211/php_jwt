<?php

/**
 * Класс пользователя
 */
class User
{
    // Подключение к БД таблице "users"
    private $_conn;
    private $_table_name = "users";


    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $password;

    // Конструктор класса User
    public function __construct($db)
    {
        $this->_conn = $db;
    }

    // Метод для создания нового пользователя
    function create()
    {

        // Запрос для добавления нового пользователя в БД
        $query = "INSERT INTO " . $this->_table_name . "
                SET
                    firstname = :firstname,
                    lastname = :lastname,
                    email = :email,
                    password = :password";

        // Подготовка запроса
        $stmt = $this->_conn->prepare($query);

        // Инъекция
        $this->firstname = htmlspecialchars(strip_tags($this->firstname));
        $this->lastname = htmlspecialchars(strip_tags($this->lastname));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));

        // Привязываем значения
        $stmt->bindParam(":firstname", $this->firstname);
        $stmt->bindParam(":lastname", $this->lastname);
        $stmt->bindParam(":email", $this->email);

        // Для защиты пароля
        // Хешируем пароль перед сохранением в базу данных
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(":password", $password_hash);

        // Выполняем запрос
        // Если выполнение успешно, то информация о пользователе будет сохранена в базе данных
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Здесь будет метод emailExists()
}