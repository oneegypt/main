<?php
if (!isset($is_forum)) {
	$is_forum = false;
}
foreach($posts as $post) {

	echo View::make('stories/post_created', array('object' => $post, 'is_forum' => $is_forum));
}

if (sizeof($posts) == 0) {
	echo '<p>No posts found...</p>';
}
/*	foreach($posts as $post) {
		$link = '/'.$post->author->type.'/'.$post->author->username.'/';
		
?>
<div class="post">
	<div class="row">
		<div class="col-md-1">
			<a href="#<?=$post->post_id?>" class="up vote-btn"><i class="fa fa-chevron-up"></i></a>
			<div class="num_votes"><?=$post->votes->sum('vote')?></div>
			<a href="#<?=$post->post_id?>" class="down vote-btn"><i class="fa fa-chevron-down"></i></a>
		</div>
		<div class="col-md-2">
			<div class="circle">&nbsp;</div>
			
		</div>
		<div class="col-md-9 content">
			<a href="<?=$link?>"><?=$post->author->display_name?></a>
			<span><?=$post->content?></span>
			<div class="meta">
				<?=date('F jS, Y h:i a', strtotime($post->created_at))?>
			</div>
		</div>
	</div>
	<div id="<?=$post->post_id?>-comments">
		<?php
			echo View::make('comments', array('post' => $post));
		?>
	</div>
	<div class="row">
		<div class="col-md-3">
			&nbsp;
		</div>
		<div class="col-md-9">
			<form class="comment-form" action="/posts/<?=$post->post_id?>/comment" method="post">
				<input class="comment" placeholder="Comment on this..." type="text"/>
				<input type="hidden" value="#<?=$post->post_id?>-comments" name="output_id"/>
			</form>
		</div>
	</div>
</div>
<?php
	}*/

?>
<?php
//	echo View::make('footer');
?>