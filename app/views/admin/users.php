<br/>
<h3>Search Users</h3>
<p>You can modify a user's privileges, make them a moderator, or ban them.</p>
<p>Search with either e-mail address or user name.</p>
<form action="/admin/users" method="get">
<label>E-mail Address</label><br/>
<input type="text" name="email" value="" class="field"/><br/>
<label>User Name</label><br/>
<input type="text" name="username" value="" class="field"/><br/><br/>
<input type="submit" value="Search" class="btn btn-primary"/>
</form>
<?php
	if (sizeof($users) > 0) {
		echo '<h3>Search Results</h3>';
		echo '<table class="table">';
		foreach($users as $user) {
			?>
			<tr>
				<td><?=$user->id?></td>
				<td><?=$user->display_name?></td>
				<td><?=$user->email?></td>
				<td>@<?=$user->username?></td>
				<td>Joined on <?=date('m/d/Y', strtotime($user->created_at))?></td>
				<td><a href="/admin/users/<?=$user->id?>">Edit</a></td>
				<td><a href="/users/<?=$user->id?>">Profile</a></td>
			</tr>
			<?php
		}
		echo '</table>';
	}

?>