<?php

class RegistrationController extends BaseController {

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

	public function register()
	{

		$validator = Validator::make(
				Input::all(),
				array('email' => 'required|unique:users|email', 'password' => 'required|min:8|confirmed', 'username' => 'required|alpha_num|unique:users')
			);

		//echo (int)$validator->failed();

		if ($validator->fails()) {
			echo json_encode(array('errors' => $validator->messages()->all())  );
			die();
		}


		$rules = array(
				'organization' => array(
						'contact_name' => 'required',
						'contact_phone' => 'required',
						'tax_id' => 'required',
						'organization_name' => 'required'
					),
				'individual' => array(
						'first_name' => 'required',
						'last_name' => 'required'
					),
				'company' => array(
						'contact_name' => 'required',
						'organization_name' => 'required'
					)

			);

		$validator2 = Validator::make(Input::all(), $rules[Input::get('type')]);

		if ($validator2->fails()) {
			echo json_encode(array('errors' => $validator2->messages()->all())  );
			die();
		}

		switch(Input::get('type')) {
			case 'organization' :
				$display_name = Input::get('organization_name');
				break;
			case 'individual' :
				$display_name = Input::get('first_name').' '.Input::get('last_name');
				break;
			case 'company' :
				$display_name = Input::get('organization_name');
				break;
			default:
				//If someone tampers with request I guess and has no type. Break off here.
				die();
				break;
		}


		$user = new User;
		$user->email = Input::get('email');
		$password = Input::get('password');
		$user->password = Hash::make($password);
		$user->username = Input::get('username');
		$user->display_name = $display_name;
		$user->type = Input::get('type');
		if ($user->type == 'organization') {
			$user->status = 'pending';
		}


		

		$response = Event::fire('feed.update', array($user));


		foreach($rules[$user->type] as $key => $value) {
			$user->$key = Input::get($key);
		}

		if (Input::has('token') ) {
			$user->access_token = Input::get('token');
			$user->facebook_uid = Input::get('facebook_uid');
			if (!empty($user->access_token)) {
				$url = 'https://graph.facebook.com/me?access_token='.$user->access_token;
				//{"id":"10101637819259034","bio":"o hello\r\n\r\ni have a dog","education":[{"school":{"id":"104336279602321","name":"Archbishop Molloy High School"},"type":"High School"},{"concentration":[{"id":"108406612523064","name":"Information Systems"},{"id":"106335129403847","name":"Business Management"}],"school":{"id":"104053009629736","name":"Stony Brook University"},"type":"College"}],"email":"dave.luke\u0040gmail.com","first_name":"Dave","gender":"male","last_name":"Luke","link":"https:\/\/www.facebook.com\/app_scoped_user_id\/10101637819259034\/","location":{"id":"110521052305522","name":"Queens, New York"},"locale":"en_US","name":"Dave Luke","relationship_status":"Single","timezone":-4,"updated_time":"2015-06-05T07:13:50+0000","verified":true,"work":[{"description":"had to put on pants","end_date":"2009-05-01","employer":{"id":"101894826563370","name":"Sotheby's"},"location":{"id":"108424279189115","name":"New York, New York"},"position":{"id":"149729891707346","name":"Applications Engineer"},"start_date":"2006-06-01"}]}
				$response = file_get_contents($url);
				//echo $response;
				$facebook = json_decode($response);
				if (isset($facebook->id)) {
					$user->description_txt = isset($facebook->bio)?$facebook->bio:null;
					if (isset($facebook->location->id)) {
						$url = 'https://graph.facebook.com/'.$facebook->location->id.'?access_token='.$user->access_token;
						$response = file_get_contents($url);
						
						$location = json_decode($response);
						if ($location->category == 'City') {
							$location_parts = explode(', ',$location->name);
							if (isset($location_parts[0])) {
								$user->city = $location_parts[0];
							}
							if (isset($location_parts[1])) {
								$user->state = $location_parts[1];
							}
						}
					}
					if (isset($facebook->work[0]) ) {
						if (isset($facebook->work[0]->employer->name)) {
							$user->employer = $facebook->work[0]->employer->name;
						}
						if (isset($facebook->work[0]->position->name)) {
							$user->title = $facebook->work[0]->position->name;
						}
						
						
					}


					$url = 'https://graph.facebook.com/me/picture?width=350&height=350&access_token='.$user->access_token;
					$pictureData = file_get_contents($url);



				}
			}
			//die();
		}

		$user->save();

		$follower = new Follower;
		$follower->follower_id = $user->id;
		$follower->user_id = $user->id;
		$follower->save();


		if (isset($pictureData) && !empty($pictureData)) {
			file_put_contents('/home/oneegypt/public_html/1egypt/public/assets/user/'.$user->id.'.jpg', $pictureData);
		}

		if ($user->type == 'individual') {
			$search_record = new Search;
			$search_record->object_id = $user->id;
			$search_record->search_terms = $user->display_name.' '.$user->username.' '.$user->email;
			$search_record->model = 'User';
			$search_record->page = 0;
			$search_record->save();
		}
		Session::put('authKey', $user->password);

		

		if ($user->type == 'organization') {
			echo json_encode(array('success' => 1, 'url' => 'http://www.oneegypt.org/processing'));
		} else {
			echo json_encode(array('success' => 1, 'url' => 'http://www.oneegypt.org/autoLogin/'.$user->id));
		}

	}



}
