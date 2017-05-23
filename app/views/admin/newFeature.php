
<h1>New Featured Slide</h1>
<p>You may feature a campaign or URL in a slider on any of the three areas of the site.</p>
<form action="/admin/features/new" method="post" enctype="multipart/form-data">
	<?php
	$title = '';
	$url = '';
	$action_text = '';
	if (isset($feature->feature_id)) {
		$title = $feature->title;
		$url = $feature->link_url;
		$action_text = $feature->action_txt;
		echo '<input type="hidden" name="feature_id" value="'.$feature->feature_id.'"/>';
	}
?>
<label>Section</label><br/>
<select name="section">
	<option value="support">Support</option>
	<option value="dialogue">Dialogue</option>
	
	<option value="work">Work</option>
	
</select><br/><br/>
<label>Slider Title</label><br/>
<input type="text" name="title" id="title" value="<?=$title?>" class="field"/><br/>
<label>Link URL</label><br/>
<input type="text" name="url" id="url" value="<?=$url?>" class="field" placeholder="http://"/><br/>
<label>Action Text</label><br/>
<input type="text" name="action" id="action" value="<?=$action_text?>" class="field" placeholder="Button label / call to action"/><br/>
<label>Featured Image</label><br/>
<input type="file" name="image" id="image"/><br/>
<button class="btn btn-primary">SUBMIT</button>
<a href="/admin/features" class="btn btn-noborder">BACK</a>
</form>