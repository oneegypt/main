<?php

class Category extends Eloquent  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'categories';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	protected $primaryKey = 'category_id';


	public function forums() {
		if ($this->type == 1) {
			return $this->hasMany('Forum', 'category', 'category_id')->where('published', '=', 1);
		} else {
			return array();
		}
	}
	public function organizations() {
		if ($this->type == 3) {
			return $this->hasMany('User', 'category_id', 'category_id')->where('type','=', 'organization')->where('status', '=', 'approved');
		} else {
			return array();
		}
	}
}
