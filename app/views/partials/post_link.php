<?php
	$image_url = $object->link_image;
	$bg_style = "background-image:url('".$image_url."');";
	?>
	<div class="attachment" style="<?=$bg_style?>">
		<div class="metadata">
			<h4><a href="<?=$object->link_url?>" target="new"><?=$object->link_title?></a></h4>
			<p><?=$object->link_description?></p>
		</div>
	</div>
	<?php

?>