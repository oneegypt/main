<!DOCTYPE HTML>
<html>
<head>
<?php
	echo View::make('header');
?>
</head>
<body>
	<?php
		echo View::make('admin.navigation');
	?>
	<br/>
	<div class="container fus-white-bg fus-section fus-feature fus-white-bg">
		<?= $child ?>
	</div>
</body>
</html>