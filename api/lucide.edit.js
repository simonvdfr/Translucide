// @todo: mettre une barre loading et readonly qd save

// Traduction
add_translation({
	"Save" : {"fr" : "Enregistrer"},
	"Save & View" : {"fr" : "Enregistrer et voir"},
	"Close the edit mode" : {"fr" : "Fermer le mode d'\u00e9dition"},
	"The changes are not saved" : {"fr" : "Les modifications ne sont pas enregistr\u00e9es"},
	"Empty element" : {"fr" : "El\u00e9ment vide"},		
	"Add to menu" : {"fr" : "Ajouter au menu"},		
	"To remove slide here" : {"fr" : "Pour supprimer glisser ici"},		
	"Paste something..." : {"fr" : "Collez quelque chose..."},
	"Upload file" : {"fr" : "T\u00e9l\u00e9charger un fichier"},
	"Change the background image" : {"fr" : "Changer l'image de fond"},
	"Drop your files to upload" : {"fr" : "D\u00e9posez vos fichiers pour les mettre en ligne"},
	"This file format is not supported" : {"fr" : "Ce format de fichier n'est pas pris en charge"},	
	"Show user info" : {"fr" : "Voir les infos utilisateur"},
	"Delete user" : {"fr" : "Supprimer l'utilisateur"},
	"User deleted" : {"fr" : "Utilisateur supprim\u00e9"},
	"Page title" : {"fr" : "Titre de la page"},
	"Description for search engines" : {"fr" : "Description pour les moteurs de recherche"},
	"Formatted web address" : {"fr" : "Adresse web format\u00e9"},
	"Permanent link: 'home' if homepage" : {"fr" : "Lien permanent: 'home' si c'est la page d'accueil"},
	"Home page" : {"fr" : "Page d'accueil"},
	"Regenerate address" : {"fr" : "R\u00e9g\u00e9n\u00e9rer l'adresse"},
	"Title H2" : {"fr" : "Titre H2"},	
	"Separator" : {"fr" : "S\u00e9parateur"},	
	"Media Library" : {"fr" : "Biblioth\u00e8que des m\u00e9dias"},		
	"Icon Library" : {"fr" : "Biblioth\u00e8que d'ic\u00f4ne"},		
	"See the source code" : {"fr" : "Voir le code source"},		
	"Link" : {"fr" : "Lien"},		
	"Add Link" : {"fr" : "Ajouter le lien"},		
	"Change Link" : {"fr" : "Modifier le lien"},		
	"Open link in new window" : {"fr" : "Ouvre le lien dans une nouvelle fen\u00eatre"},		
	"Remove the link from the selection" : {"fr" : "Supprimer le lien de la s\u00e9lection"},
	"Delete image" : {"fr" : "Supprimer l'image"},		
	"Delete file" : {"fr" : "Supprimer le fichier"},
	"Zoom link" : {"fr" : "Lien zoom"},
	"Add" : {"fr" : "Ajouter"},
	"Width" : {"fr" : "Largeur"},
	"Height" : {"fr" : "Hauteur"}
});


// Type de navigateur
$.browser = {};
$.browser.mozilla = /mozilla/.test(navigator.userAgent.toLowerCase()) && !/webkit    /.test(navigator.userAgent.toLowerCase());
$.browser.webkit = /webkit/.test(navigator.userAgent.toLowerCase());
$.browser.opera = /opera/.test(navigator.userAgent.toLowerCase());
$.browser.msie = /msie/.test(navigator.userAgent.toLowerCase());


// Rapatrie le contenu
get_content = function(content)
{
	var content_array = content.replace(/\.|#/, '');

	data[content_array] = {};

	// Contenu des champs éditables
	$(document).find(content+" .editable").not("header nav .editable").each(function() {
		// Si on est en mode pour voir le code source
		if($(this).hasClass("view-source")) var content_editable = $(this).text(); else var content_editable = $(this).html();
		if($(this).html()) data[content_array][this.id] = content_editable;
	});
	
	// Contenu des images éditables
	$(document).find(content+" .editable-img img").each(function() {
		if($(this).attr("src")) data[content_array][this.id] = $(this).attr("src");
	});
	
	// Contenu des bg images éditables
	$(document).find(content+" [data-editable='bg']").each(function() {
		if($(this).attr("data-bg")) data[content_array][$(this).attr("data-id")] = $(this).attr("data-bg");
	});
	
	// Checkbox fa
	$(document).find(content+" .editable-checkbox").each(function() {
		if($(this).hasClass("fa-check")) data[content_array][$(this).attr("id")] = true;							
	});

	// Contenu des select // content+" input, "+
	$(document).find(content+" .editable-select").each(function() {
		data[content_array][$(this).attr("id")] = $(this).val();
	});

	// Contenu des input hidden éditables
	$(document).find(content+" .editable-hidden").each(function() {
		if($(this).val()) data[content_array][this.id] = $(this).val();
	});
}


// Sauvegarde les contenus
save = function(callback) 
{
	// @todo: disable/unbind sur save pour dire que l'on est en train de sauvegarder
	
	// Si image sélectionnée : raz propriétés image (sécurité pour ne pas enregistrer de ui-wrapper)
	if(memo_img) img_leave();

	// Animation sauvegarde en cours (loading)
	$("#save i").removeClass("fa-save").addClass("fa-spin fa-cog");

	data = {};
	
	data["nonce"] = $("#nonce").val();// Pour la signature du formulaire

	data["url"] = clean_url();// Url de la page en cours d'édition

	data["permalink"] = $("#admin-bar #permalink").val();// Permalink

	data["title"] = $("#admin-bar #title").val();// Titre de la page
	data["description"] = $("#admin-bar #description").val();// Description pour les serp

	data["state"] = $("#admin-bar #state").val();// Etat d'activation de la page
	
	get_content(".content");// Contenu de la page

	get_content("header");// Contenu du header

	get_content("footer");// Contenu du footer

	// Contenu du menu de navigation
	data["nav"] = {};
	$(document).find("header nav ul a").not("#add-nav ul a").each(function(index) {
		data["nav"][index] = {
			href : $.trim($(this).attr('href')),
			text : $(this).text(),
			id : $(this).attr('id') || "",
			class : $(this).attr('class') || "",
			target : $(this).attr('target') || ""
		};
	});

	//console.log(data)
	
	// On sauvegarde en ajax les contenus éditables
	$.ajax({
		type: "POST",
		url: path+"api/ajax.admin.php?mode=update",
		data: data
	})
	.done(function(html) {
		// Affichage/exécution du retour
		$("body").append(html);

		// S'il y a une fonction de retour
		if(typeof callback === "function") callback();
	})
	.fail(function() {
		error(__("Error"));
	});
}


// Changement d'état des boutons de sauvegarde
tosave = function() {	
	$("#save i").removeClass("fa-spin fa-cog").addClass("fa-save");// Affiche l'icône disant qu'il faut sauvegarder sur le bt save	
	$("#save, #preview").removeClass("saved").addClass("to-save");// Changement de la couleur de fond du bouton pour indiquer qu'il faut sauvegarder
}


// Champs requis
$.fn.required = function(txt){						
	$(this).addClass("invalid").attr("title", txt).tooltip().tooltip("open").focus().effect("highlight");
}


// Fonctions pour catcher la sélection
range_selects_single_node = function(range) {
	var start_node = range.startContainer;
	return start_node === range.endContainer && start_node.hasChildNodes() && range.endOffset === range.startOffset + 1;
}

selected_element = function(range) {
	if (range_selects_single_node(range))// La sélection comprend un seul élément
		return range.startContainer.childNodes[range.startOffset];
	else if (range.startContainer.nodeType === 3)// La sélection commence à l'intérieur d'un noeud de texte, donc obtenir son parent
		return range.startContainer.parentNode;
	else// La sélection commence à l'intérieur d'un élément
		return range.startContainer;
}


// Barre d'outil de mise en forme et de contenu
exec_tool = function(command, value, ui) {
	ui = ui || false;
	value = value || null;	
				
	// Sélectionne le contenu car on a perdu le focus en entrant dans les options
	if((command == "CreateLink" || command == "insertImage" || command == "insertIcon" || command == "insertHTML" || command == "insertText") && memo_selection && memo_range) {
		memo_selection.removeAllRanges();
		memo_selection.addRange(memo_range);		
	}
	
	if(command)
	{
		// Si icône
		if(command == "insertIcon") {
			command = "insertHTML";
			value = "<span class='fa'>&#x"+ value +";</span>";
		}
		
		// Si alignement
		if(/justify/.test(command)) {
			$(memo_node).removeAttr("align").css("text-align","");
		}
		

		// Exécution de la commande
		document.execCommand(command, ui, value);
		

		// A sauvegarder	
		tosave();

		// Si on justify on supprime l'éventuel span intérieur
		if(/justify/.test(command))
		{
			// Désélectionne les alignements
			$("[class*='fa-align']").parent().removeClass("checked");			

			// check le bt d'alignement
			$("#align-"+command.match(/justify(.*)/)[1].toLowerCase()).addClass("checked");
			
			// Si il y a un span avec des style on le supprime (chrome)
			if($("span", $(memo_node))[0])
				$("div", memo_node).html($("span", $(memo_node).context.innerHTML).html());
		}

		if(command == "CreateLink")
		{
			// Si Target = blank
			if($("#target-blank").hasClass("checked")) memo_selection.anchorNode.parentElement.target = "_blank";// @todo verif marche sous firefox ??
			else $(memo_node).removeAttr("target");
			
			$("#txt-tool #option").hide("slide", 300);// Cache le menu d'option avec animation
		}
		else
			$("#txt-tool #option").hide();// Cache le menu d'option rapidement

		$("#unlink").remove();// Supprime les boutons de unlink
	}

	$(memo_focus).focus();// On focus le contenu édité pour faire fonctionner onblur = close toolbox

	// Recrée une sélection en fonction des changements de la dom
	memo_selection = window.getSelection();
	memo_range = memo_selection.getRangeAt(0);
	memo_node = selected_element(memo_range);
}


// Menu avec les options d'ajout/modif de lien
link_option = function()
{		
	$("#txt-tool #option").hide();// Réinitialise le menu d'option
	$("#target-blank").removeClass("checked");// Réinitialise la colorisation du target _blank

	var href = $(memo_node).closest('a').attr('href');// On récupère le href de la sélection en cours

	if(href) {
		// Si target = blank
		if(memo_node.target == "_blank") $("#target-blank").addClass("checked");

		$("#txt-tool #option #link").val(href);
		$("#txt-tool #option button span").text(__("Change Link"));
		$("#txt-tool #option button i").removeClass("fa-plus").addClass("fa-save");
	}
	else {
		$("#txt-tool #option #link").val('');
		$("#txt-tool #option button span").text(__("Add Link"));
		$("#txt-tool #option button i").removeClass("fa-save").addClass("fa-plus");
	}
	
	$("#txt-tool #option").show("slide", 300);
}

// Si target blank
target_blank = function(mode) {
	if(mode == true || !$("#target-blank").hasClass("checked")) $("#target-blank").addClass("checked");
	else $("#target-blank").removeClass("checked");
}

// Ajout/Suppression d'un element html
html_tool = function(html){
	// Si on est déjà dans un élément entouré du 'HTML' demandé : on le supp
	if($(memo_node).closest(html).length){
		$("#"+html).removeClass("checked");
		$(memo_node).replaceWith($(memo_node).html());
	}
	else {
		$("#"+html).addClass("checked");
		exec_tool('formatBlock', html);
	}
}

// Voir le code source
view_source = function(memo){
	// Si on est déjà en mode view source on remet en html
	if($(memo).hasClass("view-source")) {
		$("#view-source").removeClass("checked");
		$(memo).removeClass("view-source").html($(memo).text());
	}
	else {
		$("#view-source").addClass("checked");
		$(memo).addClass("view-source").text($(memo).html()).html();
	}
}



// Dialog box avec effet de transfert
dialog_transfert = function(mode, source, target, callback) {

	// @todo: faire en sorte que la dialog fadeIn et fadeOut lorsqu'elle apparaît/disparaît. Pas juste visibility:hidden/visible...

	$.ajax({
			url: path+"api/ajax.admin.php?mode=dialog-"+mode, 
			data: {
				"target": target,
				"source": (target == "bg" ? $(source).attr("data-id") : (source.id || $("img", source).attr("id"))),
				"width": $(source).hasClass("editable-img") ? $("img", source).attr("width") : "",
				"height": $(source).hasClass("editable-img") ? $("img", source).attr("height") : "",
				"nonce": $("#nonce").val()
			}
		})
		.done(function(html){

			// Pour ne pas instancier plusieurs fois la dialog
			$(".dialog-"+mode).remove();
			
			// Création de la dialog invisible
			$("body").append(html);

			// Si l'ajax renvoie bien la dialog demandée
			if($(".dialog-"+mode).length) 
			{			
				// Instanciation de la dialog en mode invisible
				$(".dialog-"+mode).dialog({
					modal: true,
					autoOpen: false,
					width: "80%",
					//show: {effect: "fadeIn"},
					hide: {effect: "fadeOut"},
					beforeClose: function ()
					{							
						$(this).dialog("widget").effect("transfer", {// Effet de transfert
							to: $(source),
							className: "ui-effects-transfer"
						}, 300);
					},						
					close: function()
					{
						$(".dialog-"+mode).remove();
						
						// S'il y a une fonction de callback on l'exécute
						if(typeof callback === "function") callback();
					},
					create: function() 
					{
						if(mode == "media") 
						{
							// Création des onglets
							$(".dialog-media").tabs({
								beforeLoad: function(event, ui) {
									ui.ajaxSettings.url += ( /\?/.test(ui.ajaxSettings.url) ? "&" : "?" ) + 'nonce=' + $("#nonce").val();											
								}
							});

							// Place les onglets à la place du titre de la dialog
							$(".ui-dialog-title").html($(".ui-tabs-nav")).parent().addClass("ui-tabs");
						}
					},
					open: function(event, ui) {
						$(".dialog-"+mode).dialog("widget").css("visibility", "hidden");

						// Effet d'ouverture : transfert
						$(source).effect("transfer", {
							to: $(".dialog-"+mode).dialog("widget"),
							className: "ui-effects-transfer"
							}, 300, function() {
								$(".dialog-"+mode).dialog("widget").hide().css("visibility", "visible").fadeIn("fast");
							}
						);						
					},
					resize: function(event, ui) 
					{
						// Masque le texte dans les tab si on réduit trop la fenêtre
						if(ui.size.width < 635) $(".ui-tabs-nav span").hide();
						else $(".ui-tabs-nav span").show();
					}						
				});

				$(".dialog-"+mode).dialog("open");				
			}
		});
}


// Ouvre la fenêtre pour ajouter une image dans la galerie des medias (intext, isolate, bg)
media = function(source, target) {
	//$(memo_focus).focus();// On focus le contenu édité pour faire fonctionner onblur = close toolbox
	
	// Dialog de gestion des medias
	dialog_transfert("media", source, target, function() {					
			// Unbind le drag&drop pour l'ajout de média
			$("body").off(".dialog-media");

			// Relance les autres events
			editable_event();
			editable_img_event();
			body_editable_img_event();
		}
	);
}


// Upload d'un média (image)
upload = function(source, file, resize)
{
	uploading = true;

	// Type de fichier supporté pour l'upload
	var mime_supported = [
		"image/jpg","image/jpeg","image/pjpeg","image/png","image/x-png","image/gif","image/x-icon",
		"application/pdf","application/zip","text/plain"		
	];

	var width = $("img", source).attr("width") || "";
	var height = $("img", source).attr("height") || "";

	if(file) 
	{
		if(mime_supported.indexOf(file.type) > 0)// C'est bien un fichier supporté
		{				
			// @todo: ajouter un cog loading en sur l'image (a coter du % ?)

			// Layer pour la progressbar
			var progressid = "progress-" + source.attr("id");
			source.append("<div id='"+progressid+"' class='progress bg-color small' style='height: "+source.outerHeight()+"px;'></div>");			
			
			// Type mime du fichier
			var mime = file.type.split("/");

			// Affiche la preview si image
			if(mime[0] == "image") 
			{
				$("img", source).addClass("to50");// On fade à moitié (50%)

				var reader = new FileReader();
				reader.onload = function(theFile) {
					// On crée un objet image pour s'assurer que l'image est bien chargée dans le browser avant de prendre sa taille pour le layer de progression d'upload
					var image = new Image();
					image.src = theFile.target.result;

					image.onload = function() {// Image bien chargée dans le navigateur
						if($("#"+progressid).length) {			
							// Si l'upload n'est pas déjà fini on calle la source et la hauteur de la progress bar
							if(!$("img", source).attr("src")) {
								$("img", source).attr("src", this.src);// On colle le bin de l'image dans le src
								$("#"+progressid).css("height", source.outerHeight()+"px");// On force la taille de progression d'upload
							}
						}
					};	
				}
				reader.readAsDataURL(file);
			}

			// Upload du fichier avec progressbar
			var data = new FormData();
			data.append("file", file);
			data.append("index", file.name);
			data.append("width", width);
			data.append("height", height);
			if(resize) data.append("resize", resize);
			data.append("nonce", $("#nonce").val());

			$.ajax({
				type: "POST",
				url: path+"api/ajax.admin.php?mode=upload-file",
				xhr: function() {
					var xhr = $.ajaxSettings.xhr();
					if(xhr.upload) {									
						xhr.upload.addEventListener("progress", function (event) {// Progressbar
							if(event.lengthComputable){
								p100 = (event.loaded * 100 / event.total);
								$("#"+progressid).css("width", p100+"%").css("height", source.outerHeight()+"px").html(Math.floor(p100) + "%");//.toFixed(2)
							}
						}, false);
					}
					return xhr;
				},
				data: data,
				cache: false,
				contentType: false,
				processData: false,
				success: function(path)
				{					
					if(path.match('dialog-connect')) $("body").append(path);// Si erreur de login
					else if(path)
					{
						source.removeClass("uploading");// Supprime le spin d'upload

						$("#"+progressid).css("width", "100%").css("height", source.outerHeight()+"px").html("100%");// Pour être sur d'afficher 100%
						
						// Si c'est une image
						if(mime[0] == "image") 
						{
							$("img", source).removeClass("to50");// On remet l'image à l'opacité normale
							$("img", source).attr("src", path);// Affiche l'image finale 
						}
						
						// Nom du fichier final si dialog médias
						if(source.attr("data-file")) {
							source.attr("data-file", path);// Pour la manipulation							
							$(".file div", source).html(path.split('/').pop());// Pour l'affichage 
						}
						
						// Détruis le layer de progressbar
						$("#"+progressid).fadeOut("medium", function() { 
							this.remove();
							source.addClass("uploaded");// Icone uploaded avec fadeinout
						});

						$(".supp", source).css("visibility","visible");// Affiche l'option de suppression

						if(!$(".dialog-media").length) tosave();// Mode : A sauvegarder
					}
					else {
						source.hide("slide", 300);
						error(__("Error"));
					}
					
					// Lance l'upload suivant
					if(typeof source_queue !== 'undefined' && source_queue.length > 0 && file_queue.length > 0)
						upload(source_queue.shift(), file_queue.shift());
					else
						uploading = false;// Fin des uploads
				}
			});

		}
		else {
			source.hide("slide", 300);
			error(__("This file format is not supported") + " : "+ file.type);
		}

		//console.log(data.files[0].type);
	}   	
}


// Insertion d'un fichier depuis la dialog media
get_file = function(id)
{	
	$(".dialog-media li").css("opacity","0.4");
	$("#"+id).css("opacity","1");
	
	// Insertion du lien vers le fichier
	exec_tool("insertHTML", "<a href=\""+ $("#"+id).attr("data-file") +"\">"+ $("#"+id).attr("data-file").split('/').pop() +"</a>");

	// Fermeture de la dialog
	$(".dialog-media").dialog("close");
	
	tosave();// A sauvegarder
}


// Insertion d'une image depuis la dialog media
get_img = function(id, link)
{	
	//@todo ajouter un loading pour informer que l'on resize l'image avant fermeture de la dialog
	//@todo voir pour faire un fade opacity en js, car en css ça fait un bug lors de l'ouverture de la dialog
	
	// Focus sur l'image choisie
	$(".dialog-media li").css("opacity","0.4");
	$("#"+id).css("opacity","1");
	
	// Resize de l'image et insertion dans la source
	$.ajax({
		type: "POST",
		url: path+"api/ajax.admin.php?mode=get-img",
		data: {
			"img": $("#"+id).attr("data-file"),
			"width": $("#dialog-media-width").val(),
			"height": $("#dialog-media-height").val(),
			"nonce": $("#nonce").val()
		},
		success: function(final_file)
		{ 
			if($("#dialog-media-target").val() == "isolate") {// Insert dans un bloc isolé
				$("#"+$("#dialog-media-source").val()).attr("src", final_file);
			}
			else if($("#dialog-media-target").val() == "intext") {// Ajout dans un contenu texte
				if(typeof link !== 'undefined' && link) exec_tool("insertHTML", "<a href=\""+ $("#"+id).attr("data-file") +"\"><img src=\""+ final_file +"\" class='fl'></a>");
				else exec_tool("insertHTML", "<img src=\""+ final_file +"\" class='fl'>");				
			}
			else if($("#dialog-media-target").val() == "bg") {// Modification d'un fond
				$("[data-id='"+$("#dialog-media-source").val()+"']").attr("data-bg", final_file);
				$("[data-id='"+$("#dialog-media-source").val()+"']").css("background-image", "url("+final_file+")");
			}

			// Fermeture de la dialog
			$(".dialog-media").dialog("close");
			
			tosave();// A sauvegarder
		}
	});
}

// Alignement de l'image intext
img_position = function(align) {
	$(memo_img).removeClass("center fl fr").addClass(align);
}

// Supprime l'image sélectionnée du contenu
img_remove = function() {
	$(memo_img).remove();
	$("#img_tool").remove();
	memo_img = null;
}

// Transfert width/height du style vers la dom img
img_transfert_style = function(event) {
	var width = $(event).css('width');
	var height = $(event).css('height');
	if(width && height) {
		$(event).attr('width', parseInt(width));
		$(event).attr('height', parseInt(height));
		$(event).removeAttr('style');
	}
	$(event).removeClass("ui-resizable");
}

// Si on focus-out/drag-leave d'une image dans bloc éditable
img_leave = function() 
{
	// Supprime la barre d'outil image
	$("#img_tool").remove();

	// Supprime le resizer s'il y en a un
	if($('.editable .ui-wrapper img').length) $('.editable .ui-wrapper img').resizable('destroy');
	else if($('.editable .ui-wrapper').length) $('.editable .ui-wrapper').remove();
		
	// Supprime le style sur l'image sélectionnée et le transfert sur la dom de l'image
	if(memo_img) {
		img_transfert_style(memo_img);
		memo_img = null;
	}
	else {		
		$(".editable img").each(function() {
			img_transfert_style(this);
		});
	}
}



// Vérifie que le contenu est sauvegardé en cas d'action de fermeture ou autres
$(window).on("beforeunload", function(){
	//if($("#admin-bar button.to-save").length || $("#save i.fa-spin").length) return __("The changes are not saved");
});



$(document).ready(function()
{						
	//@todo: ajouter le choix de la template de la page en cours, la langue
	

	/************** ADMINBAR **************/

	// Barre du haut avec bouton sauvegarder			
	adminbar = "<div id='admin-bar'>";

		adminbar+= "<div id='user' class='fl pat'><i class='fa fa-fw fa-user bigger' title=\""+ __("Show user info") +"\"></i></div>";


		adminbar+= "<div id='meta-responsive' class='fl mat none small-screen'><i class='fa fa-fw fa-pencil bigger' title=\""+ __("Page title") +"\"></i></div>";

		adminbar+= "<div id='meta' class='fl mat w30 no-small-screen'>";
			adminbar+= "<input type='text' id='title' value=\""+ document.title +"\" title=\""+ __("Page title") +"\" maxlength='60' class='w100 bold'>";
			adminbar+= "<div class='w50'>";
				adminbar+= "<div class='tooltip slide-left fire pas mas mlt'>";

					adminbar+= "<div class='small'>"+ __("Description for search engines") +" :</div>";
					adminbar+= "<input type='text' id='description' value=\""+ ($('meta[name=description]').attr("content") != undefined ? $('meta[name=description]').attr("content") : "") +"\" maxlength='160' class='w100'>";

					adminbar+= "<div class='small mtt'>"+ __("Formatted web address") +" :</div>";
					adminbar+= "<input type='text' id='permalink' value=\""+ permalink +"\" placeholder=\""+ __("Permanent link: 'home' if homepage") +"\" maxlength='60' class='w50 mrm'>";
					adminbar+= "<input type='checkbox' id='homepage'"+ (permalink == "home" ? " checked" : "") +"> <label for='homepage' class='mrs'>"+ __("Home page") + "</label>";
					adminbar+= "<label id='refresh-permalink'><i class='fa fa-fw fa-refresh'></i>"+ __("Regenerate address") + "</label>";
					
				adminbar+= "</div>";
			adminbar+= "</div>";
		adminbar+= "</div>";		


		adminbar+= "<button id='save' class='fr mat small' title=\""+ __("Save") +"\"><span class='no-small-screen'>"+ __("Save") +"</span> <i class='fa fa-fw fa-save big'></i></button>";

		adminbar+= "<button id='preview' class='fr mat small' title=\""+ __("Save & View") +"\"><span class='no-small-screen'>"+ __("Save & View") +"</span> <i class='fa fa-fw fa-eye big'></i></button>";

		adminbar+= "<select id='state' class='fr mat fa-select'><option value='active'>&#xf00c; "+ __("Active") +"</option><option value='deactivate'>&#xf00d; "+ __("Deactivate") +"</option></select>";

		adminbar+= "<div id='close' class='fr bigger' title=\""+ __("Close the edit mode") +"\"><i class='fa fa-fw fa-times-circle-o'></i></div>";

	adminbar+= "</div>";

	$("body").append(adminbar).addClass("body-margin-top");
	
	// Ajout des variables dans les inputs (pour le problème de double cote ")
	$("#admin-bar #title").val(document.title);
	$("#admin-bar #description").val(($('meta[name=description]').attr("content") != undefined ? $('meta[name=description]').attr("content") : ""));

	// Etat de la checkbox homepage onready
	if($("#admin-bar #permalink").val() == "home") {
		$("#admin-bar #homepage").prop("checked", true);
	}
	// Si on change le permalink on verif que c'est 'home'
	$("#admin-bar #permalink").keyup(function() {
		if($(this).val() == "home") $("#admin-bar #homepage").prop("checked", true);
		else $("#admin-bar #homepage").prop("checked", false);
	});
	// Changement au click de la checkbox homepage
	$("#admin-bar #homepage").change(function() {
		if(this.checked) $("#admin-bar #permalink").val("home");
		else refresh_permalink("#admin-bar");
		tosave();// A sauvegarder
	});
	// Click refresh permalink
	$("#admin-bar #refresh-permalink").click(function() {
		refresh_permalink("#admin-bar");
	});

	// Ajout de l'état de la page
	$("#admin-bar #state").val(state);

	// Ouverture de l'édition du title si en mode responsive
	$("#meta-responsive i").on('click',	function() {
		$("#meta").addClass("tooltip slide-left fire pat").css({"position": "absolute", "top": $("#admin-bar").height()}).fadeToggle();			
	});


	/************** CONTENTEDITABLE **************/

	// spellcheck="false" wrap="off" autofocus placeholder="Enter something ..."
	
	// Pour corriger le drag&drop de texte dans firefox span > div
	$(".editable").replaceWith(function () { 

		// Pour corriger les div qui ne prennent pas toutes la largeur a cause des img en float
		var style = null;
		if($(this).parent().is("article")) style = "width: "+$(this).parent().width()+"px;";//if($(this).parent().children().length <= 1)

		// Clean la dom
		return $("<"+ $(this)[0].tagName.toLowerCase() +"/>", { 
			class: "editable",
			id: this.id,
			html: this.innerHTML,
			style: style,
			placeholder: $(this).attr("placeholder")
		});
	});

	// Rends les textes éditables
	$(".editable").attr("contenteditable","true");


	// Si readonly
	$(".editable.readonly").attr("contenteditable", false);

	// Si champ numerique on ne garde que les chiffre et les points
	$(".editable.number").on("keypress", function(event){//input keyup keydown change
		if(
			(!/^(46|44)$/.test(event.keyCode) && !(event.keyCode >= 48 && event.keyCode <= 57))// Si pas point/virgule et pas chiffre
			||
			(/^(46|44)$/.test(event.keyCode) && /[.,]/.test(this.innerHTML))// Si point/virgule si déjà présent
		) 
		event.preventDefault(); 
	});


	// Place les contenus au-dessus pour les rendre éditables à coup sur
	$(".editable").parent().css("z-index", "10");


	/************** MENU NAV **************/

	// Rends le menu de navigation éditable
	$("header nav li").attr("contenteditable","true").addClass("editable");

	// Ajout d'une zone de drag pour chaque élément
	$("header nav li").prepend("<div class='dragger'></div>");
	
	// Bloc d'option pour le menu de navigation
	addnav = "<div id='add-nav'>";
		addnav+= "<div class='open zone' title='"+ __("Add to menu") +"'><i class='fa fa-fw fa-plus bigger vam'></i></div>";
		addnav+= "<div class='tooltip pat'>";
			addnav+= "<ul class='block unstyled plm man tl'>";
				addnav+= "<li class='add-empty'><div class='dragger'></div>"+__("Empty element")+"</li>";
			addnav+= "</ul>";
		addnav+= "</div>";	
	addnav+= "</div>";	
	$("header nav ul").after(addnav);
	
	// Déplace un élément du menu add vers le menu courant au click sur le +
	$("#add-nav").on("click", ".dragger", function(event) {
		event.preventDefault();  
		//event.stopPropagation();

		if($(this).parent().hasClass("add-empty")) $("header nav ul:first").append($(this).parent().clone());// Copie
		else $($(this).parent()).appendTo("header nav ul:first");// Déplace
		
		// Rends editable les éléments du menu
		$("header nav ul:first li").attr("contenteditable","true").addClass("editable");
		editable_event();

		tosave();// A sauvegarder
			
		// Désactive les liens dans le menu d'ajout
		$("#add-nav ul a").click(function() { return false; });
	});
	
	// Drag & Drop des éléments du menu principal
	$("header nav ul:first").sortable({
		connectWith: "header nav ul",
		handle: ".dragger",
		axis: "x", 
		start: function(event) {
			$("#add-nav .open").addClass("del").children().removeClass("fa-plus").addClass("fa-trash");

			$(".editable").off();//$("body").off(".editable");		
			$("header nav ul:first li").attr("contenteditable","false").removeClass("editable");
		},
		stop: function() {
			$("#add-nav .open").removeClass("del").children().removeClass("fa-trash").addClass("fa-plus");
			
			// Rends editable les éléments du menu
			$("header nav ul:first li").attr("contenteditable","true").addClass("editable");
			editable_event();

			tosave();// A sauvegarder
			
			// désactive les liens dans le menu d'ajout
			$("#add-nav ul a").click(function() { return false; });
		}
	});
	
	// Rend clonable uniquement le bloc vide
	$(".add-empty").draggable({
		connectToSortable: "header nav ul:first",
		helper: "clone",
		revert: "invalid"
    });	

	// Page disponible absente du menu
	add_page = false;
	$("header").on({
		"mouseenter": function(event) {
			if(!add_page)
			{
				// Liste les pages déjà dans le menu
				var menu = {};
				$(document).find("header nav ul a").each(function(index) { menu[index] = $(this).attr('href'); });

				// Cherche dans la base les pages manquantes
				$.ajax({
					url: path+"api/ajax.admin.php?mode=add-nav",
					data: {
						"menu" : menu,
						"nonce": $("#nonce").val()
					},
					success: function(html){ 
						$("#add-nav ul").append(html);		
						
						// Pour éviter de relancer l'ajax
						add_page = true;

						// Rends draggable les pages manquantes du menu
						$("#add-nav ul").sortable({
							connectWith: "header nav ul",
							start: function() {
								$(".editable").off();//$("body").off(".editable");
								$("header nav ul:first li").attr("contenteditable","false").removeClass("editable");									
							},
							stop: function() {
								$("header nav ul:first li").attr("contenteditable","true").addClass("editable");
								editable_event();
								tosave();// A sauvegarder
							}
						});
							
						// désactive les liens
						$("#add-nav ul a").click(function() { return false; });
						
						// Affichage du menu d'ajout
						$("#add-nav").css({opacity: 0, display: 'inline-block'}).animate({opacity: 0.8}, 300);
					}
				});
			}
			else $("#add-nav").css({opacity: 0, display: 'inline-block'}).animate({opacity: 0.8}, 300);
		},
		"mouseleave": function(event) {
			$("#add-nav").fadeOut("fast");
		}
	});


	/************** TOOLBOX **************/

	// Barre d'outils de mise en forme : toolbox
	toolbox = "<ul id='txt-tool' class='toolbox'>";
		toolbox+= "<li><button onclick=\"html_tool('h2')\" id='h2' title=\""+__("Title H2")+"\"><i class='fa fa-fw fa-header'></i></button></li>";
		toolbox+= "<li><button onclick=\"exec_tool('bold')\"><i class='fa fa-fw fa-bold'></i></button></li>";
		toolbox+= "<li><button onclick=\"exec_tool('italic')\"><i class='fa fa-fw fa-italic'></i></button></li>";
		toolbox+= "<li><button onclick=\"exec_tool('underline')\"><i class='fa fa-fw fa-underline'></i></button></li>";
		toolbox+= "<li><button onclick=\"exec_tool('justifyLeft')\" id='align-left'><i class='fa fa-fw fa-align-left'></i></button></li>";
		toolbox+= "<li><button onclick=\"exec_tool('justifyCenter')\" id='align-center'><i class='fa fa-fw fa-align-center'></i></button></li>";
		toolbox+= "<li><button onclick=\"exec_tool('justifyRight')\" id='align-right'><i class='fa fa-fw fa-align-right'></i></button></li>";
		toolbox+= "<li><button onclick=\"exec_tool('justifyFull')\" id='align-justify'><i class='fa fa-fw fa-align-justify'></i></button></li>";
		toolbox+= "<li><button onclick=\"exec_tool('InsertHorizontalRule')\" title=\""+__("Separator")+"\"><i class='fa fa-fw fa-arrows-h'></i></button></li>";
		toolbox+= "<li><button onclick=\"view_source(memo_focus)\" id='view-source' title=\""+__("See the source code")+"\"><i class='fa fa-fw fa-code'></i></button></li>";
		toolbox+= "<li><button onclick=\"dialog_transfert('icon', memo_focus)\" title=\""+__("Icon Library")+"\"><i class='fa fa-fw fa-flag'></i></button></li>";
		toolbox+= "<li><button onclick=\"media(memo_focus, 'intext')\" title=\""+__("Media Library")+"\"><i class='fa fa-fw fa-picture-o'></i></button></li>";
		//toolbox+= "<li><button onclick=\"exec_tool('unlink')\"><i class='fa fa-fw fa-chain-broken'></i></button></li>";
		toolbox+= "<li><button onclick=\"link_option(); $('#txt-tool #option #link').select();\" title=\""+__("Add Link")+"\"><i class='fa fa-fw fa-link'></i></button></li>";
		toolbox+= "<li id='option'>";
			toolbox+= "<input type='text' id='link' placeholder='http://' title=\""+ __("Link") +"\" class='w150p small'>";
			toolbox+= "<a href=\"javascript:target_blank();void(0);\" title=\""+ __("Open link in new window") +"\" id='target-blank' class='o50 ho1'><i class='fa fa-external-link mlt mrt vam'></i></a>";
			toolbox+= "<button onclick=\"exec_tool('CreateLink', $('#txt-tool #option #link').val())\" class='small plt prt'><span>"+ __("Add Link") +"</span><i class='fa fa-fw fa-plus'></i></button>";
		toolbox+= "</li>";
	toolbox+= "</ul>";
	
	// Init la toolbox
	$("body").append(toolbox);
	

	// Action sur les champs éditables
	editable_event = function() {		
		$(".editable").on({
			"focus.editable": function() {// On positionne la toolbox
				console.log("debug bind "+ Math.random());
				memo_focus = this;// Pour memo le focus en cours

				adminbar_height = $("#admin-bar").outerHeight();
				toolbox_height = $("#txt-tool").outerHeight();
				this_offset_top = $(memo_focus).offset().top;

				// Si on est en mode view source on colore le bt view-source
				if($(memo_focus).hasClass("view-source"))
					$("#view-source").addClass("checked");
				else
					$("#view-source").removeClass("checked");

				// Affichage de la boîte à outils texte
				if($("#txt-tool").css("display") == "none")// Si pas visible				
				$("#txt-tool")
					.show()
					.offset({
						top: ( this_offset_top - toolbox_height - 8 ),
						left: ( $(this).offset().left )
					});	

				// Scroll la toolbox si on descend
				$window.on("scroll click.scroll-toolbox", function(event) {
					// Si (Hauteur du scroll + hauteur de la bar d'admin en haut + hauteur de la toolbox + pico) > au top de la box editable = on fixe la position de la toolbox en dessou de la barre admin
					if(($window.scrollTop() + adminbar_height + toolbox_height + 12) > this_offset_top) 
						$("#txt-tool").css({top: adminbar_height + 5 + "px", position: "fixed"});	
					else
						$("#txt-tool").css({top: this_offset_top - toolbox_height - 8 + "px", position: "absolute"});
				});
			},
			"blur.editable": function() {
				if($("#txt-tool:not(:hover)").val()=="") {
					$("#txt-tool").hide();// ferme la toolbox
					$window.off(".scroll-toolbox");// Désactive le scroll de la toolbox
				}
				if($("#unlink:not(:hover)").val()=="") $("#unlink").remove();// Supprime les bouton de unlink
			},
			"dragstart.editable": function() {// Pour éviter les interférences avec les drag&drop d'image dans les champs images
				$("body").off(".editable-img");// Désactive les events image
				$("#img_tool").remove();// Supprime la barre d'outil image
			},
			"dragend.editable": function() {// drop dragend
				// Active les events block image
				editable_img_event();
				body_editable_img_event();

				memo_img = null;
				img_leave();// Raz Propriétés image
			},
			"mouseup.editable": function(event)// Si on click dans un contenu éditable
			{			
				$("#unlink").remove();// Supprime les boutons de unlink
				$("#txt-tool #option").hide();// Cache le menu d'option		

				// @todo voir si le fait de ne pas raz les memo_ ne crée pas de problème colatéraux...
				
				// Mémorise la sélection pour la retrouver au focus après ajout de lien
				memo_selection = window.getSelection();				
				if(memo_selection.anchorNode) {
					memo_range = memo_selection.getRangeAt(0);
					memo_node = selected_element(memo_range);//memo_selection.anchorNode.parentElement memo_range.commonAncestorContainer.parentNode
				}
				

				// Si on est sur un h2 on check l'outil dans la toolbox
				if($(memo_node).closest("h2").length) $("#txt-tool #h2").addClass("checked");
				else $("#txt-tool #h2").removeClass("checked");
					

				// Désélectionne les alignements
				$("[class*='fa-align']").parent().removeClass("checked");
				
				var align = null;

				// On cherche le type d'alignement si on est dans un bloc aligné avec les style
				if($(memo_node).closest("div [style*='text-align']")[0]) var align = $(memo_node).closest("div [style*='text-align']").css("text-align");
				
				// On cherche le type d'alignement si on est dans un bloc aligné avec align=
				if($(memo_node).closest("div [align]")[0]) var align = $(memo_node).closest("div [align]").attr("align");
								
				// On check le bon alignement
				if(align) $("#align-"+align).addClass("checked");


				// Si on sélectionne un contenu
				if(memo_selection.toString().length > 0)
				{
					// Si on est sur un lien
					if($(memo_node).closest("a").length)
					{
						// Ouverture du menu lien en mode modif
						link_option();
						
						// Positionnement de la croix unlink
						var left = event.clientX + 5;
						var top = $(memo_node).closest('a').offset().top - 5; // $(memo_node).closest('a').position().top - 5 // event.clientY

						// Croix pour supprimer le lien : unlink
						$("body").append("<a href=\"javascript:exec_tool('unlink');void(0);\" id='unlink'><i class='fa fa-close' style='position: absolute; left:"+ left +"px; top:"+ top +"px;' title='"+ __("Remove the link from the selection") +"'></i></a>");
					}
				}
				//else memo_selection = memo_range = memo_node = null;// RAZ Sélection		
			}
			
		});
	}

	// Exécute l'event sur les champs éditables
	editable_event();

	
	// Supprime la mise en forme des contenus copier/coller [contenteditable]
	$(".editable").on({"paste": function(event) {
		event.preventDefault();
		var text = (event.originalEvent || event).clipboardData.getData("text/plain") || prompt(__("Paste something..."));// text/html
		exec_tool("insertText", text);// insertHTML insertText
	}});


	// Action sur le input de lien si keyup Enter
	$("#txt-tool #option #link").keyup(function(event) { if(event.keyCode == 13) exec_tool("CreateLink", $("#txt-tool #option #link").val()) });

	// Si focus dans les options de lien on supp le bt unlink
	$("#txt-tool #option").on("click", function() { $("#unlink").remove() });



	/************** IMAGES DANS LES BLOCS TEXTE **************/
	memo_img = null;

	// @todo: voir si on ne peux pas le déplacer sur le blur des event .editable
	$("body").click(function(event) {
		// Si on n'est pas sur une image dans un contenu éditable on supp la toolbox image
		if(!$(event.target).is('.editable img')) img_leave();// Raz Propriétés image
		
		// Supprime le layer de redimensionnement d'image
		if($("#resize-tool").html() != undefined && !$(event.target).closest("#resize-tool").is('#resize-tool')) $("#resize-tool").remove();
		
	});

	// Affiche les options de gestion d'alignement sur les images ajouter
	$(".editable").on("click", "img", function(event) {
		
		event.stopPropagation();
		
		// Mémorise l'image sélectionnée
		memo_img = this;		
		
		// Si Chrome on ajoute le resizer jquery
		if($.browser.webkit)
		{
			// Rend l'image resizeable
			$(this).resizable({aspectRatio: true});
			
			// Pour styler le block image sélectionné (Même alignement et display entre le div et l'img)
			$(this).parent(".ui-wrapper").addClass($(this).attr('class'));
			$(this).parent(".ui-wrapper").css('display', $(this).css('display'));
		}
		
		// Boîte à outils image
		option = "<ul id='img_tool' class='toolbox'>";
			option+= "<li><button onclick=\"img_position('fl')\"><i class='fa fa-fw fa-align-left'></i></button></li>";
			option+= "<li><button onclick=\"img_position('center')\"><i class='fa fa-fw fa-align-center'></i></button></li>";
			option+= "<li><button onclick=\"img_position('fr')\"><i class='fa fa-fw fa-align-right'></i></button></li>";
			option+= "<li><button onclick=\"img_remove()\" title=\""+ __("Delete image") +"\"><i class='fa fa-fw fa-close'></i></button></li>";
		option+= "</ul>";

		$("body").append(option);

		$("#img_tool")
			.show()
			.offset({
				top: ( $(this).offset().top - $("#img_tool").height() - 8 ),
				left: ( $(this).offset().left )
			});
	});



	/************** IMAGES SEUL **************/
	
	// On highlight les zones où l'on peut droper des fichiers
	body_editable_img_event = function() {
		$("body")
			.on({
				"dragover.editable-img": function(event) {// Highlight les zone on hover dragover/dragenter
					event.stopPropagation();
					$(".editable").off();//$("body").off(".editable");// Désactive les events sur les contenu éditables
					$(".editable-img").addClass("drag-zone");
					$(".editable-img img").addClass("drag-img");
			},
				"dragleave.editable-img": function(event) {// Clean les highlight on out
					event.stopPropagation();
					editable_event();// Active les events sur les contenus éditables
					$(".editable-img").removeClass("drag-zone");
					$(".editable-img img").removeClass("drag-img");
				}
			});
	}

	// Exécute l'event sur le body pour les images
	body_editable_img_event();


	// Rends éditables les images
	$(".editable-img").append("<div class='open-dialog-media'><i class='fa fa-upload'></i> "+__("Upload file")+"</div>");
	editable_img_event = function() {
		$(".editable-img")
			.on({
				"dragover.editable-img": function(event) {// Highlight la zone on hover
					event.preventDefault();  
					event.stopPropagation();
					$(this).addClass("drag-over");
					$("img", this).addClass("drag-img");
				},
				"dragleave.editable-img": function(event) {// Clean le highlight on out
					event.preventDefault();  
					event.stopPropagation();
					$(this).removeClass("drag-over");
					$("img", this).removeClass("drag-img");
				},
				"drop.editable-img": function(event) {// On lache un fichier sur la zone
					event.preventDefault();  
					event.stopPropagation();
					$(this).removeClass("drag-over");
					$("img", this).removeClass("drag-img");
					
					// Upload du fichier dropé
					if(event.originalEvent.dataTransfer) upload($(this), event.originalEvent.dataTransfer.files[0], true);
				},
				"mouseenter.editable-img": function(event) {// Hover zone upload		
					$(this).addClass("drag-over");
					$("img", this).addClass("drag-img");
					$(".open-dialog-media", this).fadeIn("fast");
				},
				"mouseleave.editable-img": function(event) {// Out
					$(this).removeClass("drag-over");
					$("img", this).removeClass("drag-img");
					$(".open-dialog-media", this).hide();
				},
				"click.editable-img": function(event) {// Ouverture de la fenêtre des médias
					// Masque le hover de l'image sélectionnée
					$(this).removeClass("drag-over");
					$("img", this).removeClass("drag-img");
					$(".open-dialog-media", this).hide();
					
					// Ouvre la dialog de media
					media(this, 'isolate');
					return false;
				}
			});
	}

	// Exécute l'event sur les images
	editable_img_event();



	/************** IMAGES BACKGROUND **************/
	
	// Ajout un fond hachuré au cas ou il n'y ai pas de bg 
	$("[data-editable='bg']").addClass("editable-bg");
	$("[data-editable='bg']").append("<a href=\"javascript:void(0)\" class='open-dialog-media'><i class='fa fa-picture-o'></i> "+__("Change the background image")+"</a>");

	// Rends éditables les images en background
	editable_bg_event = function() {
		$("[data-editable='bg']")
			.on({
				"mouseenter.bg": function(event) {// Hover zone upload		
					$("> .open-dialog-media", this).fadeIn("fast");
				},
				"mouseleave.bg": function(event) {// Out
					$("> .open-dialog-media", this).fadeOut("fast");
				}
			});		
	}

	// Exécute l'event sur les images
	editable_bg_event();

	// Ouverture de la fenêtre des médias
	$("[data-editable='bg'] > .open-dialog-media").click(function() {
		media($(this).parent()[0], 'bg');
	});


	/************** CHAMPS HIDDEN **************/
	
	// Transforme les inputs hidden en texte visible
	$(".editable-hidden").hide().attr("type","text");
	$(".editable-hidden").each(function() {
		$(this).attr("placeholder", $(this).attr("id")).attr("title", $(this).attr("id"));
	});
	$("label.none").slideDown();
	$(".editable-hidden").slideDown();


	/************** CHAMPS SELECT **************/
	$(".editable-select").attr("data-option", function(i, data) {
		
		// Option sélectionnée
		var selected = $(".editable-select").attr("data-selected");

		// Extraction du json
		var json = jQuery.parseJSON(data);							
		
		// Création des options avec le json
		var html = '';
		$.each(json, function(cle, val){ html += '<option value="'+ cle +'"'+(cle == selected?" selected":"")+'>'+ val +'</option>'; });
		
		// Les attribue
		var attr = {};
		$.each(this.attributes, function() { attr[this.name] = this.value; });
		
		// Remplace les select
		$(".editable-select").replaceWith($("<select/>", attr).html(html));
	})


	/************** CHAMPS CHECKBOX **************/
	$(".editable-checkbox, [for]").on("click", function(event) {//@todo voir si le [for] ne crée pas de bug collatérale
		if($(this).attr("for")) id = $(this).attr("for");
		else id = this.id;

		if($("#"+id).hasClass("fa-check")) $("#"+id).removeClass("fa-check yes").addClass("fa-times no");
		else $("#"+id).removeClass("fa-times no").addClass("fa-check yes");
	})


	/************** SLIDESHOW **************/

	// Rends les slideshow éditable
	//$(".editable-slide")


	// Rends les icônes éditable
	//$(".editable-icon")



	/************** USERS **************/

	// Ouverture de l'admin des users
	$("#user i").on("click mouseenter",	// touchstart click
		function(event) {

			event.stopPropagation();
			event.preventDefault();			

			if(!$("#user .absolute").length && event.type == "mouseenter")
			{
				$.ajax({
					url: path+"api/ajax.php?mode=user",
					data: {"nonce": $("#nonce").val()},
					success: function(html){ 
						$("#user").append(html);						
						
						// Pour fermer l'admin user quand on click en dehors
						close = false;
						$(document).on("click",	
							function(event) {
								if(!$(event.target).parents().is("#user .absolute") && $("#user .absolute").is(":visible") && close == false)//event.type == 'click'
									if($("#user button.to-save").length || $("#user button i.fa-spin").length)// Si fiche pas sauvegardé on shake
										$("#user .absolute > div").effect("highlight");
									else
										$("#user .absolute").fadeOut("fast", function(){ close = true; });
							}
						);
					}
				});
			}
			else if($("#user .absolute").length && !$("#user .absolute").is(":visible")&& close == true)// Si on click et que l'ajax a déjà été fait
			{
				close = true;
				$("#user .absolute").fadeIn("fast", function(){ close = false; });
			}
			else if(event.type == 'click' && $("#user .absolute").is(":visible") && close == false )// Si on click sur le bt user de l'admin-bar
			{
				$("#user .absolute").fadeOut("fast", function(){ close = true; });
			}
		}
	);


	/************** EXECUTION DES FONCTIONS D'EDITION DES PLUGINS **************/
	$(edit).each(function(key, funct){
		funct();
	});


	/************** ACTION **************/

	// Si on ferme l'admin
	$("#close").click(function() {	
		reload();
	});

	// On rétablit la page en mode visiteur
	$("#preview").click(function() {
		save(function() {
			reload();
		});				
	});

	// Si on sauvegarde
	$("#save").click(function() {	
		save();
	});

	// Capture des actions au clavier
	$(document).keydown(function(event) 
	{
		// Si on appuie sur ctrl + s = sauvegarde
		if((event.ctrlKey || event.metaKey)) {
			if(String.fromCharCode(event.which).toLowerCase() == 's'){
				event.preventDefault();
				save();		
			}
		}
		// Si on utilise tape du texte dans un contenu éditable on change le statut du bouton sauvegardé
		else if(event.target.className == 'editable' || event.target.id == 'title' || event.target.id == 'description' || event.target.id == 'permalink') 
		{
			if(String.fromCharCode(event.which).match(/\w/) || event.keyCode == 13)// Caractères texte ou entrée
			{
				tosave();// A sauvegarder
			}
			else if(event.keyCode == 46)// Suppr
			{
				tosave();// A sauvegarder
				
				// Si on est sur une image est que l'on clique sur Supp on supprime l'image du contenu
				if(memo_img) {
					event.preventDefault();
					img_remove();
				}
			}			
		}
	});

	// On change une info dans un menu select
	$("#admin-bar select").change(function() {
		tosave();// A sauvegarder
	});

	// Désactive le click pour ne pas relancer l'admin
	$(".bt.edit").off("click");

});	