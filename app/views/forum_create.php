<!DOCTYPE HTML>
<html>
	<head>
		<title>Forums</title>
		<?= View::make('header') ?>
		<style>
	#tags {
		border:1px solid #ccc;
		min-height:100px;
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

	#error {
		color:red;
	}
</style>
	</head>
	<body>
		<?=View::make('navigation')->with('highlight', 'dialogue')?>
		<div class="container fus-white-bg fus-section fus-feature fus-white-bg">
			<div class="col-md-8">
				<h3>Start a Forum</h3>
				<p>Create a forum to engage a conversation about a particular topic</p>
				<label>Form Title</label><br/>
				<input type="text" id="forum_title" value="" placeholder="" class="field"/><br/>
				<label>Description</label><br/>
				<textarea id="description" class="field" style="height:150px;"></textarea><br/>
				<label>Main Category</label><br/>
				<select name="category">
					<option value="">-----</option>
					<?php
						foreach($categories as $category) {
							echo '<option value="'.$category->category_id.'">'.$category->category_name.'</option>';
						}
					?>
				</select><br/>

				<label>Forum Type</label><br/>
				<select name="type">
					<option value="">-----</option>
					<?php
						foreach($types as $type) {
							echo '<option value="'.$type->forum_type_id.'">'.$type->forum_type_name.'</option>';
						}
					?>
				</select><br/>

				<input type="hidden" name="tags" value=""/>
				<label>Tags</label><br/>
				<div id="tags">
				
					<input type="text" id="tag-input" placeholder="Enter tag, press enter to add." autocomplete="off"/>
				</div>

				<br/>
				<button type="button" class="btn btn-primary" id="create-btn">CREATE</button>
				<a href="/support" class="btn btn-default">CANCEL</a>
				<span id="error"></span>
			</div>
			<div class="col-md-4">
				
			</div>
		</div>
		<script>
			$('#create-btn').click(function() {
				var tags = '';
				$('.tag').each(function() {
					tags += escape($(this).children('span').text())+',';
				});
				$('input[name=tags]').val(tags);
				
				$('#error').html('');
				$.post('/forums/create', {
						title: $('#forum_title').val(),
						description: $('#description').val(),
						category: $('select[name=category]').val(),
						type: $('select[name=type]').val(),
						tags: $('input[name=tags]').val()
					}, function(data) {
					if (data.url) {
						document.location = data.url;
					} else if (data.message) {
						$('#error').html(data.message);
					}
				},'json');
			});
		</script>
		<script>
var submit = false;
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
</script>
	</body>
</html>