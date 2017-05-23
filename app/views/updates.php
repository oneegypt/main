<!DOCTYPE HTML>
<html>
<head>
	<?= View::make('header')?>
	<style>
		.comments-section {
			border:1px solid #eee;
			background-color:#f8f8f8;	
			padding:10px 0;
		}
	</style>
</head>
<body>
<?=View::make('navigation') ?>
<div class="container fus-white-bg fus-section fus-feature fus-white-bg">
	<div class="row">
		<div  class="col-md-8">
			<h3>News Feed</h3>
			<div id="feed">

			</div>
			<div class="centered">
				<button class="btn btn-default" onclick="loadFeed();" id="load-more"><i class="fa fa-refresh"></i> Load more posts</button>
			</div>
		</div>
		<div class="col-md-4">
			<h3>Forum Updates</h3>
			<div id="notifications">
				<p>Keep up to date with forums you follow.</p>
			</div>
			<?php
				$data = profileComplete(Auth::user()->id);
				echo json_encode($data);
			?>
		</div>
		
	</div>
</div>
<script>
	var offset = 0;
	function loadFeed() {
		$('#load-more').find('i').addClass('fa-spin');
		$.getJSON('/feed.json', {offset:offset}, function(json) {
			$('#load-more').find('i').removeClass('fa-spin');
			offset = json.offset;
			$('#feed').append(json.html);
			reinit_feed();
		});
	}

	$(document).ready(function() {
		loadFeed();
		forum_updates();
		setInterval('forum_updates();', 15000);
	});

	function forum_updates() {
		$.getJSON('/notifications.json', {} , function(json) {
			var html = '<table class="table">';
			for(var i = 0; i < json.length; i++) {
				var url = '/forums/'+json[i].forum_id+'/threads/'+json[i].forum_thread_id+'/1';
				if (json[i].num_replies) {
					html += '<tr><td>'+'<i title="Someoe replied to your post in this thread" class="fa fa-reply"></i></td><td><a href="'+url+'">'+json[i].topic+'</a></td><td>'+json[i].num_replies+' replies</td></tr>';
				} else {
					html += '<tr><td>'+'<i title="You are following this forum" class="fa '+json[i].icon+'"></i></td><td><a href="'+url+'">'+json[i].topic+'</a></td><td>'+json[i].num_updates+' new posts</td></tr>';
				}
			}
 				
			html += '</table>';
			if (html.length > 0) {
				$('#notifications').html(html);
			}
		});
	}

</script>
<?= View::make('footer') ?>
</body>
</html>