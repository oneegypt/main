<?= View::make('top')->with('highlight', 'work') ?>
<?php

  		if (!empty($listing->latitude) ) {
  	?>

  	<script src="https://maps.googleapis.com/maps/api/js"></script>

  	<script>
  	function initialize() {
  		$('#map-canvas').css('height', '230px');
	  	var mapCanvas = document.getElementById('map-canvas');
	  	var myLatlng = new google.maps.LatLng(<?=$listing->latitude?>,<?=$listing->longitude?>);
		var mapOptions = {
	      center: new google.maps.LatLng(<?=$listing->latitude?>, <?=$listing->longitude?>),
	      zoom: 16,
	      mapTypeId: google.maps.MapTypeId.ROADMAP
	    }
	    var map = new google.maps.Map(mapCanvas, mapOptions);
	    var marker = new google.maps.Marker({
		      position: myLatlng,
		      map: map,
		      title: '<?=$listing->postedBy->display_name?>'
		  });
	}
	google.maps.event.addDomListener(window, 'load', initialize);
    </script>
    <?php
}
    ?>
 
<div class="row">
	<div class="col-md-8">
		<h3><?=$listing->listing_title?></h3>
		<p><i class="fa fa-calendar"></i> Posted on <?=date('F jS, Y h:i a', strtotime($listing->created_at))?></p>
		<p style="background-color:#f2f2f2;padding:10px;"><?=$listing->listing_body?></p>
		<p><?php
			$tags = explode(',', substr($listing->tags,0 ,-1));
			foreach ($tags as $tag) {
				echo '<a href="/work?tag='.urlencode($tag).'" class="tag" style="margin-right:10px;"><i class="fa fa-tag"></i> '.strtolower(urldecode($tag)).'</a>'; 
			}
		?></p>	
		<?php
			if (strpos($listing->action_url, '@') > 0) {

				//If email address
				$action_url = 'mailto:'.$listing->action_url;
			} else {
				$action_url = $listing->action_url;
			}

		?>
		<a href="<?=$action_url?>" class="btn btn-primary"><i class="fa fa-envelope"></i> APPLY FOR POSITION</a>
		<?php
			if (Auth::check()) {

				if ($favorited) {
					?>

		<a href="/unfavoriteListing/<?=$listing->listing_id?>" class="btn" id="favorited-btn"><i class="fa fa-check"></i> FOLLOWING</a>
		<script>
			$('#favorited-btn').hover(function() {
				$(this).html('<i class="fa fa-remove"></i> UNFOLLOW');
			}, function() {
				$(this).html('<i class="fa fa-check"></i> FOLLOWING');
			});
		</script>
					<?php
				} else {
		?>
		<a href="/favoriteListing/<?=$listing->listing_id?>" class="btn"><i class="fa fa-star"></i> FOLLOW</a>
		<?php
				}
			}
		?>	
	</div>
	<div class="col-md-4">
		<h3>Who posted this job?</h3>
		<?php
			if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$listing->creator_id.'.jpg')) {
				$path = Image::path('/assets/user/'.$listing->creator_id.'.jpg', 'resizeCrop', 50, 50);
				echo '<div class="circle" style="background-image:url(\''.$path.'\');margin:0px;float:left;margin-right:10px;">&nbsp;</div>';
			}
		?>
		<a href="/user/<?=$listing->postedBy->id?>"><?=$listing->postedBy->display_name?></a>
		<?php
			if (!empty($listing->postedBy->description_txt)) {
				$excerpt = str_replace("\n", '<br/>', $listing->postedBy->description_txt);

				if (strlen($excerpt) > 300) {
					$excerpt = substr($excerpt, 0, 300).'... <a href="/user/'.$listing->postedBy->id.'">Read more</a>';
				}
				echo '<p>'.$excerpt.'</p>';
			}
		?>
		<div id="map-canvas"></div><br/>
	</div>
</div>