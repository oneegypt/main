<?php

class Forum extends Eloquent  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'forums';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	protected $primaryKey = 'forum_id';

	public function creator() {
		return $this->hasOne('User', 'id', 'creator_id');
	}



	public function posts() {
		return $this->hasMany('Post', 'forum_id', 'forum_id');
	}

	public function getURL() {
		return '/forums/'.$this->forum_id;
	}

	public function getSearchName() {
		return $this->title;
	}

	public function followers() {
		return $this->hasMany('ForumFollower', 'forum_id' ,'forum_id');
	}

	public function cat() {
		return $this->hasOne('Category', 'category_id', 'category');
	}

	public function forumPosts() {
		return $this->hasManyThrough('ForumPost', 'ForumThread', 'forum_id', 'forum_thread_id')->orderBy('forum_posts.created_at', 'desc');
	}

	public function participants() {
		return $this->hasManyThrough('ForumPost', 'ForumThread', 'forum_id', 'forum_thread_id')->groupBy('posted_by')->orderBy('forum_posts.created_at', 'desc');
	}

	public function type() {
		return $this->hasOne('ForumType', 'forum_type_id' ,'forum_type_id');
	}

	public function scopePopular($query) {
		return $query->join('forum_threads', 'forum_threads.forum_id' ,'=' ,'forums.forum_id')->join('forum_posts', 'forum_posts.forum_thread_id', '=', 'forum_threads.forum_thread_id')->groupBy('forums.forum_id')->select(DB::raw('*, count(DISTINCT posted_by) as num_participants'))->orderBy('num_participants', 'desc');
	}

}
