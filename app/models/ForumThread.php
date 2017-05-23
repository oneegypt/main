<?php

class ForumThread extends Eloquent  {

	protected $table = 'forum_threads';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	protected $primaryKey = 'forum_thread_id';

	public function thread() {
		return $this->belongsTo('Forum', 'forum_id', 'forum_id');
	}

	public function author() {
		return $this->hasOne('User', 'id', 'creator_user_id');
	}

	public function posts() {
		return $this->hasMany('ForumPost', 'forum_thread_id', 'forum_thread_id');
	}

	public function lastPost() {
		return $this->hasOne('ForumPost', 'forum_thread_id', 'forum_thread_id')->orderBy('created_at', 'desc');
	}
}