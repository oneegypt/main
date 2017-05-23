<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Post extends Eloquent  {

	use SoftDeletingTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'posts';



    protected $dates = ['deleted_at'];
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	protected $primaryKey = 'post_id';

	public function author() {
		return $this->hasOne('User', 'id', 'author_id');
	}

	public function votes() {
		return $this->hasMany('Vote', 'post_id', 'post_id');
	}

	public function comments() {
		return $this->hasMany('Comment', 'post_id', 'post_id')->orderBy('created_at', 'asc');
	}

	public function forum() {
		return $this->hasOne('Forum', 'forum_id', 'forum_id');
	}

	public function myvote() {
		if (Auth::check()) {
			return $this->hasOne('Vote', 'post_id', 'post_id')->where('votes.user_id','=', Auth::user()->id);
		} else {
			return $this->hasOne('Vote', 'post_id', 'post_id')->where('votes.user_id','=', -1);
		}
	}

}
