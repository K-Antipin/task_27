<?php

namespace App\controllers;

use App\core\Controller;
use App\data\DB;
use App\models\entities\User;
use stdClass;
use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\HtmlFormatter;

class Admin extends Controller
{
    public function index()
    {
        if (!$_SESSION['auth']) header('Location: /');
        // $payload = DB::findAll('users');
        $this->view->render('user/users.phtml', 'template.phtml', 'admin'); //, $payload
    }

    //не реализовано
    public function add()
    {
        $this->view->render('user/add.phtml', 'template.phtml');
    }

    //не реализовано
    public function create()
    {
        if (
            !isset($_POST)
            || $_SERVER["REQUEST_METHOD"] !== "POST"
        ) {
            header('Location: /admin/add');
        }

        $entity = new \stdClass();
        $entity->username = $_POST['username'];
        $entity->email = $_POST['email'];
        $entity->role = $_POST['role'];
        $user = new User($entity);
        $userId = DB::create($user, 'users');
        if ($userId) {
            header('Location: /admin');
        }
    }
    public function show($data)
    {
        if (!empty($data) && intval($data[0])) {
            $id = $data[0];
            $payload = DB::get('users', $id);
        }

        if (!isset($payload) || $payload['id'] === 0) {
            header('Location: /error');
        }
        $this->view->render('user/show.phtml', 'template.phtml', $payload);
    }



    public function loginIn()
    {
        if (isset($_POST["token"]) && ($_POST["token"] == $_SESSION["CSRF"])) {
            http_response_code(200);
            $log = new Logger('mylogger');
            if (!empty($_POST['login']) && !empty($_POST['password'])) {
                $user = DB::get('users', $_POST['login'], 'email = ?');
                $hash = $this->generateCode();
                if (!isset($user)) {
                    $log->pushHandler(new StreamHandler('mylog.log', Logger::INFO));
                    $log->info('Auth error', ['mess' => 'Пользователь не найден', 'login' => $_POST['login'], 'password' => $_POST['password']]);
                    die(json_encode(['error' => 'Пользователь не найден']));
                } else {
                    if (\password_verify($_POST['password'], $user->password)) {
                        $obj = new stdClass;
                        $obj->id = $user->id;
                        $obj->user_hash = $hash;
                        $obj->user_ip = ip2long($_SERVER['REMOTE_ADDR']);
                        $obj->updated = time();
                        DB::update($obj, 'users');
                        setcookie("id", $user['id'], time() + 60 * 60 * 24 * 30, "/");
                        setcookie('hash', $hash, time() + 60 * 60 * 24 * 30, '/', $_SERVER['SERVER_NAME'], false, true);
                        $_SESSION["id"] = $user['id'];
                        $_SESSION["name"] = $user['name'];
                        $_SESSION["hash"] = $hash;
                        $_SESSION["role"] = $user['role'];
                        $_SESSION["auth"] = \true;
                        die(json_encode('Успешный вход'));
                    } else {
                        $log->pushHandler(new StreamHandler('mylog.log', Logger::INFO));
                        $log->info('Auth error', ['mess' => 'Не верный пароль', 'login' => $_POST['login'], 'password' => $_POST['password']]);
                        die(json_encode(['error' => 'Не верный пароль']));
                    }
                }
            } elseif (empty($_POST['login']) && !empty($_POST['password'])) {
                die(json_encode(['error' => 'Введите логин']));
            } elseif (!empty($_POST['login']) && empty($_POST['password'])) {
                die(json_encode(['error' => 'Введите пароль']));
            } elseif (empty($_POST['login']) && empty($_POST['password'])) {
                die(json_encode(['error' => 'Логин и пароль отсутствуют']));
            }
        }
    }

    public function register()
    {
        if (isset($_POST["token"]) && ($_POST["token"] == $_SESSION["CSRF"])) {
            http_response_code(200);
            // die(json_encode($_POST));
            if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['passwordConfirm'])) {
                // die(json_encode($_POST));
                $LoginExists = DB::get('users', $_POST['email'], 'email = ?');
                $hash = $this->generateCode();
                if (!isset($LoginExists)) {
                    // die(json_encode(["error" => "Все ок" , $_POST]));
                    if ($_POST['password'] !== $_POST['passwordConfirm']) die(json_encode(["error" => "Пароль и подтверждение не совпадают"]));
                    $obj = new stdClass;
                    $obj->name = $_POST['name'];
                    $obj->email = $_POST['email'];
                    $obj->role = 'user';
                    $obj->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $obj->user_hash = $hash;
                    $obj->user_ip = ip2long($_SERVER['REMOTE_ADDR']);
                    $obj->created = time();
                    $user = new User($obj);
                    $id = DB::create($user, 'users');
                    if ($id) {
                        setcookie("id", $user['id'], time() + 60 * 60 * 24 * 30, "/");
                        setcookie('hash', $hash, time() + 60 * 60 * 24 * 30, '/', $_SERVER['SERVER_NAME'], false, true);
                        $_SESSION["id"] = $user['id'];
                        $_SESSION["name"] = $user['name'];
                        $_SESSION["hash"] = $hash;
                        $_SESSION["role"] = $user['role'];
                        $_SESSION["auth"] = \true;
                        die(\json_encode('Успешная регистрация'));
                    } else {
                        die(json_encode(['error' => 'Неизвестная ошибка']));
                    }
                } else {
                    die(json_encode(["error" => "Почта {$_POST['email']} уже зарегистрирована"]));
                }
            } else {
                die(json_encode(['error' => 'Неизвестная ошибка']));
            }
        }
    }

    public function registerVk()
    {
        // die;
        // Параметры приложения
        $clientId     = '51624627'; // ID приложения
        $clientSecret = 'ExYGMabGKhn6iy3Gh9jj'; // Защищённый ключ
        $redirectUri  = 'http://localhost:8000/admin/registerVk'; // Адрес, на который будет переадресован пользователь после прохождения авторизации
        $version  = '5.126'; // Адрес, на который будет переадресован пользователь после прохождения авторизации

        // Формируем ссылку для авторизации
        $params = array(
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'v'             => $version,
            'scope'         => 'email,offline',
        );

        if (empty($_GET['code'])) {
            \header('Location: http://oauth.vk.com/authorize?' . http_build_query($params));
        }


        $params = array(
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'code'          => $_GET['code'],
            'redirect_uri'  => $redirectUri
        );

        if (!$content = @file_get_contents('https://oauth.vk.com/access_token?' . http_build_query($params))) {
            $error = error_get_last();
            throw new Exception('HTTP request failed. Error: ' . $error['message']);
        }

        $response = json_decode($content);

        // Если при получении токена произошла ошибка
        if (isset($response->error)) {
            throw new Exception('При получении токена произошла ошибка. Error: ' . $response->error . '. Error description: ' . $response->error_description);
        }
        // \var_dump($response);
        if (!empty($response->access_token)) {
            $params = array(
                'v'            => $version,
                'uids'         => $response->user_id,
                'access_token' => $response->access_token,
            );

            $info = file_get_contents('https://api.vk.com/method/users.get?' . http_build_query($params));
            $info = json_decode($info);

            $LoginExists = DB::get('users', $response->email, 'email = ?');
            $hash = $this->generateCode();

            if (!isset($LoginExists)) {
                $obj = new stdClass;
                $obj->name = $info->response[0]->first_name;
                $obj->email = $response->email;
                $obj->role = 'VK';
                $obj->password = password_hash($response->access_token, PASSWORD_DEFAULT);
                $obj->user_hash = $hash;
                $obj->user_ip = ip2long($_SERVER['REMOTE_ADDR']);
                $obj->created = time();
                $user = new User($obj);
                $id = DB::create($user, 'users');
                if ($id) {
                    setcookie("id", $id, time() + 60 * 60 * 24 * 30, "/");
                    setcookie('hash', $hash, time() + 60 * 60 * 24 * 30, '/', $_SERVER['SERVER_NAME'], false, true);
                    $_SESSION["id"] = $id;
                    $_SESSION["name"] = $obj->name;
                    $_SESSION["hash"] = $hash;
                    $_SESSION["role"] = $obj->role;
                    $_SESSION["auth"] = \true;
                    \header('Location: /');
                } else {
                    die(json_encode(['error' => 'Неизвестная ошибка']));
                }
            } else {
                $obj = new stdClass;
                $obj->id = $LoginExists['id'];
                $obj->user_hash = $hash;
                $obj->user_ip = ip2long($_SERVER['REMOTE_ADDR']);
                $obj->updated = time();
                DB::update($obj, 'users');
                setcookie("id", $LoginExists['id'], time() + 60 * 60 * 24 * 30, "/");
                setcookie('hash', $hash, time() + 60 * 60 * 24 * 30, '/', $_SERVER['SERVER_NAME'], false, true);
                $_SESSION["id"] = $LoginExists['id'];
                $_SESSION["name"] = $LoginExists['name'];
                $_SESSION["hash"] = $hash;
                $_SESSION["role"] = $LoginExists['role'];
                $_SESSION["auth"] = \true;
                \header('Location: /');
            }
        }
    }

    public function exit()
    {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
        setcookie('id', '', time() - 3600 * 24 * 30 * 12, '/');
        setcookie('hash', '', time() - 3600 * 24 * 30 * 12,  '/', $_SERVER['SERVER_NAME'], false, true);
        die(json_encode(['error' => \false, 'mess' => 'Вы вышли']));
    }

    private function generateCode()
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < 10) {
            $code .= $chars[mt_rand(0, $clen)];
        }
        return $code;
    }
}
