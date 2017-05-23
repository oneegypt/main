<!DOCTYPE HTML>
<html>
	<head>
		<title>Forums</title>

		<?= View::make('header') ?>
		<style>
			.tag-link {
				margin-right:15px;
				display:inline-block;
			}

			<?php
			if (!empty($forum->banner_image_url) ) {
				$bg_url = Image::path($forum->banner_image_url, 'resize', 1200, 520);
				?>
				body {
					background-image:url('<?=$bg_url?>');
					background-repeat:no-repeat;
					background-size:100%;
					background-position:center 90px;
					

				}
				<?php
			}

			?>
			body {
				background-color:#f2f2f2;
				}
				.top-of-forum {
					margin-top:180px;
				}
				.top-of-forum h3 {
					font-weight:700;
					text-shadow:0px 0px 20px rgba(0,0,0,.77);
				}

				.thread small {
					color:#666;
				}

				.thread {
					padding:0 10px 10px;
					border-bottom:1px solid #e2e2e2;
					margin-bottom:10px;
				}

				.thread .thread-title {
					font-size:15px;
				}

				.white-backshadow {
					color:#fff;
					text-shadow:0px 0px 4px rgba(0,0,0,.4);
					padding:20px;
					font-size:16px;
					font-weight:700;
				}

				.white-backshadow:hover {
					color:#e2e2e2;
				}

				.socratic {
					border-bottom:5px solid #56d650;
					color:#fff;
					padding:5px 20px;
					font-size:13px;
					background-color:#333;
				}

				.socratic .type-tip {
					background-color:#56d650;
					color:#333;
				}

				.type-tip {
					border-radius:50%;
					line-height:15px;
					font-size:12px;
					height:15px;
					width:15px;
					text-align:center;
					
					display:inline-block;
					font-weight:700;
				}

				.practices {
					border-bottom:5px solid #d66050;
					color:#fff;
					padding:10px;background-color:#333;
					font-size:13px;
				}

				.practices .type-tip {
					background-color:#d66050;
					color:#333;
				}

				.trends .type-tip {
					background-color:#f3e659;
					color:#333;
				}

				.trends {
					border-bottom:5px solid #f3e659;
					color:#fff;
					padding:10px;
					background-color:#333;
					font-size:13px;
				}

				.popover-title,.popover-content {
					color:#333;
				}

				.guidelines {
					color:#fff;
					font-size:12px;
					text-decoration:underline;
				}

				.forum-sidebar, .forum-sidebar li {
					font-size:14px;
				}

				.forum-description {
					background-color:#f6f6f6;
					border:1px solid #ccc;
					font-size:15px;
					padding:17px;
					margin-top:12px;
					margin-bottom:12px;
				}

				.tag-link {
					font-size:12px;
				}

				div.guiding-questions {
					margin-top:17px;
					margin-bottom:17px;
				}
		</style>
	</head>
	<body>

		<?php
			echo View::make('navigation'); 
		?>
		<div class="container top-of-forum">
			<h3>
				<?php
					if ($forum->open == false) {
						echo '<i class="fa fa-lock"></i>&nbsp;';
					}

				?>

				<?=$forum->title?></h3>


			<?php
				$following = 0;

				if (Auth::check() ) {
					$following = DB::table('forum_followers')->where('id', '=', Auth::user()->id)->where('forum_id','=', $forum->forum_id)->count();
			
					if ($following > 0) {
			?>
					<a href="/forum/<?=$forum->forum_id?>/unfollow" class="btn btn-primary" id="unfollow"><i class="fa fa-check"></i> FOLLOWED</a>

			<?php
					} else {
			?>
			<a href="/forum/<?=$forum->forum_id?>/follow" class="btn btn-primary"><i class="fa fa-star"></i> FOLLOW</a>
			<?php
					}
				} else {
			?>
				<a href="/forum/<?=$forum->forum_id?>/follow" class="btn btn-primary"><i class="fa fa-star"></i> FOLLOW</a>
		
			<?php
				}

				if (Auth::check() ) {
					$is_mod = DB::table('user_privileges')->where(array(
							'user_id' => Auth::user()->id,
							'scope' => $forum->forum_id,
							'privilege_key' => 'mod'
						))->count();
					if ($is_mod > 0) {
						echo '<a href="/forum/settings/'.$forum->forum_id.'" class="white-backshadow"><i class="fa fa-gears"></i></a>';
					}

				}
			?>

			<br/><br/>
		</div>
		<?php
			if (!empty($forum->type)) {
					switch($forum->type) {
						case 'socratic':
							$type_title = 'Socratic Discussion';
							$tooltip = 'Forums based on the Socratic method of questioning and inquiry to increase understanding and encourage thinking.';
							break;
						case 'trends':
							$type_title = 'Industry Trends Discussion';
							$tooltip = 'Forums discussing new and popular trends, innovations, or recent findings in a specific industry.';
							break;
						case 'practices':
							$type_title = 'Best Practices Discussion';
							$tooltip = 'Forums highlighting the best practices, methods, and techniques used in many different industries.';
							break;
						default:
							break;
					}
				?>
				<div class="container <?=$forum->type?>">
					This is a <strong><?=$type_title?></strong>&nbsp;<a href="#" class="type-tip" data-toggle="popover" title="<?=$type_title?>" data-content="<?=$tooltip?>">?</a> <a class="guidelines" href="http://info.oneegypt.org/policy" target="new">User Guidelines</a>
				</div>

				<?php
			}

		?>
		<div class="container fus-white-bg fus-section fus-feature fus-white-bg">

			<div class="col-md-8">
			
				<!--div class="row">
					<div class="col-md-3">
						<a href="/forums/create" class="btn btn-primary btn-block">Start a Forum</a>
					</div>
					<div class="col-md-6">
						<input type="text" class="field btn-block" id="search" placeholder="Search organizations, campaigns, forums"/>
					</div>
					<div class="col-md-3">
						<a href="/forums/start" class="btn btn-warning btn-block">INVITE FRIENDS</a>
					</div>
				</div-->
				<?php
					if (Auth::check() && Auth::user()->id == $forum->creator_id) {

						if (empty($forum->banner_image_url) ) {
							?>
							<br/><div class="alert alert-warning alert-dismissible" role="alert">
							  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							  <strong>Upload a banner image!</strong> You can do this in your <a href="/forum/settings/<?=$forum->forum_id?>">forum settings</a>.
							</div>
							<?php
						}
					}
				?>
				<?php
					if ($forum->open == 1) {
						$create_link = '/forums/'.$forum->forum_id.'/new';
						$create_link_disabled = '';
					} else {
						$create_link = '#';
						$create_link_disabled = ' style="opacity:.44;" ';
					}

				?>
				<div class="row">
					<div class="col-md-12">
						<a class="btn btn-primary" <?=$create_link_disabled?> href="<?=$create_link?>"><i class="fa fa-plus"></i> CREATE NEW THREAD</a>
						<br/><br/>
					</div>
				</div>

				<div class="row">
					<div id="posts" class="col-md-12">
						<?php
							foreach($threads as $thread) {
								$author = $thread->author;
								?>
								<div class="row thread">
									<div class="col-md-1">
										<div class="circle" style="background-image:url('<?=$author->thumbnail()?>');">&nbsp;</div>
									</div>
									<div class="col-md-11">
										<a class="thread-title" href="/forums/<?=$thread->forum_id?>/threads/<?=$thread->forum_thread_id?>/1"><?=$thread->topic?></a><br/>
										<small>Last post <?=ago($thread->updated_at)?> ago from 
											<?php
											if (!is_null($thread->lastPost) ) {
										
										echo $thread->lastPost->author->display_name;
										
											}

										?>

											- <i class="fa fa-comment"></i> <?=sizeof($thread->posts)?></small>
									</div>
								</div>

								<?php
							}


						?>
					</div>
				</div>
			</div>
			<div class="col-md-4 forum-sidebar">

						<?php
							$follower_count = DB::table('forum_followers')->where('forum_id','=', $forum->forum_id)->count();

							echo '<i class="fa fa-group"></i> '.number_format($follower_count).' following<br/><br/>';

							if (!Cache::has('forum_followers_'.$forum->forum_id)) {
								$followers = User::join('forum_followers' ,'forum_followers.id' ,'=', 'users.id')->where('forum_followers.forum_id', '=', $forum->forum_id)->take(25)->get();
								Cache::add('forum_followers_'.$forum->forum_id, $followers, 120);
							}
							$followers = Cache::get('forum_followers_'.$forum->forum_id);

							foreach($followers as $follower) {
								if ($follower->thumbnail()) {
									echo '<a href="/'.$follower->type.'/'.$follower->username.'"><img src="'.$follower->thumbnail().'"/></a>';
								}
							}
						?>
						<div class="forum-description">
							<p><?=$forum->description?></p>
						</div>
						<?php
							if (!empty($forum->youtube_video_id)) {
								echo '<strong>Featured Video</strong><br/>';
								?>
								<iframe width="320" height="200" src="https://www.youtube.com/embed/<?=$forum->youtube_video_id?>" frameborder="0" allowfullscreen></iframe>
								<?php
							}
						?>
						<p>
						<?php
							foreach($tags as $tag) {
								if (empty($tag->tag_name)) {
									continue;
								}
								echo '<a href="/forums/tagged/'.$tag->tag_name.'" class="tag-link"><i class="fa fa-tag"></i> '.urldecode($tag->tag_name).'</a>';
							}
						?>
						</p>

						<?php
							if (sizeof($moderators) > 0) {
								echo '<strong>Moderators</strong><br/>';
								//echo json_encode($moderators);
								foreach($moderators as $mod) {
									echo '<i class="fa fa-user"></i> <a href="/'.$mod->user->type.'/'.$mod->user->username.'">'.$mod->user->display_name.'</a>';
								}
								echo '<Br/><br/>';
							}
							echo '<div class="guiding-questions">';
							echo '<strong>Guiding Questions</strong><br/>';
							for($i = 1; $i <= 5; $i++) {
								$attribute = 'guiding_question_'.$i;
								if (!empty($forum->$attribute)) {
									echo '<i class="fa fa-arrow-right"></i> '.$forum->$attribute.'<br/>';
								}
							}
							echo '</div>';
							if (!empty($forum->guidelines)) {
								echo '<strong>Additional Comments</strong><br/>';
								echo $forum->guidelines;
							}

						?>

			</div>
		</div>
	<script>
		$('#unfollow').hover(function() {
			$(this).html('<i class="fa fa-remove"></i> UNFOLLOW').removeClass('btn-primary');
		}, function() {
			$(this).html('<i class="fa fa-check"></i> FOLLOWED').addClass('btn-primary');
		});

		$(function () {
		  $('[data-toggle="popover"]').popover();
		})
	</script>
		<?php
		echo View::make('footer');

		?>
	</body>
</html>