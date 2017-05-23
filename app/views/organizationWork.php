<!DOCTYPE HTML>
<html>
<head>
	<?= View::make('header') ?>
</head>
<body>
	<?=View::make('navigation')->with('highlight', 'work') ?>
	<div class="container white-bg fus-white-bg fus-section">
		<div class="row">
			<div class="col-md-12">
			<h3>Job listings for <a href="/user/<?=$user->id?>"><?=$user->display_name?></a></h3>

			<?php

				foreach($user->listings as $job) {
						$style = '';
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
								<a href="/listings/<?=$job->listing_id?>/aasdfasdf" class="job-title"><?=$job->listing_title?></a><br/>
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
			?>
			</div>
		</div>
	</div>
</body>
</html>