<div id="login-screen" class="screen" style="background-color:rgba(0,0,0,.85);">
	<div class="container">
		<h3>Login to OneEgypt</h3>

		<label>E-mail</label><br/>
		<input type="email" name="email" class="field" placeholder="you@email.com"/><br/>
		<label>Password</label><br/>
		<input type="password" name="password" class="field" /><br/><br/>
		<button class="btn btn-primary" id="sign-in" type="button">Sign in</button>
		<br/><br/><span style="font-weight:400;">Not signed up yet? <a href="/register">Join One Egypt here</a>.</span>
		<a href="#" class="close-btn">&times;</a>
	</div>
</div>
<script>
	
	function doLogin1() {
		
		$.post('http://www.oneegypt.org/login', {email: $('input[name=email]').val(), password: $('input[name=password]').val()}, function(json) {
			if (json.success) {
				logged_in = true;
				reload = true;
				$('#login-screen').fadeOut(100);
				if (action) {
					$(action).click();
				}
			} else {
				alert(json.error);
			}
		},'json');
	}
	$('#sign-in').click(doLogin1);

	var logged_in = false;
	var action = false;
	var reload = false;

	<?php
		if (Auth::check()) {
			echo 'logged_in = true;';
		}
	?>

	$.fn.vote = function() {
			if ($(this).data('init')) {
				return false;
			}

			$(this).data('init', true);
			$(this).click(function(e) {
				e.preventDefault();

				if (!logged_in) {
					$('#login-screen').fadeIn(250);
					action = $(this);
					return false;
				}

				var increment = 1;
				if ($(this).hasClass('down')) {
					increment = -1;
				}
				$(this).siblings().removeClass('inactive');
				$(this).removeClass('inactive');
				var post_id = $(this).attr('href');
				post_id = post_id.substr(1);
				var t = $(this);
				$.post('/posts/'+post_id+'/vote', {vote: increment}, function(data) {
					$(t).siblings('.num_votes').html(data.total);
					if (data.vote == 1) {
						$(t).parent().find('.vote-btn.up').addClass('active').removeClass('inactive');
						$(t).parent().find('.vote-btn.down').addClass('inactive').removeClass('active');
					} else if (data.vote < 0) {
						$(t).parent().find('.vote-btn.down').addClass('active').removeClass('inactive');
						$(t).parent().find('.vote-btn.up').addClass('inactive').removeClass('active');
					} else {
						$(t).parent().find('.vote-btn.up').removeClass('active').removeClass('inactive');
						$(t).parent().find('.vote-btn.down').removeClass('inactive').removeClass('active');
					}
				},'json');
			});
	}

	$('.vote-btn').vote();

	$.fn.commentForm = function() {
		return $(this).each(function() {

			if ($(this).onsubmit == null && !$(this).data('init')) {
				$(this).data('init', true);

				$(this).submit(function() {

					

					var url = $(this).attr('action');
					var content = $(this).find('.comment').val();
					$(this).find('.comment').val('');
					var output_id = $(this).find('input[name=output_id]').val();
					$.post(url, {content: content}, function(data) {
						$(output_id).html(data);
					});	
					return false;
				});
			}
		});
	}

	$('.comment-form').commentForm();

	
	$.fn.commentLink = function() {
		$(this).click(function(e) {



			$(this).find('i').removeClass('fa-arrow-up').addClass('fa-spin').addClass('fa-refresh');
			e.preventDefault();
			var skip = $(this).data('skip');
			var link = $(this);
			var post_id = $(this).data('post-id');
			$.get('/posts/'+post_id+'/comments/'+skip, {}, function(html) {
				$(link).parent().parent().after(html);
				$(link).parent().parent().siblings().find('.prev-comments').commentLink();
				$(link).parent().parent().remove();
			} );
		});
	};

	$('.prev-comments').each(function() {
		$(this).commentLink();
	});

	$('.prevent-default a').click(function(e) {
		e.preventDefault();

	});

	function reinit_feed() {
			$('.comment-form').commentForm();

			$('.prev-comments').each(function() {
				$(this).commentLink();
			});

			$('.prevent-default a').click(function(e) {
				e.preventDefault();

			});

			$('.vote-btn').vote();
	}

	function delete_post(id) {
		$.post('/posts/'+id+'/delete', {}, function() {
			$('#post-'+id).remove();
		});
	}

	
	
	$('.give').click(function(e) {
		e.preventDefault();

		if (!logged_in) {
			action = $(this);
			$('#login-screen').show();
			return false;
		}

		var href = $(this).attr('href');
		$.get(href, {}, function(html) {
			$('#overlay').html(html);
			calculate();
			$('#overlay').parent().fadeIn(280);
			$('#overlay').find('input[name=amount]').change(function() {
				calculate();
			});
			$('#add_tip').change(calculate);


			$('#complete-donation').click(function() {

				if ($('#expiration').length > 0) {

					var expiration = $('#expiration').val();
					var parts = expiration.split('/');
					if (parts.length == 2) {
						var month = parts[0];
						var year = parts[1];
					} else {
						$('#donation-message').css('color','red').html('Invalid expiration date format. Please use the format MM/YYYY');
						return false;
					}
					Stripe.card.createToken({
						number: $('#cc').val(),
						exp_month : month,
						exp_year: year,
						cvc : $('#cvc').val()
					}, donationHandler);
				} else {
					saved_card();
				}
			});


		});
	});
	$('.nav-tabs li a').click(function(e) {
		e.preventDefault();
		$(this).parent().siblings().removeClass('active');
		$(this).parent().addClass('active');
		
		$(this).parent().siblings().each(function() {
			$($(this).find('a').attr('href')).hide();
		});
		var href = $(this).attr('href');
		$(href).show();
		
	});


	function calculate() {
		var amount = $('input[name=amount]').val();

		if (parseFloat(amount)) {
			var amount = parseFloat(amount);
			var tip = 0;
			if ($('#add_tip').prop('checked')) {
				var tip = amount*.1;
			}
			
			var total = amount+tip;
			$('.donation-amount').html('$'+amount.toFixed(2));
			$('.tip-amount').html('$'+tip.toFixed(2));
			var totalStr = total.toFixed(2);
			$('.total-amount').html('$'+totalStr);
			return total;
		}
	}



		Stripe.setPublishableKey('<?=Config::get('app.stripe_publishable_key')?>');

		$(function () {
			  $('[data-toggle="tooltip"]').tooltip()
		});

		$('.close-btn').click(function(e) {
			e.preventDefault();
			$('.screen').fadeOut(200);
		});

		function setCookie(cname, cvalue, exdays) {
		    var d = new Date();
		    d.setTime(d.getTime() + (exdays*24*60*60*1000));
		    var expires = "expires="+d.toUTCString();
		    document.cookie = cname + "=" + cvalue + "; " + expires;
		}

		function getCookie(cname) {
			    var name = cname + "=";
			    var ca = document.cookie.split(';');
			    for(var i=0; i<ca.length; i++) {
			        var c = ca[i];
			        while (c.charAt(0)==' ') c = c.substring(1);
			        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
			    }
			    return false;
			}
</script>

<footer class="fus-section"> 
  <div class="container">
    <ul class="list-inline">
      <li><a href="http://info.oneegypt.org/policy">User Policies</a></li>
      <li><a href="http://info.oneegypt.org/terms-of-service/">Terms of Service</a></li>
      	<li><a href="http://info.oneegypt.org/privacy-policy/">Privacy Policy</a></li>
      <li><a href="http://info.oneegypt.org/faq/">FAQ</a></li>
      <li><a href="http://info.oneegypt.org/non-profits">Non Profits</a></li>
      <li><a href="http://info.oneegypt.org/dialogue">About</a></li>
      <li><a href="http://info.oneegypt.org/contact">Contact</a></li>
      <li><a href="http://www.oneegypt.org/login">Login</a></li>
    </ul>
    <div class="credit">
      <a href="#top" class="scrollto" class="footer-brand"><img src="/assets/OneEgypt-white-100.png" style="margin-top:-5px;" class="small-logo"/></a>
      <p>Built by <a href="http://www.aeyn.org" target="_blank">AEYN</a></p>
    </div>
  </div>
</footer>