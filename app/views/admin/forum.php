			<script src="//mindmup.github.io/bootstrap-wysiwyg/bootstrap-wysiwyg.js" ></script>
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
<form  id="form" action="" method="post" enctype="multipart/form-data" onsubmit="return check();">
	<br/>
	<label>Forum Title</label><br/>
<input type="text" name="title" class="form-control" value="<?=$forum->title?>"/><br/>
<label>Description</label><br/>
<textarea name="description_txt" class="form-control" style="width:100%;"><?=$forum->description?></textarea><br/>
<label>Category</label><br/>
<select name="category">
	<?php
		foreach($categories as $category) {
			$selected = '';
			if (isset($forum->category) && $category->category_id == $forum->category) {
				$selected = " SELECTED";
			}

			echo '<option value="'.$category->category_id.'" '.$selected.'>'.$category->category_name.'</option>';
		}
	?>	
</select><br/><br/>
<label>Forum Type</label><br/>
<select name="type">
	<?php
		$types = array(
				'socratic' => 'Socratic Discussion',
				'trends' => 'Industry Trends',
				'practices' => 'Best Practices'
			);

		foreach($types as $key => $value) {
			$select = '';
			if ($key == $forum->type) {
				$select = ' SELECTED';
			}
			echo '<option value="'.$key.'" '.$select.'>'.$value.'</option>';
		}

	?>
</select><br/><br/>
<label>Tags</label><br/>
<div id="tags">
	<?php
	if (isset($forum)) {
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
	}
	?>
	<input type="text" id="tag-input" placeholder="Enter tag, press enter to add." autocomplete="off"/>
</div>
<input type="hidden" name="tags" value=""/>
<br/>
<label>Banner Image</label>
<input type="file" name="banner" value=""/>
<br/><br/>
<label>Embeddable Content</label>
<small>You can embed a YouTube/Vimeo video in the forum's sidebar to highlight information to be discussed.</small><br/>
<textarea type="text" name="embeddable_content" class="form-control"><?=$forum->embeddable_content?></textarea><br/><br/>

<label>Guiding Questions</label><br/>
<input type="text" name="guiding_question_1" value="<?=addslashes($forum->guiding_question_1)?>" class="form-control" placeholder="Question #1" />
<input type="text" name="guiding_question_2" value="<?=addslashes($forum->guiding_question_2)?>" class="form-control" placeholder="Question #2" />
<input type="text" name="guiding_question_3" value="<?=addslashes($forum->guiding_question_3)?>" class="form-control" placeholder="Question #3"/>
<input type="text" name="guiding_question_4" value="<?=addslashes($forum->guiding_question_4)?>" class="form-control" placeholder="Question #4" />
<input type="text" name="guiding_question_5" value="<?=addslashes($forum->guiding_question_5)?>" class="form-control" placeholder="Question #5" />

<label>Forum Guidelines</label>
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
    <a class="btn" data-edit="insertunorderedlist" title="Unordered List"><i class="fa fa-list-ul"></i></a>
  </div>
  <div class="btn-group">
  <a class="btn dropdown-toggle" data-toggle="dropdown" title="Hyperlink"><i class="fa fa-link"></i></a>
</div>
</div>
<div id="editor" style="height:200px;width:480px;border:1px solid #ccc;"><?=$forum->guidelines?></div><br/><br/>
<input type="hidden" value="" name="guidelines"/>

<label>Publish?</label>
<input type="radio" name="published" value="1" id="pub_1"/>&nbsp;<span>Yes</span>
<input type="radio" name="published" value="0" id="pub_0"/>&nbsp;<span>No</span><br/><Br/>
<label>Forum is open?</label>
<input type="radio" name="open" value="1" id="open_1"/>&nbsp;<span>Yes</span>
<input type="radio" name="open" value="0" id="open_0"/>&nbsp;<span>No</span>
<script>
	<?php
		if (isset($forum)) {
			?>
			$('#pub_<?=(int)$forum->published?>').prop('checked','checked');
			$('#open_<?=(int)$forum->open?>').prop('checked','checked');
			<?php
		}

	?>

	  $(function(){
			$('#editor').wysiwyg();

		});
</script>


<br/><br/><input type="button" value="Submit Changes"  id="submit-btn" class="btn btn-primary"/>
<a href="/admin/forums" class="btn btn-default">Back</a>
</form>
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

		var html =  $('#editor').cleanHtml();
	  	$('input[name=guidelines]').val(html);

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