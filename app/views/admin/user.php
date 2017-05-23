<br/>
<h3>@<?=$user->username?></h3>
<?php
	if (sizeof($user->privileges) > 0) {
		echo '<table class="table">';
		echo '<tr><th>Permission Key</th><th>Scope</th><th>Date Added</th><th></th></tr>';
		foreach($user->privileges as $priv) {
			$scope = 'Global';
			if ($priv->scope > 0) {
				$forum = Forum::find($priv->scope);
				$scope = '<a href="/forums/'.$forum->forum_id.'/">'.$forum->title.'</a>';
			}
			?>
			<tr>
				<td><?=$priv->privilege_key?></td>
				<td><?=$scope?></td>
				<td><?=date('m/d/Y h:i a', strtotime($priv->created_at))?></td>
				<td><a href="/admin/deletePermission/<?=$priv->user_privilege_id?>"><i class="fa fa-trash"></i></a></td>
			</tr>
			<?php
		}
		echo '</table>';
	} else {
		echo '<p>No privileges assigned to this user yet.</p>';
	}
?>
<h3>Add a privilege</h3>
<form action="" onsubmit="return validForum();" method="post">
	<?php
		$available_privileges = array(
				'mod' => 'Forum Moderator',
				'global_mod' => 'Global Moderator',
				'ban_user' => 'Able to ban users'
			);
	?>
	<label>Permission</label><br/>
	<select name="privilege_key">
		<?php
			foreach($available_privileges as $key => $label) {
				echo '<option value="'.$key.'">'.$label.'</option>';
			}
		?>
	</select><br/><br/>
	<label>Forum</label><br/>
	<input type="hidden" name="forum_id" value="0"/>
	<input type="text" name="url" placeholder="Enter Forum URL" class="field" onkeyup="crawlForum();"/>&nbsp;<span id="preview"></span><br/>
	<small>Forum does not apply to Global Mod permission.</small><br/><br/>
	<button type="submit" class="btn btn-primary" id="submit-btn"><i class="fa fa-plus"></i> Add Permission</button>
</form>
<?php


?>
<script>
var timeoutId = 0;
	function crawlForum() {
		var url = $('input[name=url]').val();
		//alert(url);
		clearTimeout(timeoutId);
		timeoutId = setTimeout(function() {
			$('#preview').html('<i class="fa fa-spin fa-refresh"></i>').css('color', '#363636');
			$.getJSON(url, {}, function(data) {
				if (data.title) {
					$('input[name=forum_id]').val(data.forum_id);
					$('#preview').html('<i class="fa fa-check"></i> Forum found: '+data.title).css('color', 'green');
				} else {
					$('input[name=forum_id]').val(0);
					$('#preview').html('<i class="fa fa-remove"></i> Could not find forum').css('color', 'red');
				}
			});
		}, 1000);
		
	}

	function validForum() {
		var forum_id = $('input[name=forum_id]').val();
		if ($('select[name=privilege_key]').val() != 'global_mod' && forum_id == 0) {
			alert('All privileges are forum-specific, except for "Global Moderator." Please enter the URL for a valid forum');
			return false;
		}
		return true;
	}
</script>