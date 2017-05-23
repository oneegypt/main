<!DOCTYPE HTML>
<html>
<head>
	<?=View::make('header')?>
	<style>
		body {
			background-color:#f2f2f2;
		}

		.forum-hero {
			background-size:cover;
			padding:20px;
		}

		.forum-content  {
			background-color:rgba(255,255,255,.90);
			padding:15px;
			color:#363636;
		}
	</style>	
</head>
<body>
	<?=View::make('navigation')->with('highlight', 'dialogue')?>
	<div class="container fus-white-bg fus-section fus-feature fus-white-bg">
		<div class="row">
			<div class="col-md-9">
				<h1>Forums tagged <span style="font-style:italic;"><?=$the_tag?></span></h1>
				<?php
					foreach($forums as $forum) {
						$image_url = $forum->banner_image_url;
						$image_url = Image::path($image_url, 'resizeCrop', 800, 120);

				?>
				<div class="forum-hero" style="background-image:url('<?=$image_url?>');">
					<div class="forum-content">
						<a href="/forums/<?=$forum->forum_id?>"><?=$forum->title?></a><br/>
						<span><?=$forum->cat->category_name?></span> - <span><?=sizeof($forum->followers)?> followers</span>
					</div>
				</div>
				<?php
					}
				?>
			</div>
			<div class="col-md-3">
				<h3>Related Tags</h3>

			</div>
		</div>

	</div>
</body>
</html>