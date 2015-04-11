<?php
session_start();
require('essentials.php');
if(checkSession()===false){
	//header('Location: index.php');
	//die();
}

require('constants.php');
require('Twitter.php');
require('Db.php');

//$twitter = new Twitter($_SESSION['token'],$_SESSION['secret']);
//$credentials = $twitter->getUserInfo();
//var_dump($credentials);

//$friends = $twitter->getFriends(2);
//$_SESSION['next_cursor'] =  $friends['next_cursor'];

//$friends= $friends['users'];
$credentials = array(
	'name'=>'arjun',
	'image'=>'https://pbs.twimg.com/profile_images/533714901802291201/xzGnIg2y_bigger.jpeg',
	'screen_name'=>'arju_rp'
);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset='utf-8' />
		<title>Twitter - Notify By Mail</title>
		<meta name="viewport"content="width=device-width,initial-scale=1">
		<link rel="stylesheet" href="plugins/css/bootstrap.min.css" />
		<link rel="stylesheet" href="plugins/css/mine.css" />
		<link href='http://fonts.googleapis.com/css?family=Slabo+27px' rel='stylesheet' type='text/css'>
	</head>
	<body>

		<div class="loading"></div>

		<div class="modal fade" id="emailModal">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        				<h3 class="modal-title">Change user email</h3>
      				</div>
      				<div class="modal-body">
        				<form>
							<div class="">
								<label>Email address</label>
								<input id="email" class="form-control" type="text" placeholder="enter email to receive tweets"/>
							</div>

						</form>
      				</div>
      				<div class="modal-footer">
        				<button  type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        				<button id="changeEmail" type="button" class="btn btn-primary">Change Email</button>
      				</div>
				</div>
  			</div>
		</div>

		<div class="dropdown">
  			<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
				<span class="glyphicon glyphicon-cog"></span>
			</button>

			<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
    			<li role="presentation"><a role="menuitem" tabindex="-1" data-toggle="modal" data-target="#emailModal" href="#">Change Email Address</a></li>
    			<li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?action=logout">Logout</a></li>
  			</ul>
		</div>

		<div class="content">

			<div class="container">

				<div class="row">
					<div class="center col-xs-10 col-xs-offset-1">
						<img class="img-rounded" src="<?php echo $credentials['image'] ?>" alt="profile-img" />
					</div>
					<div class="col-xs-1">

					</div>

				</div>
				<div class="row">
					<div class="center col-md-10 col-md-offset-1">
						<a target="_blank" href="https://twitter.com/<?php echo $credentials['screen_name'] ?>" ><?php echo '@'.$credentials['screen_name'] ?> </a>
					</div>
				</div>
				<div class="row">
					<div class="center col-md-10 col-md-offset-1">
						<h2>Welcome <?php echo $credentials['name'] ?></h2>
					</div>
				</div>

				<div class="row">
					<div class="center col-md-10 col-md-offset-1">
					</div>
				</div>

				<div class="row">
					<div class="col-md-6 col-md-offset-3">

					</div>
				</div>

			</div>

			<div id="following" class="container">

				<div class="row center">

					<div class="col-sm-2 user-box">
						<img class="img-rounded" src="https://pbs.twimg.com/profile_images/533714901802291201/xzGnIg2y_bigger.jpeg" alt="profile-img" /><br/>
						<a class="user-link" href="https://twitter.com/arju_rp">@arjun_rp</a>
						<p class="user-name" >Arjun RP</p>
						<a href="#" class="user-follow btn btn-primary">Follow via mail</a>
					</div>

				</div>
				<div class="row">
					<div class="col-md-4 col-md-offset-4">
						<button id="show-more" class=" btn btn-success">Show More. . .</button>
					</div>
				</div>

			</div>


		</div>


		<script src="plugins/js/jquery.min.js"></script>
		<script src="plugins/js/bootstrap.min.js"></script>
		<script>
			$(document).ready(function(){
				$('.loading').css('display','none');

				$('#changeEmail').click(function(){
					var email = $('#email').val();
					if(email===''){
						alert('Please enter your mail address');
						return;
					}
					$.ajax({
						type:'post',
						url:'ajax.php',
						data:'id=1&email='+email,
						success:function(data){
							try{
								data = JSON.parse(data);
								if(data.success===true){

								}
								else{
									alert(data.error);
								}
							}
							catch(e){
								alert('Invalid Response From Server');
							}

						},
						error:function(){}
					})
				});


			});
		</script>
	</body>
</html>

