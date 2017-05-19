// TOOLTIP LIGHT

$(document).ready(function()
{
	// CSS
	$("body").append("<style>.lightip-txt{display:none;position: absolute;max-width:250px;padding:1rem;border-radius:5px;background:#fff;box-shadow:0 0 5px #aaa;text-align:center;text-shadow:none;color:#222;z-index:9999}</style>");


	// JS
	$(".lightip").on({
		"mouseenter": function(event) { 

			titletip = $(this).attr("title");

			if(titletip) 
			{
				// Supp le title
				$(this).attr("title","");

				// Crée la tooltip
				$(this).after("<div class='lightip-txt'>"+ titletip +"</div>");

				// Position à gauche
				var obj_width = $(this).outerWidth();
				var tooltip_width = $(".lightip-txt").outerWidth();

				if(obj_width > tooltip_width) 
					var left = ($(this).outerWidth() - $(".lightip-txt").outerWidth())/2;
				else
					var left = 0;

				// Positionne la tooltip
				$(".lightip-txt").css({
					"top": -($(".lightip-txt").outerHeight() + 5),
					"left": left
				})
				.fadeIn();
			}
		},
		"mouseleave": function(event) {
			$(this).attr("title", titletip);
			$(".lightip-txt").fadeOut().remove();
		}
	});


	// Action si on lance le mode d'edition
	edit.push(function() {
		// Masque les tooltips
		$(".lightip-txt").hide();
	});
});