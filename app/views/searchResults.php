<!DOCTYPE HTML>
<html>
<head>
	<?= View::make('header')?>
	<style>
	.thumb-large {
		width:100px;
		height:100px;
		background-color:#f8f8f8;
	}

	.searchResult h2 a {
		color:#363636;
	}

	.searchResult h2 a:hover {
		color:#000;
		text-decoration:underline;
	}

	.searchResult {
		text-align:left !important;
	}

	.searchResult h2 {
		font-size:18px;
		padding:0px;
		margin:0px;

	}
	.searchResult h4 {
		font-size:14px !important;
		padding:0px;
	}
	</style>
</head>
<body class="fus-blue">
	<?=View::make('navigation') ?>

	<div class="container fus-white-bg fus-section fus-feature fus-white-bg">
		<div class="row">
			<div class="col-md-8 ">
				<h1><?=$total_results?> results</h1>
				<div class="dropdown">
					<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
					   	<i class="fa fa-chevron-down"></i> Filter
					  </button>
					  <ul class="dropdown-menu prevent-default" role="menu" aria-labelledby="dropdownMenu1">
					    <li role="presentation"><a role="menuitem" tabindex="-1" href="?q=<?=Input::get('q')?>">All Types</a></li>
					    <li role="presentation"><a role="menuitem" tabindex="-1" href="?q=<?=Input::get('q')?>&t=individual">Only Individuals</a></li>
					    <li role="presentation"><a role="menuitem" tabindex="-1" href="?q=<?=Input::get('q')?>&t=organization">Only Organizations</a></li>
					    <li role="presentation"><a role="menuitem" tabindex="-1" href="?q=<?=Input::get('q')?>&t=company">Only Companies</a></li>
					</ul>
				</div>
				<br/>
				<?php
					foreach($results as $user) {
						$description = '';
						if (!empty($user->title) && !empty($user->city) && !empty($user->country)) {
							$description = $user->title.' from '.$user->city.', '.$user->country;
						} else if ($user->type == 'organization') {
							$description = 'Organization';
						} else if ($user->type == 'individual'){
							$description = 'Joined OneEgypt on '.date('F jS, Y', strtotime($user->created_at));
						}

						$photo_style = '';
						if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$user->id.'.jpg') ) {
							$path = Image::path('/assets/user/'.$user->id.'.jpg', 'resizeCrop', 100, 100);
							$photo_style = "background-image:url('$path');";
						}
						?>
						<div class="searchResult row">
							<div class="col-md-2">
								
								<div class="thumb-large" style="<?=$photo_style?>">
									&nbsp;
								</div>
							</div>
							<div class="col-md-8  " >
								<div class="fus-section-header" style="text-align:left;padding:5px;margin-bottom:5px;">
									<h2><a href="/<?=$user->type?>/<?=$user->username?>"><?=$user->display_name?></a></h2>
									<h4><?=$description?></h4>
								</div>
								<div>
									<small><?=$user->follower_count()?> followers / <?=$user->following_count()?> followed / <?=$user->recent_donation_quantity() ?> recent donations</small>

								</div>
							</div>
						</div>

						<?php
					}
				?>
			</div>

		</div>
		<div class="row">
			<div class="col-md-8">
				<?php
					for($i = 1; $i <= $total_pages; $i++) {
						$className = 'btn-default';
						if ($i == $page) {
							$className = 'btn-primary';
						}
						$t = '';
						if (Input::has('t')) {
							$t = '&t='.Input::get('t');
						}
						echo '<a href="?q='.Input::get('q').'&p='.$i.'&'.$t.'" class="btn '.$className.'">'.$i.'</a>&nbsp;';
					}

				?>
			</div>
		</div>
	</div>
</body>
</html>