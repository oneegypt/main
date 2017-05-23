<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ForumPost extends Eloquent  {

	protected $table = 'forum_posts';
	use SoftDeletingTrait;


    protected $dates = ['deleted_at'];
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	protected $primaryKey = 'forum_post_id';

	public function thread() {
		return $this->belongsTo('ForumThread', 'forum_thread_id', 'forum_thread_id');
	}

	public function author() {
		return  $this->hasOne('User', 'id', 'posted_by');
	}

	public function repliedTo() {
		return $this->hasOne('ForumPost', 'forum_post_id', 'reply_to')->withTrashed();
	}

	public function scopeRecent($query, $user_id) {
		return $query->join('forum_threads', 'forum_threads.forum_thread_id', '=', 'forum_posts.forum_thread_id')->join('forum_followers', 'forum_followers.forum_id', '=', 'forum_threads.forum_id')->where('forum_followers.id', '=', $user_id)->take(15)->orderBy('forum_posts.updated_at', 'desc');
	}
}