<?php


// Параметры приложения
$clientId     = '51624627'; // ID приложения
$clientSecret = 'ExYGMabGKhn6iy3Gh9jj'; // Защищённый ключ
$redirectUri  = 'http://localhost:8000/test.php'; // Адрес, на который будет переадресован пользователь после прохождения авторизации
$version  = '5.126'; // Адрес, на который будет переадресован пользователь после прохождения авторизации

// Формируем ссылку для авторизации
$params = array(
    'client_id'     => $clientId,
    'redirect_uri'  => $redirectUri,
    'response_type' => 'code',
    'v'             => $version, // (обязательный параметр) версиb API https://vk.com/dev/versions

    // Права доступа приложения https://vk.com/dev/permissions
    // Если указать "offline", полученный access_token будет "вечным" (токен умрёт, если пользователь сменит свой пароль или удалит приложение).
    // Если не указать "offline", то полученный токен будет жить 12 часов.
    'scope'         => 'email,offline',
);

// Выводим на экран ссылку для открытия окна диалога авторизации
echo '<a href="http://oauth.vk.com/authorize?' . http_build_query($params) . '">Авторизация через ВКонтакте</a>';

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


if (!empty($response->access_token)) {
    $params = array(
        'v'            => $version,
        'uids'         => $response->user_id,
        'access_token' => $response->access_token,
        // 'fields'       => 'photo_big',
    );

    $info = file_get_contents('https://api.vk.com/method/users.get?' . http_build_query($params));
    $info = json_decode($info);
    // $info['response'][0]['email'] = $response->email;
    var_dump($info);
}
