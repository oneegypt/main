<!DOCTYPE HTML>
<html>
<head>
	<?=View::make('header')?>
	<?php
		$user = Auth::user();
	?>
</head>
<body class="fus-yellow">
	<?=View::make('navigation')?>
	<div class="container fus-white-bg fus-section fus-feature fus-white-bg">
		<div class="row">
			<div class="col-md-8">
				<?=Form::model($user, array('url' => '/profile', 'method' => 'POST'))?>
					<h3>Edit Your Profile</h3>
					<?php
						if (Session::has('errors')) {
							echo '<span class="errors">'.Session::get('errors').'</span><br/>';
						}
						

					?>

					<?php
						if ($user->type == 'individual') {
					?>
					<label>First Name</label><br/>
					<?=Form::text('first_name', $user->first_name, array('class' => 'field'))?><br/>
					<label>Last Name</label><br/>
					<?=Form::text('last_name', $user->last_name, array('class' => 'field'))?><br/>
					<label>Gender</label><br/>
					<?= Form::select('gender', array('' => '-----', 'male' => 'Male', 'female' => 'Female'))?><br/>
					<label>Title</label><br/>
					<?=Form::text('title', $user->title, array('class' => 'field'))?><br/>
					<label>Employer, Company, Organization</label><br/>
					<?=Form::text('employer', $user->employer, array('class' => 'field'))?><br/>
					<br/>
					<?php
						} else {
					?>	
					<label>Contact Name</label><br/>
					<?=Form::text('contact_name', $user->contact_name, array('class' => 'field'))?><br/>
					<label>Contact Phone</label><br/>
					<?=Form::text('contact_phone', $user->contact_phone, array('class' => 'field'))?><br/>
					<?php
						}
					?>
					<label>Biography</label><br/>
					<?=Form::textarea('description_txt', $user->description_txt, array('class' => 'field', 'placeholder' => 'Tell us about yourself'))?><br/>
					
					<label>City</label><br/>
					<?=Form::text('city', $user->city, array('class' => 'field'))?><br/>
					<label>State/Province</label><br/>
					<?=Form::text('state', $user->state, array('class' => 'field'))?><br/>
					<label>Country</label><br/>
					<?=Form::text('country', $user->country, array('class' => 'field'))?><br/><br/>
					
					<button type="submit" class="btn btn-primary">Save Profile</button>
					<a href="<?=$user->type?>/<?=$user->username?>" class="btn">Cancel</a>
				</form>
 			</div>
		</div>
	</div>
</body>
</html>