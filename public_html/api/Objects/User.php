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

    /**
     * Проверка, существует ли электронная почта в нашей базе данных
     * @return bool
     */
    public function emailExists(): bool
    {

        // Запрос, чтобы проверить, существует ли электронная почта
        $query = "SELECT id, firstname, lastname, password
            FROM " . $this->_table_name . "
            WHERE email = ?
            LIMIT 0,1";

        // Подготовка запроса
        $stmt = $this->_conn->prepare($query);

        // Инъекция
        $this->email=htmlspecialchars(strip_tags($this->email));

        // Привязываем значение e-mail
        $stmt->bindParam(1, $this->email);

        // Выполняем запрос
        $stmt->execute();

        // Получаем количество строк
        $num = $stmt->rowCount();

        // Если электронная почта существует,
        // Присвоим значения свойствам объекта для легкого доступа и использования для php сессий
        if ($num > 0) {

            // Получаем значения
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Присвоим значения свойствам объекта
            $this->id = $row["id"];
            $this->firstname = $row["firstname"];
            $this->lastname = $row["lastname"];
            $this->password = $row["password"];

            // Вернём "true", потому что в базе данных существует электронная почта
            return true;
        }

        // Вернём "false", если адрес электронной почты не существует в базе данных
        return false;
    }


    /**
     * Обновить запись пользователя
     * @return bool
     */
    public function update(): bool
    {

        // Если в HTML-форме был введен пароль (необходимо обновить пароль)
        $password_set=!empty($this->password) ? ", password = :password" : "";

        // Если не введен пароль - не обновлять пароль
        $query = "UPDATE " . $this->table_name . "
            SET
                firstname = :firstname,
                lastname = :lastname,
                email = :email
                {$password_set}
            WHERE id = :id";

        // Подготовка запроса
        $stmt = $this->conn->prepare($query);

        // Инъекция (очистка)
        $this->firstname=htmlspecialchars(strip_tags($this->firstname));
        $this->lastname=htmlspecialchars(strip_tags($this->lastname));
        $this->email=htmlspecialchars(strip_tags($this->email));

        // Привязываем значения с HTML формы
        $stmt->bindParam(":firstname", $this->firstname);
        $stmt->bindParam(":lastname", $this->lastname);
        $stmt->bindParam(":email", $this->email);

        // Метод password_hash () для защиты пароля пользователя в базе данных
        if(!empty($this->password)){
            $this->password=htmlspecialchars(strip_tags($this->password));
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(":password", $password_hash);
        }

        // Уникальный идентификатор записи для редактирования
        $stmt->bindParam(":id", $this->id);

        // Если выполнение успешно, то информация о пользователе будет сохранена в базе данных
        if($stmt->execute()) {
            return true;
        }

        return false;
    }



}