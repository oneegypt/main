<!DOCTYPE HTML>
<html>
<head>
<?php
	echo View::make('header');
?>
<style>
	.error {
		color:red;
	}
</style>
</head>
<body class="fus-blue">
	<div class="navbar navbar-fixed-top fus-navbar-solid" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<a href="/" class="navbar-brand"><i class="fa fa-gear"></i> OneEgypt Adminstrator</a>

		</div>
		<div class="navbar-collapse collapse">
		</div>
	</div>
</div>
	<div class="container fus-white-bg fus-section fus-feature fus-white-bg">
		<div class="row">
			<div class="col-md-4">&nbsp;</div>
			<div class="col-md-4">
				<br/><Br/><br/>
				<div class="well">
					<form action="/admin/auth" method="post">
						<?php
							if (Session::has('error')) {
								echo '<p class="error">'.Session::get('error').'</p>';
							}
						?>
						<label>Password</label><br/>
						<input type="password" name="password" value="" class="field"/><br/><br/>
						<button class="btn btn-primary">Sign in</button>
					</form>
				</div>
			</div>
			<div class="col-md-4">&nbsp;</div>
		</div>
	</div>
</body>
</html>