<?php

class Vote extends Eloquent  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'votes';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	protected $primaryKey = 'vote_id';

	public function user() {
		return $this->belongsTo('User', 'id', 'user_id');
	}

}
