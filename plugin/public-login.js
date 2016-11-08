$(document).ready(function()
{			

	// Si déjà connecter on change le bouton connexion en déconnexion
	if(get_cookie("auth"))
	{
		var connexion = "nav a[href$=connexion], nav a[href$=login], nav a[href$=deconnexion], nav a[href$=logout]";
		var inscription = "nav a[href$=inscription], nav a[href$=compte], nav a[href$=registration], nav a[href$=account]";

		// Sauvegarde de l'ancien lien
		var old_connexion_txt = $(connexion).html();
		var old_connexion_href = $(connexion).attr("href");
		var old_inscription_txt = $(inscription).html();
		var old_inscription_href = $(inscription).attr("href");

		// Changement
		$(connexion).html(__("Disconnection")).attr("href", function(i, val) {
			return val.replace("connexion", "deconnexion");
		});			
		$(inscription).html(__("Mon compte")).attr("href", function(i, val) {
			return val.replace("inscription", "compte");
		});				

		// Au clique sur les liens de déconnexion
		$(connexion).on("click", function(event)
		{
			event.preventDefault();
			logout();// Déconnexion
		});

		// Si mode édition on rétablit les liens originaux
		edit.push(function() {
			$(connexion).html(old_connexion_txt);
			$(connexion).attr("href", old_connexion_href);
			$(inscription).html(old_inscription_txt);
			$(inscription).attr("href", old_inscription_href);
		});
	}
	else
	{
		// Au clique sur les liens de connexion
		$("a[href$=connexion], a[href$=login]").on("click", function(event)
		{
			event.preventDefault();

			if(!$("#login-dialog").length)// && event.type == "mouseenter"
			{
				var $this = this;

				// Layer qui va contenir le form de login
				$("body").append("<div id='login-dialog' class='none' style='position: absolute;'></div>");

				// Ouverture du login
				$.ajax({
					url: path+"plugin/public-login.php?ajax=true",
					success: function(html){ 

						// Formulaire de login
						$("#login-dialog").html(html);

						// On affiche et positionne le layer de login
						$("#login-dialog")
							.css("z-index", "10")
							.offset({
								top: $($this).offset().top + $($this).height() + 20,
								left: $($this).offset().left - 20
							})
							.fadeIn();	


						// Pour fermer le layer quand on click en dehors
						close = false;
						$(document).on("click",	
							function(event) {
								if(!$(event.target).parents().is("#login-dialog") && $("#login-dialog").is(":visible") && close == false)
									$("#login-dialog").fadeOut("fast", function(){ close = true; });							
							}
						);
					}
				});

			}
			else if($("#login-dialog").length && !$("#login-dialog").is(":visible")&& close == true)// Si on click et que l'ajax a déjà été fait
			{
				close = true;
				$("#login-dialog").fadeIn("fast", function(){ close = false; });
			}
			else if(event.type == 'click' && $("#login-dialog").is(":visible") && close == false )// Si on click sur le lien de connexion
			{
				$("#login-dialog").fadeOut("fast", function(){ close = true; });
			}

		});
	}

});
