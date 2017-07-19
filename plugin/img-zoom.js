
// Fonction de zoom sur les liens contenant des images
img_zoom = function(event)
{
	event.preventDefault();

	// Objet cliquer
	this_source = this;

	// Désactive le clic pour éviter de lancer plusieurs fois la même instance
	$(this_source).off("click.zoom");

	// Désactive le lien
	$(this_source).on("click.disable", function(event){
		return false;
	});

	// Image à charger
	img = $(this_source).attr("href");


	// SI C'EST UN LIEN TEXTE
	// @todo faire cette partie


	// SI LE LIEN ENTOURE UNE IMAGE
	var id = new Date().getTime();

	// Taille d'origine
	original_width = $("img", this).width();
	original_height = $("img", this).height();
	original_top = $("img", this).offset().top;
	original_left = $("img", this).offset().left;
	
	// Copie l'image en une version flottante zoomable // appendTo(this)
	$("img", this).clone().appendTo("body").attr("id", "clone"+id).addClass("clone").css({
		position: "absolute",
		width: original_width,
		height: original_height,
		top: original_top,
		left: original_left,
		zIndex: 102,
		transform: "none",
		cursor: "pointer"
	})

	// Ajout le bloc de progression
	$("#clone"+id).after("<div id='progress"+ id +"'><div class='progress bg-color box-shadow'></div><i class='fa fa-fw fa-refresh fa-spin biggest tc color'></i></div>");

	// Initialise le bloc de progression
	$("#progress"+ id).css({
		position: "absolute",
		width: original_width,
		height: original_height,
		top: original_top,
		left: original_left,
		zIndex: 104
	})

	// Initialise la barre de progression de download
	//$("#progress"+id+" .progress").css("width", "0")

	// Initialise l'icone de chargement
	$("#progress"+id+" .fa-spin").css({
		position: "absolute",
		top: ((original_height - $("#progress"+id+" .fa-spin").height()) / 2),
		left: ((original_width - $("#progress"+id+" .fa-spin").width()) / 2)
	})

	// Charge l'image
	$.ajax({
		type: "GET",
		url: img,
		xhr: function() {	
			var xhr = new window.XMLHttpRequest();
			xhr.addEventListener("progress", function(event){// Download progress
				if(event.lengthComputable) {
					var p100 = (event.loaded * 100 / event.total);
					$("#progress"+id+" .progress").css("width", p100+"%");//Math.floor(p100)
				}
			}, false);
			return xhr;
		},
		success: function()
		{			
			// Désactive la désactivation du click sur l'image
			$(this_source).off("click.disable");

			// Ajout du fond gris
			$("body").append("<div id='under-zoom' style='display: none; background-color: rgba(200, 200, 200, 0.8); z-index: 100; position: absolute; top: 0; left: 0; right: 0;'></div>");			
			
			// Inject la grande image
			$("#clone"+id).attr("src", img);

			// Image bien charger dans la dom
			$("#clone"+id).one("load", function()
			{				
				// Supprime le loading
				$(".progress").remove();
				$("#progress"+id).fadeOut("fast", function(){ $(this).remove() });

				// Calcule de la position de l'image zoomé
				var img_clone_width = $("#clone"+id)[0].naturalWidth;
				var img_clone_height = $("#clone"+id)[0].naturalHeight;
				var window_width = $(window).width();
				var window_height = $(window).height();
				if(window_width > img_clone_width) var left = (window_width - img_clone_width) / 2; else var left = 0;
				if(window_height > img_clone_height) var top = (window_height - img_clone_height) / 2; else var top = 0;

				// Calcule la taille du fond gris
				if($(document).width() > img_clone_width) var bg_width = $(document).width(); else var bg_width = img_clone_width;
				if($(document).height() > img_clone_height) var bg_height = $(document).height(); else var bg_height = img_clone_height;

				// Donne la bonne taille au fond gris
				$("#under-zoom").css({
					width: bg_width,
					height: bg_height
				});

				// Affiche le fond gris
				$("#under-zoom").fadeIn();

				// Zoom à la bonne taille
				$("#clone"+id).animate({
					width: $("#clone"+id)[0].naturalWidth,
					height: $("#clone"+id)[0].naturalHeight,
					top: top,
					left: left
				}, 'slow');

				
				// @todo: ajouter onmousedown, si on reste cliquer on peut se déplacer dans l'image si elle est plus grande que l'écran

				// Si on click dans l'écran on dézoom
				$("body").on("click.dezoom", function(event){
					event.preventDefault();
					event.stopPropagation();
					
					// Supprime le clic sur tout l'écran
					$("body").unbind("click.dezoom");

					// Dézoom
					$("#clone"+id).animate({
							width: original_width,
							height: original_height,
							top: original_top,
							left: original_left	
						},
						'slow',
						function() {			
							// Supprime les clones
							$(".clone").fadeOut("fast", function(){ $(this).remove() });

							// Re-active le lien sur l'image
							$(this_source).on("click.zoom", img_zoom);
						}
					);

					// Supprime le fond gris
					$("#under-zoom").fadeOut("medium", function(){ $(this).remove() });
					
				});

			});
		},
		error: function()// Si l'image en grand n'existe pas
		{			
			light(__("Zoom Not Available"));

			// Supprime le loading
			$(".progress").remove();
			$("#progress"+id).remove();

			// Supprime le clic sur tout l'écran
			$("body").unbind("click.dezoom");

				// Supprime les clones
			$(".clone").remove()

			// Supprime le fond gris
			$("#under-zoom").remove()

			// Re-active le lien sur l'image
			$(cible).on("click.zoom", img_zoom);
		}
	});

}


$(function()
{	
	// Ajoute la traduction courante
	add_translation({"Zoom Not Available" : {"fr" : "Agrandissement indisponible"}});

	cible = "a[href$='.jpg'], a[href$='.png'], a[href$='.gif']";

	// Si on click sur un lien vers une images on zoom dessu
	$(cible).on("click.zoom", img_zoom);

	// Supprime les zoom en mode edition
	edit.push(function() {
		$(cible).off("click.zoom");
		$(".clone").remove();
		$("#under-zoom").fadeOut("medium", function(){ $(this).remove() });
	});
});