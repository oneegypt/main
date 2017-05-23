<style>
	#tags {
		border:1px solid #ccc;
		min-height:100px;
	}

	#tag-input {
		border:0px solid #ccc;
		padding:5px;
		font-size:13px;
		width:50%;
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
<form id="cat-form" action="/settings/map" method="post">
	
	<input type="hidden" name="locations" value=""/>
</form>
<label>Locations</label><br/>
<small>What countries or cities benefit from your non-profit organization? i.e. Egypt or Cairo, Egypt</small><br/>
<div id="tags">
	<?php
		foreach($locations as $loc) {
			?>
			<div class="tag">
				<span><?=$loc->term?></span>
				<a href="#" class="remove-tag">&times;</a>
			</div>
			<?php
		}
	?>
	<input type="text" id="tag-input" placeholder="Enter location, press enter to add." autocomplete="off"/>
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
		$('input[name=locations]').val(tags);
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