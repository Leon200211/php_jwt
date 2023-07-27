<?php
error_reporting(0);

// Заголовки
header("Access-Control-Allow-Origin: http://authentication-jwt/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Подключение к БД
// Файлы, необходимые для подключения к базе данных

require_once "Config/Database.php";
require_once "Objects/User.php";

// Получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// Создание объекта "User"
$user = new User($db);


// Получаем данные
$data = json_decode(file_get_contents("php://input"));

// Устанавливаем значения
$user->firstname = $data->firstname;
$user->lastname = $data->lastname;
$user->email = $data->email;
$user->password = $data->password;


// Поверка на существование e-mail в БД
// $email_exists = $user->emailExists();


// Создание пользователя
if (
    !empty($user->firstname) &&
    !empty($user->email) &&
    // $email_exists == 0 &&
    !empty($user->password) &&
    $user->create()
) {
    // Устанавливаем код ответа
    http_response_code(200);

    // Покажем сообщение о том, что пользователь был создан
    echo json_encode(array("message" => "Пользователь был создан"));
} else {  // Сообщение, если не удаётся создать пользователя
    // Устанавливаем код ответа
    http_response_code(400);

    // Покажем сообщение о том, что создать пользователя не удалось
    echo json_encode(array("message" => "Невозможно создать пользователя"));
}
