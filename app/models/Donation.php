<?php

class Donation extends Eloquent  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'donations';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	protected $primaryKey = 'donation_id';

	public function donater() {
		return $this->hasOne('User', 'id', 'user_id');
	}

	public function campaign() {
		return $this->hasOne('Campaign', 'campaign_id', 'campaign_id');
	}

	public function scopeRecent($query, $user_id) {
		return $query->join(DB::raw('donations b'), 'b.campaign_id', '=', 'donations.campaign_id')->where('donations.user_id', '=', $user_id)->orderBy('donations.created_at', 'desc')->take(15);
	}
}
