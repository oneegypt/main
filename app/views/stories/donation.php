<div id="donation-<?=$object->donation_id?>" class="status-post">
<div class="row" style="padding-top:12px;">
	<div class="col-md-1 col-sm-1 col-xs-1">
		
			<div class="num_votes" style="margin-top:10px;text-align:center;"><i class="fa fa-heart"></i></div>
			
		</div>

	<div class="col-md-1 col-sm-1 col-xs-1">

		<?php
			$photo_style2 = '';

			//echo json_encode($object);
			if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$object->user_id.'.jpg') ) {
				$path = Image::path('/assets/user/'.$object->user_id.'.jpg', 'resizeCrop', 50, 50);
				$photo_style2 = "background-image:url('$path');";
			}

		?>
		<div class="circle" style="<?=$photo_style2?>">

		</div>
	</div>
	<div class="col-md-9 col-sm-9 col-xs-9">

		<?php
			if ($object->campaign_id > 0) {
				$campaign = Campaign::find($object->campaign_id);
		?>

		<a href="/user/<?=$object->user_id?>/"><?=$object->donater->display_name?></a> gave a contribution to <a href="/campaign/<?=$object->campaign_id?>"><?=$campaign->title?></a>.<br/>
		
		<?php
			} 


			$diff = time()-strtotime($object->updated_at);

			$when = '';
			$units = '';
			$tokens = array (
		        31536000 => 'y',
		        2592000 => 'm',
		        604800 => 'w',
		        86400 => 'd',
		        3600 => 'h',
		        60 => 'm',
		        1 => 's'
		    );
		    foreach ($tokens as $unit => $text) {
		        if ($diff < $unit) {
		        	continue;
		        } else {
			        $numberOfUnits = floor($diff / $unit);
			        echo '<small>'.$numberOfUnits.''.$text.' ago</small>';
			        break;
		    	}
		    }
			
		?>
				
	</div>
	<div class="col-md-1 col-sm-1 col-xs-1">
		&nbsp;
	</div>
</div>
</div>