<!DOCTYPE HTML>
<html>
	<head>
		<title>Forums</title>

		<?= View::make('header')->with('highlight', 'dialogue') ?>
		
		<style>
			.tag-link {
				margin-right:15px;
				display:inline-block;
			}

			#editor {overflow-y:scroll;
				height:130px;
				color:#363636;
				border:1px solid #ccc;
				margin-top:1px;
				padding:3px;
				max-height:300px}


			<?php
			if (!empty($forum->banner_image_url) ) {
				$bg_url = Image::path($forum->banner_image_url, 'resizeCrop', 1200, 520);
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

				.post-author h4{
					padding:0px;
					margin:0px;
				}

				.btn-toolbar .btn {
					border:1px solid #e2e2e2;
					color:#666;
				}

				.thread-post {
					padding:14px;
					border-bottom:1px dashed #e2e2e2;
				}

				.quote {
					padding:8px 13px;
					background-color:#f2f2f2;

				}

				.thread-header {
					background-color:#f2f2f2;
					color:#333;
					max-width:100%;
					padding:15px;
					margin:0;
					box-shadow:0px 2px 2px rgba(0,0,0,.25);
				}

				.thread-header a {
					color:#333;
				}

				.thread-header a h3 {
					padding:0px;
					margin:0px;
					font-size:18px;
				}

				a.paginator {
					margin-right:10px;
					display:inline;
				}
				a.paginator.current {
					font-weight:700;
				}
		</style>
		<script src="//mindmup.github.io/bootstrap-wysiwyg/bootstrap-wysiwyg.js" ></script>
	</head>
	<body>

		<?php
			echo View::make('navigation'); 
			$thread_url = '/forums/'.$forum->forum_id.'/threads/'.$thread->forum_thread_id.'/'.$page;
			$forum_url = '/forums/'.$forum->forum_id;
		?>
		<div class="container top-of-forum">
			<a href="<?=$forum_url?>" style="color:#fff;"><h3>
				<?php
					if ($forum->open == false) {
						echo '<i class="fa fa-lock"></i>&nbsp;';
					}

				?>

				<?=$forum->title?></h3></a>
			<br/><br/>
		</div>
		<div class="container fus-white-bg fus-section fus-feature fus-white-bg" style="padding:0px;margin:auto;">
			<div class="row thread-header">
				<div class="col-md-10 ">
					<a href="<?=$thread_url?>"><h3><?=$thread->topic?></h3></a>
				</div>
				<div class="col-md-2">&nbsp;
				</div>
			</div>
			<?php
				foreach($posts as $post) {

					if ($post->author->id == $thread->creator_user_id) {
						$type = 'Original Poster';
					} else {
						$type = 'Participant';
					}

					$image_url = $post->author->thumbnail();
					?>
					<div class="thread-post">
						<div class="row">
							<div class="col-md-2">
								<div class="circle" style="background-image:url('<?=$image_url?>');">&nbsp;</div>
								
							</div>
							<div class="col-md-8">
								<a class="post-author" href="/<?=$post->author->type?>/<?=$post->author->username?>"><h4>@<?=$post->author->username?></h4><?=$post->author->display_name?></a>
							</div>
							<div class="col-md-2">
								<span>Posted <?=ago($post->created_at)?> ago</span><br/>
								<?php
								if (is_null($post->deleted_at)) {
								?>
								<a href="#reply" onclick="$('#to_whom').html('@<?=$post->author->username?>');$('input[name=reply_to]').val(<?=$post->forum_post_id?>);"><i class="fa fa-reply"></i> Reply</a>

								<?php
								}

									if (Auth::check()) {
										$is_mod = DB::table('user_privileges')->where('privilege_key', '=', 'mod')->where('scope', '=', $thread->forum_id)->where('user_id', '=', Auth::user()->id)->count();
										if ($is_mod == 0) {
											$is_mod = DB::table('user_privileges')->where('privilege_key', '=', 'global_mod')->where('user_id', '=', Auth::user()->id)->count();
										}

										if ($is_mod > 0) {
											if (is_null($post->deleted_at)) {
												echo '<br/><a href="/forumPosts/'.$post->forum_post_id.'/delete"><i class="fa fa-remove"></i> Delete Post</a>';
											} else {
												echo '<br/><a href="/forumPosts/'.$post->forum_post_id.'/restore"><i class="fa fa-refresh"></i> Restore Post</a>';
											}
										}
									}

								?>

							</div>
						</div>
						<div class="row">
							<div class="col-md-2 centered"><small><?=$type?></small></div>
							<div class="col-md-8" style="">
								<?php
									if (!is_null($post->reply_to)) {

										$replied = $post->repliedTo;
										$profile_link = '/'.$replied->author->type.'/'.$replied->author->username;
										echo '<div class="quote">';
										echo '<span><a href="'.$profile_link.'">@'.$replied->author->username.'</a> wrote on '.date('F jS Y h:i a', strtotime($replied->created_at)).':</span><br/>';
										if (is_null($replied->deleted_at)) {
											echo '<p>'.$replied->content_txt.'</p>';
										} else {
											echo '<p>[Original post was deleted by a moderator]</p>';
										}
										echo '</div>';
									}
								?>
								<?php
									if (is_null($post->deleted_at)) {
										echo $post->content_txt;
									} else {
										echo '[This post has been deleted by a moderator]';
									}
								?>
							</div>
						</div>
					</div>

					<?php
				}
			?>
			<div class="row">
				<div class="col-md-2">&nbsp;</div>
				<div class="col-md-8">
					<br/><span>Pages:</span>&nbsp;
					<?php
						for($i = 1; $i <= $total_pages; $i++) {
							$class = '';
							if ($i == $page) {
								$class= 'current';
							}
							echo '<a href="/forums/'.$forum->forum_id.'/threads/'.$thread->forum_thread_id.'/'.$i.'" class="paginator '.$class.'">'.$i.'</a>';
						}

					?>
				</div>
			</div>
			<?php
				if (Auth::check() && $forum->open == 1) {
			?>
			<div class="row" id="reply">
				<div class="col-md-2">&nbsp;</div>
				<div class="col-md-8">
					<br/>
					<h4>Reply to <span id="to_whom">Thread</span></h4>
					<div class="btn-toolbar" data-role="editor-toolbar" data-target="#editor">
				      <div class="btn-group">
				        <a class="btn dropdown-toggle" data-toggle="dropdown" title="Font Size"><i class="fa fa-text-height"></i>&nbsp;<b class="caret"></b></a>
				          <ul class="dropdown-menu">
				          <li><a data-edit="fontSize 5"><font size="5">Huge</font></a></li>
				          <li><a data-edit="fontSize 3"><font size="3">Normal</font></a></li>
				          <li><a data-edit="fontSize 1"><font size="1">Small</font></a></li>
				          </ul>
				      </div>
				      <div class="btn-group">
				        <a class="btn" data-edit="bold" title="Bold (Ctrl/Cmd+B)"><i class="fa fa-bold"></i></a>
				        <a class="btn" data-edit="italic" title="Italic (Ctrl/Cmd+I)"><i class="fa fa-italic"></i></a>
				        <a class="btn" data-edit="strikethrough" title="Strikethrough"><i class="fa fa-strikethrough"></i></a>
				        <a class="btn" data-edit="underline" title="Underline (Ctrl/Cmd+U)"><i class="fa fa-underline"></i></a>
				      </div>
				      <div class="btn-group">
						  <a class="btn dropdown-toggle" data-toggle="dropdown" title="Hyperlink"><i class="fa fa-link"></i></a>
						    <div class="dropdown-menu input-append">
							    <input class="span2" placeholder="URL" type="text" data-edit="createLink"/>
							    <button class="btn" type="button">Add</button>
				        	</div>
				    </div>
				      <div class="btn-group">
				        <a class="btn" title="Insert picture (or just drag & drop)" id="pictureBtn"><i class="fa fa-image"></i></a>
				        <input type="file" data-role="magic-overlay" data-target="#pictureBtn" data-edit="insertImage" />
				      </div>
				  </div>
				  <div id="editor"></div>
				  <form action="" method="post" id="reply_form">
				  		<input type="hidden" name="reply_to" value="0"/>
				  		<input type="hidden" name="html" value=""/>
					  <button class="btn btn-primary" type="button" onclick="submit_post();"><i class="fa fa-pencil"></i> SUBMIT REPLY</button>
					</form>
				  </div>

			  </div>
			  <?php
			  	} else if ($forum->open == 0) {
			  		echo '<div class="row">';
			  		echo '<div class="col-md-2">&nbsp;</div>';
			  		echo '<div class="col-md-8">';
			  		echo '<h4>Closed Forum</h4>';
			  		echo '<p>This forum is closed, and posting has been disabled.</p>';
			  		echo '</div>';
			  		echo '</div>';

			  	} else {
			  		?>
			  		<div class="row" id="reply">
						<div class="col-md-2">&nbsp;</div>
						<div class="col-md-8">
							<br/>
							<h4>Reply to <span id="to_whom">Thread</span></h4>
							<p>You must <a href="/auth?uri=<?=urlencode($_SERVER['REQUEST_URI'])?>">login</a> to reply to this thread.</p>
						</div>
					</div>
			  		<?php
			  	}
			  ?>	
		</div>
		<script>
		  $(function(){
			$('#editor').wysiwyg();

		});

		  function submit_post() {
		  	var html =  $('#editor').cleanHtml();
		  	$('input[name=html]').val(html);
		  	$('#reply_form').submit();
		  }

			$('.dropdown-menu input').click(function() {return false;})
		    .change(function () {$(this).parent('.dropdown-menu').siblings('.dropdown-toggle').dropdown('toggle');})
        .keydown('esc', function () {this.value='';$(this).change();});

      $('[data-role=magic-overlay]').each(function () { 
        var overlay = $(this), target = $(overlay.data('target')); 
        overlay.css('opacity', 0).css('position', 'absolute').offset(target.offset()).width(target.outerWidth()).height(target.outerHeight());
      });
		</script>
	</body>
	</html>