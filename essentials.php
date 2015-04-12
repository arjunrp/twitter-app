<?php
/* Function to check user session */
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

/*
	Function to get last tweet id of a user, this id will be saved in db(twitter_following),
	and passed to API(since_id) when we fectch the tweets came after this tweet
*/
function getLastTweets($tomail){
	$return = array();
	if(empty($tomail)){
		return ;
	}
	foreach($tomail as $user){
		if(empty($user)){
			continue;
		}
		else{
			array_push($return,array('userid'=>$user[0]['userid'],'tweet'=>$user[0]['id']));
		}
	}
	return $return;
}

/* Create a HTML string from tweets which will be mailed to user */
function createMessage($tomail){
	if(empty($tomail)){
		return false;
	}
	$str = '<html><body><h2>Tweets From People You Follow</h2><div>';
	foreach($tomail as $user){
		if(empty($user)){
			continue;
		}
		else{
			foreach($user as $tweet){
				$str .= '<div>';
				$str .= '<span><a href="https://twitter.com/'.$tweet['screenname'].'" target="_blank">@'.$tweet['screenname'].'</a></span>&nbsp&nbsp';
				$str .= '<span>'.$tweet['time'].'</span>';
				$str .= '<p>'.htmlentities($tweet['text']).'</p>';
				$str .= '</div>';
			}
		}
	}
	$str .= '</div></body></html>';
	return $str;
}

/* Send mail to users */
function sendMail($to,$message){
	//return true;
	$headers = 'MIME-Version: 1.0\r\n';
	$headers .= 'MIME-Version: 1.0\r\n';
	$headers .= 'Content-Type: text/html; charset=ISO-8859-1\r\n';
	return mail($to,'Mail My Follower - Twitter Application',$message,$headers);

}


