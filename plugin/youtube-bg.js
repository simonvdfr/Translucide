// VIDEO YOUTUBE EN BACKGROUND
// Ajouter data-youtube="https://youtu.be/id_de_votre_video" dans un div et le tour est joué

// Appel de l'api youtube
var tag = document.createElement("script");
tag.src = "https://www.youtube.com/player_api";
var firstScriptTag = document.getElementsByTagName("script")[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

// Fonction de l'api youtube pour instancier les vidéos
function onYouTubePlayerAPIReady(){
	
	// Boucle sur tous les blocs qui réclame une vidéo en Bg
	$("[data-youtube]").each(function(i)
	{
		var contener = this;

		// Récupère l'id du bloc qui doit contenir une vidéo ou en crée un
		if(!$(contener).attr("id")) uid_bg_video = $(contener).attr("id", "youtube-" + i);
		else uid_bg_video = $(contener).attr("id");
	
		// Ajout une class pour identifier les contenus au dessus de la vidéo
		$(contener).children().addClass(uid_bg_video + "-children");

		// Place les éléments du contenant au-dessus de la div qui contient la vidéo
		 $(contener).css("position","relative");
		 $(contener).wrapInner("<div class='over-video' style='position: absolute; z-index: 2; width: 100%; height: 100%; overflow: hidden;'></div>");

		// Récupération de l'id de la vidéo
		var match = $(contener).attr("data-youtube").match(/^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/);
		id_video = (match && match[2].length == 11) ? match[2] : false;
			
		// Création d'une div pour la metre en bg
		 $(contener).append("<div id='video-"+uid_bg_video+"' style='position: absolute; top: 0; left: 0; z-index: 1; opacity: 0; transition: opacity .5s;'></div>");

		// Injection de la vidéo hd1080 hd720
		player = new YT.Player("video-"+uid_bg_video, {
			events: {
				"onReady": function(){
					player.loadVideoById({"videoId": id_video, "suggestedQuality": "hd720"});
					player.mute();
				},
				"onStateChange": function(event){
					//console.log(event.data);

					// Vidéo chargée : on l'affiche progressivement
					if(event.data === 1) $("#video-"+uid_bg_video).css("opacity","1");

					// Vidéo terminée : on la relance (loop maison)
					if(event.data === YT.PlayerState.ENDED) player.playVideo(id_video);
				}
			},
			playerVars: {
				autoplay: 0,
				autohide: 1,
				modestbranding: 1,
				rel: 0,
				showinfo: 0,
				controls: 0,
				disablekb: 1,
				enablejsapi: 0,
				iv_load_policy: 3}
		});

		var addsize = 100;
		
		// On étire un peut la vidéo pour cacher le logo Youtube
		$(window).on("load", function(){
			// Dimension du conteneur supérieur
			var h = $(contener).height() + addsize;
			
			// Taille du player
			player.setSize(h*16/9, h);
			
			// Position du player
			$("#video-"+uid_bg_video).css({
				"top": - addsize / 2,
				"left": ($(contener).width() - $("#video-"+uid_bg_video).width()) / 2
			});
		});

		// Si on redimensionne la fenêtre (responsive)
		$(window).on("resize", function(){
			// Position du player
			$("#video-"+uid_bg_video).css({
				"left": ($(contener).width() - $("#video-"+uid_bg_video).width()) / 2
			});
		});

		// Désactive les vidéos lors du mode édition
		edit.push(function() {
			$("#video-"+uid_bg_video).remove();
			$("."+uid_bg_video+"-children").unwrap();
		});
	});	
}