<!DOCTYPE HTML>
<html>
<head>
	<title>OneEgypt - Login</title>
	<?=View::make('header')?>
</head>
<body>
	<?=View::make('navigation')?>
	<div class="container  fus-white-bg fus-section fus-feature fus-white-bg">
		<div class="row">
			<div class="col-md-12">
				<form action="/login" method="post">

					<h3>Login</h3>
					<p>Sign in with your e-mail / password.</p>
					<?php
						if (Session::has('error')) {
							echo '<div class="errors" style="margin-bottom:7px;">'.Session::get('error').'</div>';
						}
					?>
					<label>E-mail Address</label><br/>
					<input type="text" class="field" name="email"/><br/>
					<label>Password</label><br/>
					<input type="password" class="field" name="password"/><br/><br/>
					<button class="btn btn-primary"><i class="fa fa-user"></i> SIGN IN</button>
					<a href="/beta" class="btn btn-success">REGISTER FOR BETA</a>
				</form>
			</div>
		</div>
	</div>
</body>
</html>