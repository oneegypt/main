<?php

class Bookmark extends Eloquent  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'bookmarks';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	protected $primaryKey = 'bookmark_id';

	public function listing() {
		return $this->hasOne('Listing', 'listing_id', 'listing_id');
	}
}
?>