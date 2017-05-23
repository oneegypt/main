<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Laravel\Cashier\BillableTrait;
use Laravel\Cashier\BillableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface, BillableInterface {

	use UserTrait, RemindableTrait, BillableTrait;

	protected $dates = ['trial_ends_at', 'subscription_ends_at'];


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

	public function attributes() {
		return $this->hasMany('Attribute', 'user_id', 'id');
	}

	public function privileges() {
		return $this->hasMany('Privilege', 'user_id', 'id');
	}

	public function getURL() {
		return '/'.$this->type.'/'.$this->username;
	}

	public function getSearchName() {
		return $this->display_name;
	}

	public function thumbnail() {
		if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$this->id.'.jpg') ) {
			$path = Image::path('/assets/user/'.$this->id.'.jpg', 'resizeCrop', 50, 50);
			return $path;
		}

		return '';
	}

	public function follower_count() {
		return DB::table('followers')->where('user_id', '=', $this->id)->count();
	}

	public function followers() {
		//$followers = DB::table('users')->join('followers', 'followers.follower_id', '=', 'users.id')->where('followers.user_id','=', $this->id)->get();
		return $this->hasManyThrough('User', 'Follower', 'user_id', 'id');
		//return $followers;
	}

	public function followed() {
		$followed = DB::table('users')->join('followers', 'followers.user_id', '=', 'users.id')->where('followers.follower_id','=', $this->id)->get();
		
		return $followed;
	}

	public function following_count() {
		return DB::table('followers')->where('follower_id', '=', $this->id)->count();
	}

	public function tags() {
		$tags = DB::table('organization_tags')->where('user_id','=', $this->id)->select('tag_name')->get();
		return $tags;
	}

	public function donations() {
		return $this->hasMany('Donation' , 'user_id' ,'id');
	}
	public function category() {
		return $this->hasOne('Category' , 'category_id' ,'category_id');
	}

	public function recent_donation_quantity() {
		$cutoff = date('Y-m-d H:i:s', (time()-(60*24*60*14)) );
		return DB::table('donations')->join('campaigns', 'campaigns.campaign_id','=','donations.campaign_id')->where('recipient_id', '=', $this->id)->where('donations.created_at' , '>=' , $cutoff)->count();
	}

	public function map() {
		return $this->hasOne('Map', 'organization_id', 'id');
	}

	public function listings() {
		return $this->hasMany('Listing', 'creator_id' ,'id');
	}

	public function scopeOrganizations($query) {
		return $query->where('type', '=', 'organization')->where('status', '=', 'approved')->leftJoin('followers', 'followers.id', '=', 'users.id')->select(DB::raw('users.*, count(followers.follower_id) as num_followers'))->groupBy('users.id')->orderBy('num_followers', 'desc');
	}

	public function scopeTop($query) {
		return $query->join('campaigns', 'campaigns.recipient_id', '=', 'users.id')->join('donations', 'donations.campaign_id', '=', 'campaigns.campaign_id')->select(DB::raw('users.*, sum(donations.amount) as total'))->groupBy('users.id')->orderBy('total', 'desc');
	}

}
