<?php

class FeedStory extends Eloquent  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'feed';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	protected $primaryKey = 'feed_item_id';

	public function user() {
		return $this->hasOne('User', 'id', 'user_id');
	}

	public function scopeOrganizations($query) {
		return $query->join('users', 'users.id' , '=', 'user_id')->where('users.type' ,'=', 'organization');
	}

	public function scopePopular($query) {
		$recently = time()-(5*24*60*60);
		$recently = date('Y-m-d H:i:s', $recently);
		return $query->where('feed.updated_at', '>=', $recently)->orderBy('popularity_score', 'desc');
	}

	public function scopeRecent($query) {
		return $query->orderBy('feed.created_at', 'desc');
	}

	public function obj() {

		if ($this->model == 'Forum') {
			$id = 'forum_id';
		} else if ($this->model == 'Post') {
			$id = 'post_id';
		} else if ($this->model == 'Donation') {
			$id = 'donation_id';
		} else if ($this->model == 'Campaign') {
			$id = 'campaign_id';
		}


		return $this->hasOne($this->model, $id, 'object_id');
	}

	

}
