<?php

function checkSession(){
	if( isset($_SESSION['loggedin']) &&
	   	isset($_SESSION['username']) &&
	   	isset($_SESSION['userid']) &&
	    isset($_SESSION['token']) &&
		isset($_SESSION['secret'])){

		if($_SESSION['loggedin']===true){
			return true;
		}
	}
	return false;
}
function createMessage($tomail){
	if(empty($tomail)){
		return false;
	}
	$str = '';
	foreach($tomail as $user){
		if(empty($user)){
			continue;
		}
		else{
			foreach($user as $tweet){
				$str .= $tweet['screenname']." - ".$tweet['time']." - ".$tweet['text']."\n";
			}
		}
	}

	return $str;
}

function sendMail($to,$message){
	return true;
	return mail($to,'Tweets from people you follow',$message);

}


