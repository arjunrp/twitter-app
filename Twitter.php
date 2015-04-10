<?php


require('twitteroauth/autoload.php');
use Abraham\TwitterOAuth\TwitterOAuth;

class Twitter{
	private static $callback_url = 'http://localhost/twitter/callback.php';

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

		return $this->connection;
	}


	public function getUserInfo(){
		$details = $this->connection->get('account/verify_credentials');

		$return = array(
			'userid'=>$details->id_str,
			'screen_name'=>$details->screen_name,
			'name'=>$details->name,
			'location'=>$details->location,
			'friends'=>$details->friends_count,
			'image'=>$details->profile_image_url,
		);
		return $return;

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
		return $this->connection->oauth('oauth/request_token',array('oauth_callback'=>self::$callback_url));
	}
	public function authorize(){
		/*
			Returns the url where the user should be forwarded.
		*/
		return $this->connection->url('oauth/authorize', array('oauth_token' => $this->oauth_token));
	}
}
