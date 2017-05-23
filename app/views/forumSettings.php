<!DOCTYPE HTML>
<html>
<head>
	<?=View::make('header')->with('highlight', 'dialogue')?>
			<style>
	#tags {
		border:1px solid #ccc;
		min-height:26px;
	}

	#tag-input {
		border:0px solid #ccc;
		padding:5px;
		font-size:13px;
		width:25%;
	}

	div.tag {
		display:inline-block;
		background-color:rgba(255, 173, 0, 1);
		color:#fff;
		font-weight:700;margin:2px 1px 2px;
		padding:3px 6px;
		border-radius:3px;
		
	}

	div.tag .remove-tag {
		color:#333;
		font-weight:700;
		text-shadow:1px 1px 1px rgba(255,255,255,.5);
	}
</style>
</head>
<body>
	<?=View::make('navigation')?>
	<div class="container fus-white-bg fus-section fus-feature fus-white-bg">
		<div class="row">
			<div class="col-md-12">
				<form  id="form" action="" method="post" enctype="multipart/form-data" onsubmit="return check();">
				<h1><?=$forum->title?></h1>
				<label>Description</label><br/>
				<textarea name="description_txt" class="field" style="width:100%;"><?=$forum->description?></textarea><br/>
				<label>Category</label><br/>
				<select name="category">
					<?php
						foreach($categories as $category) {
							echo '<option value="'.$category->category_id.'">'.$category->category_name.'</option>';
						}
					?>	
				</select><br/><br/>
				<label>Tags</label><br/>
				<div id="tags">
					<?php
					$tags = DB::table('forum_tags')->where('forum_id', '=', $forum->forum_id)->get();

					foreach($tags as $tag) {
						if (empty($tag->tag_name)) {
							continue;
						}
						?>
						<div class="tag">
							<span><?=$tag->tag_name?></span>
							<a href="#" class="remove-tag">&times;</a>
						</div>
						<?php
					}
					?>
					<input type="text" id="tag-input" placeholder="Enter tag, press enter to add." autocomplete="off"/>
				</div>
				<input type="hidden" name="tags" value=""/>
				<br/>
				<label>Banner Image</label>
				<input type="file" name="banner" value=""/>
				<br/><br/>
				<label>Youtube Video ID</label>
				<small>You can embed a YouTube video in the forum's sidebar to highlight information to be discussed.</small><br/>
				<input type="text" name="youtube_video_id" value="<?=$forum->youtube_video_id?>" placeholder="2aL0n_WiofM" class="field"/><br/><br/>
				<br/><br/><input type="button" value="Submit Changes"  id="submit-btn" class="btn btn-primary"/>
				<a href="/forums/<?=$forum->forum_id?>/p/1" class="btn btn-default">Back</a>
			</form>
			</div>
		</div>
	</div>
		<script>
var submit_it = false;
	$('#tag-input').keyup(function(e) {
		var val = $(this).val();
		if (e.keyCode == 13) {
			e.preventDefault();
			$(this).before('<div class="tag"><span>'+val+'</span> <a href="#" class="remove-tag">&times;</a></div>');
			$(this).val('');
			$('.remove-tag').each(function() {
				if (!$(this).onclick) {
					$(this).click(function(e) {
						e.preventDefault();
						$(this).parent().remove();
					});
				}
			});
		}
	});

	function check() {
		var tags = '';
		$('.tag').each(function() {
			tags += escape($(this).children('span').text())+',';
		});
		$('input[name=tags]').val(tags);
		return submit_it;
	}

	$('#submit-btn').click(function(e) {
		submit_it = true;
		$('#form').submit();
	});

	$('.remove-tag').each(function() {
				if (!$(this).onclick) {
					$(this).click(function(e) {
						e.preventDefault();
						$(this).parent().remove();
					});
				}
			});
</script>
	<?=View::make('footer')?>
</body>
</html>