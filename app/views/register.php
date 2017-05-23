<!DOCTYPE HTML>
<html>
	<head>
			<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<title>OneEgypt</title>
		<?=View::make('header')?>
		<style>
			.centered {
				text-align:center;
			}
		</style>
	</head>
	<body>
		<?= View::make('navigation') ?>
		<div class="container fus-section fus-feature fus-white-bg">
			<div class="row">
				<div class="col-md-12 fus-section-header">
					<h3>Sign up for OneEgypt</h3>
					<h4>Building communities and bringing people together</h4>
				</div>
			</div>
			<div class="row ">
				<div class="col-md-4 ">
					<div class="thumbnail">
						<div class="caption">
								
								<h3>Non-Profit</h3>
								<p>Raise support for your organization.</p>
							<a href="/register/organization" class="btn fus-nobtn">REGISTER</a>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="thumbnail">
						<div class="caption">
								
								<h3>Individual</h3>
								<p>Get involved in causes, campaigns, and communities.</p>
							<a href="/register/individual" class="btn fus-nobtn">REGISTER</a>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="thumbnail">
						<div class="caption">
								
								<h3>Company</h3>
								<p>Post jobs, find help.</p>
							<a href="/register/company" class="btn fus-nobtn">REGISTER</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>