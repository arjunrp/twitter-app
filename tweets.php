<?php
/*
	Page to fetch new tweets and mail them to a particular user
	This page can be added to the cronjob tasks inorder to repeat the mailing process over certain interval
	http://code.tutsplus.com/tutorials/scheduling-tasks-with-cron-jobs--net-8800

*/

require('essentials.php');
require('constants.php');
require('Twitter.php');
require('Db.php');

$twitter = new Twitter(TWITTER_ACCESS_TOKEN,TWITTER_ACCESS_TOKEN_SECRET);

$db = new Db();
if($db->isOk()===false){
	die('db_error');
}

$tomail = array();
$user = '';

$following = $db->getFollowing();
/* $following willbe in the format
	user   following  last_tweet
----------------------------------
	user1  follower1  lastTweet
	user1  follower2  lastTweet
	user1  follower3  lastTweet
								----Send Mail to user1
	user2  follower1  lastTweet
	user2  follower2  lastTweet
	user2  follower3  lastTweet
								----Send Mail to user2

So while traversing the array, once user changes, we can mail the feed to him

*/
foreach($following as $fuser){
	if($fuser['user']!==$user && $user!==''){
		/*
			Process the $tomail array format the messages and build a feed based on that
			Then send mails
			Reset the tomail array

		*/
		processTweets($db,$tomail,$user);
		$tomail = array();
	}
	$r = $twitter->getNewTweets($fuser['following'],$fuser['last_tweet']);
	if($r===false){

		var_dump($fuser);
		die('API Error: '.$twitter->getHTTPCode());

	}
	if(!empty($r)){
		array_push($tomail,$r);
	}
	$user = $fuser['user'];
}


processTweets($db,$tomail,$user);



function processTweets($db,$tomail,$user){
	$userDetails = $db->getDetails($user);
	$message = createMessage($tomail,$userDetails['username']);
	if($message!==false){
		$lastTweets = getLastTweets($tomail);
		$mail = sendMail($userDetails['email'],$message);
		if($mail===true){
			foreach($lastTweets as $tweet){
				$db->setLasttweet($user,$tweet['userid'],$tweet['tweet']);
			}
			echo "Mailed successfully to ".$user." - ".$userDetails['username']."<br/>";
		}
	}
	else{
		echo "No new tweets for ".$user." - ".$userDetails['username'];
		echo "<br/>";
	}
}
