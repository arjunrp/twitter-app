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

if(isset($_GET['denied'])){
	die("User did not authorize");
}

if(!isset($_GET['oauth_token']) || !isset($_GET['oauth_verifier'])){
	die("No tokens from Twitter");

}

$oauth_token = $_GET['oauth_token'];
$oauth_verifier = $_GET['oauth_verifier'];

$twitter = new Twitter($_SESSION['token'],$_SESSION['secret']);
$tokens = $twitter->getUserToken($oauth_verifier);
if($tokens===false){
	die('API Error,Code'.$twitter->getHTTPCode());
}
$twitter = new Twitter($tokens['oauth_token'],$tokens['oauth_token_secret']);
$credentials = $twitter->getUserInfo();

if($credentials===false){
	die('API Error,Code'.$twitter->getHTTPCode());
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

	if($twitter->updateStatus('Its fun out here!!')===false){
		echo 'Twitter status update failed';
		//header('Location: index.php?action=logout');
		die();

	}
	header('Location: home.php');
	die();
}
else{
	die('q error');
}

