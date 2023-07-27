<?php
error_reporting(0);

// Заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Требуется для кодирования веб-токена JSON
include_once "config/core.php";
include_once "libs/php-jwt/BeforeValidException.php";
include_once "libs/php-jwt/ExpiredException.php";
include_once "libs/php-jwt/SignatureInvalidException.php";
include_once "libs/php-jwt/JWT.php";
include_once "libs/php-jwt/Key.php";

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// Файлы, необходимые для подключения к базе данных
include_once "Config/Database.php";
include_once "Objects/User.php";

// Получаем соединение с БД
$database = new Database();
$db = $database->getConnection();

// Создание объекта "User"
$user = new User($db);

// Получаем данные
$data = json_decode(file_get_contents("php://input"));

// Получаем jwt
$jwt = isset($data->jwt) ? $data->jwt : "";

// Если JWT не пуст
if ($jwt) {

    // Если декодирование выполнено успешно, показать данные пользователя
    try {

        // Декодирование jwt
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

        // Нам нужно установить отправленные данные (через форму HTML) в свойствах объекта пользователя
        $user->firstname = $data->firstname;
        $user->lastname = $data->lastname;
        $user->email = $data->email;
        $user->password = $data->password;
        $user->id = $decoded->data->id;

        // Создание пользователя
        if ($user->update()) {
            // Нам нужно заново сгенерировать JWT, потому что данные пользователя могут отличаться
            $token = array(
                "iss" => $iss,
                "aud" => $aud,
                "iat" => $iat,
                "nbf" => $nbf,
                "exp" => 1000,
                "data" => array(
                    "id" => $user->id,
                    "firstname" => $user->firstname,
                    "lastname" => $user->lastname,
                    "email" => $user->email
                )
            );

            $jwt = JWT::encode($token, $key, 'HS256');

            // Код ответа
            http_response_code(200);

            // Ответ в формате JSON
            echo json_encode(
                array(
                    "message" => "Пользователь был обновлён",
                    "jwt" => $jwt
                )
            );
        }

        // Сообщение, если не удается обновить пользователя
        else {

            // Код ответа
            http_response_code(401);

            // Показать сообщение об ошибке
            echo json_encode(array("message" => "Невозможно обновить пользователя"));
        }
    }

        // Если декодирование не удалось, это означает, что JWT является недействительным
    catch (Exception $e) {

        // Код ответа
        http_response_code(401);

        // Сообщение об ошибке
        echo json_encode(array(
            "message" => "Доступ закрыт",
            "error" => $e->getMessage()
        ));
    }
}

// Показать сообщение об ошибке, если jwt пуст
else {

    // Код ответа
    http_response_code(401);

    // Сообщить пользователю что доступ запрещен
    echo json_encode(array("message" => "Доступ закрыт"));
}