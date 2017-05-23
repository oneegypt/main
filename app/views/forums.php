<?php
	/*
	* Support page.
	*/
?>
<!DOCTYPE HTML>
<html>
<head>
	<title>OneEgypt.org - Home</title>
	<?= View::make('header')?>
	<link href="/slick.css" rel="stylesheet">
	<script src="/slick.js" type="text/javascript"></script>
	<style>
		.help {
			position:fixed;
			bottom:0px;
			right:50px;
			background-color:#363636;
			color:#fff;
			box-shadow:0px 0px 3px rgba(0,0,0,.5);
			border-top-left-radius:5px;
			border-top-right-radius:5px;
			padding:15px;
			font-weight:700;
		}

		.help a {
			color:#fff;
			font-size:18px;
		}

.organization-slider .carousel-inner .active.left { left: -33%; }
.organization-slider .carousel-inner .next        { left:  33%; }
.organization-slider .carousel-inner .prev        { left: -33%; }
.organization-slider .item:not(.prev) {visibility: visible;}
.organization-slider .item.right:not(.prev) {visibility: hidden;}
.organization-slider .rightest{ visibility: visible;}

.organization-slider {
	height:320px;
	text-align:center;
}

.organization-slider a.org-title {
	font-weight:700;
	font-size:16px;
	color:#363636;
}

.place-holder {
	height:150px;
	margin:auto;
	width:150px;
	background-color:#f2f2f2;
}

.update-feed  {
	font-size:13px;
}

.organization-slide {
	height:250px;
	text-align:center;
}

.slick-carousel {
	position:relative;
}

.selected-page {
	background-color:#363636;
	color:#fff;
}

.slick-arrow {
	background-color:#fff;
	display:block;
	width:25px;
	height:25px;
	border-radius:50%;
	border:2px solid #363636;
	color:#363636;
	text-align:center;
	line-height:22px;
	font-size:13px;
	box-shadow:0px 1px 4px rgba(0,0,0,.15);
	padding:0px;
	font-weight:700;
	position:absolute;
	top:45%;
	z-index:900;
}

.slick-next {
	right:0px;

}
.slick-prev {
	left:0px;
}
	</style>
</head>
<body class="">
	<?=View::make('navigation')->with('highlight','support')?>
	<div class="container fus-white-bg fus-section fus-feature fus-white-bg" style="min-height:100%;" >
		<br/>
		<div id="carousel-example-generic" class="carousel slide" data-ride="carousel" >
		  <!-- Indicators -->
		  <ol class="carousel-indicators">
		    <?php
		    	$active = 'active';
		    	for($i = 0; $i < sizeof($sliders);$i++) {

		    		echo '<li data-target="#carousel-example-generic" data-slide-to="'.$i.'" class="'.$active.'"></li>';
		    		$active = '';
		    	}
		    ?>
		  </ol>

		  <!-- Wrapper for slides -->
		  <div class="carousel-inner" role="listbox">
		  	<?php
		  	$active = 'active';
		  	foreach($sliders as $slide) { 
		  		$image_url = Image::path($slide->image_url, 'resize', 1200, 420, true);
		  		?>
		    <div class="item <?=$active?>" style="background-image:url('<?=$image_url?>');height:480px;background-size:cover;background-position:center center;">
		      <div class="carousel-caption">
		       <h3 style="font-weight:700;"><?=$slide->title?></h3><br/>
		       <a href="<?=$slide->link_url?>" class="btn btn-primary"><?=$slide->action_txt?></a><br/><br/>
		      </div>
		    </div>
		    <?php
		    $active = '';
		}
		    ?>
		    
		  </div>

		  <!-- Controls -->
		  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
		    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
		    <span class="sr-only">Previous</span>
		  </a>
		  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
		    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
		    <span class="sr-only">Next</span>
		  </a>
		</div>


				<div class="row">
					<div class="col-md-12 centered">
						<h2>Support and stay connected to organizations and causes you care about related to Egypt and intercultural community building.</h2>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 centered">
						<br/><br/>
						<input type="text" class="field "  id="search2" placeholder="Search by organization, campaign or cause" style="width:66%;"/>
					</div>
				</div>
				
			<div class="row">

				<div class="col-md-8">
					<div class="row">
						<div class="col-md-12">
							<form action="/support" method="get" id="filter">
								<br/><label>View by category</label>&nbsp;<select name="category" class="form-control" onchange="$('#filter').submit();">
									<option value="0">-----</option>
									<?php
										foreach($categories as $cat) {
											$selected = '';
											if ($cat->category_id == Input::get('category')) {
												$selected = ' SELECTED';
											}

											echo '<option value="'.$cat->category_id.'" '.$selected.'>'.$cat->category_name.'</option>';
										}

									?>
								</select><br/><br/>
							</form>
						</div>	
					</div>
					<?php

					foreach($organizations as $organization) {
								echo '<div class="col-md-3">';
								?>
								<div class="organization-slide">
			                  	<?php
			                  	if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$organization->id.'.jpg') ) {
									$path = Image::path('/assets/user/'.$organization->id.'.jpg', 'resizeCrop', 150, 150);
									echo '<a href="/organization/'.$organization->username.'"><img src="'.$path.'"/></a><br/>';
								} else {
									echo '<div class="place-holder">&nbsp;</div>';
								}

			                  	?>
				                  <a href="/organization/<?=$organization->username?>" class="org-title"><?=$organization->display_name?></a>
				                  <p style="font-size:12px;"><?=substr($organization->description_txt, 0, 100)?>...</p>
				             	</div>

								<?php
								echo '</div>';
							
						}
					?>
					<div class="row">
						<div class="col-md-12"  style="padding-top:100px;">
							<?php
								for($i = 1; $i <= $num_pages; $i++) {
									$pager = '/support?a=1';
									if (Input::has('c')) {
										$pager .= '&c='.Input::get('c');
									}

									$pager .= '&p='.$i;
									
									$className = '';

									if (Input::has('p') && Input::get('p') == $i) {
										$className = 'selected-page';
									} else if (!Input::has('p') && $i == 1) {
										$className = 'selected-page';
									}

									echo '<a href="'.$pager.'" style="margin-right:10px;padding:6px;" class="'.$className.'">'.$i.'</a>';
								}
							?>
						</div>
					</div>
				</div>
				
				<div class="col-md-4 update-feed">
					<h3>Top Campaigns</h3>
					<table class="table">
					<?php
						//echo json_encode($campaigns);
						foreach($campaigns as $campaign) {
							?>
							<tr>
								<td><a href="/campaign/<?=$campaign->campaign_id?>"><?=$campaign->title?></a></td>
								<td>$<?=number_format($campaign->total, 2)?> raised</td>
							</tr>

							<?php
						}
					?>
				</table>
				<h3>Top Non-profits</h3>
				<table class="table">
					<?php
						foreach($top_non_profits as $non_profit) {
							?>
							<tr>
								<td><a href="/organization/<?=$non_profit->username?>"><?=$non_profit->display_name?></a></td>
								<td>$<?=number_format($non_profit->total,2)?> raised</td>

							</tr>

							<?php
						}

					?>
				</table>


					<h3>Recent activity</h3>
					<?php
					foreach($recent as $story) {
						

						if (!isset($story->donation_id)) {
							$image_url = '';
							if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$story->creator_id.'.jpg') ) {
								$image_url = Image::path('/assets/user/'.$story->creator_id.'.jpg', 'resizeCrop', 50, 50);
								
							}
							//echo json_encode($story);
							//die();
							//$story->load('creator');
							echo '<div class="row">';
							echo '<div class="col-md-2"><div class="circle" style="background-image:url(\''.$image_url.'\');"/>&nbsp;</div></div>';
							echo '<div class="col-md-10"><a href="/user/'.$story->user_id.'">'.$story->creator->display_name.'</a> started a new campaign: <a href="/campaign/'.$story->campaign_id.'">'.$story->title.'</a><br/><small>'.ago($story->created_at).' ago</small></div>';
							echo '</div>';
						} else  {
							$image_url = '';
							if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$story->user_id.'.jpg') ) {
								$image_url = Image::path('/assets/user/'.$story->user_id.'.jpg', 'resizeCrop', 50, 50);
								
							}
							echo '<div class="row">';
							echo '<div class="col-md-2"><div class="circle" style="background-image:url(\''.$image_url.'\');">&nbsp;</div></div>';
							echo '<div class="col-md-10"><a href="/user/'.$story->user_id.'">'.$story->donater->display_name.'</a> made a contribution to <a href="/campaign/'.$story->campaign_id.'">'.$story->campaign->title.'</a><br/><small>'.ago($story->created_at).' ago</small></div>';
							echo '</div>';
						}
				
					}

					?>
				</div>
			</div>
	</div>

	<?php echo View::make('footer'); ?>

	<div class="screen" style="display:none;" id="help">
		<div class="centered" style="width:800px;margin:auto;background-color:#fff; color:#363636;padding:20px;margin-top:80px;">
			<h3>What can you do?</h3>
			<div class="row">
				<div class="col-md-4">
					<h1><i class="fa fa-search"></i></h1>
					<p><strong>Search for organizations and causes</strong>, that you are passionate about, to learn more</p>
				</div>
				<div class="col-md-4">
					<h1><i class="fa fa-star"></i></h1>
					<p><strong>Stay informed</strong> and grow your network by following organizations or individuals with similar interests</p>
				</div>
				<div class="col-md-4">
					<h1><i class="fa fa-heart"></i></h1>
					<p><strong>Start or join campaigns</strong> to promote and support causes you care about. </p>
				</div>
			</div>
			<div style="text-align:center;margin-top:40px;">
				<button type="button" onclick="$('.screen').fadeOut(200);setCookie('popup', 1, 30);$('.help').fadeIn(250);" class="btn btn-primary">Ok, got it!</button>
			</div>
		</div>
	</div>
	<div class="help">
		<a href="#" onclick="$('#help').fadeIn(250);$('.help').fadeOut(250);">?</a>
	</div>
	<script>
		$(document).ready(function() {
			if (getCookie('popup') == false) {
				$('.screen').fadeIn(200);
			}
			
			$('.slick-carousel').slick({
				slidesToShow: 4,
				slidesToScroll: 1,
				centerMode: true
			});

			$('.slick-next').html('<i class="fa fa-arrow-right"></i>');
			$('.slick-prev').html('<i class="fa fa-arrow-left"></i>');
		});


	</script>
	<div id="search-results-show">

	</div>
	<script>
		var tId = 0;
		$('#search2').keyup(function() {
			clearTimeout(tId);
			var q = $(this).val();
			var top = $(this).offset().top + $(this).height()+20;
			var left = $(this).offset().left;
			var width = $(this).width();
			tId = setTimeout(function() {
				$.get('/openSearch', {page: 2, q: q}, function(html) {
					$('#search-results-show').fadeIn(200).html(html).css('top', top).css('left', left).css('width', width);
				});
			}, 500);
		});
	</script>
</body>
</html>