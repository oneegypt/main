<?php

class AdminController extends BaseController {
	public function showOrg($id) {
		requireAdmin();
		$user = User::find($id);
		if ($user->type != 'organization') {
			die('User is not organization type...');
		}

		$reason = Attribute::where('user_id', '=', $user->id)->where('attribute_key' ,'=', 'status_reason')->first();

		if (empty($reason)) {
			$reason = new Attribute;
			$reason->user_id = $user->id;
			$reason->attribute_key = 'status_reason';
		}

		$reason->attribute_value = Input::get('reason');
		$reason->save();

		if (Input::get('status') == 'approved') {
			$user->approved_at = date('Y-m-d H:i:s');
			$user->status = 'approved';

			Mail::send('emails.approval', ['reason' => $reason->attribute_value, 'user' => $user], function($message) use ($user) {
				$message->to($user->email);
				$message->subject('You have been approved to OneEgypt.org');
			});

			$campaign = Campaign::where('recipient_id', '=', $user->id)->where('general', '=', true)->first();
			if (empty($campaign)) {
				$campaign = new Campaign;
				$campaign->recipient_id = $user->id;
				$campaign->user_id = $user->id;
				$campaign->general = true;
				$campaign->title = $user->display_name.' General Fund';
				$campaign->save();
			}

		} else if (Input::get('status') == 'pending') {
			$user->approved_at = null;
			$user->status = 'pending';
		} else {
			$user->approved_at = null;
			$user->status = 'declined';
		}
		$user->save();
		return Redirect::to('/admin/organizations/status/'.$user->status);
	}

	public function users() {
		$users = array();
		if (Input::has('email') || Input::has('username')) {
			$users = User::where('email', '=', strtolower(Input::get('email')))->orWHere('username', 'LIKE', Input::get('username'))->get();
		}


		return View::make('admin.home')->nest('child', 'admin.users', array('users' => $users));
	}

	public function user($user_id) {
		$user = User::find($user_id);
		return View::make('admin.home')->nest('child', 'admin.user', array('user' => $user));
	}

	public function savePrivileges($user_id) {
		
		$count = DB::table('user_privileges')->where('user_id', '=', $user_id)->where('privilege_key', '=', Input::get('privilege_key'))->where('scope' , '=', Input::get('forum_id'))->count();

		if ($count == 0) {
			$priv = new Privilege;
			$priv->user_id = $user_id;
			$priv->privilege_key = Input::get('privilege_key');
			$priv->scope = Input::get('forum_id');
			$priv->save();
		} else {
			$priv = Privilege::where('user_id', '=', $user_id)->where('scope', '=', Input::get('forum_id'))->where('privilege_key', '=', Input::get('privilege_key'))->first();
		}

		$searchRecord = Search::where('model' ,'=', 'User')->where('object_id', '=', $user_id)->where('page', '=', Config::get('app.dialogue_page'))->first();

		if (empty($searchRecord)) {
			
			$searchRecord = new Search;
			$searchRecord->object_id = $user_id;
			$searchRecord->page = Config::get('app.dialogue_page');
			$searchRecord->model = 'User';
		}
		$user = User::find($user_id);
		$searchRecord->search_terms = $user->first_name.' '.$user->last_name.' '.$user->email.' '.$user->username;

		if ($priv->scope > 0) {
			$forum = Forum::find($priv->scope);
			$searchRecord->search_terms .= ' '.$forum->title;
		}

		$searchRecord->save();


		return Redirect::to('/admin/users/'.$user_id);
	}

	public function showCategories() {

	}

	public function showForums() {

		$forums = Forum::where('category' , '!=', 0)->orderBy('category')->get();


		return View::make('admin.home')->nest('child', 'admin.forums', array('forums' => $forums));
	}

	public function editForum($id) { 
		requireAdmin();
		if ($id > 0) {
			$forum = Forum::find($id);

			$tags = DB::table('forum_tags')->where('forum_id', $id)->get();
		} else {
			$forum = new Forum;
			$tags = array();
		}
		$categories = DB::table('categories')->where('type', '=', 1)->get();

		return View::make('admin.home')->nest('child', 'admin.forum', array('categories' => $categories, 'forum' => $forum, 'tags' => $tags));
	}

	public function saveForum($id) {
		requireAdmin();
		if ($id > 0) {
			$forum = Forum::find($id);
		} else {
			$forum = new Forum;
		}
		$forum->title = Input::get('title');
		$forum->description = Input::get('description_txt');
		$forum->category = Input::get('category');
		$forum->embeddable_content = Input::get('embeddable_content');
		$forum->published = Input::get('published');
		$forum->open = Input::get('open');
		$forum->guidelines = Input::get('guidelines');
		$forum->guiding_question_1 = Input::get('guiding_question_1');
		$forum->guiding_question_2 = Input::get('guiding_question_2');
		$forum->guiding_question_3 = Input::get('guiding_question_3');
		$forum->guiding_question_4 = Input::get('guiding_question_4');
		$forum->guiding_question_5 = Input::get('guiding_question_5');
		$forum->type = Input::get('type');

		$forum->save();

		if (!empty($_FILES['banner']['tmp_name'])) {
				
			if ($_FILES['banner']['type'] != 'image/jpeg') {
				Session::flash('Banner images must be JPG.');
				return Redirect::to('/admin/forums/'.$id);
			} else {
				$filename = time().'-'.$forum->forum_id.'.jpg';
				$path = '/home/oneegypt/public_html/1egypt/public/assets/forums/'.$filename;
				copy($_FILES['banner']['tmp_name'], $path);
				$url = '/assets/forums/'.$filename;
				$forum->banner_image_url = $url;
				$forum->save();
			}

		}


		$searchRecord = Search::where('object_id','=', $forum->forum_id)->where('model', '=', 'Forum')->first();
		if (empty($searchRecord)) {
			$searchRecord = new Search;
			$searchRecord->object_id =  $forum->forum_id;
			$searchRecord->model = 'Forum';
			$searchRecord->page = Config::get('app.dialogue_page');
		}

		$searchRecord->search_terms = $forum->title;
		

		$tags = explode(',', Input::get('tags'));
		DB::table('forum_tags')->where('forum_id', $id)->delete();
		foreach($tags as $tag) {
			$searchRecord->search_terms .= (' '.urldecode($tag));
			DB::table('forum_tags')->insert(array('forum_id' => $forum->forum_id, 'tag_name' => urldecode($tag) ));
		}

		$searchRecord->save();



		//$categories = DB::table('categories')->get();
		return Redirect::to('/admin/forums');
		
	}

	public function categories() {
		$categories = Category::where('type','=','1')->get();
		return View::make('admin.home')->nest('child', 'admin.categories', array('categories' => $categories));
	}

	public function createCategory() {

		return View::make('admin.home')->nest('child' ,'admin.category');
	}

	public function editCategory($id) {
		$category = Category::find($id);
		return View::make('admin.home')->nest('child' ,'admin.category', array('category' => $category) );
	}

	public function saveCategory($id=false) {
		requireAdmin();
		if (!$id) {
			$category = new Category;
		} else {
			$category = Category::find($id);
		}
		$category->type == 1;
		$category->category_name = Input::get('category_name');
		$category->description = Input::get('description');
		$category->icon = Input::get('icon');
		$category->save();

		return Redirect::to('/admin/categories');
	}

	public function deleteCategory($id) {
		$category = Category::find($id);
		if ($category->type == 1 && sizeof($category->forums) == 0) {
			$category->delete();
		} else {
			Session::flash('error', 'Cannot delete category with active forums attached to it.');
		}
		return Redirect::to('/admin/categories');
	}
}