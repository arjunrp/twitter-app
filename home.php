<?php
session_start();
require('essentials.php');
if(checkSession()===false){
	header('Location: index.php');
	die();
}

require('constants.php');
require('Twitter.php');
require('Db.php');

$twitter = new Twitter($_SESSION['token'],$_SESSION['secret']);
$credentials = $twitter->getUserInfo();
//var_dump($credentials);
?>
<html>
	<body>
		<img src="<?php echo $credentials['image']?>"/>
	</body>
</html>


