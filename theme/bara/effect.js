// Fonction qui fade les éléments présents à l'écran
fadein_onscroll = function(){
	$(".tofadein").each( function(i){            
		var bottom_of_object = $(this).offset().top + 200;//$(this).offset().top + $(this).outerHeight()
		var bottom_of_window = $(window).scrollTop() + $(window).height() ;//$(window).scrollTop() + $(window).height()

		// Si l'objet est complètement visible dans la fenêtre = fade
		if(bottom_of_window  > bottom_of_object && $(this).css("opacity") == 0){
			$(this).animate({"opacity":"1"}, 800);					
		}
	});
}


$(document).ready(function()
{
	// MENU BURGER
	$(".burger").click(function() {
		// Animation sur le buger
		$(this).toggleClass("active");

		// Fond gris derrière le menu
		if(!$(".responsiv-overlay").length)
		{
			$("body").append("<div class='responsiv-overlay'></div>");

			// Ferme le menu si on click sur l'overlay
			$(".responsiv-overlay").click(function() {
				$("body").removeClass("responsiv-nav");
				$(".burger").removeClass("active");
			})
		}
		
		// Ouverture du menu
		$("body").toggleClass("responsiv-nav");		
	});


	// EFFET CHARGEMENT DE PAGE SUR LIEN DU MENU 
	/*$("header a").click(function() {
		
		var href = this.href;// Conserve le lien

		$("body").append("<div class='wave-wine'></div>");// Ajoute le bloc pleine page

		// Fadein sur le bloc
		$(".wave-wine").fadeIn(300, function(){
			$(this).css("background-position", "100% 0");// Lancement de l'animation
			setTimeout(function() { document.location = href; }, 600);// Redirection après un temps
		});

		return false;		
	});*/
	
	

	// BG IMAGE SOUS LE HEADER
	if($(".under-header").length) {
		$(".under-header").css("margin-top", -$("header").outerHeight());// Calage
		$(".under-header").append("<div class='overlay'></div>");// Diminue l'opacité
		$("header a").css("color", "#fff");// Lien en blanc
		$("header #header-logo, header .burger").css({// Couleur du logo inversé
			"-webkit-filter": "invert(1)",
			"filter": "invert(1)"
		});		
	}


	// AFFICHE LES ÉLÉMENTS DÉJÀ VISIBLE
	fadein_onscroll();


	$(window).scroll(function(e) {

		// ANIMATION SUR LES CONTENUS
		fadein_onscroll();

		// PARALLAX DES BG
		var window_scrolltop = $(window).scrollTop();
		var window_height = $(window).outerHeight();
		var window_bottom = window_scrolltop + window_height;
		
		//console.log("window_scrolltop: "+ window_scrolltop +" / window_height: "+ window_height +" / window_bottom: "+ window_bottom)

		$(".parallax").each(function() {
			var parallax_top = $(this).offset().top;
			var parallax_height = $(this).outerHeight();
			var parallax_bottom = parallax_top + parallax_height;

			//console.log("parallax_top: "+ parallax_top +" / parallax_height: "+ parallax_height +" / parallax_bottom: "+ parallax_bottom)
			
			// Si un bg parallax entre dans le champ de vision on le scroll
			if((parallax_top <= window_bottom) && (parallax_bottom >= window_scrolltop))
			{
				// / 2|4 => on divise pour un défilement plus lent
				// Si bg déjà visible quand on arrive sur la page on change le calcule
				if(parallax_top < window_height)
					p100 = parseInt(((window_scrolltop) * 100 / (parallax_bottom))) / 4;		
				else 
					p100 = parseInt(((window_bottom - parallax_top) * 100 / (window_height + parallax_height))) / 2;		
				
				//console.log(p100)
				
				$(this).css("background-position", "50% " + p100 + "%");
			}
		});

	});

});	