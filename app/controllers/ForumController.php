<?php

class ForumController extends BaseController {

	public function settings($id) { 
		if (!Auth::check()) {
			die('You must be logged in to edit a forum\'s settings.');
		}

		$forum = Forum::find($id);
		$is_mod = DB::table('user_privileges')->where(array('user_id' => Auth::user()->id, 'scope' => $forum->forum_id, 'privilege_key' => 'mod') )->count();

		if ( ($is_mod > 0) ||  Auth::user()->id == $forum->creator_id) {
			$tags = DB::table('forum_tags')->where('forum_id', $id)->get();
			$categories = DB::table('categories')->get();
			return View::make('forumSettings', array('categories' => $categories, 'forum' => $forum, 'tags' => $tags));
		} else {
			return Redirect::to('/login');
		}
	}

	public function saveSettings($id) {
		$forum = Forum::find($id);

		if (Auth::check() && Auth::user()->id == $forum->creator_id) {

			if (!empty($_FILES['banner']['tmp_name'])) {
					
				if ($_FILES['banner']['type'] != 'image/jpeg') {
					Session::flash('Banner images must be JPG.');
					return Redirect::to('/forum/settings/'.$id);
				} else {
					$filename = time().'-'.$forum->forum_id.'.jpg';
					$path = '/home/oneegypt/public_html/1egypt/public/assets/forums/'.$filename;
					copy($_FILES['banner']['tmp_name'], $path);
					$url = '/assets/forums/'.$filename;
					$forum->banner_image_url = $url;
				}

			}
			


			$tags = explode(',', Input::get('tags'));
			DB::table('forum_tags')->where('forum_id', $id)->delete();
			foreach($tags as $tag) {
				DB::table('forum_tags')->insert(array('forum_id' => $forum->forum_id, 'tag_name' => urldecode($tag) ));
			}

			$forum->description = Input::get('description_txt');
			$forum->category = Input::get('category');
			$forum->youtube_video_id = Input::get('youtube_video_id');

			$forum->save();


			//$categories = DB::table('categories')->get();
			return Redirect::to('/forums/'.$forum->forum_id);
		} else {
			return Redirect::to('/login');
		}
	}


	public function followForum($id) {
		if (!Auth::check()) {
			return Redirect::to('/auth?redirect_uri='.$_SERVER['REQUEST_URI']);
		}

		$forum = Forum::find($id);
		if (DB::table('forum_followers')->where('forum_id','=', $id)->where('id','=', Auth::user()->id)->count() == 0) {
			DB::table('forum_followers')->insert(array('forum_id' => $id, 'id' => Auth::user()->id, 'created_at' => date('Y-m-d H:i:s')));
		}
		return Redirect::to('/forums/'.$id);
	}

	public function unfollowForum($id) {
		if (!Auth::check()) {
			return Redirect::to('/auth?redirect_uri='.$_SERVER['REQUEST_URI']);
		}

		$forum = Forum::find($id);
		if (DB::table('forum_followers')->where('forum_id','=', $id)->where('id','=', Auth::user()->id)->count() > 0) {
			DB::table('forum_followers')->where('forum_id','=', $id)->where('id','=', Auth::user()->id)->delete();
		}
		return Redirect::to('/forums/'.$id);
	}

	public function taggedForums($tag) {
		$page = Input::has('page')?Input::get('page'):1;
		$page--;
		$take = 25;
		$skip = $take*$page;

		$forums = Forum::join('forum_tags', 'forum_tags.forum_id','=', 'forums.forum_id')->where('forum_tags.tag_name' ,'=', strtolower($tag))->get();

		return View::make('taggedForums', array('forums' => $forums, 'the_tag' => $tag));
		//echo json_encode($forums);

	}

	public function newThread($forum_id) {
		if (!Auth::check()) {
			return Redirect::to('/auth?uri='.$_SERVER['REQUEST_URI']);
		}
		$forum = Forum::find($forum_id);
		return View::make('create_forum_thread', array('forum' => $forum));
	}

	public function createNewThread($forum_id) {
		$validator = Validator::make(Input::all(), array(
				'topic' => 'required',
				'content_txt' => 'required'
			));

		if ($validator->fails()) {
			Session::flash('error', 'All fields required');
			Input::flash();
			return Redirect::to('/forums/'.$forum_id.'/new');
		}

		$robot = true;

		if (Input::has('g-recaptcha-response')) {
			$token = Input::get('g-recaptcha-response');
			$url = 'https://www.google.com/recaptcha/api/siteverify';

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, 'response='.$token.'&secret='.Config::get('app.recaptcha_secret'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			$captcha = json_decode($response);
			if (isset($captcha->success) && $captcha->success = true) {
				$robot = false;
			}
		}

		if ($robot == true) {
			Session::flash('error', 'Please verify that you are not a robot!');
			Input::flash();
			return Redirect::to('/forums/'.$forum_id.'/new');
		}

		$forum = Forum::find($forum_id);
		$thread = new ForumThread;
		$thread->forum_id = $forum_id;
		$thread->creator_user_id = Auth::user()->id;
		$thread->topic = Input::get('topic');
		$thread->save();

		$post = new ForumPost;
		$post->forum_thread_id = $thread->forum_thread_id;
		$post->content_txt = Input::get('content_txt');
		$post->posted_by = Auth::user()->id;
		$post->save();

		return Redirect::to('/forums/'.$forum_id);
	}

	public function showThread($forum_id, $thread_id, $page) {

		if (Auth::check()) {
			$results = DB::select('select * from thread_visits where thread_id = ? AND user_id = ? ', array($thread_id, Auth::user()->id));
			if (sizeof($results) > 0) {
				DB::update('UPDATE thread_visits SET updated_at = \''.date('Y-m-d H:i:s').'\' WHERE thread_id = ? AND user_id = ? ', array($thread_id, Auth::user()->id));
			} else {
				$now = date('Y-m-d H:i:s');
				DB::insert('INSERT INTO  thread_visits (thread_id, user_id, created_at, updated_at) VALUES (?,?,?,?) ', array($thread_id, Auth::user()->id, $now, $now));
			}
		}


		$page_size = 10;
		$thread = ForumThread::find($thread_id);
		$skip = ($page-1)*$page_size;
		$forum = Forum::find($forum_id);
		$posts = ForumPost::where('forum_thread_id', '=', $thread_id)->withTrashed()->orderBy('created_at', 'asc')->skip($skip)->take($page_size)->get();
		$num_posts = ForumPost::where('forum_thread_id', '=', $thread_id)->withTrashed()->count();
	//	echo $num_posts;
		$num_pages = ceil($num_posts/$page_size);

		return View::make('forum_thread', array('page' => $page, 'posts' => $posts, 'forum' => $forum, 'thread' => $thread, 'total_pages' => $num_pages) );
	}

	public function writeReply($forum_id, $thread_id, $page) {
		$post = new ForumPost;
		$post->forum_thread_id = $thread_id;
		$post->content_txt = Input::get('html');
		$post->posted_by = Auth::user()->id;

		if (Input::get('reply_to') > 0) {
			$post->reply_to = Input::get('reply_to');

			$original = ForumPost::find($post->reply_to);
			$notification = new Notification;
			$notification->user_id = $original->posted_by;
			$notification->message = Auth::user()->display_name.' replied to your post in a thread';
			$notification->link = '/forums/'.$forum_id.'/threads/'.$thread_id.'/1';
			$notification->save();

		}

		$thread = ForumThread::find($thread_id);
		$thread->touch();

		$post->save();
		return Redirect::to('/forums/'.$forum_id.'/threads/'.$thread_id.'/1');
	}
}

