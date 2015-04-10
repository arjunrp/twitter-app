<?php
class DB{
	private $object;
	public function __construct(){
		$connection = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
		if($connection){
			mysqli_set_charset($connection,'utf-8');
			$this->object = $connection;
		}
		else{
			$this->object = false;
		}
	}

	public function escape($string){
		return mysqli_real_escape_string($this->object,$string);
	}
	public function error(){
		$code = mysqli_errno($this->object);
		if($code===0){
			return false;
		}
		return array(
			'code' => $code,
			'error' => mysqli_error($this->object)
		);

	}
	public function addNewUser($userid,$username,$token,$secret){
		return mysqli_query($this->object,"INSERT INTO twitter_user(userid,username,oauth_token,oauth_secret)
			VALUES(
				'".$this->escape($userid)."',
				'".$this->escape($username)."',
				'".$this->escape($token)."',
				'".$this->escape($secret)."')
			ON DUPLICATE KEY UPDATE
			oauth_token=VALUES(oauth_token),
			oauth_secret=VALUES(oauth_secret)");
	}
	public function isOk(){
		if($this->object){
			return true;
		}
		else{
			return false;
		}
	}
	public function __destruct(){
		if($this->object!==false){
			mysqli_close($this->object);
		}

	}

}
