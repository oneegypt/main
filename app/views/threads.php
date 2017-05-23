<!DOCTYPE HTML>
<html>
<head>
	<?=View::make('header')?>

</head>
<body>
	<?=View::make('navigation')?>
	<div class="container fus-white-bg fus-section">
		<h1>Messages</h1>
		<ul class="nav nav-pills">
			<?php
				$statuses = array('inbox', 'sent');
				$uri = $_SERVER['REQUEST_URI'];
				$parts = explode('/', $uri);
				$part = $parts[sizeof($parts)-1];
				$parts = explode('?', $part);
				$part = $parts[0];
				if (!in_array($part, $statuses)) {
					die();
				}

				foreach($statuses as $status) {
					$class= '';
					if ($status == $part) {
						$class = 'active';
					}
					echo '<li class="'.$class.'"><a href="/messages/'.$status.'">'.ucwords($status).'</a></li>';
				}
			?>
		</ul>
		<br/>
<table class="table">
<?php
	foreach($threads as $thread) {
		$participant = DB::table('thread_participants')->join('users', 'thread_participants.id', '=', 'users.id')->where('thread_id', '=', $thread->thread_id)->where('thread_participants.id', '!=', Auth::user()->id)->first();
		$count = DB::table('messages')->where('thread_id', '=', $thread->thread_id)->groupBy('sender_id')->count();
		$re = '';
		if ($count > 1) {
			$re = 'RE: ';
		}

		$unread = '';
		if (isset($thread->unread) && is_null($thread->unread)) {
			$unread = 'font-weight:700;';
		}
		?>
		<tr>
			<td style="width:25%;"><?=$participant->display_name?></td>
			<td style="width:50%;"><a href="/thread/<?=$thread->thread_id?>" style="<?=$unread?>"><?=$re?><?=$thread->subject_txt?></a></td>
			<td><?=date('F jS, Y h:i a', strtotime($thread->max_date))?></td>
		</tr>
		<?php
	}
?>
</table>
</div>
<?=View::make('footer')?>
</body>