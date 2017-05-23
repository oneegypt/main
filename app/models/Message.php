<?php

class Message extends Eloquent  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'messages';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	protected $primaryKey = 'message_id';

	public function sender() {
		return $this->hasOne('User', 'id', 'sender_id');
	}
	
	public function thread() {
		return $this->belongsTo('Thread', 'thread_id', 'thread_id');
	}

}
