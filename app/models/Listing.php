<?php

class Listing extends Eloquent  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'listings';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	protected $primaryKey = 'listing_id';

	
	public function postedBy() {
		return $this->hasOne('User', 'id', 'creator_id');
	}

	public function categories() {
		 return $this->belongsToMany('Category', 'listing_categories', 'listing_id', 'category_id');
	}
}
