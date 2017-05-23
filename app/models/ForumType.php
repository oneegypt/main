<?php

class ForumType extends Eloquent  {

		/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'forum_types';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	protected $primaryKey = 'forum_type_id';

	
}