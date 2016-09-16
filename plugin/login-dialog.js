$(document).ready(function()
{			

	// Si déjà connecter on change le bouton connexion en déconnexion
	if(get_cookie("auth"))
	{
		var cible = "a[href=connexion], a[href=connection], a[href=deconnexion]";

		// Sauvegarde de l'ancien lien
		var old_dom = $(cible).parent().html();

		// Changement
		$(cible).html(__("Disconnection")).attr("href","deconnexion");			

		// Au clique sur les liens de déconnexion
		$("a[href=deconnexion]").on("click", function(event)
		{
			event.preventDefault();

			// Déconnexion
			$.ajax({
				url: "api/ajax.php?mode=logout",
				success: function(html){ 
					$("body").html(html);// Retour
					reload();// recharge la page
				}
			});

		});

		// Si mode édition on rétablit le lien original
		edit.push(function() {
			$(cible).parent().html(old_dom);
		});
	}
	else
	{
		// Au clique sur les liens de connexion
		$("a[href=connexion], a[href=connection]").on("click", function(event)
		{
			event.preventDefault();

			if(!$("#login-dialog").length)// && event.type == "mouseenter"
			{
				var $this = this;

				// Layer qui va contenir le form de login
				$("body").append("<div id='login-dialog' class='login none' style='position: absolute;'></div>");

				// Ouverture du login
				$.ajax({
					url: "api/ajax.php?mode=public-login",
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
