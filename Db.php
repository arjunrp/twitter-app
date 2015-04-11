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
	public function addNewUser($userid,$username,$name,$token,$secret){
		return mysqli_query($this->object,"INSERT INTO twitter_user(userid,username,name,email,oauth_token,oauth_secret)
			VALUES(
				'".$this->escape($userid)."',
				'".$this->escape($username)."',
				'".$this->escape($name)."',
				'',
				'".$this->escape($token)."',
				'".$this->escape($secret)."')
			ON DUPLICATE KEY UPDATE
			oauth_token=VALUES(oauth_token),
			oauth_secret=VALUES(oauth_secret)");
	}
	public function getDetails($userid){
		$res = mysqli_query($this->object,"SELECT email,userid,username,name,oauth_token AS token,oauth_secret AS secret FROM twitter_user WHERE userid='".$this->escape($userid)."'");
		if(!$res){
			return false;
		}
		$row = mysqli_fetch_assoc($res);
		return $row;
	}

	public function follow($appuser,$user){
		return mysqli_query($this->object,"INSERT INTO twitter_following VALUES(
										'".$this->escape($appuser)."','".$this->escape($user)."')");
	}

	public function unFollow($appuser,$user){
		return mysqli_query($this->object,"DELETE FROM twitter_following WHERE
										user = '".$this->escape($appuser)."'
										AND following = '".$this->escape($user)."'");

	}
	public function checkFollowers($userid,$users){
		$str = '';
		foreach($users as $user){
			$str .= "'".$this->escape($user['id'])."',";
		}
		$str = trim($str,',');
		$res = mysqli_query($this->object,"SELECT following FROM twitter_following
							WHERE user='".$this->escape($userid)."' AND following IN(".$str.") ");

		if($res==false){
			return false;
		}
		$result = array();
		while($r = mysqli_fetch_row($res)){
			array_push($result,$r[0]);
		}
		foreach($users as $key=>$user){
			if(in_array($user['id'],$result)){
				$users[$key]['following']=true;
			}
			else{
				$users[$key]['following']=false;
			}
		}
		return $users;




	}
	public function updateEmail($email,$userid){
		return mysqli_query($this->object,"UPDATE twitter_user
							SET email = '".$this->escape($email)."'
							WHERE userid = '".$this->escape($userid)."'");


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
