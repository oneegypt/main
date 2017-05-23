<?php
	foreach($results as $record) {
		$icon = '';
		$url = '';
		$title = '';
		$type = '';
		echo '<div class="row">';
		switch($record->model) {
			case 'User':
				if ($page == 1) {
					$type = 'Moderator';
					$icon = 'fa-user';
					$title = $record->record->display_name;
				} else if ($page == 2) {
					$type = 'Organization ('.$record->record->category->category_name.')';
					$icon = 'fa-group';
					$title = $record->record->display_name;
				}
				$url = '/user/'.$record->object_id;
				
				break;
			case 'Forum':
				$type = 'Forum';
				$icon = 'fa-comment-o';
				$url = '/forums/'.$record->object_id;
				$title = $record->record->title;
				break;
			case 'Thread';
				$type = 'Thread';
				$url = '/forums/'.$record->record->forum_id.'/thread/'.$record->object_id.'/1';
				$icon = 'fa-pencil';
				break;
			case 'Campaign':
				$type = 'Campaign ('.$record->record->recipient->display_name.')';
				$url = '';
				$icon = 'fa-heart';
				$title = $record->record->title;
			
			default:
				break;
		}
		echo '<div class="col-md-1"><i class="fa '.$icon.'"></i></div>';
		echo '<div class="col-md-11"><a href="'.$url.'"><strong>'.$title.'</strong></a>&nbsp;<small>'.$type.'</small></div>';
		echo '</div>';
	}
?>