<?php
error_reporting(0);

// Заголовки
header("Access-Control-Allow-Origin: http://authentication-jwt/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// Файлы необходимые для соединения с БД
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
$user->email = $data->email;
$email_exists = $user->emailExists();




// Подключение файлов JWT
include_once "config/core.php";
include_once "libs/php-jwt/BeforeValidException.php";
include_once "libs/php-jwt/ExpiredException.php";
include_once "libs/php-jwt/SignatureInvalidException.php";
include_once "libs/php-jwt/JWT.php";
use \Firebase\JWT\JWT;

// Существует ли электронная почта и соответствует ли пароль тому, что находится в базе данных
if ($email_exists && password_verify($data->password, $user->password)) {

    $token = array(
        "iss" => $iss,
        "aud" => $aud,
        "iat" => $iat,
        "nbf" => $nbf,
        "data" => array(
            "id" => $user->id,
            "firstname" => $user->firstname,
            "lastname" => $user->lastname,
            "email" => $user->email
        )
    );

    // Код ответа
    http_response_code(200);

    // Создание jwt
    $jwt = JWT::encode($token, $key, 'HS256');
    echo json_encode(
        array(
            "message" => "Успешный вход в систему",
            "jwt" => $jwt
        )
    );
}

// Если электронная почта не существует или пароль не совпадает,
// Сообщим пользователю, что он не может войти в систему
else {

    // Код ответа
    http_response_code(401);

    // Скажем пользователю что войти не удалось
    echo json_encode(array("message" => "Ошибка входа"));
}