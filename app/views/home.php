<!DOCTYPE HTML>
<html>
<head>
	<title>OneEgypt.org - Home</title>
	<?= View::make('header')?>
</head>
<body>
	<div class="container fus-white-bg fus-section fus-feature fus-white-bg" >
		<div class="row">
			<div class="col-md-12">

			</div>
		</div>
		<div class="row">
			<div class="col-md-8">
				<br/>
				<div class="row">
					<div class="col-md-3">
						<a href="/forums/create" class="btn btn-primary btn-block">Start a Forum</a>
					</div>
					<div class="col-md-6">
						<input type="text" class="field btn-block" placeholder="Search organizations, campaigns, forums"/>
					</div>
					<div class="col-md-3">
						<a href="/forums/start" class="btn btn-warning btn-block">Action 2</a>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<h3><a href="/<?=$user->type?>/<?=$user->username?>"><?=$user->display_name?></a></h3>

			</div>
		</div>
	</div>
</body>
</html>