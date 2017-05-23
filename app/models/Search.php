<?php

class Search extends Eloquent  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'search';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	protected $primaryKey = 'search_id';

	public function record() {
		//if user
		$foreign_key = 'id';

		if ($this->model == 'Forum') {
			$foreign_key = 'forum_id';
		} else if ($this->model == 'User') {
			$foreign_key = 'id';
		} else if ($this->model == 'Post') {
			$foreign_key = 'post_id';
		} else if ($this->model == 'Campaign') {
			$foreign_key = 'campaign_id';
		}

		return $this->hasOne($this->model, $foreign_key, 'object_id');
	}

}
