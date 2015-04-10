<?php

require('constants.php');
require('Twitter.php');
require('Db.php');

session_start();

if(!isset($_GET['oauth_token']) || !isset($_GET['oauth_verifier'])){
	echo "Error";
	die();
}
$oauth_token = $_GET['oauth_token'];
$oauth_verifier = $_GET['oauth_verifier'];

$twitter = new Twitter($_SESSION['oauth_token'],$_SESSION['oauth_token_secret']);
$tokens = $twitter->getUserToken($oauth_verifier);
$twitter = new Twitter($tokens['oauth_token'],$tokens['oauth_token_secret']);



$credentials = $twitter->getUserInfo();
var_dump($credentials);

$db = new Db();
if($db->isOk()===false){
	die('db error');
}
$a = $db->addNewUser($credentials['userid'],
					 $credentials['screen_name'],
					 $tokens['oauth_token'],
					 $tokens['oauth_token_secret']);

var_dump($db->error());

