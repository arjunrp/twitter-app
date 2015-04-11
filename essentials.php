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

function sendMail($to,$subject,$message){



}
