<?php

class Campaign extends Eloquent  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'campaigns';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	protected $primaryKey = 'campaign_id';

	public function creator() {
		return $this->hasOne('User', 'id', 'user_id');
	}

	public function recipient() {
		return $this->hasOne('User', 'id', 'recipient_id');
	}

	public function donations() {
		return $this->hasMany('Donation', 'campaign_id', 'campaign_id')->orderBy('created_at', 'desc');
	}

	public function scopePopular($query) {
		return $query->join('donations', 'donations.campaign_id', '=', 'campaigns.campaign_id')->select(DB::raw('campaigns.*, sum(donations.amount) as total'))->groupBy('campaigns.campaign_id')->orderBy('total', 'desc');
	}



}
