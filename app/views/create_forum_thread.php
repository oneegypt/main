<!DOCTYPE HTML>
<html>
	<head>
		<title>New Thread</title>

		<?= View::make('header') ?>
		<style>
			.tag-link {
				margin-right:15px;
				display:inline-block;
			}

			<?php
			if (!empty($forum->banner_image_url) ) {
				$bg_url = Image::path($forum->banner_image_url, 'resizeCrop', 1200, 520);
				?>
				body {
					background-image:url('<?=$bg_url?>');
					background-repeat:no-repeat;
					background-size:100%;
					background-position:center 90px;
					

				}
				<?php
			}

			?>
			body {
				background-color:#f2f2f2;
				}
				.top-of-forum {
					margin-top:90px;
				}
				.top-of-forum h3 {
					font-weight:700;
					text-shadow:0px 0px 20px rgba(0,0,0,.77);
				}
		</style>
	</head>
	<body>

		<?php
			echo View::make('navigation'); 
		?>
		<div class="container top-of-forum">
			<h3><?=$forum->title?></h3>
			
		</div>
		<div class="container fus-white-bg fus-section fus-feature fus-white-bg">
			<div class="row">
				<div class="col-md-8">
					<form action="" method="post">
						<?php
							if (Session::has('error')) {
								?>
								<div class="alert alert-danger">
									<i class="fa fa-remove"></i> <?=Session::get('error') ?>
								</div>

								<?php
							}

						?>
					<label>Topic</label><br/>
					<input type="text" name="topic"class="field" name="field" style="width:100%;" value="<?=Input::old('topic')?>"/><br/>
					<label>Post Body</label><br/>
					<textarea name="content_txt" class="field" style="width:100%;height:100px;"><?=Input::old('content_txt')?></textarea><br/><br/>
					<div class="g-recaptcha" data-sitekey="6LeZsQgTAAAAALrMDsqVvfJ0iXEFXhMNUWyJbgW3"></div><br/>
					<button class="btn btn-primary"><i class="fa fa-plus"></i> SUBMIT THREAD</button>
 					</form>
				</div>
			</div>
		</div>
	</body>
	</html>