<?php

session_start();
require('essentials.php');

if(checkSession()===true){
	header('Location: home.php');
	die();
}
require('constants.php');
require('Twitter.php');

$connection = new Twitter();
$url = $connection->authorize();
if($url===false){
	var_dump($url);
	die('error');

}
$_SESSION['token'] = $connection->oauth_token;
$_SESSION['secret'] = $connection->oauth_token_secret;
header('Location:'.$url);
die();
