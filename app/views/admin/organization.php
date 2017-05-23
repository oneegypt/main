<br/><br/>
	<?php
		$statuses = array('pending', 'approved', 'declined');
		$part = $org->status;

	?>
<h3><?=$org->display_name?></h3>
<table class="table">
	<tr><td><label>status</label></td>
		<td><?=is_null($org->approved_at)?'Not yet approved':'Approved'?></td></tr>
	<?php
		$fields = array(
				'email',
				'contact_name',
				'contact_phone',
				'tax_id',
				'created_at',
				'description_txt'
			);
		foreach($fields as $key) {
			if (!is_null($org->$key)) {
				echo '<tr><td><label>'.str_replace('_', ' ', $key).'</label></td><td>'.$org->$key.'</td></tr>';
			}
		}
	?>
</table>
<form action="" method="post">
	<label>Status</label><br/>
	<select name="status">
		<?php
			$st = 'pending';
			if (!is_null($org->status)) {
				$st = $org->status;
			}

			$statuses = array('approved', 'declined', 'pending');
			foreach($statuses as $status) {
				$selected = ($status==$st)?' selected ':'';

				echo '<option value="'.$status.'" '.$selected.'>'.$status.'</option>';
			}

			$reason = Attribute::where('user_id','=', $org->id)->where('attribute_key', '=', 'status_reason')->first();
			if (empty($reason)) {
				$reason = '';
			} else {
				$reason = $reason->attribute_value;
			}
		?>

	</select><br/><br/>
	<label>Reason</label><br/>
	<small>Reason for decline or approval. This message is relayed to the applicant in an e-mail.</small><br/>
	<textarea name="reason" class="field" style="width:100%;"><?=$reason?></textarea><br/><br/>
	<button class="btn btn-primary">Save Changes</button>
	<?php
	echo '<a href="/admin/organizations/status/'.$part.'">Back to '.$part.' organizations</a>';

	?>
</form>