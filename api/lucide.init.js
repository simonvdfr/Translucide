// Traduction
translation = {
	"" : {"fr" : ""},
	"Edit the content of the page" : {"fr" : "Modifier le contenu de la page"},
	"Add a content" : {"fr" : "Ajouter un contenu"},
	"Thank you to select a template" : {"fr" : "Merci de s\u00e9lectionner un model de page"},
	"Back to Top" : {"fr" : "Retour en haut"},
	"Error" : {"fr" : "Erreur"},
	"Validate the connection in the popup" : {"fr" : "Valider la connexion dans la fen\u00eatre"},
	"State" : {"fr" : "Etat"},
	"Active" : {"fr" : "Actif"},
	"Deactivate" : {"fr" : "D\u00e9sactiv\u00e9"},
	"Visitors do not see this content" : {"fr" : "Les visiteurs ne voient pas ce contenu"},
	"Disconnection" : {"fr" : "D\u00e9connexion"}
}

// Fonction d'ajout d'une liste de traduction
add_translation = function(new_translation) {
	$.each(new_translation, function(i, val) { 
		translation[i] = val; 
		translation[i.toLowerCase()] = val;// Lowercase les index de traduction
	});
}

// Ajoute la traduction courante
add_translation(translation);


// Obtenir le contenu d'un cookie
get_cookie = function(key) {
	var value = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
	return value ? value[2] : '';
}

// Crée un cookie
set_cookie = function(key, val, days) {
	var expire = new Date();
	expire.setTime(expire.getTime() + (days*24*60*60*1000));
	document.cookie = key + "="+ val +"; expires="+ expire.toGMTString() +"; path=/";
	if(days == 0) document.cookie = key + "="+ val +"; expires="+ expire.toGMTString() +"; path=/; domain="+ location.hostname +";";
}


// Detect si on est sur mobile
ismobile = function(){
	if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) return true; else return false;
}


// Déconnexion
logout = function() {
	$.ajax({
		url: path+"api/ajax.php?mode=logout",
		success: function(html){ 
			$("body").html(html);// Retour
			reload();// Recharge la page	
		}
	});
}


// Traduit un texte
__ = function(txt) {
	if(typeof translation[txt] !== 'undefined' && translation[txt][get_cookie('lang')]) return translation[txt][get_cookie('lang')];	
	else return txt;
}


// Affichage d'un message d'erreur
error = function(txt){		
	$("#error, #under-error").remove();

	// Ajout du fond gris
	$("body").append("<div id='under-error' class='none absolute' style='background-color: rgba(200, 200, 200, 0.8); z-index: 101; top: 0; left: 0; right: 0;'></div>");
		
	// Donne la bonne taille au fond gris et l'affiche
	$("#under-error")
	.css({
		width: $(document).width(),
		height: $(document).height()
	})
	.fadeIn();

	// Box avec le message d'erreur
	$("body").append("<div id='error' class='ui-state-error ui-corner-all pointer pam absolute no tc'><i class='fa fa-exclamation-triangle mrs'></i>" + txt + "<i class='fa fa-times absolute big grey o50' style='top: -8px; right: -8px;'></i></div>");
	var height = $("#error").outerHeight();
	
	// Affichage de la box
	$("#error")
		.css({
			zIndex: 102,
			opacity: 0,
			top: -height,
			left: (($(window).width() - $("#error").outerWidth()) / 2),
		})
		.animate({
			opacity: 1,
			top: ($(window).scrollTop() + (($(window).height() - height) / 2))
		}, 500)
		.on("click", function(){ 
			$("#error, #under-error").fadeOut("fast", function(){ $(this).remove() });
		});
}

// Affichage d'un message positif
light = function(txt){		
	$("#highlight").remove();
	
	// Box avec le message d'information
	$("body").append("<div id='highlight' class='ui-state-highlight ui-corner-all pointer pam absolute tc'><i class='fa fa-info-circle color mrs'></i>" + txt + "</div>");
	var height = $("#highlight").outerHeight();
	
	// Affichage
	$("#highlight")
		.css({
			opacity: 0,
			top: -height,
			left: (($(window).width() - $("#highlight").outerWidth()) / 2),
		})
		.animate({
			opacity: 1,
			top: ($(window).scrollTop() + (($(window).height() - height) / 2))
		}, 500)
		.on("click", function(){ $(this).fadeOut("fast", function(){ $(this).remove() }); });
}


// Url en cours nettoyé
clean_url = function() {
	return location.protocol +'//'+ location.host + location.pathname + location.search;
}

// Recharge la page en cours
reload = function() {
	document.location.href = clean_url();
}


// Liste des fonctions d'édition des plugin
edit = [];


// Formulaire d'ajout d'une page
add_content = function()
{	
	//@todo envoyer les bonnes informations si on crée une page ou si c'est un article ou autre
	$.ajax({url: path+"api/ajax.admin.php?mode=add-content&callback=add_content"})
		.done(function(html) {	
			// Contenu de la dialog d'ajout
			$("body").append(html);		
			
			// Création de la dialog
			$(".dialog-add").dialog({
				modal: true,
				width: "60%",
				buttons: {
					"OK": function() {
						// Dans quel onglet on se situe
						type = $(".ui-tabs-nav .ui-state-active").data("filter");

						if(!$(".dialog-add #add-"+type+" #tpl").val()) error(__("Thank you to select a template"));
						else {
							$.ajax({
								type: "POST",
								url: path + "api/ajax.admin.php?mode=insert",
								data: {
									"title": $(".dialog-add #add-"+type+" #title").val(),
									"tpl": $(".dialog-add #add-"+type+" #tpl").val(),
									"permalink": $(".dialog-add #add-"+type+" #permalink").val(),
									"type": type,
									"nonce": $("#nonce").val()// Pour la signature du formulaire
								}
							})
							.done(function(html) {		
								$(".dialog-add").dialog("close");
								$("body").append(html);
							});
						}
					}
				},
				create: function() 
				{
					// Création des onglets
					$(".dialog-add").tabs();

					// Place les onglets à la place du titre de la dialog
					$(".ui-dialog-title").html($(".ui-tabs-nav")).parent().addClass("ui-tabs");
					
				},
				close: function() {
					$(".dialog-add").remove();					
				}
			});
		});
}


// Crée le permalink à partir du titre de la page
refresh_permalink = function(target) {
	// Animation de chargement
	$(target+" #refresh-permalink i").addClass("fa-spin");

	// Récupère l'url encodée
	$.ajax({
		type: "POST",
		url: path+"api/ajax.admin.php?mode=make-permalink",
		data: {"title": $(target+" #title").val(), "type": type, "nonce": $("#nonce").val()},
		success: function(url){ 
			$(target+" #refresh-permalink i").removeClass("fa-spin");
			$(target+" #permalink").val(url);

			$(target+" #homepage").prop("checked", false);// On uncheck l'option homepage
			
			if($("#admin-bar").length) tosave();// A sauvegarder
		}
	});
}


// Renvoi un mot de passe
$.fn.make_password = function() {
	var $this = this;

	// Animation de chargement
	$(".fa-refresh").addClass("fa-spin");

	// Récupère un password
	$.ajax({
		type: "POST",
		url: path+"api/ajax.php?mode=make-password",
		data: {"nonce": $("#nonce").val()},
		success: function(password){ 
			$(".fa-refresh").removeClass("fa-spin");
			$this.attr("type","text").val(password);
		}
	});
}


// Fermeture de la dialog de connexion
close_dialog_connect = function() 
{	
	if($("#dialog-connect").length) $("#dialog-connect").fadeOut().dialog("close");
}

// Recharge la page et lance le mode édition
reload_edit = function() {	
	edit_launcher("reload_edit");
}

// Lance le mode édition
edit_launcher = function(callback) 
{	
	close_dialog_connect();

	// Si le mode édition n'est pas déjà lancé
	if(!$("#admin-bar").length) 
	{
		$.ajax({url: path+"api/ajax.admin.php?mode=edit&type="+type+(callback?"&callback="+callback:""), cache: false})
		.done(function(html) {				
			$("body").append(html);
		});
	}
};



$(document).ready(function()
{
	// On met en background les images data-bg
	$("[data-bg]").css("background-image", function() {
		return "url(" + $(this).attr("data-bg") + ")";
	});

	// Bouton ajout de page/article
	$("body").prepend("<a href='javascript:void(0);' class='bt fixed add' title='"+ __("Add a content") +"'><i class='fa fa-fw fa-plus bigger vam'></i></a>");
	
	// Bouton d'édition si la page existe dans la base
	if(typeof state !== 'undefined' && state) $("body").prepend("<a href='javascript:void(0);' class='bt fixed edit' title='"+ __("Edit the content of the page") +"'><i class='fa fa-fw fa-pencil bigger vam'></i></a>");

	// Page désactivé => message admin
	if(typeof state !== 'undefined' && state && state != "active" && get_cookie("auth").indexOf("edit-page")) {
		$("body").append("<a href='javascript:void(0);' class='bt fixed construction bold' title=\""+ __("Visitors do not see this content") +"\"><i class='fa fa-fw fa-user-secret bigger vam no'></i>"+ __("State") +" : "+ __(state) +"</a>");
		$(".bt.fixed.construction").click(function(){ $(this).slideUp(); });
	}
	
	// Bouton pour remonter en haut au scroll
	$("body").prepend("<a href='javascript:void(0);' class='bt fixed top' title='"+ __("Back to Top") +"'><i class='fa fa-fw fa-chevron-up bigger vam'></i></a>");	


	// Smoothscroll to top
	$("a.bt.fixed.top").click(function() {
		$("html, body").animate({scrollTop: 0}, 300);
		return false;
	});

	// Bind le bouton d'édition
	$("a.bt.edit").click(function() 
	{
		// Si la page n'est pas activée  et que l'on n'est pas admin on callback un reload
		edit_launcher(((state != "active" && !get_cookie("auth").indexOf("edit-page")) ? "reload_edit":"edit_launcher"));

		$("a.bt.fixed.edit").fadeOut();
		$("a.bt.fixed.add").fadeOut();
	});	

	// Bind le bouton d'ajout
	$("a.bt.add").click(function() 
	{
		add_content();
	});	


	hover_add = false;

	// Affichage du bouton add
	$("a.bt.fixed.edit").hover(
		function() {
			$("a.bt.fixed.add").css("right", parseInt($("a.bt.fixed.edit").css("right")) + "px");// même niveau right
			$("a.bt.fixed.add").fadeIn();//fadeIn
			$("a.bt.fixed.add").css("bottom", parseInt($("a.bt.fixed.edit").css("bottom")) + $("a.bt.fixed.edit").outerHeight() + "px");// au dessus bt edit
			hover_add = true;
		},
		function() {
			hover_add = false;
			setTimeout(function() { if(!hover_add) $("a.bt.fixed.add").fadeOut("fast");	}, 1000);
	});
	
	// Onhover bouton add on le conserve visible
	$("a.bt.fixed.add").hover(
		function() { hover_add = true; },
		function() {
			hover_add = false;
			setTimeout(function() { if(!hover_add) $("a.bt.fixed.add").fadeOut("fast");	}, 1000);
	});


	$window = $(window);
	
	// Si on a une scrollbar
	if ($("body").height() > $window.height()) 
	{        
		// Au scroll on affiche ou pas les boutons flottants
		$window.on("scroll", function() 
		{
			// Affichage du bouton scroll to top
			if($window.scrollTop() > 50) $("a.bt.fixed.top").show();
			else
			{
				$("a.bt.fixed.top").fadeOut("fast", function(){
					$("a.bt.fixed.edit").css("right","20px");
				});
			}

			// Si la barre d'administration n'est pas ouverte et la dialog de connexion inexistante
			if(!$("#admin-bar").length && !$("#dialog-connect").length)
			{
				// Affichage du bouton d'édition  avec 50px de marge OU si on est admin
				if(($(document).height() - 50) <= ($window.height() + $window.scrollTop()) || get_cookie("auth").indexOf("edit-page")) 
				{	
					// Décale l'icone si il y a le bt to top
					if($("a.bt.fixed.top").css("display") != "none") $("a.bt.fixed.edit").css("right","70px");
					
					// Affichage du bouton d'édition
					$("a.bt.fixed.edit").fadeIn("slow");				
				}
				else if($("a.bt.fixed.edit").css("display") == "block")
					$("a.bt.fixed.edit").fadeOut();
			}
		});
    }
	else if(!$("#admin-bar").length && !$("#dialog-connect").length)// On affiche au bout de x seconde le bouton d'édition
	{
		if(typeof state !== 'undefined')
			if(state) $("a.bt.fixed.edit").delay("2500").fadeIn("slow");
			else $("a.bt.fixed.add").delay("2500").fadeIn("slow");	
	}

	
	// Verifi les droits, si l'admin n'est pas lancé
	if(get_cookie("auth").indexOf("edit-page") && !$("#admin-bar").length && !$("#dialog-connect").length)
	{
		// Si on appuie sur la touche haut ou bas on ouvre le bouton d'édition
		$(document).keyup(function(event) {			
			if((event.which == 38 || event.which == 40) && !$("#admin-bar").length) $("a.bt.fixed.edit").fadeIn();			
		});
	}
	
});	