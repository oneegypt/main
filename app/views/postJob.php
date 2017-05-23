<?php
	/*
	* Post Listing page.
	*/
?>
<!DOCTYPE HTML>
<html>
<head>
	<title>OneEgypt.org - Home</title>
	<?= View::make('header')?>
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
</head>
<body class="">
	<?=View::make('navigation')->with('highlight','work')?>
	<div class="container fus-white-bg fus-section fus-feature fus-white-bg" style="min-height:100%;">
		<div class="row">
			<div class="col-md-6">
				<form action="" id="the-form" method="post" onsubmit="return submitForm();">
					<h1>Job Listing Form</h1>
					<?php
						if (Session::has('error')) {
							?>
							<p class="error"><?=Session::get('error')?></p>
							<?php
						}

					?>
					<label>Job Title</label><br/>
					<input type="text" name="listing_title" id="listing_title" class="field" value="<?=Input::old('listing_title')?>"/><br/>
					<label>Job Description & Qualifications</label><br/>
					<textarea name="listing_description" id="listing_description" class="field" style="width:60%;height:200px;"><?=Input::old('listing_description')?></textarea><br/>
					<label>Category</label><br/>
						<?php
							$category_id = Input::old('category_id');
							foreach($categories as $category) {

								if ($category->category_id == $category_id) {
									$selected = ' checked';
								} else {
									$selected = '';
								}
								echo '<input name="category_id[]" id="cat-'.$category->category_id.'" value="'.$category->category_id.'" '.$selected.' type="checkbox"/>&nbsp;<label for="cat-'.$category->category_id.'" style="font-weight:300;font-size:13px;">'.$category->category_name.'</label><br/>';
							}
						?>
					<br/><label>Job Type</label><br/>
					<select name="type">
						<?php
							$types = array('' => '---', 'full-time' => 'Full Time', 'part-time' => 'Part Time', 'temporary' => 'Temporary', 'volunteer' => 'Volunteer');
							foreach($types as $key => $type) {
								if ($key == Input::old('type') ) {
									$selected = ' SELECTED';
								} else {
									$selected = '';
								}
								echo '<option value="'.$key.'" '.$selected.'>'.$type.'</option>';
							}

						?>
					</select><br/><br/>

					<label>Tags</label><br/>
					<div id="tags" style="width:80%;">
						<input type="text" id="tag-input" placeholder="Enter tag, press enter to add." autocomplete="off" style="width:50%;"/>
					</div>
					<br/>
					<input type="hidden" name="tags" value=""/>
					<label>Job Address</label><br/>
					<small>Users will be able to find this job if location is nearby</small><br/>
					<input type="text" name="address" placeholder="Street Address" class="field"/><br/>
					<input type="text" name="city" placeholder="City" class="field" style="margin-top:10px;"/><br/>
					<input type="text" name="state" placeholder="State" class="field" style="margin-top:10px;"/><br/>
					<input type="text" name="country" placeholder="Country" class="field" style="margin-top:10px;"/><br/><br/>

					<label>E-mail or URL to apply for position</label><br/>
					<input type="text" name="action_url" value=""/><br/><br/>
					<input type="button" value="Submit Listing" class="btn btn-primary" onclick="do_submit();"/>
				</form>
			</div>
		</div>
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

	function do_submit() {
		submit = true;
		$('#the-form').submit();
	}

	function submitForm() {
		var tags = '';
		$('.tag').each(function() {
			tags += escape($(this).children('span').text())+',';
		});
		$('input[name=tags]').val(tags);
		return submit;
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
</body>
</html>