// Traduction
translation = {
	"" : {"fr" : ""},
	"Edit the content of the page" : {"fr" : "Modifier le contenu de la page"},
	"Add content" : {"fr" : "Ajouter un contenu"},
	"Thank you to select a template" : {"fr" : "Merci de sélectionner un model de page"},
	"Back to Top" : {"fr" : "Retour en haut"},
	"Error" : {"fr" : "Erreur"},
	"Activation status" : {"fr" : "Etat d'activation"},
	"Active" : {"fr" : "Actif"},
	"Deactivate" : {"fr" : "Désactivé"},
	"Visitors do not see this content" : {"fr" : "Les visiteurs ne voient pas ce contenu"},
}

// Fonction d'ajout d'une liste de traduction
add_translation = function(new_translation) {
	Object.keys(new_translation).forEach(function(i){
		translation[i] = new_translation[i]; 
		translation[i.toLowerCase()] = new_translation[i];// Lowercase les index de traduction
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


// Traduit un texte
__ = function(txt) {	
	if(typeof txt == 'object') {// Si l'argument txt est un tableau de traduction
		add_translation(txt);// On ajoute la traduction
		var txt = Object.keys(txt)[0];// On met la clé dans la variable
	}

	if(typeof translation[txt] !== 'undefined' && translation[txt][get_cookie('lang')]) 
		return translation[txt][get_cookie('lang')];	
	else 
		return txt;
}


// Déconnexion
logout = function() {
	var xhr = new XMLHttpRequest();
	xhr.open('GET', path+'api/ajax.php?mode=logout', true);
	xhr.onload = function() {
		document.body.insertAdjacentHTML('beforeend', this.response);
		reload();// Recharge la page	
	}
	xhr.send();
}


// Affichage d'un message d'erreur
error = function(txt, fadeout){		
	$("#error, #under-error").remove();

	// Ajout du fond gris
	$("body").append("<div id='under-error' class='none absolute' style='background-color: rgba(200, 200, 200, 0.8); z-index: 1001; top: 0; left: 0; right: 0;'></div>");
		
	// Donne la bonne taille au fond gris et l'affiche
	$("#under-error")
	.css({
		width: $(document).width(),
		height: $(document).height()
	})
	.fadeIn();

	// Box avec le message d'erreur fa-times
	$("body").append("<div id='error' class='pointer pam absolute no tc'><i class='fa fa-attention mrs'></i>" + txt + "<i class='fa fa-cancel absolute big grey o50' style='top: -8px; right: -8px;'></i></div>");
	var height = $("#error").outerHeight();
	
	// Affichage de la box
	$("#error")
		.css({
			zIndex: 1002,
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

	// Disparition au bout de x seconde
	if(fadeout)
	{
		window.setTimeout(function(){ 
			$("#error, #under-error").fadeOut("400", function(){ $(this).remove(); });
		}, fadeout);
	}
}

// Affichage d'un message positif
light = function(txt, fadeout){		
	$("#highlight").remove();
	
	// Box avec le message d'information
	$("body").append("<div id='highlight' class='pointer pam absolute tc'><i class='fa fa-info-circled color mrs'></i>" + txt + "</div>");
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

	// Disparition au bout de x seconde
	if(fadeout)
	{
		window.setTimeout(function(){ 
			$("#highlight").fadeOut("400", function(){ $(this).remove(); });
		}, fadeout);
	}
}


// Url en cours nettoyé
clean_url = function() {
	return location.protocol +'//'+ location.host + location.pathname + location.search;
}

// Recharge la page en cours
reload = function() {
	document.location.href = clean_url();
}


// Liste des fonctions d'édition
edit = [];
after_save = [];
before_save = [];
before_data = [];


// Formulaire d'ajout d'un contenu
add_content = function()
{	
	var xhr = new XMLHttpRequest();
	xhr.open('GET', path+'api/ajax.admin.php?mode=add-content&callback=add_content', true);
	xhr.onload = function() {
		//document.body.innerHTML += this.response;
		//document.body.insertAdjacentHTML('beforeend', this.responseText);
		//$("body").append(this.response);

		// Dialog d'ajout
		// Pour bien exécuter le js injecté par l'Ajax ES6
		const response = document.createRange().createContextualFragment(this.response);
		document.body.append(response);
	}
	xhr.send();
}


// Déplacer dans add-content de ajax.admin.php et aussi dans edit.js (dupliquer mais pas utile en front)
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

// @todo repasser dans edit.js car pas suffisament utiliser pour etre charger à chaque fois & supp de install.php
// Renvoi un mot de passe
$.fn.make_password = function() {
	var $this = this;

	// Animation de chargement
	$(".fa-arrows-cw").addClass("fa-spin");

	// Récupère un password
	$.ajax({
		type: "POST",
		url: path+"api/ajax.php?mode=make-password",
		data: {"nonce": $("#nonce").val()},
		success: function(password){ 
			$(".fa-arrows-cw").removeClass("fa-spin");
			$this.attr("type","text").val(password);
		}
	});
}

// Recharge la page et lance le mode édition
reload_edit = function() {	
	edit_launcher("reload_edit");
}

// Lance le mode édition
edit_launcher = function(callback) 
{	
	if($("#dialog-connect").length) $("#dialog-connect").fadeOut().dialog("close");

	// Si le mode édition n'est pas déjà lancé
	if(!$("#admin-bar").length) 
	{
		$.ajax({url: path+"api/ajax.admin.php?mode=edit&type="+type+(callback?"&callback="+callback:""), cache: false})
		.done(function(html) {				
			$("body").append(html);
		});
	}
};



$(function()
{
	$root = $("html, body");
	$body = $("body");
	$window = $(window);

	// Détecte si le navigateur gère nativement les LAZYLOAD des images, si oui on assigne les images au src
	if('loading' in HTMLImageElement.prototype) {
		$.each($("img[loading='lazy']"), function() 
    	{
			$(this).attr("src", $(this).data("src")).removeAttr("data-src");
		});
	}

	// Sélectionne les animations, les backgrounds et les images en lazy loading si le navigateur ne les gère pas nativement
    $animation = $(".animation, [data-lazy='bg'], img[loading='lazy']:not([src=''])");

	hover_add = false;
	edit_on = false;



	// MODULE
	// Masque le module/bloc duplicable vide de défaut
	$(".module > li:last-child").hide();



	// BOUTON EDITION | AJOUT
	// Bouton ajout de page/article
	$("body").prepend("<a href='javascript:void(0);' class='bt fixed add' title='"+ __("Add content") +"'><i class='fa fa-fw fa-plus bigger vam'></i></a>");

	// Bind le bouton d'ajout
	$("a.bt.add").click(function(){
		add_content();
	});	


	// Bouton d'édition ou de connexion si la page existe dans la base
	if(get_cookie("auth").indexOf("edit-page") > 0) var icon_edit = "pencil"; else var icon_edit = "key";// logé ou pas ?
	if(typeof state !== 'undefined' && state) $("body").prepend("<a href='javascript:void(0);' class='bt fixed edit' title='"+ __("Edit the content of the page") +"'><i class='fa fa-fw fa-"+ icon_edit +" bigger vam'></i></a>");

	// Bind le bouton d'édition
	$("a.bt.edit").click(function() 
	{
		// Si la page n'est pas activée et que l'on n'est pas admin on callback un reload
		edit_launcher(((state != "active" && get_cookie("auth").indexOf("edit-page") < 0) ? "reload_edit":"edit_launcher"));

		$("a.bt.fixed.edit").fadeOut();

		// Force l'affichage du bouton  +
		$("a.bt.fixed.add").show().css({"bottom":"10px", "opacity":".2"});
		edit_on = true;
	});	


	// Mode édition au ctrl+e
	$(document).keydown(function(event) 
	{
		if(!$("#admin-bar").length)// Admin pas lancé
		{
			if(event.ctrlKey || event.metaKey)
			if(String.fromCharCode(event.which).toLowerCase() == 'e') {
				event.preventDefault();
				$("a.bt.edit").click();
			}
		}
	});


	// Affichage du bouton add au survole du bt edition
	$("a.bt.fixed.edit").hover(
		function() {
			$("a.bt.fixed.add").fadeIn();//fadeIn
			$("a.bt.fixed.add").css("bottom", parseInt($("a.bt.fixed.edit").css("bottom")) + $("a.bt.fixed.edit").outerHeight() + "px");// au dessus bt edit
			hover_add = true;
		},
		function() {
			hover_add = false;
			setTimeout(function() { if(!hover_add && !edit_on) $("a.bt.fixed.add").fadeOut("fast");	}, 1000);
	});
	
	// Onhover bouton add on le conserve visible
	$("a.bt.fixed.add").hover(
		function() { hover_add = true; },
		function() {
			hover_add = false;
			setTimeout(function() { if(!hover_add && !edit_on && $("a.bt.fixed.edit").length) $("a.bt.fixed.add").fadeOut("fast");	}, 1000);
	});


	// AFFICHAGE DU BOUTON D'ÉDITION || AJOUT
	// Si bt edit activé dans config & pas de barre d'admin & pas de dialog de connexion & state de défini
	if(typeof bt_edit !== 'undefined'
		&& !$("#admin-bar").length
		&& !$("#dialog-connect").length
		&& typeof state !== 'undefined') 
	{
		if(state) $("a.bt.fixed.edit").delay("2000").fadeIn("slow");// get_cookie("auth").indexOf("edit-page")
		else $("a.bt.fixed.add").delay("2000").fadeIn("slow");	
	}



	// PAGE DÉSACTIVÉ => message admin
	if(typeof state !== 'undefined' && state && state != "active" && get_cookie("auth").indexOf("edit-page") > 0) {
		$("body").append("<a href='javascript:void(0);' class='bt fixed construction bold' title=\""+ __("Visitors do not see this content") +"\"><i class='fa fa-fw fa-attention vam no'></i> "+ __("Activation status") +" : "+ __(state) +"</a>");
		$(".bt.fixed.construction").click(function(){ $(this).slideUp(); });
	}


	
	// BT TOP activé dans la config
	if(typeof bt_top !== 'undefined')
	{
		// Bouton pour remonter en haut au scroll
		$("body").prepend("<a href='javascript:void(0);' class='bt fixed top' title='"+ __("Back to Top") +"'><i class='fa fa-fw fa-up-open bigger'></i></a>");	

		// Smoothscroll to top
		$("a.bt.fixed.top").click(function() {
			$root.animate({scrollTop: 0}, 300);
			return false;
		});
	}



	// SMOOTHSCOLL SUR LES ANCRES // pas sur les onglet d'ajout de contenu
	$(document).on("click", "a[href^='#']:not(.ui-tabs-anchor)", function(event) {
		event.preventDefault();

		if(typeof lucide === 'undefined')// Si pas en mode edit
		{
			var hashtag = $(this).attr("href").substr(1);
			var name_exist = $('[name="'+hashtag+'"]');

			// name || id ?
			if(name_exist.length) 
				var anchor = name_exist;
			else {
				var id_exist = $('[id="'+hashtag+'"]');
				if(id_exist.length) var anchor = id_exist;
			}

			console.log(anchor);

			// Ancre existante on scroll
			if(anchor != undefined)
			$root.animate({ 
				scrollTop: anchor.offset().top
			}, 800, "linear");
		}
	});



	// ACTION SUR LES ONSCROLL
	$window.on("scroll resize load", function ()
	{

		// AFFICHAGE DU BOUTON SCROLL TO TOP
		if(typeof bt_top !== 'undefined')
		{
			if($window.scrollTop() > 50) $("a.bt.fixed.top").show();
			else $("a.bt.fixed.top").fadeOut("fast");
		}



		// ANIMATION SUR LES CONTENUS // CHARGEMENT DES LAZYLOAD
		window_height = $window.outerHeight();//height
    	window_top = $window.scrollTop();
    	window_bottom = (window_top + window_height);


    	$.each($animation, function() 
    	{
    		var $element = $(this);
    		var element_height = $element.outerHeight();
    		var element_top = $element.offset().top;
    		var element_bottom = (element_top + element_height);

			// Vérifier si ce conteneur actuel est dans la fenêtre (!=lazy)
			if (
				(element_bottom >= window_top) &&
				(element_top <= window_bottom) &&
				!$element[0].hasAttribute("loading") &&
				!$element.data("lazy")
			) 
				$element.addClass("fire");
			else 
				$element.removeClass("fire");		


			// LAZY LOAD DES IMAGES (avec marge pour préload avant entré dans la fenetre)
			var marge = 300;
			if(
				(element_bottom + marge) >= window_top
				&& (element_top - marge) <= window_bottom
				//element_top <= window_bottom
			)
				if($element.data("bg") && $element.css("background-image") == "none")// Si background
				{			
					$element.css("background-image", function() {
						return "url(" + $element.attr("data-bg") + ")";
					});					
				}
				else if($element.data("src") && !$element.attr("src") && $element.parent().css("display") != "none")// Si image
				{
		    		// Si l'image est dans data-src mais n'est pas chargé et que le parent est visible
					$element.attr("src", $element.data("src"));
				}
		});



		// PARALLAX DES BG
		$(".parallax").each(function() {
			var parallax_top = $(this).offset().top;
			var parallax_height = $(this).outerHeight();
			var parallax_bottom = parallax_top + parallax_height;
			var parallax_speed = $(this).data("parallax");

			// vitesse de défilement : 1 = image a 100% à la fin du scroll / 2|4 => on divise pour un défilement plus lent
			if(typeof parallax_speed === 'undefined') var parallax_speed = 2;

			// Si un bg parallax entre dans le champ de vision on le scroll
			if((parallax_top <= window_bottom) && (parallax_bottom >= window_top))
			{
				
				// Si bg déjà visible quand on arrive sur la page on change le calcule
				if(parallax_top < window_height)
					p100 = parseInt(((window_top) * 100 / (parallax_bottom))) / parallax_speed;		
				else 
					p100 = parseInt(((window_bottom - parallax_top) * 100 / (window_height + parallax_height))) / parallax_speed;		
		
				$(this).css("background-position", "50% " + p100 + "%");
			}
		});


	});

	$window.trigger("scroll");



	// MENU BURGER
	function toggleBurger() {
		document.body.classList.toggle('responsive-nav');
		document.querySelector('.burger').classList.toggle('active');
	}

	// Au clic, afficher ou masquer le menu burger
	document.querySelector('.burger').onclick = function() {
		toggleBurger();
	};

	// Ferme le menu si on click sur l'overlay gris du fond
	document.querySelector('.responsive-overlay').onclick = function() {
		toggleBurger();
	};
	
});