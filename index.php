<?php

session_start();
require('constants.php');
require('Twitter.php');

$connection = new Twitter();
$url = $connection->authorize();
$_SESSION['oauth_token'] = $connection->oauth_token;
$_SESSION['oauth_token_secret'] = $connection->oauth_token_secret;
header('Location:'.$url);
