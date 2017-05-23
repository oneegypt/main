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
	<style>
		.forum-title {
			font-weight:700;
		}

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

	
	</style>
</head>
<body class="">
	<?=View::make('navigation')->with('highlight','dialogue')?>
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
		  		$image_url = Image::path($slide->image_url, 'resize', 1220, 420);
		  		?>
		    <div class="item <?=$active?>" style="background-image:url('<?=$slide->image_url?>');height:420px;background-size:cover;background-position:center center;" >
		    	
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
						<h2>Participate in forums moderated by global experts on socio-economic issues relevant to Egypt and the international community.</h2>
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">&nbsp;
					</div>
					<div class="col-md-8">
						<br/><br/>
						<input type="text" class="field "  id="search2" placeholder="Search by topic, moderator or keyword" style="width:100%;"/>
					</div>
				</div>
					<?php
						$i = 0;
						foreach($categories as $cat) {

							if ($i%3 == 0) {
								echo '<div class="row">';
							}

							echo '<div class="col-md-4">';
							echo '<h3 style="font-weight:700;"><i class="fa '.$cat->icon.'"></i> '.$cat->category_name.'</h3>';
							echo '<small>'.$cat->description.'</small><br/><br/>';
							foreach($cat->forums as $forum) {
								$forum->num_participants = sizeof($forum->participants);
								$last_posted = '';
								if (sizeof($forum->posts) > 0) {
									$last_posted = 'Last post on '.date('F jS, Y', strtotime($forum->posts[0]->created_at));
								}
								?>
								<div class="forum">
									<a href="/forums/<?=$forum->forum_id?>" class="forum-title"><?=$forum->title?></a>
									<br/><small><?=number_format($forum->num_participants)?> participants - <?=$last_posted?></small>
								</div>

								<?php
							}

							echo '</div>';
							$i++;
							if ($i%3 == 0) {
								echo '</div>';
							}
						}

						if ($i%3 > 0) {
							echo '</div>';
						}
					?>
				
						
	</div>
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
				$.get('/openSearch', {page: 1, q: q}, function(html) {
					$('#search-results-show').fadeIn(200).html(html).css('top', top).css('left', left).css('width', width);
				});
			}, 500);
		});
	</script>
	<?php echo View::make('footer'); ?>


	<div class="screen" style="display:none;" id="help">
		<div class="centered" style="width:800px;margin:auto;background-color:#fff; color:#363636;padding:20px;margin-top:80px;">
			<h3>Types of Forums</h3>
			<div class="row">
				<div class="col-md-4">
					<h1><i class="fa fa-lightbulb-o"></i></h1>
					<strong>Socratic Discussions</strong><br/>
					<p>Forums based on the Socratic method of questioning and inquiry to increase understanding and encourage thinking. </p>
				</div>
				<div class="col-md-4">
					<h1><i class="fa fa-thumbs-o-up"></i></h1>
					<strong>Best Practices</strong><br/>
					<p>Forums highlighting the best practices, methods, and techniques used in many different industries. </p>
				</div>
				<div class="col-md-4">
					<h1><i class="fa fa-line-chart"></i></h1>
					<strong>Industry Trends</strong><br/>
					<p>Forums discussing new and popular trends, innovations, or recent findings in a specific industry. </p>
				</div>
			</div>
			<div style="text-align:center;margin-top:40px;">
				<button type="button" onclick="$('.screen').fadeOut(200);setCookie('popup-dialogue', 1, 30);$('.help').fadeIn(250);" class="btn btn-primary">Ok, got it!</button>
			</div>
		</div>
	</div>	
	<div class="help">
		<a href="#" onclick="$('#help').fadeIn(250);$('.help').fadeOut(250);">?</a>
	</div>
	<script>
	$(document).ready(function() {
			if (getCookie('popup-dialogue') == false) {
				$('#help').fadeIn(200);
			}
			
			
		});
	</script>
</body>
</html>