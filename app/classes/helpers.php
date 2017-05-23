<?php
	function ago($updated_at) {
		$diff = time()-strtotime($updated_at);

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
		        return $numberOfUnits.''.$text;
		        //break;
	    	}
	    }

}
	
?>