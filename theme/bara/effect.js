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


	// SMOOTHSCOLL SUR LES ANCRES
	$("a[href*='#']").on("click", function(event) {
		event.preventDefault();
		$("html, body").animate({ scrollTop: $($(this).attr("href")).offset().top}, 1000, "linear");
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
		//$("header a").css("color", "#fff");// Lien en blanc
		/*$("header #header-logo, header .burger").css({// Couleur du logo inversé
			"-webkit-filter": "invert(1)",
			"filter": "invert(1)"
		});*/	
	}



	// ANIMATION SUR LES ONSCROLL
    var $animation = $(".animation");
    var $window = $(window);

	$window.on("scroll resize load", function () {

		// ANIMATION SUR LES CONTENUS
		var window_height = $window.outerHeight();//height
    	var window_top = $window.scrollTop();
    	var window_bottom = (window_top + window_height);

    	$.each($animation, function() {
    		var $element = $(this);
    		var element_height = $element.outerHeight();
    		var element_top = $element.offset().top;
    		var element_bottom = (element_top + element_height);

			//check to see if this current container is within viewport
			if ((element_bottom >= window_top) &&
				(element_top <= window_bottom)) {
				$element.addClass("fire");
			}
			else $element.removeClass("fire");			
		});


		// PARALLAX DES BG
		$(".parallax").each(function() {
			var parallax_top = $(this).offset().top;
			var parallax_height = $(this).outerHeight();
			var parallax_bottom = parallax_top + parallax_height;

			// Si un bg parallax entre dans le champ de vision on le scroll
			if((parallax_top <= window_bottom) && (parallax_bottom >= window_top))
			{
				// / 2|4 => on divise pour un défilement plus lent
				// Si bg déjà visible quand on arrive sur la page on change le calcule
				if(parallax_top < window_height)
					p100 = parseInt(((window_top) * 100 / (parallax_bottom))) / 4;		
				else 
					p100 = parseInt(((window_bottom - parallax_top) * 100 / (window_height + parallax_height))) / 2;		
		
				$(this).css("background-position", "50% " + p100 + "%");
			}
		});

	});

	$window.trigger("scroll");

});	