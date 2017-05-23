<?php

class CommentVote extends Eloquent  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'comment_votes';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	protected $primaryKey = 'comment_vote_id';

	public function user() {
		return $this->belongsTo('User', 'id', 'user_id');
	}

}
