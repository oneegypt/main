
<!DOCTYPE HTML>
<html>
<head>
		<title><?=$user->display_name?> on OneEgypt.org</title>

		<?=View::make('header') ?>
		<style>
			.photo {
				width:180px;
				height:180px;
				background-size:cover;
				background-position:center center;
				background-color:#f2f2f2;
				text-align:center;
				border-radius:5px;
			}

			
			.photo form {
				display:none;
				height:100%;
				width:100%;
			}

			.btn-success {
				background:#98cf2d !important;
				background-color:#98cf2d !important;
				color:#fff !important;
				border:1px solid #98cf2d !important;
			}

			.photo input {
				opacity:0;
				position:absolute;
				z-index:20;
				height:100%;
				width:100%;
				left:0;top:0;
			}
			.opacity {
				width:100%;
				height:100%;
				background-color:rgba(255,255,255,.85);
			}

			.campaign {
				padding:6px 12px;
				border:1px solid #e2e2e2;
				color:#363636;
				margin-bottom:10px;
			}

			.btn-no-border {
				border:1px solid transparent !important;
			}

			.status_update {
				padding:16px;
				/*background-color:#f8f8f8;
			
				border-top-left-radius:5px;
				border-top-right-radius:5px;
				border-top:1px solid #ccc;
				border-left:1px solid #ccc;
				border-right:1px solid #ccc;*/
			}

			.status_update textarea {
				margin-bottom:5px;
			}

			.profile-tabs a {
				font-weight:700;
				font-size:15px;
			}

			.profile-tabs a small {
				font-weight:400;
			}
			.profile h1 {
				margin-top:10px;
				font-weight:400;
			}

			a.tag {
				margin-right:9px;
			}

			.btn-donate {
				background-color:rgba(255, 173, 0, 1) !important;
				color:#fff !important;
				border:1px solid rgb(255, 173, 0) !important;
			}

			.donor {
				float:left;padding:5px;
				width:110px;
				font-size:11px;
				text-align:center;
			}
		</style>
</head>
<body class="profile">
<?php
	
	echo View::make('navigation');

	$photo_style = '';
	$delete_link = '';
	if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$user->id.'.jpg') ) {
		$path = Image::path('/assets/user/'.$user->id.'.jpg', 'resizeCrop', 180, 180);
		$photo_style = "background-image:url('$path');";
		$delete_link = '&nbsp;<a href="/me/deletePhoto"><i class="fa fa-remove"></i> Remove photo</a>';
	}

	$popOver_attrs = '';
?>
<div class="container fus-white-bg fus-section fus-feature fus-white-bg" style="min-height:100%;">
	<div class="row">
		<div class="col-md-2">
			<div class="photo" style="<?=$photo_style?>;position:relative;">
				<?php
					if (Auth::check() && Auth::user()->id == $user->id) {
						?>
						<form id="photo-form" action="/upload" method="post" enctype="multipart/form-data" target="target">
							<div class="opacity">
								<br/><br/>
							<input type="file" name="photo" onchange="$('#photo-form').submit();"/><br/>
							<button class="btn btn-primary" type="submit" style="position:absolute;bottom:25%;left:0px;width:100%;display:block;z-index:9;">Choose Photo</button>
							</div>
						</form>
						
						<?php
					} else {
						echo '&nbsp;';
					}
				?>

			</div>
			
		</div>
		<div class="col-md-6">
			<h1><a href="/<?=$user->type.'/'.$user->username?>"><?=$user->display_name?></a></h1>
			<?php
				if ($following && !$allow_edit) {
			?>
			<a href="/user/<?=$user->id?>/unfollow" class="btn btn-warning"><i class="fa fa-minus"></i> UNFOLLOW</a>
			<?php
				} else if (!$allow_edit) {
			?>
			<a href="/user/<?=$user->id?>/follow" class="btn btn-primary"><i class="fa fa-plus"></i> FOLLOW</a>
			<?php
				}
				if (!Auth::check() || (Auth::check() && Auth::user()->id != $user->id) ) {
			?>
			<a href="#" class="btn  btn-primary" onclick="showMessageScreen();" id='message-btn'><i class="fa fa-envelope"></i> MESSAGE</a>

			<?php
				}
				if (Auth::check()) {
				if (!is_null($user->stripe_recipient_id) && $general_campaign_id > 0) {
			?>
			<a href="/organization/<?=$user->username?>/campaign/<?=$general_campaign_id?>?amount=25" class="btn btn-success give"><i class="fa fa-heart"></i> DONATE</a>
			<?php
				}
			?>
			
			<?php
				}

				if (sizeof($user->listings) > 0) {
					?>
					<a href="/organization/<?=$user->username?>/work/" class="btn btn-primary"><i class="fa fa-wrench"></i> VOLUNTEER</a>
					<?php
				}

				?>
				<br/><br/>
				<?php

				if (!empty($user->city) && !empty($user->state) && !empty($user->country)) {
					echo '<i class="fa fa-arrow-right"></i> <strong>'.$user->city.'</strong>, <strong>'.$user->state.'</strong> / <strong>'.$user->country.'</strong><br/>';
				} else if (!empty($user->city) && !empty($user->state)) {
					echo '<i class="fa fa-arrow-right"></i> <strong>'.$user->city.'</strong>, <strong>'.$user->state.'</strong><br/>';
				}


				if (!empty($user->title) && !empty($user->employer)) {
					echo '<span><i class="fa fa-arrow-right"></i> <strong>'.$user->title.'</strong> at <strong>'.$user->employer.'</strong></span><br/>';
				} else if (!empty($user->title)) {
					echo '<span><i class="fa fa-arrow-right"></i> Works as <strong>'.$user->title.'</strong></span><br/>';
				} else if (!empty($user->employer)) {
					echo '<span><i class="fa fa-arrow-right"></i> Works at <strong>'.$user->employer.'</strong></span><br/>';
				}
				if (!is_null($user->category_id)) {
					echo '<span><i class="fa fa-arrow-right"></i> '.$user->category->category_name.'</span><br/>';
				}
				
			?>
			
			<?php
				if ($allow_edit) {
					
					if (empty($user->description_txt)) {
						$popOver_attrs = 'data-trigger="manual" data-toggle="popover" title="Fill out your profile" data-content="Write a short bio about yourself." ';
					}
					echo '<a data-placement="bottom" class="popover-link" href="/settings/profile" '.$popOver_attrs.'><i class="fa fa-pencil"></i> Edit Profile</a>';

					echo $delete_link;
				}
			?>
			
		</div>
		<div class="col-md-4">
			<ul class="nav nav-tabs profile-tabs">
				<li role="presentation" class="active"><a href="#followers"><?=$user->follower_count()?><br/><small>followers</small></a></li>
				<li role="presentation"><a href="#followed"><?=$user->following_count()?><br/><small>followed</small></a></li>
				<li role="presentation"><a href="#donated">$<?=$donated_amount?><br/><small>donated</small></a></li>
				<li role="presentation"><a href="#raised">$<?=$raised_amount?><br/><small>raised</small></a></li>
			</ul>
			<div id="followers" class="tab-content active">
				<?php
					foreach($user->followers() as $follower) {
						$path = '';
						if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$follower->follower_id.'.jpg') ) {
							$path = Image::path('/assets/user/'.$follower->follower_id.'.jpg', 'resizeCrop', 50, 50);
							
						}
						echo '<a data-toggle="tooltip" data-placement="bottom" title="'.addslashes($follower->display_name).'" href="/'.$follower->type.'/'.$follower->username.'" style="display:inline-block;"><img src="'.$path.'" style="width:40px;height:40px;"/></a>';
					}
				?>
			</div>
			<div id="followed" class="tab-content">
				<?php
					foreach($user->followed() as $followed) {
						$path = '';
						if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$followed->user_id.'.jpg') ) {
							$path = Image::path('/assets/user/'.$followed->user_id.'.jpg', 'resizeCrop', 50, 50);
							
						}
						echo '<a data-toggle="tooltip" data-placement="bottom" title="'.addslashes($followed->display_name).'" href="/'.$followed->type.'/'.$followed->username.'" style="display:inline-block;"><img src="'.$path.'" style="width:40px;height:40px;"/></a>';
					}
				?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8">
			<?php
				if (Session::has('message')) {
					echo '<p class="message">'.Session::get('message').'</p>';
				}
			?>
			<ul class="nav nav-tabs profile-tabs">
				<li role="presentation" class="active"><a href="#info">Information</a></li>
				<li role="presentation"><a href="#feed">Activity</a></li>
			</ul>
			<div id="info">
			<h3>About</h3>
			<p><?=str_replace("\n", '<br/>', $user->description_txt)?></p>

			<?php
			if ($user->type == 'organization') {
				if (sizeof($user->tags()) > 0) {
					echo '<h3>Tags</h3>';
					echo '<p>';
					foreach($user->tags() as $tag) {
						echo '<a href="/tag/'.$tag->tag_name.'" class="tag"><i class="fa fa-tag"></i> '.$tag->tag_name.'</a>';
					}
					echo '</p>';
				}


				$donors = DB::table('campaigns')->where('recipient_id', '=', $user->id)->join('donations', 'donations.campaign_id', '=', 'campaigns.campaign_id')->join('users', 'donations.user_id', '=', 'users.id')->where('users.id', '!=', $user->id)->orderBy('donations.created_at', 'DESC')->groupBy('users.id')->take(6)->get();
				echo '<h3>Recent Donors</h3>';
				if (sizeof($donors) == 0) {
					echo '<p>No recent donors</p>';
				} else {
					echo '<div style="overflow:auto;">';
					foreach($donors as $donor) {
						echo '<div class="donor" >';
						$photo_style = '';
						if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$donor->id.'.jpg') ) {
							$path = Image::path('/assets/user/'.$donor->id.'.jpg', 'resizeCrop', 100, 100);
							$photo_style = "background-image:url('$path');";
						}

						echo '<div style="'.$photo_style.';width:100px;height:100px;border-radius:2px;background-color:#ccc;">&nbsp;</div>';
						echo '<a href="/'.$donor->type.'/'.$donor->username.'">'.$donor->display_name.'</a>';
						echo '</div>';
					}
					echo '</div>';
				}
				$user->map;
				//echo $user->map->url_parameters;
				if (!empty( $user->map) ) {
					$edit_link = '';
					if (Auth::check() && Auth::user()->id == $user->id){ 
						$edit_link = '<a href="/settings/map"><i class="fa fa-pencil"></i> Edit Locations</a><br/>';
					}
					echo '<h3>Places that we help</h3>';
					echo $edit_link;
					echo '<img style="width:100%;" src="https://maps.googleapis.com/maps/api/staticmap'.$user->map->url_parameters.'&size=870x380" style=""/>';
					echo '<br/><br/>';
				}
			}
			?>



			
		</div>
				<div id="feed" style="display:none;">
					<?php
				if ($allow_edit) {
			?>
			<br/><div class="status_update">
				<?php
					if (empty($popOver_attrs)) {
						$post_count = DB::table('posts')->where('author_id', '=', $user->id)->count();
						if ($post_count == 0) {
							$popOver_attrs = $popOver_attrs = ' data-placement="bottom" data-trigger="manual" data-toggle="popover" title="Write your first post" data-content="Go ahead and share some of your thoughts. Maybe send out a greeting to the rest of the community!" '; 
						}
					} else {
						$popOver_attrs = '';
					}
				?>
				<input type="hidden" id="forum_id" value="0"/>
				<textarea class="field full-width" id="post_txt" placeholder="Share your thoughts, post links."></textarea>
				<img src="<?=$user->thumbnail()?>" style="height:32px;"/><button type="button" <?=$popOver_attrs?> class="popover-link btn btn-primary" id="submit-post"><i class="fa fa-share"></i> SUBMIT POST</button><button type="button" class="btn btn-no-border"><i class="fa fa-camera"></i></button><button type="button" class="btn btn-no-border"><i class="fa fa-trash"></i></button>
			</div>
			<?php
				}

			?>
					<?php
						foreach($feed as $story) {
							$object = '';
							if ($story->model == 'Post') {

								$object = Post::find($story->object_id);
								
							} else if ($story->model == 'Donation') {

								$object = Donation::find($story->object_id);
							}
							if (!empty($object)) {
									echo View::make('stories/'.$story->type, array('object' => $object, 'story_id' => $story->story_id));
							}
							
						}
					?>
				</div>
				
			
		</div>
		<div class="col-md-4 fus-section-header" style="text-align:left;">
			<?php
				
				if ($allow_edit && ($user->type == 'organization') && is_null($user->stripe_recipient_id)) {
					echo '<h4>Start accepting donations</h4>';
					?>
					<p>Get started raising contributions for your organization's cause. You will need to enter bank details to receive donations.</p>
					<a href="/settings/bank" class="btn btn-primary"><i class="fa fa-pencil"></i> Edit Bank Details</a>
					<?php
				} else if ($user->type == 'organization' && !is_null($user->stripe_recipient_id)) {
					echo '<h4>Start a Campaign</h4>';
					?>
					<p>Start a campaign for <?=$user->display_name?>, and raise funds for a worthy cause.</p>
					<a href="/organization/<?=$user->username?>/campaign" class="btn btn-primary"><i class="fa fa-plus"></i> Start Campaign</a>
					<?php
				}
				//echo json_encode($campaigns);
				if (sizeof($campaigns) > 0) {
					echo '<br/><br/>';
					foreach($campaigns as $campaign) {
						echo '<div class="campaign" style="text-align:left;">';
						//echo json_encode($campaign);
						$donated_amount = DB::table('donations')->where('campaign_id', '=', $campaign->campaign_id)->sum('amount');
						?>
						<div class="row">
							<div class="col-md-12">
								<h4><?=$campaign->title?><br/><a href="/campaign/<?=$campaign->campaign_id?>" style="font-size:12px;">Details</a></h4>
							</div>
						</div>
						<div class="row">
							<div class="col-md-9">
								<p>Organizer: <?=$campaign->creator->display_name?></p>
								<p>Donated: $<?=$donated_amount?></p>
								<p>Goal: $<?=number_format($campaign->goal_in_cents/100,2)?></p>
								<a href="/organization/<?=$user->username?>/campaign/<?=$campaign->campaign_id?>?amount=25" class="give btn btn-primary"><i class="fa fa-heart"></i> Give $25</a>&nbsp;&nbsp;
								<a href="/organization/<?=$user->username?>/campaign/<?=$campaign->campaign_id?>?amount=50" class="give">Other Amount...</a>
							</div>
							<div class="col-md-3">
								<img src="<?=$campaign->creator->thumbnail()?>"/>
							</div>
						</div>
						<?php
						echo '</div>';
					}
				}
			?>
		</div>
	</div>
	</div>
	<div id="send-message" class="screen" style="background-color:rgba(0,0,0,.85);">
		<div class="container">
			<h3>Message <?=$user->display_name?></h3>
			<label>Subject</label><br/>
			<input type="text" name="subject" id="subject" class="field"/><br/><br/>
			<label>Message</label><br/>
			<textarea name="message" id="message" class="field"></textarea><br/><br/>
			<button class="btn btn-primary" id="send-btn">Send</button>
			<a href="#" onclick="$('.screen').fadeOut(250);" class="close-btn">&times;</a><br/>
			<div id="send-result"></div>
		</div>
	</div>
<iframe id="target" name="target" style="width:0px;height:0px;border:0px;"></iframe>
<script>

	function showMessageScreen() {
		if (!logged_in) {
			$('#login-screen').fadeIn(250);
			action = $('#message-btn');
			return false;
		}

		$('#send-message').fadeIn(200);
	}

	$('.photo').hover(function() {
		$('#photo-form').show();
	}, function() {
		$('#photo-form').hide();
	});

	$('#submit-post').click(function() {

		$(this).find('i').removeClass('fa-share').addClass('fa-spin').addClass('fa-refresh');
		$.post('/posts/create', {post_txt: $('#post_txt').val(), forum_id: 0}, function(data) {
			$('#submit-post').find('i').addClass('fa-share').removeClass('fa-spin').removeClass('fa-refresh');
			$('#post_txt').val('');
			get_feed();

		});
		$('.popover-link').popover('hide');
	});

	function get_feed() {
		$.get('/user/<?=$user->id?>/feed', { }, function(html) {
			$('#feed').html(html);

			//Set all event listeners for new HTML.
			reinit_feed();
		});
	}

	function donationHandler(status, response) {
		if (response.error) {
			$('#donation-message').css('color', 'red').html('<i class="fa fa-remove"></i> '+response.error.message);

		} else {
			var campaign_id = $('#donation_campaign_id').val();
			var total = calculate();
			var donation_amount = $('input[name=amount]').val();
			var save_card = $('#save_card').prop('checked');
			$.post('/organization/<?=$user->username?>/campaign/'+campaign_id, {save_card: save_card, total: total, donation: donation_amount, token: response.id}, function(response) {
				if (response.success == 1) {
					$('#complete-donation').remove();
					$('#donation-message').css('color', 'green').html('<i class="fa fa-check"></i> Thank you for your donation!');
					setTimeout(function() { $('.screen').fadeOut(200); }, 2000);

					if (reload) {
						//If user logged in via ajax, reload page after donation to refresh view.
						document.location.reload();
					} else {

						get_feed();
					}
				} else {
					$('#donation-message').css('color', 'red').html('<i class="fa fa-remove"></i> An error occurred');
				}
				
			},'json');
		}
	}

	function saved_card() {
			var campaign_id = $('#donation_campaign_id').val();
			var total = calculate();
			var donation_amount = $('input[name=amount]').val();
			$.post('/organization/<?=$user->username?>/campaign/'+campaign_id, { total: total, donation: donation_amount, token: ''}, function(response) {
				if (response.success == 1) {
					$('#complete-donation').remove();
					$('#donation-message').css('color', 'green').html('<i class="fa fa-check"></i> Thank you for your donation!');
					setTimeout(function() { $('.screen').fadeOut(200); }, 2000);
					get_feed();
				} else {
					$('#donation-message').css('color', 'red').html('<i class="fa fa-remove"></i> An error occurred');
				}
			},'json');
		}
		$('#send-btn').click(function() {
			var message = $('#message').val();
			var subject = $('#subject').val();
			$.post('/user/<?=$user->id?>/message', {message: message, subject: subject}, function(data) {
				if (data.success) {
					$('#send-result').html(data.message).css('color', '#98cf2d');
					$('#message').val('');
					$('#subject').val('');
				} else {
					$('#send-result').html(data.message).css('color', 'red');
				}
			},'json');
		});
</script>
<?php
	echo View::make('footer');

?>
<div class="screen">
	<div class="overlay  fus-white-bg fus-section fus-feature fus-white-bg " id="overlay">

	</div>
</div>
<script>
$('.popover-link').each(function() {
	if ($(this).data('content')) {
		$(this).popover('show');
	}
});
</script>
</body>

</html>