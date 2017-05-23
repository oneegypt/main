<p>Hi <?=$user->contact_name?></p>
<p>This e-mail is to notify you that your non-profit application at OneEgypt.org has been approved.</p>
<?php
	if (!empty($reason)) {
		echo '<p>Note from the administrator:</p>';
		echo '<p>'.$reason.'</p>';
	}
?>
<a href="http://www.oneegypt.org/login">Sign in</a> to create your profile