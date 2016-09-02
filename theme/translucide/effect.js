// Fonction qui fade les element présent à l'ecran
fadein_onscroll = function(){
	$(".tofadein").each( function(i){            
		var bottom_of_object = $(this).offset().top + 200;//$(this).offset().top + $(this).outerHeight()
		var bottom_of_window = $(window).scrollTop() + $(window).height() ;//$(window).scrollTop() + $(window).height()

		// If the object is completely visible in the window = fade it
		if(bottom_of_window  > bottom_of_object && $(this).css("opacity") == 0){
			$(this).animate({"opacity":"1"}, 800);					
		}
	});
}


$(document).ready(function()
{
	// ANIMATION SUR LE LOGO
	window.setTimeout(function(){
		$(".logo .shape").addClass("shape-load");

		window.setTimeout(function(){
			$(".logo .shine").addClass("shine-load");
			$("h2.under").css('visibility','visible').hide().fadeIn("slow")
		}, 1800);

	}, 500);

	// MENU BURGER
	$(".burger").click(function() {
		// Animation sur le buger
		$(this).toggleClass("active");

		// Fond gris derrière le menu
		if(!$(".responsiv-overlay").length)
		{
			$("body").append("<div class='responsiv-overlay'></div>");

			/* Ferme le menu si on click sur l'overlay*/
			$(".responsiv-overlay").click(function() {
				$("body").removeClass("responsiv-nav");
				$(".burger").removeClass("active");
			})
		}
		
		// Ouvertur du menu
		$("body").toggleClass("responsiv-nav");		
	});


	// AFFICHE LES ÉLÉMENTS DÉJÀ VISIBLE
	fadein_onscroll();


	$(window).scroll(function(e) {

		// ANIMATION SUR LES CONTENUS
		fadein_onscroll();

	});

});