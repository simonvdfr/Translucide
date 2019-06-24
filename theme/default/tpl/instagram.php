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
		
		// Suivre la procédure a l'écran une fois la template attribuer à une page
		var token = '';
		var num = 4;

		if(token == '' || typeof token == 'undefined') 
		{
			// Si pas de token => la notice
			$("ul#instagram").append("<li class='mtm'>Pour obtenir votre CLIENT ID & TOKEN<ul class='mod'><li class='clear'>Créez votre compte client sur <a href='https://www.instagram.com/developer/' target='_blank'>instagram.com/developer</a> => <i>Manage Clients</i> => <i>Register a New Client</i></li><li class='clear'>Mettez l'URL de votre site <input type='text' value='<?=$GLOBALS['home'];?>' onfocus='this.select()'> dans <i>Valid redirect URIs</i></li><li class='clear'>dans l'onglet <i>Security</i> décoché la case <i>Disable implicit OAuth</i></li><li class='clear'>Une fois l'opération finie vous serrez redirigé vers votre site avec le token dans l'URL (#access_token=) à récupérer et coller dans le fichier Php qui affiche les photos instagram</li></ul><div class='mtm'><b>CLIENT ID</b> <input type='text' name='client_id' id='client_id'> <button id='get_token'></button></div></li>");

			// Si on a saisie le client id
			$("ul#instagram #client_id").on("change keyup", function(event) {
				event.preventDefault();
				$("ul#instagram button").html("Obtenir le token");
			});

			// Au click redirection vers insta
			$("ul#instagram #get_token").on("click", function(event) {
				event.preventDefault();
				document.location.href = 'https://api.instagram.com/oauth/authorize/?response_type=token&client_id='+ $("ul#instagram #client_id").val() +'&redirect_uri=<?=urlencode($GLOBALS['home']);?>';
			});
		}
		else {
			//var token_split = token.split(".");
			//var uid = token_split[0];// profilePage_

			$.ajax({
				url: "https://api.instagram.com/v1/users/self/media/recent",// /users/'+ uid +'/media/recent
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
		}
	});
	</script>
