
	<br/><Br/><a href="/admin/categories" class="btn btn-primary"><i class="fa fa-group"></i> Manage Categories</a>
	<a href="/admin/forum/0" class="btn btn-primary"><i class="fa fa-plus"></i> Create New Forum</a><br/><br/>
<?php


	echo '<table class="table">';
	echo '<tr><th>ID</th><th>Forum Title</th><th>Status</th><th>Category</th><th>Updated at</th></tr>';
	foreach($forums as $forum) {
		$status = $forum->published?'Published':'Draft';
		echo '<tr>';
		echo '<td>'.$forum->forum_id.'</td><td><a href="/admin/forum/'.$forum->forum_id.'">'.$forum->title.'</a></td>';
		echo '<td>'.$status.'</td>';
		$forum->cat;
		if (isset($forum->cat)) {
			echo '<td>'.$forum->cat->category_name.'</td>';
		} else {
			echo '<td>Uncategorized</td>';
		}
		echo '<td>'.date('m/d/Y H:i', strtotime($forum->updated_at)).'</td>';
		
		echo '</tr>';
	}
	echo '</table>';
?>