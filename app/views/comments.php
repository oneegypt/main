<?php
	//$count = $post->comments->count();
	
	
	
	if (!isset($skip)) {
		$count = $post->comments->count();
		$skip = $count-5;
	}

	$comments = Comment::where('post_id','=',$post->post_id)->skip($skip)->take(5)->get();

	if ($skip > 0) {
		$next_page_count = $skip;
		if ($skip > 5) {
			$next_page_count = 5;
		}
		$new_skip = $skip-$next_page_count;
		echo '<div class="row"><div class="col-md-3">&nbsp;</div><div class="col-md-9" style="padding:5px;border-bottom:1px solid #f2f2f2;"><a href="#" data-skip="'.$new_skip.'" class="prev-comments" data-post-id="'.$post->post_id.'"><i class="fa fa-arrow-up"></i> See previous '.$next_page_count.' comments</a></div></div>';
	}

	foreach($comments as $comment) {
		$comment_user = User::find($comment->user_id);

		$thumbnail = $comment_user->thumbnail();
		?>
		<div class="row">
			<div class="col-md-2 col-sm-2 col-xs-2">
				&nbsp;
			</div>
			<div class="col-md-1 col-sm-1 col-xs-1">
				<div class="circle" style="background-image:url('<?=$thumbnail?>');">&nbsp;</div>
			</div>
			<div class="col-md-9 col-sm-9 col-xs-9">
				<a href="/user/<?=$comment->user_id?>"><?=$comment->author->display_name?></a>&nbsp;
				<?=$comment->content?>
				<?php

				$diff = time()-strtotime($comment->updated_at);

				$when = '';
				$units = '';
				$tokens = array (
			        31536000 => 'y',
			        2592000 => 'm',
			        604800 => 'w',
			        86400 => 'd',
			        3600 => 'h',
			        60 => 'm',
			        1 => 's'
			    );
			    foreach ($tokens as $unit => $text) {
			        if ($diff < $unit) {
			        	continue;
			        } else {
				        $numberOfUnits = floor($diff / $unit);
				        echo '<br/><small><div class="line">&nbsp;</div><i class="fa fa-clock-o"></i> '.$numberOfUnits.''.$text.' ago</small>';
				        break;
			    	}
			    }
				
			?>
			</div>
		</div>

		<?php
	}
?>