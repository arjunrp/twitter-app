<?php

session_start();
require('essentials.php');
if(checkSession()===true){
	header('Location: home.php');
	die();
}

require('constants.php');
require('Twitter.php');
require('Db.php');

if(!isset($_GET['oauth_token']) || !isset($_GET['oauth_verifier'])){
	echo "No tokens from Twitter";
	die();
}
$oauth_token = $_GET['oauth_token'];
$oauth_verifier = $_GET['oauth_verifier'];

$twitter = new Twitter($_SESSION['token'],$_SESSION['secret']);
$tokens = $twitter->getUserToken($oauth_verifier);
$twitter = new Twitter($tokens['oauth_token'],$tokens['oauth_token_secret']);



$credentials = $twitter->getUserInfo();
if($credentials===false){
	die('error');
}
$db = new Db();
if($db->isOk()===false){
	die('db error');
}
$a = $db->addNewUser($credentials['userid'],
					 $credentials['screen_name'],
					 $credentials['name'],
					 $tokens['oauth_token'],
					 $tokens['oauth_token_secret']);
if($a!==false){

	/* Restart user session */
	session_destroy();
	session_start();
	$_SESSION['loggedin'] = true;
	$_SESSION['username'] = $credentials['screen_name'];
	$_SESSION['userid'] = $credentials['userid'];
	$_SESSION['token'] = $tokens['oauth_token'];
	$_SESSION['secret'] = $tokens['oauth_token_secret'];
	$_SESSION['email'] = '';
	header('Location: home.php');
	die();
}
else{
	die('q error');
}

