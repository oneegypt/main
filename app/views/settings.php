<!DOCTYPE HTML>
<html>
<head>
	<?=View::make('header')?>
	<style>
		.settings-side-bar a{
			display:block;
			padding:5px;
			border-bottom:1px solid #f2f2f2;
		}
	</style>
</head>
<body>
	<?=View::make('navigation')?>
<div class="container  fus-white-bg fus-section fus-feature fus-white-bg">
	<div class="row">
		<div class="col-md-3 settings-side-bar">
			<a href="/settings/profile">Profile</a>
			
		<?php if ($user->type == 'organization')	 { ?>
		<a href="/settings/category">Category / Tags</a>
		<a href="/settings/map">Map / Focused Region</a>
		<a href="/settings/bank">Bank Details</a>

		<?php } ?>
		<a href="/settings/billing">Credit Card</a>
		</div>
		<div class="col-md-8">
			<?=$child?>
		</div>	
	</div>
</div>
<?= View::make('footer') ?>
</body>
</html>