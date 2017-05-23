<br/>
<h3>Forum Categories</h3>

<a class="btn btn-primary" href="/admin/category/new"><i class="fa fa-plus"></i> Add Category</a><br/><br/>
<?php
	if (Session::has('error')) {
		echo '<p class="error" style="color:red;">'.Session::get('error').'</p>';
	}
?>
<?php

	echo '<table class="table">';
	echo '<tr><th>ID</th><th>Category Name</th><th>Forum Count</th><th>Delete</th></tr>';
	foreach($categories as $category) {
		?>
		<tr>
			<td><?=$category->category_id?></td>
			<td><i class="fa <?=$category->icon?>"></i></td>
			<td><a href="/admin/category/edit/<?=$category->category_id?>"><?=$category->category_name?></a></td>
			<td><?=sizeof($category->forums)?></td>
			<td><a href="/admin/category/delete/<?=$category->category_id?>"><i class="fa fa-trash"></i></a></td>
		</tr>
		<?php
	}
	echo '</table>';
?>