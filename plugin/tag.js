$(function()
{
	// AJOUT DU SYSTÈME DE CATÉGORIE
	
	// Lib js pour le tri des tag
	var script = document.createElement('script');
	script.src = path+"plugin/sortable-nested.min.js";
	document.body.appendChild(script);	

	// Ajout du input tag
	$("#admin-bar").append("<div id='tag' class='fl mat mrs nowrap'><i class='fa fa-tag acolor mlt vam'></i> <input type='text' id='tags' value=\""+$(".content #tags").text().trim()+"\" placeholder=\""+ __("Catégorie") +"\" class='w100'></div>");

	// A sauvegarder si changement dans les tags de la page
	$("#admin-bar #tag > input").on("keydown", function(event) {
		tosave();	
	});

	// Action au passage au dessu du champs tag
	$("#admin-bar #tag").on("click mouseenter",// touchstart
		function(event) {

			event.stopPropagation();
			event.preventDefault();		

			if(!$("#admin-bar #tag .absolute").length && event.type == "mouseenter")
			{
				$.ajax({
					url: path+"plugin/tag.php?mode=tag-tree",
					data: {"id": id, "nonce": $("#nonce").val()},
					success: function(html){ 
						$("#admin-bar #tag").append(html);						
						
						// Pour fermer l'admin user quand on click en dehors
						close_tag = false;
						$(document).on("click",	
							function(event) {
								if(
									!$(event.target).parents().is("#admin-bar #tag .absolute") &&
									$("#admin-bar #tag .absolute").is(":visible") &&
									close_tag == false
								)
									if($("#admin-bar #tag button.to-save").length || $("#admin-bar #tag button i.fa-spin").length)// Si l'arbre des tags pas sauvegardé on shake
										$("#admin-bar #tag .absolute > div").effect("highlight");
									else 
										$("#admin-bar #tag .absolute").fadeOut("fast", function(){ close_tag = true; });
							}
						);
					}
				});
			}
			else if($("#admin-bar #tag .absolute").length && !$("#admin-bar #tag .absolute").is(":visible") && close_tag == true)// Si on click et que l'ajax a déjà été fait
			{
				close_tag = true;
				$("#admin-bar #tag .absolute").fadeIn("fast", function(){ close_tag = false; });
			}
		}
	);


	// AJOUT DU TAG LORS DE LA SAUVEGARDE
	function callback() {
		//@todo ajouter une alert si les tags n'on pas réussit a être sauvegardé
		$.ajax({
			type: "POST",
			url: path+"plugin/tag.php?mode=tag",
			data: {"id": id, "tags": $("#admin-bar #tags").val(), "nonce": $("#nonce").val()},
			success: function(html){
				$("body").append(html);
			}
		});	
	};
});	