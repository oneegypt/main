<?php

class Comment extends Eloquent  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'comments';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	protected $primaryKey = 'comment_id';

	public function author() {
		return $this->hasOne('User', 'id', 'user_id');
	}

	public function post() {
		return $this->belongsTo('Post', 'post_id', 'post_id');
	}

	public function votes() {
		return $this->hasMany('CommentVote', 'comment_id', 'comment_id');
	}

	public function myvote() {
		if (Auth::check()) {
			return $this->hasOne('CommentVote', 'comment_id', 'comment_id')->where('comment_votes.user_id', '=', Auth::user()->id);
		} else {
			return $this->hasOne('CommentVote', 'comment_id', 'comment_id')->where('comment_votes.user_id', '=', -1);
		}
	}
}
