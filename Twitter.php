<?php


require('twitteroauth/autoload.php');
use Abraham\TwitterOAuth\TwitterOAuth;

class Twitter{

	private static $callback_url = 'http://maxxtechdistribution.com/twitter/callback.php';
	//private static $callback_url = 'http://localhost/twitter/callback.php';

	public $connection;
	public $oauth_token;
	public $oauth_token_secret;


	function __construct($oauth_token='',$oauth_token_secret=''){
		if($oauth_token===''){
			$tmp = new TwitterOAuth(TWITTER_KEY,TWITTER_SECRET);
			$tokens = $tmp->oauth('oauth/request_token',array('oauth_callback'=>self::$callback_url));
   			$oauth_token=$tokens['oauth_token'];
   			$oauth_token_secret=$tokens['oauth_token_secret'];
		}
		$this->oauth_token = $oauth_token;
		$this->oauth_token_secret = $oauth_token_secret;
		$this->connection = new TwitterOAuth(TWITTER_KEY,
											 TWITTER_SECRET,
											 $this->oauth_token,
											 $this->oauth_token_secret
											);

	}

	public function updateStatus($status){
		$res = $this->connection->post('statuses/update',array('status'=>$status));
		if($this->connection->getLastHttpCode()===200){
			return true;
		}
		else{
			if($res->errors[0]->code===187){
				return true;
			}
			return false;
		}
	}

	public function getNewTweets($user,$lastTweet){
		$tweets = $this->connection->get('statuses/user_timeline',array(
					'user_id'=>$user,
					'trim_user'=>false,
					'since_id'=>$lastTweet,
					'include_rts'=>false,
					'exclude_replies'=>true
		));
		if($this->connection->getLastHttpCode()!==200){
			return false;
		}
		$result = $tmp = array();
		foreach($tweets as $tweet){

			$tmp['username'] = $tweet->user->name;
			$tmp['screenname'] = $tweet->user->screen_name;
			$tmp['userid'] = $tweet->user->id_str;
			$tmp['id'] = $tweet->id_str;
			$tmp['text'] = $tweet->text;
			$tmp['time'] = date('Y-m-d H:i:s',strtotime($tweet->created_at));
			array_push($result,$tmp);
		}
		return $result;
	}

	public function getLastTweet($user){
		$tweets = $this->connection->get('statuses/user_timeline',array(
					'count'=>'1',
					'user_id'=>$user,
					'trim_user'=>true,
					'include_rts'=>false,
					'exclude_replies'=>true
		));
		return $tweets[0]->id_str;
	}
	public function getFriends($count=10,$cursor=-1){
		$friends = $this->connection->get('friends/list',array(
			'count'=>$count,
			'cursor'=>$cursor,
			'skip_status'=>true,
			'include_user_entities'=>false));

		$return = array();
		if($this->connection->getLastHttpCode()===200){
			$return['users']=array();
			$return['next_cursor'] = $friends->next_cursor_str;
			$return['prev_cursor'] = $friends->previous_cursor_str;
			foreach($friends->users as $friend){
				array_push($return['users'],array(
						'id'=>$friend->id_str,
						'name'=>$friend->name,
						'screen_name'=>$friend->screen_name,
						'image'=>$friend->profile_image_url
					));
			}
			return $return;
		}
		else{
			return false;
		}

	}
	public function getUserInfo(){
		$details = $this->connection->get('account/verify_credentials');
		if($this->connection->getLastHttpCode()===200){
			return array(
				'userid'=>$details->id_str,
				'screen_name'=>$details->screen_name,
				'name'=>$details->name,
				'location'=>$details->location,
				'friends'=>$details->friends_count,
				'image'=>$details->profile_image_url,
			);

		}
		else
			return false;
	}
	public function getUserToken($oauth_verifier){
		/*
			RETURNS
				oauth_token
				oauth_token_secret
				user_id
				screen_name
		*/
		$accesstoken =  $this->connection->oauth('oauth/access_token',array('oauth_verifier'=>$oauth_verifier));
		if($this->connection->getLastHttpCode()===200){
			return $accesstoken;
		}
		else
			return false;

	}
	public function getSelfToken(){
		/*
			RETURNS oauth_token
					oauth_token_secret
					oauth_callback_confirmed
		*/
		$token = $this->connection->oauth('oauth/request_token',array('oauth_callback'=>self::$callback_url));
		if($this->connection->getLastHttpCode()===200){
			return $token;
		}
		else
			return false;

	}
	public function authorize(){
		/*
			Returns the url where the user should be forwarded.
		*/
		$url = $this->connection->url('oauth/authorize', array('oauth_token' => $this->oauth_token));
		return $url;
	}
	public function getHTTPCode(){
		return $this->connection->getLastHttpCode();
	}
}
