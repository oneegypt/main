<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	if (Auth::check()) {
		return Redirect::to('/homefeed');
	} else {
		return Redirect::to('http://info.oneegypt.org/');
	}
	
});

Route::get('/register', function() {
	//echo 'hi';
	return View::make('register');
});

Route::get('/register/{type}', function($type) {

	if ($type == 'individual' && !Input::has('code')) {
		return Redirect::to('/beta');
	} else if (Input::has('code')) {
		$invitation = Invitation::where('code', '=', Input::get('code'))->first();
		if (empty($invitation)) {
			return Redirect::to('/beta');
		}
	}

	return View::make('register2', array('type' => $type));
});

Route::post('/register', 'RegistrationController@register');
Route::get('/home', 'HomeController@home');
Route::get('/support', 'HomeController@forums');
Route::get('/forums/create', 'HomeController@forumsCreate');
Route::post('/forums/create', 'HomeController@createForum');
Route::get('/forums/{id}/p/{page}', 'HomeController@viewForum');
Route::get('/forums/{forum_id}/threads/{thread_id}/{page}', 'ForumController@showThread');
Route::post('/forums/{forum_id}/threads/{thread_id}/{page}', 'ForumController@writeReply');
Route::get('/forums/{id}', function($id) {
	return Redirect::to('/forums/'.$id.'/p/1');
});
Route::post('posts/create', 'HomeController@createPost');
Route::post('posts/{id}/vote', 'HomeController@vote');
Route::post('posts/{id}/comment', 'HomeController@comment');

Route::get('/login', function() {
	return View::make('login');
});

Route::get('/logout', function() {
	Auth::logout();
	return Redirect::to('/login');
});

Route::post('/login', function() {
	if (Auth::attempt(array('email' => Input::get('email'), 'password' => Input::get('password'))) ) {

		if (Auth::user()->type == 'organization' && Auth::user()->status != 'approved') {

			die('You must be approved by an administrator to use an organization account.');
		}

		Auth::user()->touch();

		if (Session::has('redirect')) {
			$redirect = Session::get('redirect');
			Session::forget('redirect');
			return Redirect::to($redirect);

		}

		if (Request::ajax()) {
			return Response::json(array('success' => true));
		} else {
			return Redirect::to('/homefeed');
		}
		
	} else {
		if (Request::ajax()) {
			return Response::json(array('success' => false, 'error' => 'Invalid login credentials'));
		} else {
			Session::flash('error', 'Invalid login credentials');
			return Redirect::to('/login');
		}
		
	}
});
Route::get('/forums/{forum_id}/posts/{post_id}', 'HomeController@viewForum');
Route::get('/forums/{forum_id}/page/{page}', function($forum_id, $page) {
	$skip = $page*25;

	$view = Input::has('view')?Input::get('view'):'recent';
	if ($view == 'recent') {
		$posts = Post::where('forum_id','=', $forum_id)->skip($skip)->take(25)->orderBy('created_at', 'desc')->get();
	} else if ($view == 'popular') {
		$cutoff = date('Y-m-d H:i:s', time()-(10*24*60*60));
		$posts = Post::where('posts.forum_id','=', $forum_id)->select(DB::raw('posts.*, feed.object_id'))->join('feed', 'feed.object_id', '=', 'post_id')->where('feed.model', '=', 'Post')->where('posts.created_at', '>=', $cutoff)->skip($skip)->take(25)->orderBy('feed.popularity_score', 'desc')->get();
	} else if ($view == 'following') {
		$user_id = Auth::check()?Auth::user()->id:0;
		$posts = Post::where('forum_id','=', $forum_id)->join('followers', 'followers.user_id', '=', 'posts.author_id')->where('followers.follower_id', '=', $user_id)->skip($skip)->take(25)->orderBy('posts.created_at', 'desc')->get();
	}
	

	return View::make('posts', array('posts' => $posts, 'is_forum' => true));
});

Route::get('/search', function() {
	$terms = Input::get('q');
	$terms = str_replace(' ', '%', $terms);
	$terms = '%'.$terms.'%';
	$search = Search::where('search_terms', 'LIKE', $terms)->get();
	$json = array();
	foreach($search as $search_obj) {
		$record = $search_obj->record;
		$record->url = $record->getURL();
		$record->search_name = $record->getSearchName();
		array_push($json, $record);
	}
	echo json_encode($json);
});

Route::get('/organization/{username}',  'HomeController@showProfile');

Route::get('/user/{id}', function($id) {
	$user = User::find($id);
	return Redirect::to('/'.$user->type.'/'.$user->username);
});

Route::get('/individual/{username}', 'HomeController@showProfile');

Route::get('/company/{username}',  'HomeController@showProfile');

Route::get('/profile', function() {
	if (!Auth::check()) {
		Session::put('redirect', '/profile');
		return View::make('/login');
		die();
	}
	$user = Auth::user();
	return View::make('editProfile', array('user' => $user));
});

Route::post('/profile', function() {
	if (Auth::check() ) {
		$user = Auth::user();
		if ($user->type == 'individual') {
			$validator = Validator::make(Input::all(), array('first_name' => 'required', 'last_name' => 'required'));
		} else if ($user->type == 'organization'){
			$validator = Validator::make(Input::all(), array('contact_name' => 'required', 'contact_phone' => 'required'));
		}

		if ($validator->fails()) {
			Session::flash('errors', $validator->messages()->first());

			Input::flash();


			return Redirect::to('/profile');
			
		}


		foreach (Input::all() as $key => $value) {
			if ($key == '_token') {
				continue;
			}
			$user->$key = $value;
		}

		if (!empty($user->city) || !empty($user->state) || !empty($user->country)) {
			$string = urlencode($user->city.', '.$user->state.' '.$user->country);
			$url = 'https://maps.googleapis.com/maps/api/geocode/json?api_key='.Config::get('app.google_api_key').'&address='.$string;
			$response = file_get_contents($url);
			$json = json_decode($response);
			if (isset($json->results[0]->geometry)) {
				$location = $json->results[0]->geometry->location;
				
				$lat = $location->lat;
				$lng = $location->lng;

				$user->latitude = $lat;
				$user->longitude = $lng;
				$user->save();
			}
		}

		$user->save();
		return Redirect::to('/'.$user->type.'/'.$user->username);
	}
});

Route::post('/upload', function() {
	 $photo = $_FILES['photo'];

	 $allowed_types = array('image/jpeg', 'image/jpg');

	 if (!in_array($photo['type'], $allowed_types)) {
	 
	 	echo "<script>window.top.alert('Please upload a JPG file.');</script>";

	 	die();
	 }

	 $user_id = Auth::user()->id;

	 copy($photo['tmp_name'], '/home/oneegypt/public_html/1egypt/public/assets/user/'.$user_id.'.jpg');


	 echo '<script>window.top.location.reload();</script>';
});

Route::get('/user/{user_id}/follow', 'HomeController@follow');
Route::get('/user/{user_id}/unfollow', 'HomeController@unfollow');
Route::get('/user/{user_id}/followers', 'HomeController@followers');
Route::get('/user/{user_id}/following', 'HomeController@following');
Route::get('/user/{user_id}/message', 'MessageController@message');
Route::post('/user/{user_id}/message', 'MessageController@sendMessage');
Route::get('/messages/inbox', 'MessageController@showMessages');
Route::get('/messages/sent', 'MessageController@sentMessages');

Route::get('/threads', function() {
	if (Auth::check() ) {
		$user_id = Input::get('user_id');
		$messages = Message::where('recipient_id', '=', $user_id)->where('sender_id','=', Auth::user()->id)->
				orWhere(function($query) use ($user_id) {
					$query->where('recipient_id', '=', Auth::user()->id)->where('sender_id', '=', $user_id);
				})->orderBy('created_at', 'asc')->get();

		$mark_read = Message::where('sender_id', '=', Input::get('user_id'))->where('recipient_id','=', Auth::user()->id)->whereNull('seen_at')->get();

		$now = date('Y-m-d H:i:s');
		foreach($mark_read as $message) {
			$message->seen_at = $now;
			$message->save();
		}


		return View::make('threads', array('messages' => $messages));
	}
});

Route::get('/feed', function() {
	$user = Auth::user();
	$feed = FeedStory::where('user_id', '=', $user->id)->where('type' ,'!=' ,'')->orderBy('created_at', 'DESC')->get();
		
	foreach($feed as $story) {
		$object = $story;
		if ($story->model == 'Post') {
			$object = Post::find($story->object_id);
			if (!empty($object)) {
				echo View::make('stories/'.$story->type, array('object' => $object));
			}
		}
		
	}
});

Route::get('/user/{id}/feed', function($id) {
	$user = User::find($id);
	$feed = FeedStory::where('user_id', '=', $user->id)->where('type' ,'!=' ,'')->orderBy('created_at', 'DESC')->get();
		
	foreach($feed as $story) {
		$object = $story;
		if ($story->model == 'Post') {
			$object = Post::find($story->object_id);
			if (!empty($object)) {
				echo View::make('stories/'.$story->type, array('object' => $object, 'story_id' => $story->story_id));
			}
		} else if ($story->model == 'Donation') {
			$object = Donation::find($story->object_id);
			if (!empty($object)) {
				echo View::make('stories/'.$story->type, array('object' => $object, 'story_id' => $story->story_id));
			}
		}
		
	}
});

Route::get('/posts/{post_id}/comments/{skip}', function($post_id, $skip) {
	$post = Post::find($post_id);
	return View::make('comments', array('post' => $post, 'skip' => $skip));
});

Route::post('/posts/{post_id}/delete', function($post_id) {
	if (Auth::check()) {
		$post = Post::find($post_id);
		if (Auth::user()->id == $post->author_id) {
			$post->delete();
		}
	}
});

Route::get('/settings/category', function() {
	if (!Auth::check()) {
		return Redirect::to('/login');
	}
	$user = User::find(Auth::user()->id);
	$categories = DB::table('categories')->where('type' ,'=', 3)->orderBy('category_name', 'asc')->get();
	$tags = DB::table('organization_tags')->where('user_id', '=', $user->id)->orderBy('tag_name', 'asc')->select('tag_name')->get();

	return View::make('settings', array('user' => $user))->nest('child' ,'settings.category', array('tags' => $tags, 'categories' => $categories));
});

Route::get('/settings/map', function() {
	if (!Auth::check()) {
		return Redirect::to('/login');
	}
	$user = User::find(Auth::user()->id);
	$locations = DB::table('map_locations')->join('maps', 'maps.map_id', '=', 'map_locations.map_id')->where('maps.organization_id', '=', $user->id)->get();

	return View::make('settings', array('user' => $user))->nest('child' ,'settings.map', array('locations' => $locations));
});

Route::get('/settings/profile', function() {
	if (!Auth::check()) {
		return Redirect::to('/login');
	}
	$user = User::find(Auth::user()->id);

	return View::make('settings', array('user' => $user))->nest('child', 'settings.profile', array('user' => $user));
});

Route::get('/settings/billing', function() {
	if (!Auth::check()) {
		return Redirect::to('/login');
	}
	$user = User::find(Auth::user()->id);

	$stripe = '';
	if (!empty($user->stripe_customer_id)) {
		$url = 'https://'.Config::get('app.stripe_secret_key').'@api.stripe.com/v1/customers/'.$user->stripe_customer_id;
		$response = file_get_contents($url);
		$stripe = json_decode($response);
	}

	return View::make('settings', array('user' => $user))->nest('child', 'settings.billing', array('stripe' => $stripe, 'user' => $user));

});

Route::post('/settings/billing', function() {
	if (!Auth::check()) {
		return Redirect::to('/login');
	}
	$user = User::find(Auth::user()->id);

	if (!empty($user->stripe_customer_id)) {
		$url = 'https://'.Config::get('app.stripe_secret_key').'@api.stripe.com/v1/customers/'.$user->stripe_customer_id;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'source='.Input::get('token'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);

		$stripe = json_decode($response);
		if (!isset($stripe->id)) {
			Session::flash('error' , 'An error occurred and your credit card could not be saved.');
		} else {
			Session::flash('message', 'Card saved');
		}
	} else {
		$url = 'https://'.Config::get('app.stripe_secret_key').'@api.stripe.com/v1/customers';
		$ch = curl_init($url);
		$params = array(
				'description' => $user->display_name.' / '.$user->id,
				'email' => $user->email,
				'source' => Input::get('token')

			);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);

		$stripe = json_decode($response);
		if (isset($stripe->id)) {
			$user->stripe_customer_id = $stripe->id;
			$user->save();
			Session::flash('message', 'Card saved');
		} else {
			Session::flash('error' , 'An error occurred and your credit card could not be saved.');
		}
	}

	return Redirect::to('/settings/billing');
});

Route::get('/settings/bank', function() {
	if (!Auth::check()) {
		return Redirect::to('/login');
	}
 	$user = User::find(Auth::user()->id);
 	$recipient = '';

 	if (!empty($user->stripe_recipient_id)) {
 		$url = 'https://'.Config::get('app.stripe_secret_key').'@api.stripe.com/v1/recipients/'.$user->stripe_recipient_id;
 		$response = file_get_contents($url);
 		$recipient = json_decode($response);
 		//var_dump($recipient);
 	}
	
	return View::make('settings', array('user' => $user))->nest('child', 'settings.recipient', array('user' => $user, 'recipient' => $recipient));
});

Route::post('/settings/recipient', function() {
	if (!Auth::check()) {
		die();
	}
	$user = Auth::user();
	$user = User::find($user->id);
	$user->tax_id = Input::get('tax_id');
	$token = Input::get('token');
	$params = array('name' => $user->display_name, 'bank_account' => $token, 'tax_id' => $user->tax_id, 'type' => 'corporation');
	$url = 'https://'.Config::get('app.stripe_secret_key').'@api.stripe.com/v1/recipients';
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	$json = json_decode($response);
	if (isset($json->id)) {
		$user->stripe_recipient_id = $json->id;
		$user->save();

		/*$num_campaigns = DB::table('campaigns')->where('recipient_id','=', $user->id)->where('type','=', 'general')->count();

		if ($num_campaigns == 0) {
			$campaign = new Campaign;
			$campaign->user_id = $user->id;
			$campaign->recipient_id = $user->id;
			$campaign->type = 'general';
			$campaign->title = $user->display_name.' General Campaign';
			$campaign->save();
		}*/

		die(json_encode(array('status' => 200)) );
	}
	die(json_encode(array( 'status' => 500)) );
});

Route::get('/organization/{username}/campaign', function($username) {
	if (!Auth::check()) {
		Session::put('redirect', '/organization/'.$username.'/campaign');
		return Redirect::to('/login');

	}
	$user = User::where('username','=', $username)->where('type','=', 'organization')->first();
	return View::make('campaign', array('user' => $user));
});

Route::post('/organization/{username}/campaign/create', function($username) {
	$org = User::where('username', '=', $username)->first();
	$campaign = new Campaign;
	$campaign->recipient_id = $org->id;
	$campaign->user_id = Auth::user()->id;
	$campaign->title = Input::get('title');
	$campaign->description = Input::get('description');
	if (Input::get('goal') > 0) {
		$campaign->goal_in_cents = Input::get('goal')*100;
	}
	if (Input::has('user_match') && Input::get('user_match') == 1) {
		$campaign->user_match = true;
	}

	$campaign->save();


	$search = new Search;
	$search->object_id = $campaign->campaign_id;
	$search->model = 'Campaign';
	$search->search_terms = $campaign->title;

	$search->search_terms .= ' '.$org->display_name;
	$search->page = Config::get('app.support_page');
	$search->save();

	$response = Event::fire('feed.update', array($campaign));
	//echo json_encode($campaign);
	return Redirect::to('/organization/'.$username);

});

Route::get('/organization/{username}/campaign/{id}', function($username, $id) {
	if (!Auth::check()) {
		die(-1);
	}
	$campaign = Campaign::find($id);
	$org = User::where('username', '=', $username)->first();
	if ($campaign->recipient_id != $org->id) {
		die();
	}
	return View::make('donate', array('org' => $org, 'campaign' => $campaign));
});

Route::post('/organization/{username}/campaign/{id}', function($username, $id) {
	if (!Auth::check()) {
		die(json_encode(array('success' => 0, 'message' => 'Please sign in to make a contribution. Your session may have timed out.')));
	}
	$campaign = Campaign::find($id);
	$org = User::where('username', '=', $username)->first();
	if ($campaign->recipient_id != $org->id) {
		die();
	}

	$token = Input::get('token');
	$amount = Input::get('total');
	$donation_amount = Input::get('donation');

	$user = User::find(Auth::user()->id);


	if (!empty($token)) {
		if (empty($user->stripe_customer_id) ) {
			$url = 'https://'.Config::get('app.stripe_secret_key').'@api.stripe.com/v1/customers';
		} else {
			$url = 'https://'.Config::get('app.stripe_secret_key').'@api.stripe.com/v1/customers/'.$user->stripe_customer_id;

		}
		$ch = curl_init($url);
		$params = array(
				'description' => $user->id.' / '.$user->display_name,
				'email' => $user->email,
				'source' => $token,
				'metadata' => array(
						'user_id' => $user->id
						
					)
			);

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);


		$customer = json_decode($response);
		//echo $response;
		if (isset($customer->id)) {
			$user->stripe_customer_id = $customer->id;
			$card = $customer->sources->data[0]->id;
			$user->save();
			DB::commit();
		}
	
	}

	$url = 'https://'.Config::get('app.stripe_secret_key').'@api.stripe.com/v1/charges';
	$params = array(
			'amount' => $amount*100,
			'currency' => 'USD',
			'customer' => $user->stripe_customer_id,
			'metadata' => array(
					'recipient_id' => $org->stripe_recipient_id,
					'recipient_name' => $org->display_name
				)
		);

	//echo json_encode($params);


	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	curl_close($ch);
	//echo $response;
	$charge = json_decode($response);
	

	if (isset($charge->id)) {
		$donation = new Donation;
		$donation->user_id = $user->id;
		$donation->campaign_id = $campaign->campaign_id;
		$donation->amount = $donation_amount;
		$donation->tip_amount = ($amount-$donation_amount);
		$donation->net = 0;
		$donation->transaction_id = $charge->id;
		$donation->save();
		Event::fire('feed.update', array($donation));

		if (!Input::has('save_card') || Input::get('save_card') == 0) {
			//Delete card. We saved the card to keep the charges associated to the customer object.
			//If the user requested not to save card though, we delete it after charge is successful.
			if (isset($card)) {
				$url = 'https://'.Config::get('app.stripe_secret_key').'@api.stripe.com/v1/customers/'.$user->stripe_customer_id.'/sources/'.$card;
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_exec($ch);

			}
		}


		echo json_encode(array('success' => 1, 'message' => 'Thanks for your generous contribution!'));
		die();
	} else {
		echo json_encode(array('success' => 0, 'message' => 'An error occurred, and you were not charged.'));
	}


});

Route::get('/auth', function() {
	$uri = Input::get('uri');
	Session::put('redirect', $uri);
	return Redirect::to('/login');
});

Route::get('/campaign/{campaign_id}', function($campaign_id) {
	$campaign = Campaign::find($campaign_id);
	$total_donations = DB::table('donations')->where('campaign_id','=', $campaign_id)->sum('amount');

	$percentage = ($total_donations/($campaign->goal_in_cents/100))*100;
	$percentage = number_format($percentage, 1);

	if ($percentage > 100) {
		$percentage = 100;
	}


	return View::make('campaignView', array('campaign' => $campaign, 'total_donations' => $total_donations, 'percentage' => $percentage));
});

Route::get('/users',function() {
	$page = Input::has('p')?Input::get('p'):1;
	$page_size = 15;
	$offset = ($page-1)*$page_size;
	$query = Input::get('q');
	$mainQuery = User::whereRaw("MATCH(email,display_name,contact_name,employer,city,state,country) AGAINST(? IN BOOLEAN MODE)", array($query));

	if (Input::has('t')) {
		$mainQuery->where('type', '=', Input::get('t'));
	}


	/*$url = 'https://maps.googleapis.com/maps/api/geocode/json?api_key='.Config::get('app.google_api_key').'&address='.urlencode(Input::get('q'));
	$response = file_get_contents($url);
	$json = json_decode($response);

	if (isset($json->results[0]->geometry)) {
		$location = $json->results[0]->geometry->location;
		
		$lat = $location->lat;
		$lng = $location->lng;
		echo $lat;
		$mainQuery->orWhereExists(function($query ) use ($lat, $lng) {
			$radius = 20;
			$query->select(DB::raw('b.*, ( 6371 * acos( cos( radians('.$lat.') ) *  cos( radians( latitude ) )
                        * cos( radians( longitude ) - radians('.$lng.')
                        ) + sin( radians('.$lat.') ) *
                        sin( radians( latitude ) ) )
                      ) AS distance'))->from(DB::raw('users b'))->having("distance", "<", $radius);

		});
	}

	

	var_dump(DB::getQueryLog());

	echo json_encode($results);
	die();*/
	$results = $mainQuery->skip($offset)->take($page_size)->get();

	$total = $mainQuery->count();
	
	$total_pages = ceil($total/$page_size);

	
	return View::make('searchResults', array('results' => $results, 'total_results' => $total, 'total_pages' => $total_pages, 'page' => $page));
});

Route::get('/autoLogin/{id}', function($id) {
	if (Session::has('authKey')) {
		$user = User::find($id);
		//echo 'ok';
		if ($user->password == Session::get('authKey')) {
			Session::forget('authKey');
			$user = Auth::loginUsingId($id);
			Auth::login($user);
			return Redirect::to('/'.$user->type.'/'.$user->username);
		}
	}
	
});

Route::get('/admin/login', function() {
	return View::make('admin.login');
});

Route::get('/admin/logout', function() {
	Session::forget('admin');
	return View::make('admin.login');
});

Route::post('/admin/auth', function() {
	$password = Input::get('password');
	if ($password == Config::get('app.admin_password')) {
		$key = Hash::make($password);
		Session::put('admin', $key);
		return Redirect::to('/admin/');
	} else {
		Session::flash('error', 'Incorrect password');
		return Redirect::to('/admin/login');
	}
});

Route::get('/admin/', function() {
	return Redirect::to('/admin/organizations/status/pending');
});


Route::get('/admin/features', function() {
	$features = DB::table('features')->orderBy('updated_at', 'desc')->get();
	return View::make('admin.home')->nest('child', 'admin.features', array('features' => $features));
});

Route::get('/admin/features/new', function() {
	requireAdmin();
	return View::make('admin.home')->nest('child', 'admin.newFeature');
});

Route::get('/admin/features/edit/{id}', function($id) {
	requireAdmin();
	$feature = DB::table('features')->where('feature_id' ,'=', $id)->first();
	return View::make('admin.home')->nest('child', 'admin.newFeature', array('feature' => $feature));
});

Route::get('/admin/features/delete', function() {
	requireAdmin();
	$id = Input::get('id');
	DB::table('features')->where('feature_id' ,'=', $id)->delete();
	return Redirect::to('/admin/features');
});

Route::post('/admin/features/new', function() {

	if (!empty($_FILES['image']['tmp_name'])) {
		$tmp_name = $_FILES['image']['tmp_name'];
		$time = time();

		copy($tmp_name, '/home/oneegypt/public_html/1egypt/public/assets/sliders/'.$time.'-'.$_FILES['image']['name']);
	}

	if (Input::has('feature_id')) {
		$update = array(
				'title' => Input::get('title'),
				'link_url' => Input::get('url'),
				'action_txt' => Input::get('action'),
				'section' => Input::has('section')?Input::get('section'):'support',
				'updated_at' => date('Y-m-d H:i:s')
			);
		if (!empty($_FILES['image']['tmp_name'])) {
			$update['image_url'] =  '/assets/sliders/'.$time.'-'.$_FILES['image']['name'];
		}
		DB::table('features')->where('feature_id', Input::get('feature_id'))->update($update);
	} else {
		DB::table('features')->insert(array(
				'image_url' => '/assets/sliders/'.$time.'-'.$_FILES['image']['name'],
				'title' => Input::get('title'),
				'link_url' => Input::get('url'),
				'action_txt' => Input::get('action'),
				'section' => Input::has('section')?Input::get('section'):'support',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			));
	}
	return Redirect::to('/admin/features');

});

Route::get('/admin/organizations/status/{status}', function($status) {
	requireAdmin();
	$orgs = User::where('type', '=', 'organization')->where('status' ,'=', $status)->orderBy('created_at', 'desc')->get();
	return View::make('admin.home')->nest('child', 'admin.organizations', array('organizations' => $orgs));
});

Route::get('/admin/organizations/{id}', function($id) {
	requireAdmin();
	$org = User::find($id);

	return View::make('admin.home')->nest('child', 'admin.organization', array('org' => $org));
});

Route::post('/admin/organizations/{id}', 'AdminController@showOrg');

Route::post('/settings/category', function() {
	if (!Auth::check()) {
		die('Not logged in.');
	}
	$tags = Input::get('tags');
	$tags = explode(',', $tags);

	foreach($tags as &$tag) {
		$tag = urldecode( strtolower($tag) );
	}

	$tags = array_unique($tags);

	DB::table('organization_tags')->where('user_id', '=', Auth::user()->id)->delete();
	$string = '';
	foreach($tags as $tag) {
		DB::table('organization_tags')->insert(array(
				'tag_name' => strtolower($tag),
				'user_id' => Auth::user()->id
			));
		$string .= ($tag.' ');
	}

	$user = User::find(Auth::user()->id);
	$user->category_id = Input::get('category_id');




	$user->save();

	$search = Search::where('object_id','=', $user->id)->where('model', '=', 'User')->where('page', '=', 2)->first();
	if (empty($search)) {
		$search = new Search;
		$search->page = Config::get('app.support_page');
		$search->model = 'User';
		$search->object_id = $user->id;
		
	}

	$search->search_terms = $user->display_name.' '.$user->email.' '.$string.' '.$user->category->category_name;
	$search->save();

	//Update search fields

	/*$search = Search::where('object_id', '=', $user->id)->where('model', '=', 'User')->first();

	if (empty($search)) {
		$search = new Search;
		$search->object_id = $user->id;
		$search->model = 'User';
	}

	$search->search_terms = $user->display_name.' '.$user->email.' '.$user->username.' '.implode(' ', $tags);
	$search->save();*/

	return Redirect::to('/organization/'.$user->username);
});

Route::post('/settings/map', function() {
	if (!Auth::check()) {
		die('Not logged in.');
	}
	$locations = Input::get('locations');
	$locations = explode(',', $locations);

	foreach($locations as &$location) {
		$location = urldecode( strtolower($location) );
	}

	$locations = array_unique($locations);

	$map = DB::table('maps')->where('organization_id', '=', Auth::user()->id)->first();

	if (empty($map)) {
		$map = array(
				'organization_id' => Auth::user()->id,
				'url_parameters' => ''
			);
		$map_id = DB::table('maps')->insertGetId($map);
	} else {
		$map_id = $map->map_id;
	}


	DB::table('map_locations')->where('map_id', '=', $map_id)->delete();
	

	$parameters = '?markers=';

	foreach($locations as $location) {
		DB::table('map_locations')->insert(array(
				'term' => strtolower($location),
				'map_id' => $map_id
			));
		$parameters .= urlencode($location).'|';
	}

	DB::table('maps')->where('map_id' , '=', $map_id)->update(array('url_parameters' => $parameters) );

	return Redirect::to('/organization/'.Auth::user()->username);
});

Route::get('/processing', function() {
	echo '<p>Thank you for joining OneEgypt. After your organization\'s application is reviewed, we will notify you of approval status.</p>';
});

Route::get('/thread/{thread_id}', 'MessageController@thread');

Route::post('/thread/{thread_id}', 'MessageController@sendMessageThread');

Route::get('/dialogue', 'HomeController@dialogueHome');

Route::get('/forum/settings/{id}', 'ForumController@settings');
Route::post('/forum/settings/{id}', 'ForumController@saveSettings');
Route::get('/forum/{id}/follow', 'ForumController@followForum');
Route::get('/forum/{id}/unfollow', 'ForumController@unfollowForum');
Route::get('/forums/tagged/{tag}', 'ForumController@taggedForums');
Route::get('/work', 'HomeController@workHome');
Route::get('/listings/post', function() {
	if (Auth::check()) {

		$categories = DB::table('categories')->where('type', '=', 2)->orderBy('category_name', 'asc')->get();

		return View::make('postJob', array('categories' => $categories));
	} else {
		return Redirect::to('/auth?uri='.$_SERVER['REQUEST_URI']);
	}
});

Route::post('/listings/post', function() {

	if (Auth::check() ) {

		$validation = Validator::make(Input::all(), 
				array(
						'listing_title' => 'required',
						'listing_description' => 'required',
						'category_id' => 'required',
						'type' => 'required'
					)
			);
		if ($validation->fails()) {
			Session::flash('error', $validation->messages()->first());
			return Redirect::to('/listings/post')->withInput();
		}

		$listing = new Listing;
		$listing->listing_title = Input::get('listing_title');
		$listing->listing_body = Input::get('listing_description');
		
		$listing->creator_id = Auth::user()->id;
		$listing->type = Input::get('type');
		$listing->address = Input::get('address');
		$listing->city = Input::get('city');
		$listing->state = Input::get('state');
		$listing->country = Input::get('country');
		$listing->tags = Input::get('tags');

		$listing->action_url = Input::get('action_url');

		if (Input::has('city') || Input::has('state') || Input::has('country')) {
			$address = urlencode($listing->address.' '.$listing->city.', '.$listing->state.' '.$listing->country);
			$url = 'https://maps.googleapis.com/maps/api/geocode/json?api_key='.Config::get('app.google_api_key').'&address='.$address;
			$response = file_get_contents($url);
			$json = json_decode($response);
			if (isset($json->results[0]->geometry)) {
				$location = $json->results[0]->geometry->location;
				
				$listing->latitude = $location->lat;
				$listing->longitude = $location->lng;
			}
		}

		$listing->save();

		foreach( Input::get('category_id') as $category_id) {
			$listing->categories()->attach($category_id);
		}

		$slug = trim(strtolower($listing->listing_title));
		$slug = str_replace(' ', '-', $slug);
		return Redirect::to('/listings/'.$listing->listing_id.'/'.$slug);
	} else {
		return Redirect::to('/login');
	}
});

Route::get('/listings/{id}/{slug}', function($id) {
	$listing = Listing::find($id);
	$favorited = false;

	if (Auth::check()) {
		$bookmark_count = Bookmark::where('listing_id', '=', $listing->listing_id)->where('user_id', '=', Auth::user()->id)->count();
		if ($bookmark_count > 0) {
			$favorited = true;
		}
	}

	return View::make('listing', array('listing' => $listing, 'favorited' => $favorited));
});

Route::get('/forums/{forum_id}/new', 'ForumController@newThread');
Route::post('/forums/{forum_id}/new', 'ForumController@createNewThread');
Route::get('/admin/users', 'AdminController@users');

Route::get('/admin/users/{user_id}', 'AdminController@user');
Route::post('/admin/users/{user_id}', 'AdminController@savePrivileges');
Route::get('/admin/deletePermission/{privilege_id}', function($privilege_id) {
	requireAdmin();

	$privilege = Privilege::find($privilege_id);
	$user_id = $privilege->user_id;
	$privilege->delete();
	return Redirect::to('/admin/users/'.$user_id);
});

Route::get('/forumPosts/{forum_post_id}/delete', function($forum_post_id) {
	if (Auth::check()) {
		$post = ForumPost::withTrashed()->find($forum_post_id);

		$thread = ForumThread::find($post->forum_thread_id);
		$is_mod = DB::table('user_privileges')->where('privilege_key', '=', 'mod')->where('scope', '=', $thread->forum_id)->where('user_id', '=', Auth::user()->id)->count();
		if ($is_mod == 0) {
			$is_mod = DB::table('user_privileges')->where('privilege_key', '=', 'global_mod')->where('user_id', '=', Auth::user()->id)->count();
		}

		if ($is_mod > 0) {
			$post->delete();
		}
		return Redirect::to('/forums/'.$thread->forum_id.'/threads/'.$post->forum_thread_id.'/1');
	}

	
});

Route::get('/forumPosts/{forum_post_id}/restore', function($forum_post_id) {
	if (Auth::check()) {
		$post = ForumPost::withTrashed()->find($forum_post_id);

		$thread = ForumThread::find($post->forum_thread_id);

		
		$is_mod = DB::table('user_privileges')->where('privilege_key', '=', 'mod')->where('scope', '=', $thread->forum_id)->where('user_id', '=', Auth::user()->id)->count();
		if ($is_mod == 0) {
			$is_mod = DB::table('user_privileges')->where('privilege_key', '=', 'global_mod')->where('user_id', '=', Auth::user()->id)->count();
		}

		if ($is_mod > 0) {
			$post->restore();
		}
		return Redirect::to('/forums/'.$thread->forum_id.'/threads/'.$thread->forum_thread_id.'/1');
	}

	
});

Route::get('/forums/tagged/{tag}', function($tag) {
	
	$forums = Forum::join('forum_tags', 'forum_tags.forum_id', '=', 'forums.forum_id')->where('tag_name', 'LIKE', $tag)->get();
	return View::make('forumSearch', array('forums' => $forums, 'tag' => $tag));
});

Route::get('/admin/categories', 'AdminController@showCategories');
Route::get('/admin/forums', 'AdminController@showForums');
Route::get('/admin/forum/{id}', 'AdminController@editForum');
Route::post('/admin/forum/{id}', 'AdminController@saveForum');
Route::get('/admin/categories', 'AdminController@categories');
Route::get('/admin/category/new', 'AdminController@createCategory');
Route::get('/admin/category/edit/{id}', 'AdminController@editCategory');
Route::post('/admin/category/new', 'AdminController@saveCategory');
Route::get('/admin/category/delete/{id}', 'AdminController@deleteCategory');
Route::post('/admin/category/edit/{id}', 'AdminController@saveCategory');
Route::get('/feed.json', function() {

		$offset = Input::get('offset');

		$user = User::find(Auth::user()->id);

		$posts = Post::join('followers', 'followers.user_id', '=', 'posts.author_id')->where('posts.forum_id' , '=', 0)->where('followers.follower_id', '=', $user->id)->orderBy('posts.created_at', 'desc')->skip($offset)->select(DB::raw('posts.*'))->take(10)->get();
		$html = '';
		foreach($posts as $post) {
			$html .= View::make('stories.post_created', array('object' => $post));
		}
		return Response::JSON(array('html' => $html, 'offset' => $offset+sizeof($posts)));

});

Route::get('/notifications.json', function() {

	$forums = Forum::join('forum_followers', 'forum_followers.forum_id', '=', 'forums.forum_id')->where('forum_followers.id', '=', Auth::user()->id)->get();
	$json = array();
	foreach($forums as $forum) {
		//Get threads that have been updated since last view
		
		$threads = DB::table('forum_threads')->join('forums','forums.forum_id', '=', 'forum_threads.forum_id')->join('categories','forums.category' ,'=', 'categories.category_id')->where('forum_threads.forum_id', '=', $forum->forum_id)->join('forum_posts', 'forum_posts.forum_thread_id', '=', 'forum_threads.forum_thread_id')->join('thread_visits', 'thread_visits.thread_id', '=', 'forum_threads.forum_thread_id')->where('thread_visits.user_id', '=', Auth::user()->id)->where('forum_posts.created_at', '>=', DB::raw('thread_visits.updated_at'))->where('thread_visits.thread_id', '=', DB::raw('forum_posts.forum_thread_id'))->groupBy('forum_threads.forum_thread_id')->select(DB::raw('count(forum_posts.forum_post_id) as num_updates, forum_threads.*, categories.*'))->get();
		
		$queries = DB::getQueryLog();

		$json = array_merge($threads, $json);
	}

	$replies = DB::table('forum_posts')->
	join('forum_threads', 'forum_threads.forum_thread_id' ,'=','forum_posts.forum_thread_id')->
	join(DB::raw('forum_posts p2'), 'p2.forum_post_id', '=', 'forum_posts.reply_to')->
	where(DB::raw('p2.posted_by'),'=', Auth::user()->id)->
	select(DB::raw('count(forum_posts.forum_post_id) as num_replies, p2.*, forum_threads.*'))->
	groupBy('forum_posts.forum_post_id')->orderBy('p2.created_at', 'DESC')->
	join('thread_visits', 'thread_visits.thread_id', '=', 'forum_threads.forum_thread_id')->
	where('thread_visits.user_id', '=', Auth::user()->id)->where('forum_posts.created_at', '>=', DB::raw('thread_visits.updated_at'))->
	where('thread_visits.thread_id', '=', DB::raw('forum_posts.forum_thread_id'))->get();
	
	//var_dump($queries);
	$json = array_merge($replies, $json);
	return Response::JSON($json);



});

Route::get('/homefeed', function() {

	if (Auth::check()) {

		$posts = ForumPost::recent(Auth::user()->id)->get();
		$donations = Donation::recent(Auth::user()->id)->get();
		$bookmarks = Bookmark::where('user_id', '=', Auth::user()->id)->orderBy('created_at', 'desc')->get();
	//	echo json_encode($posts);

		return View::make('newsfeed2', array('forum_posts' => $posts, 'donations' => $donations, 'bookmarks' => $bookmarks));

	} else {
		return Redirect::to('/auth?uri='.$_SERVER["REQUEST_URI"]);
	}
	
});

Route::get('/newsfeed', function() {
	return View::make('newsfeed2');
});

Route::get('/self', function() {
	$follower = new Follower;
	$follower->follower_id = Auth::user()->id;
	$follower->user_id = Auth::user()->id;
	$follower->save();
});

Route::get('/backbone/posts', function() {
	$offset = 0;

	$user = User::find(Auth::user()->id);

	$posts = Post::with('myvote')->with('author')->with('comments')->with('comments.author')->with('comments.myvote')->with('comments.votes')->join('followers', 'followers.user_id', '=', 'posts.author_id')->where('posts.forum_id' , '=', 0)->where('followers.follower_id', '=', $user->id)->orderBy('posts.created_at', 'desc')->skip($offset)->select(DB::raw('posts.*'))->take(100)->get();
	foreach($posts as &$post) {
		if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$post->author->id.'.jpg') ) {
			$path = Image::path('/assets/user/'.$post->author->id.'.jpg', 'resizeCrop', 50, 50);

			$post->author->thumbnail = ''.$path;
		} else {
			$post->author->thumbnail = '';
		}

		$post->num_votes = $post->votes->sum('vote');
		if ($post->author->id == Auth::user()->id) {
			$post->corner_action = '<a href="#" class="float-corner delete-post"><i class="fa fa-remove"></i></a>';
		} else {
			$post->corner_action = '<a href="#" class="float-corner flag-post"><i class="fa fa-flag"></i></a>';;
		}

		if ($post->attachment == true ) {
			$post->attached_view = '<br/>'.View::make('partials.post_link', array('object' => $post));
		} else {
			$post->attached_view = '';
		}
		$post->up_class = '';
		$post->down_class = '';
		if (!empty($post->myvote) && $post->myvote->vote == 1) {
			$post->up_class = 'active';
			$post->down_class = 'inactive';
		} else if (!empty($post->myvote) && $post->myvote->vote == -1) {
			$post->up_class = 'inactive';
			$post->down_class = 'active';
		}

		foreach($post->comments as &$comment) {

			$comment->num_votes = $comment->votes->sum('vote');

			if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$comment->author->id.'.jpg') ) {
				$path = Image::path('/assets/user/'.$comment->author->id.'.jpg', 'resizeCrop', 50, 50);

				$comment->author->thumbnail = ''.$path;
			} else {
				$comment->author->thumbnail = '';
			}

			if ($comment->author->id == Auth::user()->id) {
				$comment->auth_user = true;
			} else {
				$comment->auth_user = false;
			}
		}

	}
	return Response::JSON($posts);
});

Route::get('/posts/{post_id}', function($post_id) {
	
	return View::make('singlePost', array('post_id' => $post_id));
});

Route::post('/backbone/comment', function() {

	if (Auth::check()) {
		$comment = new Comment;
		$comment->user_id = Auth::user()->id;
		$comment->content = Input::get('content');
		$comment->post_id = Input::get('post_id');
		$comment->save();

		if (Auth::user()->id != $comment->post->author_id) {
			$notification = new Notification;
			$notification->user_id = $comment->post->author_id;
			$notification->message = Auth::user()->display_name.' commented on your post';
			$notification->link = '/posts/'.$comment->post->post_id;
			$notification->save();
		}
		
	}
	return Response::JSON($comment);
});

Route::get('/backbone/comments/{post_id}', function($post_id) {
	$comments = Comment::with('myvote')->with('author')->where('post_id', '=', $post_id)->orderBy('created_at', 'asc')->get();
	foreach($comments as &$comment) {
		$comment->num_votes = $comment->votes->sum('vote');

		if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$comment->author->id.'.jpg') ) {
			$path = Image::path('/assets/user/'.$comment->author->id.'.jpg', 'resizeCrop', 50, 50);

			$comment->author->thumbnail = ''.$path;
		} else {
			$comment->author->thumbnail = '';
		}

		if ($comment->author->id == Auth::user()->id) {
			$comment->auth_user = true;
		} else {
			$comment->auth_user = false;
		}



	}
	return Response::JSON($comments);
});

Route::get('/backbone/post/{post_id}', function($post_id) {
	$post = Post::with('author')->with('comments')->with('comments.author')->with('comments.myvote')->with('comments.votes')->where('post_id','=', $post_id)->first();
	if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$post->author->id.'.jpg') ) {
		$path = Image::path('/assets/user/'.$post->author->id.'.jpg', 'resizeCrop', 50, 50);

		$post->author->thumbnail = ''.$path;
	} else {
		$post->author->thumbnail = '';
	}

	$post->num_votes = $post->votes->sum('vote');
	if ($post->author->id == Auth::user()->id) {
		$post->corner_action = '<a href="#" class="float-corner delete-post"><i class="fa fa-remove"></i></a>';
	} else {
		$post->corner_action = '<a href="#" class="float-corner flag-post"><i class="fa fa-flag"></i></a>';;
	}

	if ($post->attachment == true ) {
		$post->attached_view = '<br/>'.View::make('partials.post_link', array('object' => $post));
	} else {
		$post->attached_view = '';
	}

	$post->up_class = '';
	$post->down_class = '';
	if (!empty($post->myvote) && $post->myvote->vote == 1) {
		$post->up_class = 'active';
		$post->down_class = 'inactive';
	} else if (!empty($post->myvote) && $post->myvote->vote == -1) {
		$post->up_class = 'inactive';
		$post->down_class = 'active';
	}

	foreach($post->comments as &$comment) {
		$comment->num_votes = $comment->votes->sum('vote');

		if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$comment->author->id.'.jpg') ) {
			$path = Image::path('/assets/user/'.$comment->author->id.'.jpg', 'resizeCrop', 50, 50);

			$comment->author->thumbnail = ''.$path;
		} else {
			$comment->author->thumbnail = '';
		}

		if ($comment->author->id == Auth::user()->id) {
			$comment->auth_user = true;
		} else {
			$comment->auth_user = false;
		}
	}
	return Response::JSON($post);
});

Route::get('/backbone/comment/{comment_id}', function($comment_id) {
	$comment = Comment::find($comment_id);
	$comment->load('myvote');

	$comment->num_votes = $comment->votes->sum('vote');

	if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$comment->author->id.'.jpg') ) {
		$path = Image::path('/assets/user/'.$comment->author->id.'.jpg', 'resizeCrop', 50, 50);

		$comment->author->thumbnail = ''.$path;
	} else {
		$comment->author->thumbnail = '';
	}

	if ($comment->author->id == Auth::user()->id) {
		$comment->auth_user = true;
	} else {
		$comment->auth_user = false;
	}

	return Response::JSON($comment);
});

Route::post('/backbone/vote', function() {
	if (!Auth::check()) {
		die();
	}


	if (Input::has('post_id')) {
		$vote = Vote::where('post_id', '=', Input::get('post_id'))->where('user_id', '=', Auth::user()->id)->first();
		if (empty($vote)) {
			$vote = new Vote;
			$vote->post_id = Input::get('post_id');
			$vote->user_id = Auth::user()->id;
			$vote->vote = Input::get('vote');
			$vote->save();
		} else {
			$vote->vote += Input::get('vote');
			if ($vote->vote > 1) {
				$vote->vote = 1;
			} else if ($vote->vote < -1) {
				$vote->vote = -1;
			}
			$vote->save();
		}
	} else if (Input::has('comment_id')) {
		$vote = CommentVote::where('comment_id', '=', Input::get('comment_id'))->where('user_id', '=', Auth::user()->id)->first();
		if (empty($vote)) {
			$vote = new CommentVote;
			$vote->comment_id = Input::get('comment_id');
			$vote->user_id = Auth::user()->id;
			$vote->vote = Input::get('vote');
			$vote->save();
		} else {
			$vote->vote += Input::get('vote');
			if ($vote->vote > 1) {
				$vote->vote = 1;
			} else if ($vote->vote < -1) {
				$vote->vote = -1;
			}
			$vote->save();
		}
	}
});

Route::post('/backbone/delete_comment/{comment_id}', function($comment_id) {
	$comment = Comment::find($comment_id);
	if (Auth::check() && $comment->user_id == Auth::user()->id ) {
		$comment->delete();
	}
});

Route::get('/me/deletePhoto', function() {
	if (!Auth::check()) {
		die('You must be logged in to perform this action.');
	}
	$path = '/home/oneegypt/public_html/1egypt/public/assets/user/'.Auth::user()->id.'.jpg';
	if (file_exists($path) ) {
		shell_exec('rm '.$path);
	}
	return Redirect::to('/'.Auth::user()->type.'/'.Auth::user()->username);
});
	
Route::get('/tag/{tag}', function($tag) {
	$organizations = User::with('followers')->where('type','=', 'organization')->join('organization_tags', 'users.id', '=', 'organization_tags.user_id')->where('tag_name','=', $tag)->get();
	return View::make('tagged_organizations', array('organizations' => $organizations, 'tag' => $tag));
});

Route::get('/notifications2.json', function() {
	$user = User::find(Auth::user()->id);
	$notifications = Notification::where('user_id', '=', Auth::user()->id)->where('created_at', '>=', $user->notification_date)->orderBy('created_at', 'desc')->get();

	if (sizeof($notifications) == 0) {
		$notifications = Notification::where('user_id', '=', Auth::user()->id)->orderBy('created_at', 'desc')->take(5)->get();
	}

	$user->notification_date = date('Y-m-d H:i:s');
	$user->save();
	return Response::JSON($notifications);

});

Route::get('/openSearch', function() {

	$page = Input::get('page');

	$q = Input::get('q');

	$searchResults = Search::where('page', '=', $page)->whereRaw("MATCH(search_terms) AGAINST('$q' IN BOOLEAN MODE)")->take(15)->get();

	return View::make('search', array('page' => $page, 'results' => $searchResults));
});

Route::get('/favoriteListing/{listing_id}', function($listing_id) {
	if (Auth::check()) {
		$bookmark = Bookmark::where('user_id', '=', Auth::user()->id)->where('listing_id', '=', $listing_id)->first();
		if (empty($bookmark)) {
			$bookmark = new Bookmark;
			$bookmark->user_id = Auth::user()->id;
			$bookmark->listing_id = $listing_id;
			$bookmark->save();
		}

		

	}
	$listing = Listing::find($listing_id);
	$slug = studly_case($listing->listing_title);
	return Redirect::to('/listings/'.$listing_id.'/'.$slug);
});

Route::get('/unfavoriteListing/{listing_id}', function($listing_id) {
	if (Auth::check()) {
		$bookmark = Bookmark::where('user_id', '=', Auth::user()->id)->where('listing_id', '=', $listing_id)->first();
		if (!empty($bookmark)) {
			$bookmark->delete();
		}

		

	}
	$listing = Listing::find($listing_id);
	$slug = studly_case($listing->listing_title);
	return Redirect::to('/listings/'.$listing_id.'/'.$slug);
});

Route::get('/organization/{username}/work', function($username) {
	$user = User::where('username','=', $username)->first();

	return View::make('organizationWork', array('user' => $user));
});

Route::get('/beta', function() {
	return View::make('beta');
});

Route::post('/beta', function() {
	$validator = Validator::make(Input::all(), array(
			'occupation' => 'required',
			'full_name' => 'required',
			'email' => 'email|required',
			'interest' => 'required'
		));
	if ($validator->fails()  ){
		Session::flash('error', $validator->messages()->first());
		Input::flash();
		return Redirect::to('/beta');		
	}

	$invite = new Invitation;
	$invite->email = Input::get('email');
	$invite->full_name = Input::get('full_name');
	$invite->occupation = Input::get('occupation');
	$invite->interest = Input::get('interest');
	$invite->save();

	Session::flash('success', 'Thank you for your interest. We will send you the registration link after we review your application.');
	return Redirect::to('/beta');
});

Route::get('/admin/beta', function() {
	return Redirect::to('/admin/beta/pending');
});

Route::get('/admin/beta/{status}', function($status) {
	$invitations = Invitation::where('status', '=', $status)->get();
	return View::make('admin.home')->nest('child', 'admin.invitations', array('invites' => $invitations));
});

Route::get('/admin/beta/view/{id}', function($id) {
	$invitation = Invitation::find($id);
	return View::make('admin.home')->nest('child', 'admin.invitation', array('invite' => $invitation));
});

Route::post('/admin/beta/view/{id}', function($id) {
	$invitation = Invitation::find($id);
	$status = Input::get('status');
	$send_email = false;

	if ($invitation->status != $status && $status == 'approved') {
		$send_email = true;
	}

	$invitation->status = $status;
	$invitation->code = str_random(12);
	$invitation->save();

	$url = 'http://www.oneegypt.org/register/individual?code='.$invitation->code;

	if ($send_email == true) {
		Mail::send('emails.invitation', array('url' => $url, 'name' => $invitation->full_name), function($message) use ($invitation) {
			$message->to($invitation->email)->subject('Welcome to OneEgypt')->from('beta@oneegypt.org');
		});
	}

	return Redirect::to('/admin/beta/'.$status);
});