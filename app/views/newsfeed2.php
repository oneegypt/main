<!DOCYTPE HTML>
<html>
<head>
<?php
	echo View::make('header');

?>
<script src="http://documentcloud.github.com/underscore/underscore.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.2.1/backbone-min.js"></script>
<style>
	#newsfeed {
		color:#333;
		list-style:none;
		margin-left:0px;

	}

	.fa.active {
		color:orange;
	}

	.fa.inactive {
		color:#ccc;
	}

	.post-content2 {
		width:520px;
		float:left;
		margin-left:10px;
	}

	.post-content2 .attachment {
		height:180px;
	}

	body {

	}

	.status-post {
		overflow:auto;
		padding:10px;
		position:relative;
		margin-bottom:3px;
	}

	ul.comments-ul {
		list-style:none;
		margin-left:0px;
		background-color:#f2f2f2;

	}

	li .comment2 {
		margin-left:40px;
		position:relative;
		padding:10px;
		overflow:auto;
		border-bottom:1px solid #ccc;
		border-top:1px solid #fff;
	}

	.comment-container {
		background-color:#f2f2f2;
		padding:10px;
	}

	.float-corner {
		position:absolute;
		right:10px;
		top:10px;
		color:#aaa;
	}

	#newsfeed .circle {
		float:left;
	}

	.voting-section {
		width:40px;float:left;
		text-align:center;
	}
	#notifications {
		color:#363636;
	}

	#post_txt {
		width:100%;
		background-color:#f2f2f2;
		border:0px;
	}

	.status-update {
		border:1px solid #ccc;
		padding:20px;
		margin-bottom:10px;
	}

	.activity-item {
		overflow:auto;
		clear:both;
		padding:8px;
		border-bottom:1px solid #e2e2e2;
		font-size:13px;

	}

	.activity-item a {
		font-weight:700;
	}

	.activity-item small {
		display:inline;
		color:#999;
	}
</style>
</head>
<body>
	<?=View::make('navigation')?>
	<div class="container" style="color:#363636;">
		<div class="row">
			<div class="col-md-8">
				<h3 style="font-weight:bold;">News feed</h3>
				<div class="status-update">
					<textarea id="post_txt" name="post_txt" class="field" placeholder="What is on your mind?"></textarea><br/>
					<button class="btn btn-primary" id="post-btn"><i class="fa fa-arrow-right"></i> Post</button>
				</div>
				<ul id="newsfeed">

				</ul>
			</div>
			<div class="col-md-4">
				<h3>Recent Activity</h3>
				<ul class="nav nav-tabs">
				  <li role="presentation" class="active"><a href="#forum-activity">Forums</a></li>
				  <li role="presentation"><a href="#campaign-activity">Campaigns</a></li>
				  <li role="presentation"><a href="#bookmarks">Work</a></li>
				</ul>
				<div id="forum-activity" class="tab-panel" >
					<?php
						foreach($forum_posts as $post) {
							$action = '';
							if ($post->reply_to > 0) {
								$action = 'replied to <a href="/user/'.$post->repliedTo->author->id.'">'.$post->repliedTo->author->display_name.'\'s</a> post in <a href="/forums/'.$post->thread->forum_id.'/threads/'.$post->thread->forum_thread_id.'/1">'.$post->thread->topic.'</a>';
							} else {
								$action = 'posted in <a href="/forums/'.$post->thread->forum_id.'/threads/'.$post->thread->forum_thread_id.'/1">'.$post->thread->topic.'</a>';
							}
							?>
							<div class="activity-item">
								<div class="col-md-3">
									<div class="circle" style="background-image:url('<?=userThumb($post->posted_by)?>');">&nbsp;</div>
								</div>
								<a href="/user/<?=$post->author->id?>"><?=$post->author->display_name?></a> <?=$action?>
								<small><?=ago($post->updated_at)?></small>
							</div>

							<?php
						}

					?>
				</div>
				<div id="campaign-activity" style="display:none;">
					<?php
						foreach($donations as $donation) {
							
							?>
							<div class="activity-item">
								<div class="col-md-3">
									<div class="circle" style="background-image:url('<?=userThumb($donation->user_id)?>');">&nbsp;</div>
								</div>
								<a href="/user/<?=$donation->donater->id?>"><?=$donation->donater->display_name?></a> donated to <a href="/campaign/<?=$donation->campaign_id?>"><?=$donation->campaign->title?></a>.
								<small><?=ago($donation->updated_at)?></small>
							</div>

							<?php
						}

					?>
				</div>
				<div id="bookmarks" style="display:none;">
					<?php
						foreach($bookmarks as $bookmark) {
							?>
							<div class="activity-item">
								<div class="col-md-3">
									<i class="fa fa-star"></i>
								</div>
								<div class="col-md-9">
									<a href="/listings/<?=$bookmark->listing_id?>/<?=$bookmark->listing->listing_title?>"><?=$bookmark->listing->listing_title?></a>
								</div>
							</div>

							<?php
						}

					?>
				</div>

				<div id="notifications">
					<p>Keep up to date with forums you follow.</p>
				</div>
				<?php
					$data = profileComplete(Auth::user()->id);
					if ($data['percent'] < 100) {
						?>
						<h3 style="font-weight:700;">Complete your profile</h3>
						<p>You must complete your profile to access all of OneEgypt's features. Your profile is <strong><?=$data['percent']?>%</strong> done.</p>
						<ul>
						<?php
						foreach($data['messages'] as $message) {
							echo '<li>'.$message.'</li>';
						}
						?>
						</ul>
						<a href="/settings/profile" class="btn btn-primary"><i class="fa fa-arrow-right"></i> Complete Profile</a>
						<?php
					}
				?>
			</div>
		</div>
	</div>
	<script>
	$(document).ready(function() {


		var NewsFeed = Backbone.Model.extend({

		});

		var Comment = Backbone.Model.extend({
			url : '/backbone/comment'
		});

		var CommentCollection = Backbone.Collection.extend({
			url: '/backbone/comments',
			model: Comment,
 			parse: function(data) {
 				return data;
 			},

			offset: 3
		});

		var Post = Backbone.Model.extend({
			comments : CommentCollection,
			url: '/backbone/post/'
		});

		var PostCollection = Backbone.Collection.extend({
			 model: Post,
 			url: '/backbone/posts/',
 			parse: function(data) {
 				return data;
 			}
		});

		var CommentView = Backbone.View.extend({
			initialize: function() {
			    this.listenTo(this.model, 'sync', this.render);
			   
			  },
			tagName: 'li',
			render: function() {
				console.log(this.model.get('myvote'));
				var html = '<div class="comment2">';
				var profile_url = '/'+this.model.get('author').type+'/'+this.model.get('author').username+'/';
				var up_class = (this.model.get('myvote') && this.model.get('myvote').vote == 1)?'active':'inactive';
				var down_class = (this.model.get('myvote') && this.model.get('myvote').vote == -1)?'active':'inactive';
				html += '<div class="voting-section"><a href="#" class="vote-up-comment"><i class="fa fa-chevron-up '+up_class+'"></i></a><br/><span style="font-weight:700;">'+this.model.get('num_votes')+'</span><br/><a href="#" class="vote-down-comment"><i class="fa fa-chevron-down '+down_class+'"></i></a></div>';
				html += '<div class="circle" style="background-image:url(\''+this.model.get('author').thumbnail+'\');">&nbsp;</div>&nbsp;<a href="'+profile_url+'"><strong>'+this.model.get('author').display_name+'</strong></a>: '+this.model.get('content');
				html += '<br/>&nbsp;<small>'+timeSince(new Date(this.model.get('created_at')))+' ago</small>';
				if (this.model.get('auth_user') == true) {
					html += '<a href="#" class="delete-comment float-corner"><i class="fa fa-remove"></i></a>';
				} else {
					html += '<a href="#" class="flag-comment float-corner"><i class="fa fa-flag"></i></a>';
				}
				html += '</div>';
				console.log(html);
				this.$el.html(html);
				return this;
			},
			events: {
				'click .vote-up-comment' : 'vote_up',
				'click .vote-down-comment' : 'vote_down',
				'click .delete-comment' : 'delete_comment'
			},vote_up: function(e) {
				e.preventDefault();
				var comment_id = this.model.get('comment_id');
				this.model.url = '/backbone/comment/'+comment_id;
				var t = this;
				
				$.post('/backbone/vote', {comment_id: comment_id, vote: 1}, function() {
					t.model.fetch();

					
				});
			},
			vote_down: function(e) {
				e.preventDefault();
				var t = this;
				var comment_id = this.model.get('comment_id');
				this.model.url = '/backbone/comment/'+comment_id;
				
				$.post('/backbone/vote', {comment_id: comment_id, vote: -1}, function() {
					t.model.fetch();
					t.render();
				});
			},
			delete_comment: function(e) {
				e.preventDefault();
				var that = this.$el;
				var comment_id = this.model.get('comment_id');
				$.post('/backbone/delete_comment/'+comment_id+'', {} , function() {
					that.remove();
				});
				
			}
		});


		var CommentCollectionView = Backbone.View.extend({
			initialize: function() {
				this.listenTo(this.collection, 'change', this.reinit);
				this.listenTo(this.collection, 'sync', this.render);
				this.offset = 2;
			},
			render: function() {
				console.log('render');
				//console.log(this.collection);
				this.$el.empty();
				var i = 0;
				
				this.collection.each(function(model) {

					if (i >= (this.collection.length-this.offset)) {
						var comView = new CommentView({model: model});
						this.$el.append(comView.render().$el);
					}
					i++;
				}, this);

				if (this.offset < this.collection.length) {
					var num_comments = this.collection.length - this.offset;
					this.$el.prepend('<li><div class="comment2"><a href="#" class="load-previous">'+num_comments+' previous comments</a></div></li>');
				}
				this.$el.append('<li><div class="comment2"><input type="text" class="field comment_txt" placeholder="Enter a comment..."/></div></li>');
				
				return this;
			},
			events: {
				'keyup .comment_txt' : 'create_comment',
				'click .load-previous' : 'load_previous_comments'

			},
			load_previous_comments: function(e) {
				e.preventDefault();
				this.offset += 5;
				this.render();
			},
			reinit: function() {
				console.log('reinit');
				//this.collection = new CommentCollection();
				this.collection.url = '/backbone/comments/'+this.model.get('post_id');
				this.collection.fetch({reset:true});
			},
			create_comment: function(e) {
				if (e.keyCode == 13) {
					//alert(this.model.get('post_id'));
					var comment = {
						content: this.$('.comment_txt').val(),
						post_id: this.model.get('post_id')
					};
					
					this.collection.create(comment);

					this.$('.comment_txt').val('');
				}

				
			},
		});

		var comments = [];

		var PostView = Backbone.View.extend({
			initialize: function() {
			    this.listenTo(this.model, 'sync', this.render);
			   
			  },
			tagName: 'li',
			my_template: _.template(
				'<div class="status-post"><div class="voting-section"><a href="#" class="vote-up-post "><i class="fa fa-chevron-up <%=up_class%>"></i></a><br/><span style="font-weight:700;"><%=num_votes%></span><br/><a href="#" class="vote-down-post"><i class="fa fa-chevron-down <%=down_class%>"></i></a></div><div class="circle" style="background-image:url(\'<%=author.thumbnail%>\');">&nbsp;</div><div class="post-content2"><strong><a href="/<%=author.type%>/<%=author.username%>"><%=author.display_name%></a></strong>: <%=content%><%=attached_view%></div><%=corner_action%></div>'

				),
			events: {
				'click .vote-up-post' : 'vote_up',
				'click .vote-down-post' : 'vote_down'
			},

			vote_up: function(e) {
				e.preventDefault();
				var post_id = this.model.get('post_id');
				this.model.url = '/backbone/post/'+post_id;
				var t = this;
				
				$.post('/backbone/vote', {post_id: post_id, vote: 1}, function() {
					t.model.fetch();

					
				});
			},
			vote_down: function(e) {
				e.preventDefault();
				var t = this;
				var post_id = this.model.get('post_id');
				this.model.url = '/backbone/post/'+post_id;
				
				$.post('/backbone/vote', {post_id: post_id, vote: -1}, function() {
					t.model.fetch();
				//	t.render();
				});
			},
			render: function(){
				console.log('new render');
				var model = this.model;
		  		this.$el.html(this.my_template(this.model.toJSON()));
		  		var commentView = new CommentCollectionView({
		  			tagName: 'ul',
		  			className: 'comments-ul',
		  			collection: new CommentCollection(model.get('comments')),
		  			model: model
		  		});

		  		this.$el.append(commentView.render().$el );


		  		return this;
			}
		});

		var NewsFeedView = Backbone.View.extend({
			el: '#newsfeed',
			 initialize: function() {
			    this.listenTo(this.collection, 'sync', this.render);
			    this.collection.fetch();
			    this.render();
			  },

			  render: function() {
			  	var t = this;
			  	var p;
			  	this.$el.empty();
			  	this.collection.each(function(model){

			  		var item = new PostView({model: model});
      				this.$el.append(item.render().$el);
			  		
			  	}, this);
			   // console.log(this.$el.html());
			    return this;
			  }			
		});



		var posts = new PostCollection();
		var view = new NewsFeedView({collection:posts});
		});
function timeSince(date) {

    var seconds = Math.floor((new Date() - date) / 1000);

    var interval = Math.floor(seconds / 31536000);

    if (interval > 1) {
        return interval + " y";
    }
    interval = Math.floor(seconds / 2592000);
    if (interval > 1) {
        return interval + " mo";
    }
    interval = Math.floor(seconds / 86400);
    if (interval > 1) {
        return interval + " d";
    }
    interval = Math.floor(seconds / 3600);
    if (interval > 1) {
        return interval + " h";
    }
    interval = Math.floor(seconds / 60);
    if (interval > 1) {
        return interval + " min";
    }
    return Math.floor(seconds) + " sec";
}

	</script>


	<script>


			function forum_updates() {
				$.getJSON('/notifications.json', {} , function(json) {
					var html = '<table class="table">';
					for(var i = 0; i < json.length; i++) {
						var url = '/forums/'+json[i].forum_id+'/threads/'+json[i].forum_thread_id+'/1';
						if (json[i].num_replies) {
							html += '<tr><td>'+'<i title="Someoe replied to your post in this thread" class="fa fa-reply"></i></td><td><a href="'+url+'">'+json[i].topic+'</a></td><td>'+json[i].num_replies+' replies</td></tr>';
						} else {
							html += '<tr><td>'+'<i title="You are following this forum" class="fa '+json[i].icon+'"></i></td><td><a href="'+url+'">'+json[i].topic+'</a></td><td>'+json[i].num_updates+' new posts</td></tr>';
						}
					}
		 				
					html += '</table>';
					if (html.length > 0) {
						$('#notifications').html(html);
					}
				});
			}

			forum_updates();
				setInterval('forum_updates();', 15000);


			$('#post-btn').click(function() {

				$(this).find('i').removeClass('fa-arrow-right').addClass('fa-spin').addClass('fa-refresh');
				$.post('/posts/create', {post_txt: $('#post_txt').val(), forum_id: 0}, function(data) {
					$('#post-btn').find('i').addClass('fa-arrow-right').removeClass('fa-spin').removeClass('fa-refresh');
					$('#post_txt').val('');
					document.location.reload();

				});
				
			});	
	</script>

	<?php
		echo View::make('footer');
	?>
</body>
</html>