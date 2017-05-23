<!DOCTYPE HTML>
<html>
<head>
	<?= View::make('header') ?>
	<style>
		.donation {
			padding:5px;
		}
	</style>
</head>
<body class="">
	<?= View::make('navigation')?>
	<div class="container fus-white-bg fus-section fus-feature fus-white-bg">
		<div class="row">
			<div class="col-md-7">
				<h1><img src="<?=$campaign->creator->thumbnail()?>"/> <?=$campaign->title?></h1>
				<p>Organized by <a href="<?=$campaign->creator->getURL()?>"><?=$campaign->creator->display_name?></a>
					to benefit <a href="<?=$campaign->recipient->getURL()?>"><?=$campaign->recipient->display_name?></a>
				</p>
				<div class="progress">
				  <div class="progress-bar progress-bar-striped progress-bar-success" role="progressbar" aria-valuenow="<?=$percentage?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=$percentage?>%;">
				   $<?=number_format($total_donations,2)?> Raised
				  </div>
				</div>

				<h3>Campaign Mission</h3>
				<p><?=str_replace("\n", '<br/>', $campaign->description)?></p>
				
				<h3>Give to this cause</h3>
				<p>You can contribute to this campaign with a credit card.</p>
				<a class="btn btn-primary give" href="/organization/<?=$campaign->recipient->username?>/campaign/<?=$campaign->campaign_id?>?amount=5"><i class="fa fa-plus"></i> $5</a>
				<a class="btn btn-primary give" href="/organization/<?=$campaign->recipient->username?>/campaign/<?=$campaign->campaign_id?>?amount=25"><i class="fa fa-plus"></i> $25</a>
				<a class="btn btn-primary give" href="/organization/<?=$campaign->recipient->username?>/campaign/<?=$campaign->campaign_id?>?amount=100"><i class="fa fa-plus"></i> $100</a>
				<a class="btn btn-primary give" href="/organization/<?=$campaign->recipient->username?>/campaign/<?=$campaign->campaign_id?>?amount=250"><i class="fa fa-plus"></i> OTHER AMOUNT</a>
			</div>
			<div class="col-md-5">
				<h3>Recent Donations</h3>
				<?php
					if (sizeof($campaign->donations) == 0) {
						echo '<div class="donation">';
						echo '<p>No contributions made yet. Be the first to <a href="/organization/'.$campaign->recipient->username.'/campaign/'.$campaign->campaign_id.'?amount=5" class="give">donate</a>.</p>';
						echo '</div>';
					}

					foreach($campaign->donations as $donation) {
						echo '<div class="donation">';
						echo '<div class="row">';
						echo '<div class="col-md-2 col-sm-2 col-xs-2">';
						echo '<img src="'.$donation->donater->thumbnail().'"/>';
						echo '</div>';
						echo '<div class="col-md-10 col-sm-9 col-xs-9">';
						echo '<a href="'.$donation->donater->getURL().'">'.$donation->donater->display_name.'</a> gave $'.number_format($donation->amount,2 );
						echo '<br/><small><div class="line">&nbsp;</div><i class="fa fa-clock-o"></i> '.ago($donation->created_at).' ago</small>';
						echo '</div></div></div>';
					}

				?>
			</div>
		</div>
	</div>
	<?=View::make('footer')?>
	<div class="screen">
	<div class="overlay  fus-white-bg fus-section fus-feature fus-white-bg " id="overlay">

	</div>
</div>
<script>
		function donationHandler(status, response) {
			if (response.error) {
				$('#donation-message').css('color','red').html(response.error.message);

			} else {
				var campaign_id = $('#donation_campaign_id').val();
				var total = calculate();
				var donation_amount = $('input[name=amount]').val();
				var save_card = $('#save_card').prop('checked');
				$.post('/organization/<?=$campaign->recipient->username?>/campaign/'+campaign_id, {save_card: save_card, total: total, donation: donation_amount, token: response.id}, function(response) {
					if (response.success == 1){
						$('#donation-message').css('color', 'green').html( response.message);
					} else {
						$('#donation-message').css('color', 'red').html( response.message);
					}
				},'json');
			}
		}

		function saved_card() {
			var campaign_id = $('#donation_campaign_id').val();
			var total = calculate();
			var donation_amount = $('input[name=amount]').val();
			$.post('/organization/<?=$campaign->recipient->username?>/campaign/'+campaign_id, { total: total, donation: donation_amount, token: ''}, function(response) {
				if (response.success == 1){
						$('#donation-message').css('color', 'green').html( response.message);
				} else {
					$('#donation-message').css('color', 'red').html( response.message);
				}
			},'json');
		}
</script>
</body>
</html>