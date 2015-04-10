<?php
	session_start();
	require('essentials.php');
	if(isset($_GET['action'])){
		if($_GET['action']==='logout'){
			session_destroy();
			header('Locaton:index.php');
		}
	}

	if(checkSession()===true){
		header('Location: home.php');
		die();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset='utf-8' />
		<title>Login :: Twitter - Notify By Mail</title>
		<meta name="viewport"content="width=device-width,initial-scale=1">
		<link rel="stylesheet" href="plugins/bootstrap.min.css" />
		<link rel="stylesheet" href="plugins/mine.css" />
		<link href='http://fonts.googleapis.com/css?family=Slabo+27px' rel='stylesheet' type='text/css'>
	</head>
	<body>

		<div class="loading"></div>
		<div class="top container">
			<div class="row">
				<div class="center col-md-10 col-md-offset-1">
					<h2>Welcome to 'Mail-My-Follower' Twitter Application</h2>
				</div>
			</div>

			<div class="row">
				<div class="center col-md-10 col-md-offset-1">
					<a class="btn-primary btn" href="signin.php" >Click here to login via Twitter</a>
				</div>
			</div>
		</div>



		<script src="plugins/jquery.min.js"></script>
		<script src="plugins/bootstrap.min.js"></script>
		<script>
			$(document).ready(function(){
				$('.loading').css('display','none');
			});
		</script>
	</body>
</html>
