<?if(!$GLOBALS['domain']) exit;?>

<style>
	#instagram-header {
		margin-top: -50px;
	    position: absolute;
	    z-index: 1;
	}
	#instagram-logo {
		border-radius: 100%;
		width: 67px;
		height: 67px;
		transition: all .3s;
	}
		#instagram-header:hover #instagram-logo { opacity: .6; }

	#instagram {
		margin: 0;
		padding: 0;
	}
		#instagram li {
			float: left;
			padding-left: 1rem;
		}
			#instagram li:hover img { opacity: .6; }
			#instagram li img {
				max-width: 230px;
				transition: all .3s;
			}
</style>

<section class="mw960p mod center mbl pbm">

		<!-- Instagram -->
		<h1 class="tc mbl"><?txt('titre-instagram')?></h1>

		<a id="instagram-header" target="_blank">
			<img id="instagram-logo">
			<span id="instagram-username" class="bold black mls"></span>
		</a>
		<ul id="instagram" class="unstyled"></ul>

		<div class="tc clear ptm">
			<a id="instagram-suivre" target="_blank" class="bold bt bg-color"><i class="fa fa-instagram mrt"></i> Suivre sur instagram</a>
		</div>

	</section>

	<script>
	$(function()
	{
		// Instagram Light
		// https://www.instagram.com/developer/
		// https://jelled.com/instagram/access-token

		var token = '0000.00.00000';
		var uid = 0000;// profilePage_
		var num = 4;

		$.ajax({
			url: "https://api.instagram.com/v1/users/" + uid + "/media/recent",// /users/self/media/recent => Sandbox
			dataType: 'jsonp',
			type: 'GET',
			data: {access_token: token, count: num},
			success: function(data){

		 		//console.log(data);
		 		$("#instagram-header, #instagram-suivre").attr("href", "https://www.instagram.com/" + data.data[0]['user']['username'] +"/");
		 		$("#instagram-username").text("@"+data.data[0]['user']['username']);
		 		$("#instagram-logo").attr("src", data.data[0]['user']['profile_picture']);

				for(cle in data.data)
				{
					$("ul#instagram").append('<li class="animation fade-in"><a href="'+data.data[cle].link+'" target="_blank"><img data-lazy="'+data.data[cle].images.low_resolution.url+'"></a></li>');
					// data.data[cle].images.low_resolution.url - image, 306х306
					// data.data[cle].images.thumbnail.url - image 150х150
					// data.data[cle].images.standard_resolution.url - image 612х612
					// data.data[cle].link - Instagram URL
				}

				// Pour bien prendre en compte les images en lazyload injecté fraichement dans la dom
				$animation = $(".animation, [data-lazy]");
			},
			error: function(data){ console.log(data); }
		});
	});
	</script>
