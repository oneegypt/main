<br/><br/>
<h3>Beta Invitation Requests</h3>
<ul class="nav nav-pills">
<li><a href="/admin/beta/pending">Pending</a></li>
<li><a href="/admin/beta/approved">Approved</a></li>
<li><a href="/admin/beta/declined">Declined</a></li>
</ul>
<table class="table">
	<tr><th>E-mail</th><th>Full Name</th><th>Occupation</th><th>Status</th><th>Request Sent</th><th></th></tr>
	<?php
		foreach($invites as $invite) {
			?>
			<tr>
				<td><?=$invite->email?></td>
				<td><?=$invite->full_name?></td>
				<td><?=$invite->occupation?></td>
				<td><?=$invite->status?></td>
				<td><?=date('F jS, Y h:i a', strtotime($invite->created_at))?></td>
				<td><a href="/admin/beta/view/<?=$invite->invitation_id?>">View</a></td>

			</tr>


			<?php
		}
	?>
</table>