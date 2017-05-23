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
</head>
<body class="">
	<?=View::make('navigation')->with('highlight','dialogue')?>
	<div class="container fus-white-bg fus-section fus-feature fus-white-bg" style="min-height:100%;" >
		<div class="row">
			<div class="col-md-12">
				<h3>Forums tagged <span style="font-style:italic;"><?=$tag?></span></h3>

			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<?php
					foreach($forums as $forum) {
						echo '<div class="forum-search-result">';
						echo '<a href="/forums/'.$forum->forum_id.'/">'.$forum->title.'</a><br/>';
						echo '<i class="fa fa-comment"></i> '.number_format(sizeof($forum->forumPosts)).' posts';
						echo ' - <i class="fa fa-user"></i> '.sizeof($forum->participants).' participants';
						if (isset($forum->forumPosts[0])) {
							echo  ' - Last post on '.date('F jS, Y h:i a', strtotime($forum->forumPosts[0]->created_at));
						}
						echo '</div>';
					}


				?>
				
			</div>
		</div>
	</div>
</body>
</html>