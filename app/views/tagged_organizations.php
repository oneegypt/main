<!DOCTYPE HTML>
<html>
<head>
	<?= View::make('header') ?>
	<style>
		.search-result {
			clear:both;
			padding:5px;
		}

	</style>
</head>
<body>
	<?= View::make('navigation') ?>
	<div class="container" style="min-height:900px;">
		<div class="row">
			<div class="col-md-8" style="color:#363636;">
				<h3>Organizations tagged <span style="font-style:italic;"><?=$tag?></span></h3>
				<?php
					foreach($organizations as $org) {
						echo '<div class="search-result">';
						if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$org->id.'.jpg') ) {
							$path = Image::path('/assets/user/'.$org->id.'.jpg', 'resizeCrop', 50, 50);
							echo '<img src="'.$path.'" style="float:left;margin-right:5px;"/>';
						} else {
							echo '<div class="place-holder">&nbsp;</div>';
						}
						echo '<a href="/organization/'.$org->username.'" style="font-weight:700;">'.$org->display_name.'</a>&nbsp;';
						echo '<small>'.substr($org->description_txt, 0, 150).'...</small><br/>';
						echo '<small><i class="fa fa-star"></i> '.sizeof($org->followers).' followers</small>';
						echo '</div>';
					}
				?>
			</div>
		</div>
	</div>
	<?=View::make('footer')?>
</body>
</html>