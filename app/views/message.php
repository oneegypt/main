<!DOCTYPE HTML>
<html>
<head>
		<?=View::make('header') ?>
		<style>
			.photo {
				width:180px;
				height:180px;
				background-size:cover;
				background-position:center center;
				background-color:#f2f2f2;
				text-align:center;
			}

			.photo form {
				display:none;
				height:100%;
				width:100%;
			}

			.photo input {
				opacity:0;
				position:absolute;
				z-index:20;
				height:100%;
				width:100%;
				left:0;top:0;
			}
			.opacity {
				width:100%;
				height:100%;
				background-color:rgba(255,255,255,.85);
			}

			#threads {
				overflow-y:auto;
				overflow-x:hidden;
				padding:10px;
				height:320px;
				border:1px solid #e2e2e2;

			}

			#message_txt {
				width:100%;
			}
		</style>
</head>
<body class="">
<?php
	echo View::make('navigation');
?>
<style>
	.messaged {
		list-style:none;
		margin-left:0px;
		padding-left:0px;
	}
	.messaged li {
		margin-left:0px;
		padding-left:0px;
		display:block;
		font-weight:400;
		line-height:45px;
		min-height:55px;
		padding:4px;
	}

	.circle {
		display:inline-block;
	}

	.messaged li.active {
		background-color:rgba(255, 173, 0, 0.93);
		color:yellow;
	}

	.messaged li.active a {
		color:#fff;
		font-weight:700;
	}

	.bolded {
		font-weight:700 !important;
	}
</style>
<div class="container fus-white-bg fus-section fus-feature fus-white-bg">
	<div class="row">
		<div class="col-md-12">
			<h1>Messages</h1>
			<ul class="nav nav-pills">
				<?php
					$statuses = array('inbox', 'sent');

					foreach($statuses as $status) {
						$class= '';
	
						echo '<li class="'.$class.'"><a href="/messages/'.$status.'">'.ucwords($status).'</a></li>';
					}
				?>
			</ul>
			<br/>
			<form action="" method="post">
				<span><?=$thread->subject_txt?></span><br/>
				<textarea name="content" id="content" class="field" style="height:100px;width:100%;" placeholder="Write your reply here..."></textarea><br/>
				<button class="btn btn-primary"><i class="fa fa-reply"></i> REPLY</button><br/><Br/>
			</form>
		</div>
	</div>
	<div class="row">
		<?php
			foreach($messages as $message) {
				echo '<div class="col-md-12" style="border-bottom:1px solid #e2e2e2;padding-bottom:15px;margin-bottom:15px;">';
				echo 'From: <a href="/user/'.$message->sender_id.'">'.$message->sender->display_name.'</a><br/>';
				echo 'Sent on: '.date('F jS, Y h:i a', strtotime($message->created_at)).'<br/>';
				echo '<p>'.$message->content.'</p>';
				echo '</div>';
			}
		?>
	</div>
</div>
<script>




</script>
</body>
</html>