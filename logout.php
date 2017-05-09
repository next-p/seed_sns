<?php

	// セッション変数の中身を上書きして空にする 変数の中をからにする
	$_SESSION = array();


	if (ini_get("session.use_cookies")){
		$params =session_get_cookie_params();
		setcookie(session_name(),'', time() -42000, $params['path']
		,$params['domain'], $params['secure'], $params['httponly']);
	}

	session_destroy();

	//Cookie情報も削除 変数の箱も消す
	setcookie('email', '', time() - 3600);
	setcookie('password', '', time() - 3600);

	header("Location: index.php");

	exit();

?>