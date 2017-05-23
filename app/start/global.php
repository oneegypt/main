<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/database/seeds',
  app_path().'/classes'

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useFiles(storage_path().'/logs/laravel.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(Exception $exception, $code)
{
	Log::error($exception);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';

Event::listen('feed.update', function($obj)
{
  	if (isset($obj->id) && ($obj->type == 'organization')) {
  		$feedStory = new FeedStory;
  		$feedStory->user_name = $obj->display_name;
  		$feedStory->user_id = $obj->id;
  		$feedStory->type = 'org_joined';
  		$feedStory->model = 'User';
  		$feedStory->object_id = $obj->id;
  		$feedStory->object_name = $obj->display_name;
  		$feedStory->story_url = '/organization/'.$obj->username;
  		$feedStory->save();
  	} else if (isset($obj->forum_id) && (!isset($obj->post_id))) {
  		$feedStory = new FeedStory;
  		$feedStory->user_name = $obj->creator->display_name;
  		$feedStory->user_id = $obj->creator->id;
  		$feedStory->type = 'forum_created';
  		$feedStory->model = 'Forum';
  		$feedStory->object_id = $obj->forum_id;
  		$feedStory->object_name = $obj->title;
  		$feedStory->story_url = '/forums/'.$obj->forum_id;
  		$feedStory->save();
  	} else if (isset($obj->post_id)) {
  		$feedStory = new FeedStory;
  		$feedStory->user_name = $obj->author->display_name;
  		$feedStory->user_id = $obj->author->id;
  		$feedStory->type = 'post_created';
  		$feedStory->model = 'Post';
  		$feedStory->object_id = $obj->post_id;
  		$feedStory->object_name = $obj->content;
  		$feedStory->story_url = '/forums/'.$obj->forum_id.'/posts/'.$obj->post_id;
  		$feedStory->save();
  	} else if (isset($obj->donation_id)) {
      $feedStory = new FeedStory;
      $feedStory->user_name = $obj->donater->display_name;
      $feedStory->user_id = $obj->donater->id;
      $feedStory->type = 'donation';
      $feedStory->model = 'Donation';
      $feedStory->object_id = $obj->donation_id;
      $feedStory->object_name = $obj->amount;
      $feedStory->story_url = '/organization/'.$obj->campaign->recipient->username.'/';
      $feedStory->save();
    } else if (isset($obj->campaign_id) && isset($obj->recipient_id)) {
      $feedStory = new FeedStory;
      $feedStory->user_name = $obj->creator->display_name;
      $feedStory->user_id = $obj->creator->id;
      $feedStory->type = 'campaign_created';
      $feedStory->model = 'Campaign';
      $feedStory->object_id = $obj->campaign_id;
      $feedStory->object_name = $obj->title;
      $feedStory->story_url = '/campaigns/'.$obj->campaign_id;
      $feedStory->save();
    }
});

Event::listen('popularity.touch', function($obj)
{
    $model = get_class($obj);
    //$key = $obj->primaryKey;
    //echo $key;
    $object_id = $obj->post_id;

    $comment_count = DB::table('comments')->where('post_id', '=', $object_id)->count();

    $vote_count = DB::table('votes')->where('post_id', '=', $object_id)->sum('vote');

    $popularity = $comment_count+$vote_count;

    $feedStory = FeedStory::where('model','=', $model)->where('object_id', '=', $object_id)->first();

    $feedStory->popularity_score = $popularity;
    $feedStory->save();


});

Validator::extend('hasProfilePic', function($attribute, $value, $parameters)
{
    if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$value.'.jpg') ) {
      return true;
    }
    return false;
});

function profileComplete($id) {
  $obj = array();

  $percent = 0;

  $user = User::find($id);

  if (empty($user)) {
    return null;
  }

  $required = array(
      'individual' => array(
          'email' => 'required|email',
          'first_name' => 'required',
          'last_name' => 'required',
          'description_txt' => 'required',
          'city' => 'required',
          'id' => 'hasProfilePic'
        ),
      'organization' => array(
          'status' => 'string:approved'
        ),
      'company' => array(
          'status' => 'string:approved'
        )
    );

  $errors = array('id.has_profile_pic' => 'Upload a photo',
      'description_txt.required' => 'Bio / description is missing',
      'required' => 'The :attribute field is missing',
      'string:approved' => 'Your organization or company must be approved by OneEgypt'

    );

  $user = json_encode($user);
  $user2 = json_decode($user);
  $user = array();
  foreach($user2 as $key => $value) {
    $user[$key] = $value;
  }
  $v = Validator::make($user, $required[$user2->type], $errors);


  if ($v->fails() == false) {
    $percent = 100;

  } else {
    $messages = $v->messages();
    $percent = (sizeof($required[$user2->type])-sizeof($v->messages()))/sizeof($required[$user2->type]);
    $percent *= 100;
    $percent = round($percent);
  }

  return array('percent' => $percent, 'messages' => $v->messages()->all());
}

function ago($updated_at) {
    $diff = time()-strtotime($updated_at);

    $when = '';
    $units = '';
    $tokens = array (
          31536000 => 'y',
          2592000 => 'mo',
          604800 => 'w',
          86400 => 'd',
          3600 => 'h',
          60 => 'min',
          1 => 'sec'
      );
      foreach ($tokens as $unit => $text) {
          if ($diff < $unit) {
            continue;
          } else {
            $numberOfUnits = floor($diff / $unit);
            return $numberOfUnits.''.$text;
            //break;
        }
      }

}

function requireAdmin() {
  if (Session::has('admin')) {
    $key = Session::get('admin');

    if (!Hash::check( Config::get('app.admin_password'), $key)) {

      Session::flash('error', 'You must login administratively to view this page or perform this action.');
      header('Location: http://www.oneegypt.org/admin/login');
      die();
    } 
  } else {
    Session::flash('error', 'You must login administratively to view this page or perform this action.');
    header('Location: http://www.oneegypt.org/admin/login');
    die();
  }
}

function userThumb($user_id) {
  if (file_exists('/home/oneegypt/public_html/1egypt/public/assets/user/'.$user_id.'.jpg') ) {
    $path = Image::path('/assets/user/'.$user_id.'.jpg', 'resizeCrop', 50, 50);
    return $path;
  } else {
    return '';
  }
}
