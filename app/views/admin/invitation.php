<h3 style="font-weight:700;"><?=$invite->full_name?></h3>
<form action="" method="post">
<table class="table">
	<tr>
		<td>E-mail Address</td>
		<td><?=$invite->email?></td>
	</tr>
	<tr>
		<td>Occupation</td>
		<td><?=$invite->occupation?></td>
	</tr>
	<tr>
		<td>Reason for interest</td>
		<td><?=$invite->interest?></td>
	</tr>
	<tr>
		<td>Status</td>
		<td>
			<?php
				$statuses = array(
						'pending' => 'Pending',
						'approved' => 'Approved',
						'declined' => 'Declined'
					);
				echo '<select name="status" class="form-control">';
				foreach($statuses as $key => $val) {
					if ($key == $invite->status) {
						$selected = "SELECTED";
					} else {
						$selected = '';
					}
					echo '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
				}
				echo '</select>';
			?>
		</td>
	</tr>
</table>
<small>Marking a request approved will send the user a unique registration link.</small><br/>
<button class="btn btn-primary">UPDATE INVITATION REQUEST</button>
<a href="/admin/beta/pending" class="btn">BACK</a>
</form>