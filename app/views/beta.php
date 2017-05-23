<!DOCTYPE HTML>
<html>
<head>
	<title>OneEgypt.org - Home</title>
<?php
	echo View::make('header');
?>
</head>
<body>
	<?=View::make('navigation')->with('highlight','dialogue')?>
	<div class="container  fus-white-bg fus-section ">
		<h3 style="font-weight:700;">OneEgypt is in beta</h3>
		<p>Thank you for your interest in OneEgypt.</p><p>The website is currently still in development, and undergoing beta testing. If you would like to browse the features of the site, and contribute to beta testing, please request an invitation to the community below.</p>
		<form action="/beta" method="post">
			<?php
				if (Session::has('error')) {
					echo '<div class="alert alert-danger">'.Session::get('error').'</div>';
				}
				if (Session::has('success')) {
					echo '<div class="alert alert-success">'.Session::get('success').'</div>';
				}
			?>
			<label>E-mail Address</label>
			<input type="email" class="form-control" name="email" value="<?=Input::old('email')?>"/>
			<label>Full Name</label>
			<input type="text" class="form-control" name="full_name" value="<?=Input::old('full_name')?>"/>
			<label>Occupation</label>
			<input type="text" class="form-control" name="occupation" value="<?=Input::old('occupation')?>"/>
			<label>Why are you interested in OneEgypt?</label>
			<textarea class="form-control" name="interest"><?=Input::get('interest')?></textarea>
			<br/>
			<button class="btn btn-primary">SUBMIT REQUEST</button>
		</form>
	</div>
</body>

</html>