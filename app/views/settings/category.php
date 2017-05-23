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
</style>
<div style="min-height:100%;">
<form id="cat-form" action="/settings/category" method="post">
	<label>Main Category</label><br/>
	<select name="category_id">
		<?php
			foreach($categories as $category) {
				$selected = '';

				if ($category->category_id == Auth::user()->type) {
					$selected = " SELECTED";
				}

				echo '<option value="'.$category->category_id.'" '.$selected.'>'.$category->category_name.'</option>';
			}
		?>
	</select><Br/><Br/>
	
	<input type="hidden" name="tags" value=""/>
</form>
<label>Tags</label><br/>
<div id="tags">
	<?php
		foreach($tags as $tag) {
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

<br/>
<button class="btn btn-primary" type="button" onclick="submitForm();">Save Categorization</button><br/><Br/>
</div>
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

	function submitForm() {
		var tags = '';
		$('.tag').each(function() {
			tags += escape($(this).children('span').text())+',';
		});
		$('input[name=tags]').val(tags);
		$('#cat-form').submit();
	}

	$(document).ready(function() {
		$('.remove-tag').each(function() {
				if (!$(this).onclick) {
					$(this).click(function(e) {
						e.preventDefault();
						$(this).parent().remove();
					});
				}
			});
	});
</script>