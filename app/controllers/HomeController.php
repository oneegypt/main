<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome()
	{
		return View::make('hello');
	}

	public function showProfile($username){



		$allow_edit = false;

		$show_tutorial = false;

		$user = User::where('username', '=', $username)->first();


		if ($user->type == 'organization' && is_null($user->approved_at)) {
			return Redirect::to('/support');
		}
		//echo json_encode(Auth::user());
		if (Auth::check() && Auth::user()->id == $user->id) {
			//echo 'hi!';
			$allow_edit = true;
		}
		$following = false;

		if (Auth::check() ) {
			$me = Auth::user();
			$following = DB::table('followers')->where('user_id', '=', $user->id)->where('follower_id', '=', $me->id)->count();
		}


		$posts = FeedStory::where('user_id', '=', $user->id)->where('type' ,'!=' ,'')->orderBy('created_at', 'DESC')->get();
		
		$campaigns = array();

		$general_id = 0;
		if ($user->type == 'organization' && !empty($user->stripe_recipient_id)) {
			$campaigns = Campaign::where('recipient_id', '=', $user->id)->where('general', '=', false)->orderBy('created_at', 'desc')->take(5)->get();
			//Any approved organization should have a general campaign.
			$general = Campaign::where('recipient_id', '=', $user->id)->where('general','=', true)->first();
			$general_id = $general->campaign_id;
		}

		$donated_amount = DB::table('donations')->where('user_id','=', $user->id)->sum('amount');

		$raised_amount = DB::table('campaigns')->where('campaigns.user_id','=', $user->id)->join('donations', 'campaigns.campaign_id','=','donations.campaign_id')->sum('amount');

		return View::make('profile', array('general_campaign_id' => $general_id,  'donated_amount' => $donated_amount, 'raised_amount' => $raised_amount, 'campaigns' => $campaigns, 'user' => $user, 'feed' => $posts, 'allow_edit' => $allow_edit, 'following' => $following));
	}

	public function home() {
	//	echo Session::get('user_id');
		$user = Auth::user();
		$attributes = array();

		foreach($user->attributes as $att) {
			$attributes[$att->attribute_key] = $att->attribute_value;
		}

		return View::make('home', array('user' => $user, 'att' => $attributes));
	}

	public function forums() {


		$sliders = DB::table('features')->where('section' ,'=', 'support')->orderBy('updated_at', 'desc')->get();
		function desc($a, $b) {
			$a1 = strtotime($a->created_at);
			$b1 = strtotime($b->created_at);

			if ($a1 < $b1) {
				return 1;

			}
			if ($a1 > $b1) {
				return -1;
			}
			return 0;
		}

		
		//$recent = FeedStory::organizations()->recent()->select(DB::raw('feed.*'))->whereIn('model', array('Donation', 'Campaign'))->take(18)->orderBy('feed.created_at' , 'desc')->get();
		
		$campaigns = Campaign::popular()->take(5)->get();
		if (Cache::has('recent')) {
			$recent = Cache::get('recent');
		} else {
			//echo 'hi';
			$recent = array();

			$donations = Donation::orderBy('created_at', 'desc')->take(15)->get();
			
			

			foreach($donations as $donation) {
				array_push($recent, $donation);
			}

			foreach($campaigns as $campaign) {
				array_push($recent, $campaign);
			}

			usort($recent, 'desc');
			$expiresAt = time()+(80*60);
			Cache::put('recent', $recent, $expiresAt );
		}

		if (Cache::has('topNonProfits')) {
			$topNonProfits = Cache::get('topNonProfits');
		} else {
			$topNonProfits = User::top()->take(5)->get();
			$expiresAt = time()+(15*60);
			Cache::put('topNonProfits', $topNonProfits, $expiresAt);
		}
		

		
		$cutoff = time()-(14*60*60*24);

		//$popular = FeedStory::organizations()->popular()->select(DB::raw('feed.*'))->whereIn('feed.model' , array('Campaign', 'Donation'))->get();

		$organizations = User::organizations();

		if (Input::has('category') && Input::get('category') != 0) {
			$organizations = $organizations->where('category_id', '=', Input::get('category'));
		}
		$blockSize = 20;

		$count = sizeof($organizations->get() );
		$num_pages = ceil($count/$blockSize);

		if (Input::has('p')) {
			$p = Input::get('p')-1;
			$offset = $p*$blockSize;
			$organizations = $organizations->skip($offset)->take($blockSize);
		} else {
			$organizations = $organizations->take($blockSize);
		}

		$organizations = $organizations->get();

		if (Cache::has('org.categories')) {
			$categories = Cache::get('org.categories');
		} else {
			$categories = DB::table('categories')->whereRaw('type = 3')->get();

			$expiresAt = time()+(60*60);
			Cache::put('org.categories', $categories, $expiresAt);
		}

		return View::make('forums', array('num_pages' => $num_pages, 'categories' => $categories, 'top_non_profits' => $topNonProfits, 'campaigns' => $campaigns,  'organizations' => $organizations, 'sliders' => $sliders, 'recent' => $recent));
	}

	public function forumsCreate() {
		if (!Auth::check() ) {
			return Redirect::to('/auth?uri='.urlencode('/forums/create') );
		}
		$categories = DB::table('categories')->orderBy('category_name', 'asc')->where('type', '=', 1)->get();
		$types = DB::table('forum_types')->orderBy('forum_type_name', 'asc')->get();
		return View::make('forum_create', array('categories' => $categories, 'types' => $types));
	}

	public function createForum() {
		if (!Auth::check()) {
			die();
		}

		$validator = Validator::make(Input::all(),
				array('title' => 'required', 'description' => 'required', 'category' => 'required', 'type' => 'required')
			);

		if ($validator->fails()) {
			return Response::JSON(array('message' => $validator->messages()->first()) );
		}



		$user_id = Auth::user()->id;
		$forum = new Forum;
		$forum->title = Input::get('title');
		$forum->description = Input::get('description');
		$forum->category = Input::get('category');
		$forum->forum_type_id = Input::get('type');

		$tags = Input::get('tags');
		$tags = explode(',', $tags);




		$forum->creator_id = $user_id;
		$forum->save();

		foreach($tags as $tag) {
			DB::table('forum_tags')->insert(array(
					'tag_name' => urldecode($tag),
					'forum_id' => $forum->forum_id
				));
		}

		$search_record = new Search;
		$search_record->object_id = $forum->forum_id;
		$search_record->search_terms = $forum->title;
		$search_record->model = 'Forum';
		$search_record->save();
		$response = Event::fire('feed.update', array($forum));

		if ($forum->forum_id > 0) {
			echo json_encode(array('url' => Config::get('app.url').'/forums/'.$forum->forum_id.''));
		}
	}

	public function viewForum($forum_id, $page=1) {

		$forum = Forum::find($forum_id);

		if (Request::ajax()) {
			return Response::JSON($forum);
		}


		
		$tags = DB::table('forum_tags')->where('forum_id','=', $forum_id)->get();

		$threads = ForumThread::where('forum_id', '=', $forum_id)->orderBy('updated_at', 'desc')->with('lastPost.author')->get();
		$moderators = Privilege::where('scope', '=', $forum_id)->where('privilege_key', '=', 'mod')->get();

		return View::make('forum', array('forum' => $forum, 'tags' => $tags, 'threads' => $threads, 'moderators' => $moderators));
	}

	public function createPost() {
		if (!Auth::check()) {
			die();
		}
		$post = new Post;

		$content = Input::get('post_txt');
		$content = strip_tags($content);

		if (str_contains($content, 'http://')) {
			$i = strpos( $content, 'http://');
		} else if (str_contains($content, 'https://')) {
			$i = strpos( $content, 'https://');
		}

		
		if (isset($i)) {
			$substring = substr($content, $i);
			$parts = explode(' ', $substring);
			if (sizeof($parts) >= 1) {
				$url = $parts[0];
			} 

		}

		if (isset($url)) {
			
			$content = str_replace($url, '<a href="'.$url.'" target="new">'.$url.'</a>', $content);
			$graph = OpenGraph::fetch($url);

			//echo $graph->title;
			//exit();
			if (!empty($graph->title) && !empty($graph->description) && !empty($graph->image)) {
				$post->attachment = true;
				$post->link_title = $graph->title;
				$post->link_description = $graph->description;
				$post->link_url = $url;
				$post->link_image = $graph->image;

			}
		}

		$post->content = $content;
		$post->forum_id = Input::get('forum_id');
		$post->author_id = Auth::user()->id;
		$post->save();

		$response = Event::fire('feed.update', array($post));
		$response = Event::fire('popularity.touch', array($post));
	
	}

	public function vote($id) {
		if (Auth::check()) {
			$user = Auth::user();
			$vote = Vote::where('user_id', '=', $user->id)->where('post_id', '=', $id)->first();

			if (!empty($vote)) {
				$vote->vote += Input::get('vote');
				if ($vote->vote > 1) {
					$vote->vote = 1;
				} else if ($vote->vote < -1) {
					$vote->vote = -1;
				}
				$vote->post_id = $id;
				$vote->save();
			} else {
				$vote = new Vote;
				$vote->user_id = $user->id;
				$vote->post_id = $id;
				$vote->vote = Input::get('vote');
				$vote->save();
			}

			$post = Post::find($id);
			$response = Event::fire('popularity.touch', array($post));
		}

		$total = DB::table('votes')->where('post_id', '=', $id)->sum('vote');
		$vote = $vote->vote;

		echo json_encode(array('total' => $total, 'vote' => $vote));
	}

	public function comment($id ) {
		if (Auth::check()) {
			$user = Auth::user();
			$comment = new Comment;
			$comment->post_id = $id;
			$comment->user_id = $user->id;
			$comment->content = Input::get('content');
			$comment->save();
		}
		$post = Post::find($id);

		return View::make('comments', array('post' => $post));
	}
	public function follow($user_id) {
		$id = 0;
		if (Auth::check()) {
			$count = DB::table('followers')->where('user_id', '=', $user_id)->where('follower_id', '=', Auth::user()->id)->count();
			if ($count == 0) {
				
				$id = DB::table('followers')->insertGetId(
						array('user_id' => $user_id, 'follower_id' => Auth::user()->id)
					);

				$notification = new Notification;
				$notification->user_id = $user_id;
				$notification->link = '/user/'.Auth::user()->id;
				$notification->message = Auth::user()->display_name.' is now following you';
				$notification->save();

			}
		} else {
			return Redirect::to('/auth?uri='.urlencode('/user/'.$user_id.'/follow'));
		}
		$user = User::find($user_id);
		
		if ($id > 0 ) {
			Session::flash('message', '<i class="fa fa-check"></i> You are now following '.$user->display_name);
		} 

		
		return Redirect::to('/'.$user->type.'/'.$user->username);
		
	}
	public function unfollow($user_id) {
		$id = 0;
		$user = User::find($user_id);
		if (Auth::check()) {
			DB::table('followers')->where('user_id', '=', $user_id)->where('follower_id', '=', Auth::user()->id)->delete();
			Session::flash('message', '<i class="fa fa-remove"></i> You are no longer following '.$user->display_name);
		}
		
		return Redirect::to('/'.$user->type.'/'.$user->username);
		
	}

	public function followers($user_id) {

	}

	public function following($user_id) {

	}

	public function dialogueHome() {

		$sliders = DB::table('features')->where('section' ,'=', 'dialogue')->orderBy('updated_at', 'desc')->get();

		$attributes = array();
		$user = '';
		
		$categories = Category::where('type','=', 1)->orderBy('category_name', 'asc')->get();



		return View::make('dialogue', array('sliders' => $sliders, 'user' => $user, 'categories' => $categories));

	}

	public function workHome() {

		$sliders = DB::table('features')->where('section' ,'=', 'work')->orderBy('updated_at', 'desc')->get();


		if (Input::has('q')) {
			$query = Input::get('q');
			$mainQuery = Listing::join('users', 'creator_id', '=', 'users.id')->whereRaw("MATCH(listing_title,listing_body,users.display_name) AGAINST(? IN BOOLEAN MODE)", array($query));
			$recent_posts = $mainQuery->orderBy('listings.created_at', 'desc')->where('creator_id', '>', 0)->take(25);

		} else {
			$recent_posts = Listing::orderBy('created_at', 'desc')->where('creator_id', '>', 0)->take(25);

		}

		if (Input::has('t')) {
			$recent_posts = $recent_posts->whereIn('listings.type' , Input::get('t'));
		}

		if (Input::has('c')) {
			$recent_posts = $recent_posts->join('listing_categories', 'listing_categories.listing_id', '=', 'listings.listing_id')->whereIn('listing_categories.category_id', Input::get('c'))->groupBy('listings.listing_id');
		}

		if (Input::has('tag')) {
			//echo json_encode
			$tag  = Input::get('tag');
			$recent_posts = $recent_posts->whereRaw("MATCH(tags) AGAINST(? IN BOOLEAN MODE)", array($tag));
		}

		if (Input::has('l')) {
			$address = urlencode(Input::get('l'));
			$url = 'https://maps.googleapis.com/maps/api/geocode/json?api_key='.Config::get('app.google_api_key').'&address='.$address;
			$response = file_get_contents($url);
			$json = json_decode($response);
			if (isset($json->results[0]->geometry)) {
				$location = $json->results[0]->geometry->location;
				
				$lat = $location->lat;
				$lng = $location->lng;
				$radius = 15;
				$recent_posts->select(DB::raw('listings.*, ( 6371 * acos( cos( radians('.$lat.') ) *  cos( radians( latitude ) )
                                * cos( radians( longitude ) - radians('.$lng.')
                                ) + sin( radians('.$lat.') ) *
                                sin( radians( latitude ) ) )
                              ) AS distance'))->having("distance", "<", $radius);
			}
		}



		$all_posts = $recent_posts->get();
		
		$total_count = sizeof($all_posts);
		$blockSize = 10;
//		if ($total_count > 0) {
		$num_pages = ceil($total_count/$blockSize);


		$offset = 0;

		if (Input::has('p')) {
			$p = Input::get('p')-1;
			$offset = $p*$blockSize;
		}
		$recent_posts = $recent_posts->take($blockSize)->skip($offset)->get();

		
		$categories = DB::table('categories')->where('type', '=', Config::get('app.category_type_jobs'))->get();

		if (Request::ajax()) {
			$types = array('full-time' => 'Full Time', 'part-time' => 'Part Time',
						'temporary' => 'Temporary',
						'contract' => 'Contract',
						'internship' => 'Internship',
						'volunteer' => 'Volunteer'

						);
			
			foreach($recent_posts as $job) {
				$style = '';
				$type = $types[$job->type];
				if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$job->creator_id.'.jpg')) {
					$style = "background-image:url('".Image::path('/assets/user/'.$job->creator_id.'.jpg', 'resizeCrop', 50, 50)."');";
				}
				?>
				<div class="job-listing row">
					<div style="float:left;width:60px;text-align:center;">
						<div class="circle" style="<?=$style?>">&nbsp;</div>
						<small><?=ago($job->created_at)?> ago</small>
					</div>
					<div class="job-content">
						<a href="/listings/<?=$job->listing_id?>/aasdfasdf" class="job-title"><?=$job->listing_title?></a> (<?=$type?>)<br/>
						<span class="employer"><?=$job->postedBy->display_name?></span><br/>
						<?php
							$excerpt = '';
							if (strlen($job->listing_body) > 200) {
								$excerpt = substr($job->listing_body,0, 200).'...';
							} else {
								$excerpt = $job->listing_body;
							}
							echo '<p>'.$excerpt.'</p>';
						?>
					</div>
					
				</div>
				<?php
			}
		} else {
			return View::make('work', array('num_pages' => $num_pages,  'sliders' => $sliders, 'recent' => $recent_posts, 'categories' => $categories));

		}
		
	}

	
}
