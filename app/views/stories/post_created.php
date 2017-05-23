<div id="post-<?=$object->post_id?>" class="status-post">
<div class="row" style="padding-top:12px;">
	<div class="col-md-1 col-sm-1 col-xs-1">
		<?php
			$num_votes = DB::table('votes')->where('post_id','=', $object->post_id)->sum('vote');
			$upvote = '';
			$downvote = '';
			
			if (Auth::check() ) {
				$vote = DB::table('votes')->where('post_id', '=', $object->post_id)->where('user_id' , '=', Auth::user()->id)->pluck('vote');
				if ($vote == 1) {
					$upvote = 'active';
					$downvote = 'inactive';
				} else if ($vote == -1) {
					$downvote = 'active';
					$upvote = 'inactive';
				}
			}
		?>
			<a href="#<?=$object->post_id?>" class="up vote-btn <?=$upvote?>"><i class="fa fa-chevron-up"></i></a>
			<div class="num_votes"><?=$num_votes?></div>
			<a href="#<?=$object->post_id?>" class="down vote-btn <?=$downvote?>"><i class="fa fa-chevron-down"></i></a>
		</div>

	<div class="col-md-1 col-sm-1 col-xs-1">

		<?php
			$photo_style2 = '';

			//echo json_encode($object);
			if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$object->author_id.'.jpg') ) {
				$path = Image::path('/assets/user/'.$object->author_id.'.jpg', 'resizeCrop', 50, 50);
				$photo_style2 = "background-image:url('$path');";
			}

		?>
		<div class="circle" style="<?=$photo_style2?>">

		</div>
	</div>
	<div class="col-md-9 col-sm-9 col-xs-9">

		<?php
			if ($object->forum_id > 0) {
				$forum = Forum::find($object->forum_id);
			
			if (isset($is_forum) && $is_forum == true) {

				?>
				<a href="/user/<?=$object->author_id?>/"><?=$object->author->display_name?></a>:

				<?php
			} else {
				?>

				<a href="/user/<?=$object->author_id?>/"><?=$object->author->display_name?></a> posted in forum <a href="/forums/<?=$object->forum_id?>"><?=$forum->title?></a>:
			<?php
				}

			?>
		<span><?=$object->content?></span><br/>
		<?php
			} else {
		?>
			<a href="/user/<?=$object->author_id?>/"><?=$object->author->display_name?></a>: <span><?=$object->content?></span><br/>
		<?php
			}
		?>
		

		<?php

			$diff = time()-strtotime($object->created_at);

			$when = '';
			$units = '';
			$tokens = array (
		        31536000 => 'y',
		        2592000 => 'months',
		        604800 => 'w',
		        86400 => 'd',
		        3600 => 'h',
		        60 => 'min',
		        1 => 's'
		    );

		    foreach ($tokens as $unit => $text) {
		        if ($diff < $unit) {
		        	continue;
		        } else {
			        $numberOfUnits = floor($diff / $unit);
			        echo '<small><div class="line">&nbsp;</div><i class="fa fa-clock-o"></i> '.$numberOfUnits.' '.$text.' ago</small>';
			        break;
		    	}
		    }
			
		?>
				<?php
		if ($object->attachment == true) {
			$image_url = $object->link_image;
			$bg_style = "background-image:url('".$image_url."');";
			?>
			<div class="attachment" style="<?=$bg_style?>">
				<div class="metadata">
					<h4><a href="<?=$object->link_url?>" target="new"><?=$object->link_title?></a></h4>
					<p><?=$object->link_description?></p>
				</div>
			</div>
			<?php
		}

		?>
	</div>
	<div class="col-md-1 col-sm-1 col-xs-1">
		<?php
			if (Auth::check() && Auth::user()->id == $object->author_id) {
		?>
		<div class="dropdown">
			<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
			   	<i class="fa fa-chevron-down"></i>
			  </button>
			  <ul class="dropdown-menu prevent-default" role="menu" aria-labelledby="dropdownMenu1">
			    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="delete_post(<?=$object->post_id?>);">Delete Post</a></li>
			    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="report_abuse(<?=$object->post_id?>);">Report Abuse/Spam</a></li>
			</ul>
		</div>
		<?php
	}
		?>
	</div>
</div>

		<?php
		if (sizeof($object->comments) >0) {
			?>
			<div id="<?=$object->post_id?>-comments" class="comments-section">
				<?=View::make('comments', array('post' => $object))?>
		</div>
			<?php
			}
			
		?>
<?php
	if (Auth::check()) {
?>
<div class="row" style="margin-top:4px;padding-bottom:12px;">
	<div class="col-md-2 col-sm-2 col-xs-2">
		&nbsp;
	</div>
	<div class="col-md-1 col-sm-1 col-xs-1">
		<div class="circle" style="background-image:url('<?=Auth::check()?Auth::user()->thumbnail():''?>');">&nbsp;</div>
	</div>
	<div class="col-md-8 col-sm-8 col-xs-8" style="padding-top:6px;">
		<form class="comment-form" action="/posts/<?=$object->post_id?>/comment" method="post">
			<input class="comment" placeholder="Write a comment..." type="text"/>
			<input type="hidden" value="#<?=$object->post_id?>-comments" name="output_id"/>
		</form>
	</div>
</div>
<?php
	} else {
		?>
<div class="row" style="margin-top:4px;padding-bottom:12px;">
	<div class="col-md-2 col-sm-2 col-xs-2">
		&nbsp;
	</div>
	<div class="col-md-9 col-sm-9 col-xs-9">
		<a href="/auth?uri=<?=urlencode($_SERVER['REQUEST_URI'])?>">Login to comment</a>
	</div>
</div>

		<?php
	}
?>
</div>