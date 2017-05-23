<style>
	#notification-overlay {
		background-color:#fff;
		display:none;
		padding:16px;
		position:absolute;
		max-width:240px;
		box-shadow:0px 3px 3px rgba(0,0,0,.1);
	}
	#notification-overlay a {
		display:block;
		border-bottom:1px solid #eee;
	}

	#notification-overlay a.close {
		position:absolute;
		top:5px;
		right:5px;
	}

	#global-search {
		width:330px;
	}
</style>
<div class="navbar navbar-fixed-top fus-navbar-solid" role="navigation" style="height:92px;position:relative;">
	<div class="container">
		<div class="navbar-header" style="position:relative;">
			<a href="/homefeed" class="navbar-brand" style=""><img src="/assets/OneEgypt-white-100.png" style="margin-top:-5px;"/></a> <span style="position:absolute;left:100px;top:46px;font-weight:700;font-size:11px;display:inline-block;">BETA</span>

		</div>
		<div class="navbar-collapse collapse">

			
			<ul class="nav navbar-nav navbar-right">
				<br/><br/>
				<?php
					$unread = '';
					$notification_count = '';
					if (Auth::check()) {
						$unread = '&nbsp;'.DB::table('messages')->join('threads', 'threads.thread_id', '=', 'messages.thread_id')->join('thread_participants', 'thread_participants.thread_id' , '=', 'threads.thread_id')->where('thread_participants.id' , '=', Auth::user()->id)->where('sender_id', '!=', Auth::user()->id)->whereNull('seen_at')->groupBy('threads.thread_id')->count();
					
						$user = User::find(Auth::user()->id);
						$notification_count = '&nbsp;'.Notification::where('user_id', '=', $user->id)->where('created_at', '>' , $user->notification_date)->count();
					}
				?>
				<li><form action="/users" method="get"><input type="text" id="global-search" placeholder="Search users by name or location" name="q"/></form></li>
				<li><a href="#" id="notification-link"><i class="fa fa-exclamation-circle"></i><?=$notification_count?></a></li>
				<li><a href="/messages/inbox"><i class="fa fa-envelope"></i><?=$unread?></a></li>

				
				<li <?php if (isset($highlight) && ($highlight == 'dialogue') ) { echo 'class="highlighted"'; }?>><a href="/dialogue"><i class="fa fa-globe"></i> Dialogue</a></li>
				<li <?php if (isset($highlight) && ($highlight == 'support') ) { echo 'class="highlighted"'; }?>><a href="/support" ><i class="fa fa-heart"></i> Support</a></li>
				<li <?php if (isset($highlight) && ($highlight == 'work') ) { echo 'class="highlighted"'; }?>><a href="/work"><i class="fa fa-users"></i> Work</a></li>
				<?php
					
					if (!Auth::check()) {
  				?>
				<li><form action="/auth" method="get"><input type="hidden" name="uri" value="<?=$_SERVER['REQUEST_URI']?>"/><button type="submit" class="btn fus-btn-white" style="margin-top:10px;font-size:12px;">Login</button></form></li>
				<?php
					} else {
					?>
				<li>
				
					<a id="dropdownMenuNav" data-toggle="dropdown" aria-expanded="true" href="#">
				   	<i class="fa fa-user"></i></a>
				 
				  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenuNav">
				    <li role="presentation"><a role="menuitem" tabindex="-1" href="/<?=Auth::user()->type?>/<?=Auth::user()->username?>/">Profile</a></li>
				    <li role="presentation"><a role="menuitem" tabindex="-1" href="/settings/profile">Settings</a></li>
				     <li role="presentation"><a role="menuitem" tabindex="-1" href="/logout">Log out</a></li>
				</ul>


				

				</li>	
					<?php	
					}
				?>
			</ul>
			
		</div>
	</div>
	<div id="notification-overlay"></div>
	<script>
		$('#notification-link').click(function(e) {
			e.preventDefault();
			var t = this;
			$.getJSON('/notifications2.json', {} , function(json) {
				var html = '';
				for(var i = 0; i < json.length; i++) {
					var n = json[i];
					html += '<a href="'+n.link+'">'+n.message+'</a><br/>';
				}
				$('#notification-overlay').html(html);
				
				var top = $(t).offset().top+$(t).height()+30;
				
				var left = $(t).offset().left;
				$('#notification-overlay').css({top: top, left: left});
				$('#notification-overlay').show();
				$('#notification-overlay').append('<a href="#" class="close">&times</a>');
				$('#notification-overlay .close').click(function(e){
					e.preventDefault();
					$(this).parent().hide();
				});
			});
		});
	</script>
</div>