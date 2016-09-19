$(document).ready(function()
{		
	//@todo:
	// barre de loading
	// smooth scrooltop
	// changer l'url qd done
	// garder la hauteur, fadeout, injecter contenu, fadein
	// ou opacity:0, injection, anim opa: 1
	// anim pour passer en height: auto a la fin

	$("header a, footer a").on("click", function(event)
	{
		event.preventDefault();

		url = $(this).attr("href");

		if(url == location.protocol+"//"+location.host+"/") url = "home";

		$.ajax({
			type: "GET",
			url: "plugin/speed-load.php?url="+url,
			xhr: function() {	
				var xhr = new window.XMLHttpRequest();
				xhr.addEventListener("progress", function(event){// Download progress
					if(event.lengthComputable) {
						var p100 = (event.loaded * 100 / event.total);
						//$("#progress"+id+" .progress-bar").css("width", p100+"%");//Math.floor(p100)
					}
				}, false);
				return xhr;
			},
			success: function(html)
			{
				$(".content").html(html);

				// Image bien charger dans la dom
				//$("#clone"+id).one("load", function()
				//{				
					// Supprime le loading
					//$(".progress-bar").remove();

				//});
			}
		});

	});

});