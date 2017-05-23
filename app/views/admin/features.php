<br/><Br/>
<a href="/admin/features/new" class="btn btn-primary"><i class="fa fa-plus"></i> New Featured Slide</a>
<br/><br/>
<table class="table">
	<tr><th>Image</th><th>Title</th><th>Section</th><th></th></tr>
	<?php
		foreach($features as $feature) {
			

			$path = Image::path($feature->image_url, 'resizeCrop', 100, 50);


			echo '<tr><td style="width:100px;"><img src="'.$path.'"/></td><td><a href="/admin/features/edit/'.$feature->feature_id.'">'.$feature->title.'</a></td>';
			echo '<td>'.$feature->section.'</td>';

			echo '<td><a href="/admin/features/delete?id='.$feature->feature_id.'"><i class="fa fa-trash"></i></a></td></tr>';
		}
	?>
</table>