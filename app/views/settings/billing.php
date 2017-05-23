<form id="cc-form" action="/settings/billing" method="post" onsubmit="return ok;">
<h3>Credit Cards</h3>

<?php
	if (Session::has('message')) {
		echo '<p style="color:green;">'.Session::get('message').'</p>';
	}

	if (Session::has('error')) {
		echo '<p id="error" style="color:red;">'.Session::get('error').'</p>';
	} else {
		?>
		<span id="error" style="color:red;"></span>
		<?php
	}

	?>
<p>You may edit or remove saved credit cards here. Saving a card allows you to give donations more easily.</p>
	<?php


	$card = 'XXXX-XXXX-XXXX-XXXX';
	$exp = '';
	if (!empty($stripe) && isset($stripe->sources->data[0])) {
		$card = 'XXXX-XXXX-XXXX-'.$stripe->sources->data[0]->last4;
		$exp = $stripe->sources->data[0]->exp_month.'/'.$stripe->sources->data[0]->exp_year;
		$brand = $stripe->sources->data[0]->brand;
		echo '<p><i class="fa fa-lock"></i> Your '.$brand.' ending in '.$stripe->sources->data[0]->last4.' is securely saved. <a href="#">Delete</a>.</p>';
	}
?>

<label>Card Number</label><br/>
<input type="text" id="cc" placeholder="XXXX-XXXX-XXXX-XXXX" value="<?=$card?>" maxlength="20" class="field"/><br/>
<label>Expiration</label><br/>
<input type="text" name="exp" id="exp" placeholder="MM/YYYY" maxlength="20" class="field" value="<?=$exp?>"/><br/>
<label>CVC</label><br/>
<input type="text" name="cvc" id="cvc" placeholder="Security Code" maxlength="6" class="field"/><br/><br/>
<input type="hidden" name="token" value="" id="token"/>
<button class="btn btn-primary" type="button" onclick="create_token();">Save Card</button>&nbsp;
</form>
<script>

	function handler(status, response) {
		if (response.error) {
			$('#error').html(response.error.message);
			ok = false;
		} else {
			ok = true;
			$('#token').val(response.id);
			$('#cc-form').submit();
		}	
	}

	var ok = false;
	function create_token() {
		var exp = $('#exp').val();
		var parts = exp.split('/');
		if (parts.length != 2) {
			$('#error').html('Invalid expiration date format. Please enter it as MM/YYYY, i.e. 02/2019');
			return false;
		}
		var month = parts[0];
		var year = parts[1];
		Stripe.card.createToken({
			number: $('#cc').val(),
			exp_month : month,
			exp_year: year,
			cvc : $('#cvc').val()
		}, handler);
	}

</script>