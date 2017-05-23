<br/><br/><ul class="nav nav-pills">
	<?php
		$statuses = array('pending', 'approved', 'declined');
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
			echo '<li class="'.$class.'"><a href="/admin/organizations/status/'.$status.'">'.ucwords($status).'</a></li>';
		}
	?>
</ul>
<h3><?=ucwords($part)?> Organizations</h3>
<table class="table">
	<tr><th>Org. Name</th><th>Contact E-mail</th><th>Contact Name</th><th>Phone</th><th>Joined</th><th></tr>
<?php

	foreach($organizations as $org) {
		?>
		<tr>
			<td><a href="/admin/organizations/<?=$org->id?>"><?=$org->display_name?></a></td>
			<td><?=$org->email?></td>
			<td><?=$org->contact_name?></td>
			<td><?=$org->contact_phone?></td>
			<td><?=date('F jS, Y h:i a', strtotime($org->created_at))?></td>
		</tr>
		<?php
	}
?>
</table>