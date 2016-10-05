$(document).ready(function()
{			

	// Si déjà connecter on change le bouton connexion en déconnexion
	if(get_cookie("auth"))
	{
		var connexion = "a[href=connexion], a[href=login], a[href=deconnexion], a[href=logout]";
		var inscription = "a[href=inscription], a[href=compte], a[href=registration], a[href=account]";

		// Sauvegarde de l'ancien lien
		var old_connexion = $(connexion).parent().html();
		var old_inscription = $(inscription).parent().html();

		// Changement
		$(connexion).html(__("Disconnection")).attr("href","deconnexion");			
		$(inscription).html(__("Mon compte")).attr("href","compte");			

		// Au clique sur les liens de déconnexion
		$(connexion).on("click", function(event)
		{
			event.preventDefault();
			logout();// Déconnexion
		});

		// Si mode édition on rétablit les liens originaux
		edit.push(function() {
			$(connexion).parent().html(old_connexion);
			$(inscription).parent().html(old_inscription);
		});
	}
	else
	{
		// Au clique sur les liens de connexion
		$("a[href=connexion], a[href=login]").on("click", function(event)
		{
			event.preventDefault();

			if(!$("#login-dialog").length)// && event.type == "mouseenter"
			{
				var $this = this;

				// Layer qui va contenir le form de login
				$("body").append("<div id='login-dialog' class='none' style='position: absolute;'></div>");

				// Ouverture du login
				$.ajax({
					url: "plugin/public-login.php?ajax=true",
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
