<!DOCTYPE HTML>
<html>
	<head>
		<?=View::make('header')?>
		<style>
			.centered {
				text-align:center;
			}

			.field {
				padding:5px;
				border:1px solid #e2e2e2;
				min-width:320px;
			}

			.row {
				margin-bottom:5px;
			}

			.errors {
				color:#ff2222;
			}
		</style>
	</head>
	<body>
		<?=View::make('navigation')?>
		<div class="container fus-section fus-feature fus-white-bg">
			<div class="row">
				<div class="col-md-12 fus-section-header">
					<h3>Sign up for OneEgypt</h3>
					<h4>Building communities and bringing people together</h4>
				</div>
			</div>

			<form id="form" onsubmit="return false;">
				<div class="row">
					<div class="col-md-2">
						<label>E-mail Address</label>
					</div>
					<div class="col-md-4">

						<input type="email" name="email" value="" class="field"/>
					</div>
					<?php
						if ($type == 'individual') {
					?>
					<div class="col-md-4">
						<a href="#" class="btn btn-primary" id="facebook"><i class="fa fa-facebook"></i> CONNECT</a><br/>
						<span>Sign in with Facebook to get started more quickly.</span>
					</div>	
					<?php
						}
					?>
				</div>
				<div class="row">
					<div class="col-md-2">
						<label>Password</label>
					</div>
					<div class="col-md-6">
						<input type="password" name="password" value="" class="field"/>
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
						<label>Confirm Password</label>
					</div>
					<div class="col-md-6">
						<input type="password" name="password_confirmation" value="" class="field"/>
					</div>
				</div>
				<?php
					echo View::make('forms/'.$type.'');
				?>
				<div class="row">
					<div class="col-md-2">
						&nbsp;
					</div>
					<div class="col-md-6">
						<div id="errors" class="errors"></div>
						<button type="button" class="btn fus-nobtn" id="complete-btn">COMPLETE REGISTRATION</button>

					</div>
				</div>
			</form>
		</div>
		<div id="fb-root"></div>
		<script>
			$('#complete-btn').click(function() {
				$('#errors').html('');
				$.post('/register', $('#form').serialize(), function(data) {
					if (data.success) {
						document.location = data.url;
					} else if (data.errors && data.errors.length > 0) {
						$('#errors').html(data.errors[0]+'<br/><br/>');
					}
				},'json');
			});

			function checkLogin() {
				FB.getLoginStatus(function(response) {
				  if (response.status === 'connected') {
				  	$('#token').val(response.authResponse.accessToken);
				  	FB.api('/me?fields=first_name,last_name,email,location', function(response) {
				  		console.log(response);
				  		if (response.id) {
				  			$('#facebook_uid').val(response.id);
				  			$('input[name=first_name]').val(response.first_name);
				  			$('input[name=email]').val(response.email);
				  			$('input[name=last_name]').val(response.last_name);
				  			$('input[name=username]').val(response.username);
				  		}
				  	});
				  }
				});
			}

			$('#facebook').click(function() {
				FB.login(function(response) {
				   checkLogin();
				 }, {scope: 'public_profile,email,user_location,user_education_history,user_work_history,user_about_me'});
			});

			window.fbAsyncInit = function() {
			    FB.init({
			      appId      : '440294336131879',
			      xfbml      : true,
			      version    : 'v2.3'
			    });
			  };


			 (function(d, s, id) {
			    var js, fjs = d.getElementsByTagName(s)[0];
			    if (d.getElementById(id)) return;
			    js = d.createElement(s); js.id = id;
			    js.src = "//connect.facebook.net/en_US/sdk.js";
			    fjs.parentNode.insertBefore(js, fjs);
			  }(document, 'script', 'facebook-jssdk'));
		</script>
	</body>
	</html>