<h3>Bank Details</h3>
<p>In order to accept charitable contributions for your organization, you need to enter your bank account details below to receive deposits.</p>
<p>Direct transfers are only available for US-based bank accounts and only in dollars.</p>
<label>Routing Number</label><br/>
<input type="text" id="routing-number" value="" class="field"/><br/>
<label>Bank Account Number</label><br/>
<input type="text" id="account-number" value="" class="field"/><br/>
<label>Tax ID</label><br/>
<input type="text" id="tax_id" value="<?=$user->tax_id?>" class="field"/><br/><br/>
<button class="btn btn-primary" type="button" onclick="complete();" id="update-btn">Update</button>&nbsp;&nbsp;<span id="error-message"></span><span id="success-message"></span><br/><br/>
<small><i class="fa fa-lock"></i> Transfers and billing features are safe, secure, and powered by <a href="https://www.stripe.com" target="_new">Stripe</a>.</small>
<script>
	Stripe.setPublishableKey('<?=Config::get('app.stripe_publishable_key')?>');

	function stripeResponseHandler(status, response) {

		if (response.error) {
			$('#update-btn').find('i').remove();
			$('#error-message').html(response.error.message);
		} else {
			var token = response.token;
			$.post('/settings/recipient', {tax_id: $('#tax_id').val(), token: token}, function(data) {
				$('#update-btn').find('i').remove();
				if (data.status == 200) {
					$('#error-message').html('');
					$('#success-message').html('Bank account connected. You may now start a campaign.');
				}
			},'json');
		}
	}

	function complete() {
		$('#success-message').html('');
		$('#error-message').html('');
		$('#update-btn').prepend('<i class="fa fa-refresh fa-spin"></i> ');
		Stripe.bankAccount.createToken({
		  country: 'US',
		  currency: 'USD',
		  routing_number: $('#routing-number').val(),
		  account_number: $('#account-number').val()
		}, stripeResponseHandler);
	}
</script>