<?php

class Thread extends Eloquent  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'threads';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	protected $primaryKey = 'thread_id';

	
	public function messages() {
		return $this->hasMany('Message', 'thread_id', 'thread_id')->orderBy('created_at', 'asc');
	}

	public function participants() {
		return $this->hasManyThrough('User', 'Participant');
	}
}
