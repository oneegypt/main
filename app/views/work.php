<?php
	/*
	* Work/Serve page.
	*/
?>
<!DOCTYPE HTML>
<html>
<head>
	<title>OneEgypt.org - Home</title>
	<?= View::make('header')?>
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

		.selected-page {
	background-color:#363636;
	color:#fff;
}

	
	</style>
</head>
<body class="">
	<?=View::make('navigation')->with('highlight','work')?>
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
		  		$image_url = Image::path($slide->image_url, 'resize', 1200, 420);
		  		?>
		    <div class="item <?=$active?>" style="background-image:url('<?=$image_url?>');background-size:cover;background-position:center center;height:420px;">
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
		<br/>
		<div class="centered">
		<h2>Apply to jobs, internships and volunteer opportunities at home or abroad related to Egypt and the MENA region.</h2>
		<br/>
		</div>
		<form action="" method="get">
		<div class="row">
			<div class="col-md-3">
				<?php
					if (Auth::check() && Auth::user()->type != 'individual') {
				?>
				<a href="/listings/post" class="btn btn-primary"><i class="fa fa-briefcase"></i> POST JOB LISTING</a>
				<?php
					} else {
						echo '&nbsp;';
					}
				?>
			</div>
			<div class="col-md-8">
				<input type="text" name="q" value="<?=Input::get('q')?>" placeholder="Search for jobs, keywords, and companies" class="field"/>
				<input type="text" name="l" value="<?=Input::get('l')?>" placeholder="Your Location" class="field"/>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">

				<label>Job Type</label><br/>
				<?php
					$types = array('full-time' => 'Full Time', 'part-time' => 'Part Time',
						'temporary' => 'Temporary',
						'contract' => 'Contract',
						'internship' => 'Internship',
						'volunteer' => 'Volunteer'

						);
					foreach($types as $key => $type) {
						$checked = '';
						if (Input::has('t')) {
							$checked = in_array($key, Input::get('t'))?' checked':'';
						}

						echo '<input '.$checked.' type="checkbox"  name="t[]"id="'.$key.'" value="'.$key.'"/>&nbsp;<span>'.$type.'</span><br/>';
					}
				?>
				<br/><label>Categories</label><br/>
				<?php
					foreach($categories as $category) {
						$checked = '';
						if (Input::has('c')) {
							$checked = in_array($category->category_id, Input::get('c'))?' checked':'';
						}
						echo '<input '.$checked.' type="checkbox" name="c[]" id="cat_'.$category->category_id.'" value="'.$category->category_id.'"/>&nbsp;';
						echo '<span>'.$category->category_name.'</span><br/>';
					}
				?>
				<br/>
				<button class="btn btn-default" type="submit"><i class="fa fa-refresh"></i> FILTER RESULTS</button>
			</div>
			<div class="col-md-9">
				<div id="jobs">
				<?php
					foreach($recent as $job) {
						$style = '';
						$type = $types[$job->type];
						if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$job->creator_id.'.jpg')) {
							$style = "background-image:url('".Image::path('/assets/user/'.$job->creator_id.'.jpg', 'resizeCrop', 50, 50)."');";
						}
						?>
						<div class="job-listing row">
							<div style="float:left;width:60px;text-align:center;">
								<div class="circle" style="<?=$style?>">&nbsp;</div>
								<small><?=ago($job->created_at)?> ago</small>
							</div>
							<div class="job-content">
								<a href="/listings/<?=$job->listing_id?>/aasdfasdf" class="job-title"><?=$job->listing_title?></a> (<?=$type?>)<br/>
								<span class="employer"><?=$job->postedBy->display_name?></span><br/>
								<?php
									$excerpt = '';
									if (strlen($job->listing_body) > 200) {
										$excerpt = substr($job->listing_body,0, 200).'...';
									} else {
										$excerpt = $job->listing_body;
									}
									echo '<p>'.$excerpt.'</p>';
								?>
							</div>
							
						</div>
						<?php
					}
					echo '</div>';
					echo '<div class="row">';
					?>
					<div class="col-md-12"  style="padding-top:100px;">
							<?php
								$pager = '/work?a=1';
								if (Input::has('c')) {
									foreach(Input::get('c') as $c) {
										$pager .= '&c[]='.$c;
									}
								}

								if (Input::has('t')) {
									foreach(Input::get('t') as $t) {
										$pager .= '&t[]='.$t;
									}
								}

								if (Input::has('q')) {
									$pager .= '&q='.Input::get('q');
								}

								if (Input::has('tag')) {
									$pager .= '&tag='.Input::get('tag');
								}

								$pager .= '&p=';
								
			

								echo '<a class="btn" href="'.$pager.'" style="margin-right:10px;padding:6px;" id="more-btn" data-page="2">MORE</a>';
								
							?>
						</div>
					<?php
					echo '</div>';
				?>
			</div>
		</div>
	</form>
	</div>
	<?= View::make('footer') ?>
		<div class="screen" style="display:none;" id="help">
		<div class="centered" style="width:800px;margin:auto;background-color:#fff; color:#363636;padding:20px;margin-top:80px;">
			<h3>What can you do?</h3>
			<div class="row">
				<div class="col-md-4">
					<h1><i class="fa fa-wrench"></i></h1>
					<strong>Work</strong><br/>
					<p>Pursue your dream job or internship related to Egypt and the Middle East.</p>
				</div>
				<div class="col-md-4">
					<h1><i class="fa fa-heart"></i></h1>
					<strong>Volunteer</strong><br/>
					<p>Apply your talents to support organizations and causes you care about. </p>
				</div>
				<div class="col-md-4">
					<h1><i class="fa fa-user"></i></h1>
					<strong>Hire</strong><br/>
					<p>Get access to a diverse pool of qualified candidates passionate about the Middle East.</p>
				</div>
			</div>
			<div style="text-align:center;margin-top:40px;">
				<button type="button" onclick="$('.screen').fadeOut(200);setCookie('popup-work2', 1, 30);$('.help').fadeIn(250);" class="btn btn-primary">Ok, got it!</button>
			</div>
		</div>
	</div>	
	<div class="help">
		<a href="#" onclick="$('#help').fadeIn(250);$('.help').fadeOut(250);">?</a>
	</div>
	<script>
	$(document).ready(function() {
			if (getCookie('popup-work2') == false) {
				$('#help').fadeIn(200);
			}
			
			$('#more-btn').click(function(e) {
				e.preventDefault();
				var page = $(this).data('page');
				page = parseInt(page);
				var href = $(this).attr('href');
				$.get(href+page, {}, function(response) {
					$('#jobs').append(response);

				});
				page++;
				$(this).data('page', ''+page);
			});
		});
	</script>
</body>
</html>