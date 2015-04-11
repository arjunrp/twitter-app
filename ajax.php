<?php
/* Handle AJAX request from logged in users */

$response=array('success' => false,
				'message' => 'no message');
session_start();
require('essentials.php');
if(checkSession()===false){
	$response['message']='Authentication Error';
	die(json_encode($response));
}
if(!isset($_POST['id'])){
	$response['message']='Invalid Request';
	die(json_encode($response));
}
$id = (int)$_POST['id'];

require('constants.php');
require('Twitter.php');
require('Db.php');

$twitter = new Twitter($_SESSION['token'],$_SESSION['secret']);

$db = new Db();
if($db->isOk()===false){
	$response['message']='Database Error';
	die(json_encode($response));
}



switch($id){
	case 1:{
		/* Change email address of a user */

		if(!isset($_POST['email'])){
			$response['message'] = 'No email id found';
			break;
		}
		$email = $_POST['email'];
		if(filter_var($email,FILTER_VALIDATE_EMAIL)===false){
			$response['message'] = 'Invalid Email Address';
			break;
		}
		if(($db->updateEmail($email,$_SESSION['userid']))!==false){
			$response['success'] = true;
			$response['message'] = "Email Changed";
			break;
		}
		$response['message'] = "DB Error while changing email";
		break;
	}
	case 2:{
		/* Load more following users */

		$friends = $twitter->getFriends(20,$_SESSION['next_cursor']);
		if($friends===false){
			$response['message'] = 'API Error while fetching details';
			break;
		}
		$_SESSION['next_cursor'] =  $friends['next_cursor'];
		$res  = $db->checkFollowers($_SESSION['userid'],$friends['users']);
		if($res===false){
			$response['message'] = 'DB Error while fetching details';
			break;
		}
		$response['success'] = true;
		$response['message'] = $res;
		$response['cursor'] = $friends['next_cursor'];
		break;
	}
	case 3:{
		/* Follow(add mail) a person */

	}
	case 4:{
		/* unFollow(remove mail) a person */

	}
	default:{
		$response['message']='Invalid Request ID';
	}
}
die(json_encode($response));
