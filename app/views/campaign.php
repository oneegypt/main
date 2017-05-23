<!DOCTYPE HTML>
<html>
<head>
<?php
	echo View::make('header');
?>
</head>
<body class="">
	<?= View::make('navigation') ?>
	<div class="container fus-white-bg fus-section fus-feature fus-white-bg">
		<div class="row">
			<div class="col-md-12">
				<form action="/organization/<?=$user->username?>/campaign/create" method="post">
				<h1>Start a Campaign</h1>
				<h5>Recipient: <?=$user->display_name?></h5>
				<label>Campaign Title</label><br/>
				<input type="text" name="title" value="<?=Input::old('title')?>" class="field" placeholder="A good title is compelling but concise" maxlength="100"/><br/>
				<label>Campaign Description</label><br/>
				<textarea name="description" class="field"><?=Input::old('description')?></textarea><br/>
				<label>Goal</label><br/>
				<input type="number" class="field" name="goal" value="<?=Input::old('goal', '0.00')?>"/>
				<br/><br/>
				<input type="checkbox" value="1" for="match" name="user_match"/> <span id="match">I will match the goal amount</span><br/>
				<small>You can match the goal amount, if reached.</small>
				<br/><br/>
				<button class="btn btn-primary"><i class="fa fa-plus"></i> Create Campaign</button>
				&nbsp;<a class="btn" style="border:1px solid transparent;" href="/organization/<?=$user->username?>/">Cancel</a>
				</form>
			</div>
		</div>
	</div>
	<script>
	$('input[name=goal]').change(function() {
		if (parseFloat($(this).val()) &&   $(this).val() > 0) {
			$('input[name=user_match]').prop('disabled', false);
			$('#match').css('opacity', 1);
		} else {
			$('input[name=user_match]').prop('disabled', 'disabled');
			$('#match').css('opacity', .5);
		}
	});
</script>
</body>
</html>