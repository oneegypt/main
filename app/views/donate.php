<div class="row">
	<div class="col-md-7">
<h3><?=$campaign->title?></h3>
		<?php
			$amount = Input::get('amount');
			$total = $amount+($amount*.05);
			$total = number_format($total, 2);
		?>
<p>Campaign Recipient: <?=$campaign->recipient->display_name?></p>
<p>Organizer: <?=$campaign->creator->display_name?></p>
<label>Donation Amount (USD)</label><br/>
<input type="number" name="amount" value="<?=number_format(Input::get('amount'),2)?>" class="field"/><br/>
<?php
	$show_form = true;
	if (!empty(Auth::user()->stripe_customer_id)) {
		$url = 'https://'.Config::get('app.stripe_secret_key').'@api.stripe.com/v1/customers/'.Auth::user()->stripe_customer_id;
		$response = file_get_contents($url);
		$customer = json_decode($response);
		if ($customer->sources->total_count > 0) {
			$show_form = false;
		}
	}
	if ($show_form) {
?>
<label>Credit Card Number</label><br/>
<input type="text" id="cc"name="cc" placeholder="XXXX-XXXX-XXXX-XXXX" class="field"/><br/>
<label>Expiration Date</label><br/>
<input type="text" id="expiration" name="expiration" value="" class="field" placeholder="MM/YYYY"/><br/>
<label>CVC</label><br/>
<input type="text" id="cvc" maxlength="6" name="cvc" value="" placeholder="Security Code" class="field"/><br/><br/>

<input name="save_card" value="1" type="checkbox" id="save_card" for="save" checked/>&nbsp;<span id="save">Save card for future donations</span>
<?php
	} else {
		echo '<small><i class="fa fa-lock"></i> Using saved card ending in '.($customer->sources->data[0]->last4).'</small><br/>';
	}

?>
<br/><input name="add_tip" value="1" type="checkbox" id="add_tip" for="tip" checked/>&nbsp;<span id="tip">Add 10% to support OneEgypt</span>
<br/><br/>
<input type="hidden" id="donation_campaign_id" value="<?=$campaign->campaign_id?>"/>
<span id="donation-message" style="color:green;font-weight:700;"></span>
<button class="btn btn-primary" type="button" id="complete-donation">Complete Donation of <span class="total-amount"><?=$total?></span></button>


</div>
<div class="col-md-5">
	<h3><i class="fa fa-lock"></i> Safe &amp; Security</h3>
	<p>OneEgypt's credit card processing platform is safe, powered by Stripe, and features 128-bit SSL encryption to ensure secure transactions.</p>
	<p>You will receive an e-mail confirmation and receipt for your charitable contribution.</p>
	<table style="font-size:13px;width:100%;">
		<tr><td><?=$campaign->recipient->display_name?></td><td class="donation-amount">$<?=number_format(Input::get('amount'),2)?></td></tr>
		<tr><td>OneEgypt.org</td><td class="tip-amount"><?=number_format((Input::get('amount')*.10),2)?></td></tr>

		<tr><td>Total</td><td class="total-amount"><?=$total?></td></tr>

	</table>
</div>
<a href="#" onclick="$('.screen').fadeOut(200);" style="position:absolute;font-size:28px;font-weight:700;color:#aaa;right:20px;top:14px;">&times;</a>
</div>
