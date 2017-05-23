<?php

class Privilege extends Eloquent  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_privileges';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	protected $primaryKey = 'user_privilege_id';

	public function user() {
		return $this->belongsTo('User', 'user_id' ,'id');
	}

}
