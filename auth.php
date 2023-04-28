<?php

if (!empty($_GET['code'])) {
	$params = array(
		'client_id'     => 51624627,
		'client_secret' => 'ExYGMabGKhn6iy3Gh9jj',
		'redirect_uri'  => 'localhost:8000/test.php',
		'code'          => $_GET['code']
	);
	
    // var_dump($params);
	// Получение access_token
	$data = file_get_contents('https://oauth.vk.com/access_token?' . urldecode(http_build_query($params)));
    // if (!$data = @file_get_contents('https://oauth.vk.com/access_token?' . http_build_query($params))) {
	// 	$error = error_get_last();
	// 	throw new Exception('HTTP request failed. Error: ' . $error['message']);
	// }
    var_dump($data);
	$data = json_decode($data, true);

	if (!empty($data['access_token'])) {
		// Получили email
		$email = $data['email'];
 
		// Получим данные пользователя
		$params = array(
			'v'            => '5.126',
			'uids'         => $data['user_id'],
			'access_token' => $data['access_token'],
			'fields'       => 'photo_big',
		);
 
		$info = file_get_contents('https://api.vk.com/method/users.get?' . urldecode(http_build_query($params)));
		$info = json_decode($info, true);	
		
		echo $email;
		print_r($info);
	}
}