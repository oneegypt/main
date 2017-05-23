			<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="/assets/theme.css" >
		<script src='https://www.google.com/recaptcha/api.js'></script>
		<script src="https://js.stripe.com/v2/"></script>
		<script src="http://mindmup.github.io/bootstrap-wysiwyg/external/jquery.hotkeys.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
		<style>

		.job-listing {
			overflow:auto;
			padding:10px;
		}

		.job-content a.job-title {
			font-weight:700;
			font-size:16px;
		}

		.job-content {
			padding-left:10px;
			float:left;
			max-width:80%;
		}

		.job-content .employer {
			font-size:13px;
			color:#666;
		}
		
				#search-results-show {
			background-color:rgba(255,255,255,.99);
			position:absolute;
			box-shadow:0px 3px 1px rgba(0,0,0,.1);
			color:#888;
		}

			.close-btn {
				position:absolute;
				top:10px;
				right:10px;
				font-size:28px;
				font-weight:700;
				color:#fff;
				display:block;
				background-color:#000;
				border-radius:50%;
				height:36px;
				width:36px;
				line-height:36px;
				text-align:center;
			}

			.screen .container {
				max-width:500px;
				margin-top:80px;
				position:relative;
			}
			.fus-white-bg {
				min-height:100%;
			}
			.centered {
				text-align:center;
			}
			.tab-content {
				display:none;
			}

			.popular-org {
				padding:5px;
			}

			.tab-content.active {
				display:block;
			}
			.screen {
				background-color:rgba(0,0,0,.55);
				position:fixed;
				width:100%;
				display:none;
				top:0px;
				height:100%;
				left:0px;
				z-index:999999999999999;

			}

			.overlay {
				background-color:#fff;
				box-shadow:0px 1px 2px rgba(0,0,0,.22);
				padding:20px;
				width:50%;
				min-width:320px;

				margin:2.5% auto 0;
			}

			#error-message {
				color:#ff0028;
			}

			#success-message {
				color:#00ff28;
			}

			.attachment {
				background-size:cover;
				width:100%;
				height:320px;
				border:1px solid #e2e2e2;
				position:relative;
			}

			.attachment .metadata a {
				color:#fff;
			}

			.attachment .metadata {
				position:absolute;
				padding:6px 20px;
				bottom:0px;
				left:0px;
				width:100%;
				color:#f2f2f2;
				background-color:rgba(0,0,0,.5);
			}

			.field {
				padding:5px;
				color:#363636;
				background-color:#fff;
				border:1px solid #777;
				min-width:320px;
				border-radius:3px;
			}

			.num_votes {
				text-align:center;
			}

			.row {
				margin-bottom:5px;
			}

			.errors {
				color:#ff2222;
			}

			.btn-block {
				display:block;
			}

			.dark {
				background-color:#363636;
			}

			.full-width {
				width:100%;
				display:block;
			}

			.comment {
				display:block;
				border:1px solid #aaa;
				padding:6px;
				border-radius:4px;
				width:100%;
			}

			.post .meta {

				font-size:11px;
			}

			.post .content {
				position:relative;
				height:100%;
			}

			.vote-btn {
				display:block;
				text-align:center;
				margin:auto;

			}
			a.active {
				color:rgba(255, 173, 0, 1);
			}
			.post {
				padding:12px;
				border-bottom:1px solid #f2f2f2;
				margin-bottom:12px;
			}

			.highlighted {
				background-color:#fff;
				color:#50a2e1 !important;
			}

			.highlighted a {
				color:#50a2e1 !important;
			}

			.status-post {
				width:100%;
				margin:auto;
				padding:8px 16px;
			}
			footer {
				background:#51b5de;
				color:#fff;
			}

			footer a {
				color:#fff;
			}

			.bolded {
				font-weight:700;
			}


			div.line {
				display:inline-block;
				margin-bottom:5px;
				height:9px;
				border-bottom:1px solid #777;
				margin-right:5px;
				width:15px;
			}

			.progress {
				font-size:18px;
				font-weight:700;
				height:40px;

			}

			.progress-bar {
				font-size:18px;
				font-weight:700;
				line-height:40px;
			}

			a.inactive {
				color:#ccc;
			}

			.circle {
				border-radius:50%;
				width:45px;
				height:45px;

				background-size:cover;
				margin:auto;
				background-color:#e2e2e2;
			}
			.search-results {
				position:absolute;
				width:200px;

			}
			.result {
				background-color:rgba(255,255,255,.9);
				padding:5px;
				margin-bottom:3px;
			}

			.feed-story {
				border-bottom:1px solid #f2f2f2;
				padding:15px;
			}

			.message {
				color:#4ed23d;
				font-weight:700;
				border-bottom:1px solid #4ed23d;
				padding:5px;
				background-color:#e2fede;
			}

			.dropdown-toggle {
				border:0px solid transparent !important;
				color:#d2d2d2 !important;
			}

			#global-search {
				background-color:rgba(0,0,0,.15);
				border:0px;
				padding:5px;
				border-radius:3px;
				color:#fff;
				width:500px;
				margin-top:12px;
			}

			body, html {
				height:100%;
				min-height:900px;
			}

			.navbar-brand:hover {
				background-color:rgba(0,0,0,.1);
				
			}

			.navbar-brand {
				height:92px;
			}
		</style>
		<script>
		var timeout_id = 0;
			$(document).ready(function() {
				$('input#search').keyup(function() {
					clearTimeout(timeout_id);
					timeout_id = setTimeout(function() {
						var q = $('#search').val();
						if (q.length == '') {
							$('.search-results').remove();
							return false;
						}


						$.getJSON('/search', {q: q}, function(json) {
							$('.search-results').remove();
							var html = '<div class="search-results">';
							for(var i = 0; i < json.length; i++) {
								html += '<div class="result">';
								html += '<a href="'+json[i].url+'">'+json[i].search_name+'</a>';
								html += '</div>';
							}
							html += '</div>';
							$('body').append(html);
							var top = $('#search').offset().top+$('#search').height()+20;
							var left = $('#search').offset().left;
							var width = $('#search').width();
							$('.search-results').css({left: left, top: top, width:width});
						});
					}, 650);
				});
			});
		</script>