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

$_SESSION['next_cursor']=-1;
$twitter = new Twitter($_SESSION['token'],$_SESSION['secret']);
$db = new Db();
if($db->isOk()===false){
	die('Cannot Connect to DB');
}
$details = $db->getDetails($_SESSION['userid']);
if($details===null){
	header('Location: index.php?action=logout');
	die();
}
$email = $details['email'];

$credentials = $twitter->getUserInfo();
if($credentials==false){
	die('Cannot connect to twitter,Code: '.$twitter->getHTTPCode());
}

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

		<div class="loading">
			<div class="loading-img">
			</div>
		</div>

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
								<input id="email" value="<?php echo $email; ?>" autocomplete="off" class="form-control" type="text" placeholder="enter email to receive tweets"/>
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
					<div style="text-align:center;" class="col-md-12">
						<button data-toggle="modal" data-target="#emailModal" class="gear btn btn-primary">Email address</button>
						<a href="index.php?action=logout" class="btn btn-primary gear">Logout</a>
					</div>
				</div>

			</div>

			<div id="following" class="container">

				<div id="users" class="row center">

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
				function createUser(screen,name,id,image,following){
					if(following===true){
						css = 'btn-danger';
						following = 'following';
						text = 'Remove from mail';
					}
					else{
						css = 'btn-primary';
						following = '';
						text = 'Follow via mail';

					}

					var html = '<div class="col-sm-2 user-box">\
						<img class="img-rounded"\ src="'+image+'" alt="" /><br/>\
						<a class="user-link" href="https://twitter.com/'+screen+'">'+screen+'</a>\
						<p class="user-name" >'+name+'</p>\
						<a href="#" data-userid="'+id+'" data-status="'+following+'" class="user-follow btn '+css+'">'+text+'</a>\
					</div>';

					return html;
				}

				function load(){
					$('.loading').css('display','block');
					$.ajax({
						type:'post',
						url:'ajax.php',
						data:'id=2',
						success:function(data){
							try{
								data = JSON.parse(data);
								if(data.success===true){
									var following = $('#users');

									if(data.cursor==='0'){
										$('#show-more').text('No more users').removeClass('btn-success').addClass('btn-danger').attr('disabled',true);
									}

									user = data.message;
									for(i in user){
										html = createUser(user[i]['screen_name'],
														 user[i]['name'],
														 user[i]['id'],
														 user[i]['image'],
														 user[i]['following']);
										following.append(html);
									}
									$("html, body").animate({ scrollTop: $(document).height() }, 1000);
								}
								else{
									alert(data.message);

								}
							}
							catch(e){
								alert('Invalid Response From Server');
								console.log(e);
							}
							$('.loading').css('display','none');

						},
						error:function(){
							alert('Cannot connect to server');
							$('.loading').css('display','none');
						}
					});
				}

				load();


				$('#users').on('click','.user-follow',function(e){
					e.preventDefault();
					var id=3,
						userid = $(this).data("userid"),
						status = $(this).data("status");

					if(status=='following'){
						id=4;
					}
					element = this;
					$.ajax({
						url:'ajax.php',
						data:'id='+id+"&userid="+userid,
						type:'post',
						success:function(data){
							try{
								data = JSON.parse(data);
								if(data.success===true){
									if(id==4){
										$(element)
											.data("status",'')
											.removeClass('btn-danger')
											.addClass('btn-primary')
											.text('Follow via mail');
									}
									else{
										$(element)
											.data("status",'following')
											.removeClass('btn-primary')
											.addClass('btn-danger')
											.text('Remove from mail');
									}
								}
								else{
									alert(data.message);
									if(data.email){
										$('#emailModal').modal('show');
									}
								}
							}
							catch(e){
								alert('Invalid Response From Server');
								console.log(e);
							}
							$('.loading').css('display','none');

						},
						error:function(){
							alert('Cannot connect to server');
							$('.loading').css('display','none');
						}

					});

				})
				$('#show-more').click(function(){
					load();
				});
				$('#changeEmail').click(function(){
					var email = $('#email').val();
					if(email===''){
						alert('Please enter your mail address');
						return;
					}
					$('.loading').css('display','block');
					$.ajax({
						type:'post',
						url:'ajax.php',
						data:'id=1&email='+email,
						success:function(data){
							try{
								data = JSON.parse(data);
								if(data.success===true){
									$('#emailModal').modal('hide');
								}
								else{
									alert(data.message);

								}
							}
							catch(e){
								alert('Invalid Response From Server');
							}
							$('.loading').css('display','none');

						},
						error:function(){
							alert('Cannot connect to server');
							$('.loading').css('display','none');
						}
					});
				});


			});
		</script>
	</body>
</html>

