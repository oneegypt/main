<?php

class MessageController extends BaseController {
	public function message($user_id) {
		if (!Auth::check()) {
			//return Response::json(array('success' => 0, 'message' => 'Your session may have timed out. Please login again.'));
		}
	



		//return View::make('message', array('users' => $users,'user' => $user));
	}

	public function sendMessageThread($thread_id) {
		$thread = Thread::find($thread_id);
		$message = new Message;
		$message->thread_id = $thread_id;
		$message->content = Input::get('content');
		$message->sender_id = Auth::user()->id;
		$message->save();

		return Redirect::to('/thread/'.$thread_id);

	}

	public function sendMessage($user_id) {
		if (!Auth::check()) {
			return Response::json(array('success' => 0, 'message' => 'Your session may have timed out. Please login again.') );
		}

		$validator = Validator::make(Input::all(), array(
				'subject' => 'required',
				'message' => 'required'

			));

		if ($validator->fails()) {
			$message = $validator->messages()->first();
			return Response::json(array('success' => 0, 'message' => $message) );
		}

		if (!Input::has('thread_id')) {
			$thread = new Thread;
			$thread->subject_txt = Input::get('subject');
			$thread->save();
			DB::table('thread_participants')->insert(array(
					'thread_id' => $thread->thread_id,
					'id' => Auth::user()->id,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				));
			DB::table('thread_participants')->insert(array(
					'thread_id' => $thread->thread_id,
					'id' => $user_id,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				));
		} else {
			$thread = Thread::find(Input::get('thread_id'));
		}

		$message = new Message;


		$message->sender_id = Auth::user()->id;
		$message->thread_id = $thread->thread_id;
		$message->content = Input::get('message');
		$message->save();

		return Response::JSON(array('success' => 1, 'message' => 'Message sent!'));
	
	}

	public function showMessages() {
		if (!Auth::check()) {
			return Redirect::to('/login');
		}

		$threads = DB::table('threads')->join('messages', 'messages.thread_id', '=', 'threads.thread_id')->join('thread_participants', 'thread_participants.thread_id', '=', 'threads.thread_id');
		$threads = $threads->where('thread_participants.id' , '=', Auth::user()->id)->where('messages.sender_id', '!=', Auth::user()->id)->groupBy('threads.thread_id')->select(DB::raw('threads.subject_txt, threads.thread_id, messages.*, MAX(messages.created_at) as max_date, MIN(messages.seen_at) as unread') )->orderBy('max_date' ,'desc')->get();

		return View::make('threads', array('threads' => $threads));
	}

	public function sentMessages() {
		if (!Auth::check()) {
			return Redirect::to('/login');
		}



		$threads = DB::table('threads')->join('messages', 'messages.thread_id', '=', 'threads.thread_id')->join('thread_participants', 'thread_participants.thread_id', '=', 'threads.thread_id');
		$threads = $threads->where('thread_participants.id' ,'!=', Auth::user()->id)->where('messages.sender_id', '=', Auth::user()->id)->groupBy('threads.thread_id')->select(DB::raw('threads.subject_txt, threads.thread_id, messages.*, MAX(messages.created_at) as max_date'))->orderBy('max_date' ,'desc')->get();

		return View::make('threads', array('threads' => $threads));
	}

	public function thread($thread_id) {
		if (!Auth::check()) {
			return Redirect::to('/login');
		}

		$participants = DB::table('thread_participants')->where('thread_id', '=', $thread_id)->where('id', '=', Auth::user()->id)->count();
		if ($participants == 0) {
			return Redirect::to('/support');
		}
		$thread = Thread::find($thread_id);
		$messages = Message::where('thread_id', '=', $thread->thread_id)->orderBy('created_at', 'desc')->get();

		DB::table('messages')->where('thread_id','=', $thread->thread_id)->where('sender_id','!=', Auth::user()->id)->update(array('seen_at' => date('Y-m-d H:i:s')));
		
		return View::make('message', array('thread' => $thread, 'messages' => $messages));


	}

}