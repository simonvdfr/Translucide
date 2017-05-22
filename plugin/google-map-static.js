// https://developers.google.com/maps/documentation/static-maps/intro
// center

add_translation({
	"Address not found" : {"fr" : "Adresse introuvable"}
});

function init_map(zoomable) 
{			
	if($("#lat").val() && $("#lng").val())
	{
		//$("#google-map").css({"min-height":"600px", "height":"100%"})

		latlng = {lat: $("#lat").val()*1, lng: $("#lng").val()*1};

		// Create a map object and specify the DOM element for display
		map = new google.maps.Map(document.getElementById('google-map'), {
			center: latlng,
			scrollwheel: zoomable || true,
			navigationControl: true,
			mapTypeControl: false,
			scaleControl: true,
			draggable: true,
			zoom: 15
		});

		// Create a marker and set its position
		marker = new google.maps.Marker({
			map: map,
			position: latlng,
			title: $("#marker-title").val()
		});
	}
}


// Positionne le marqueur et écoute
move_marker = function(latlng)
{
	// Supprime le marqueur courant
	marker.setMap(null);

	// Ajout un marqueur déplaçable
	marker = new google.maps.Marker({
		map: map,
		position: latlng,
		draggable: true,
		title: $("#marker-title").val()
	});
	
	// Si on manipule le marqueur on retourne la lat lng dans le input hidden
	google.maps.event.addListener(marker, 'dragend', function(event) {
		$("#lat").val(this.getPosition().lat());
		$("#lng").val(this.getPosition().lng());
	});
}

// Recherche de position a partir d'une adresse
position_search = function()
{
	timer = null;

	// loading
	$("#marker-title").after("<i class='fa fa-spin fa-cog' style='position: absolute; margin-top: 1.4rem; margin-left: -1.8rem; color: rgba(117, 137, 140, 0.5);'></i>");
	
	// Recherche à partir de l'adresse
	geocoder = new google.maps.Geocoder();
	geocoder.geocode({"address": $("#marker-title").val()}, function(results, status) {
		if(status == google.maps.GeocoderStatus.OK) {
			// On centre la carte sur le résultat
			map.setCenter(results[0].geometry.location);

			// Positionne la marqueur
			move_marker(results[0].geometry.location);	
				
			// Colle les coordonnées dans les inputs
			$("#lat").val(results[0].geometry.location.lat());
			$("#lng").val(results[0].geometry.location.lng());
		}
		else error(__("Address not found") +" ("+ status+")");	
		
		// Enlève l'icon de loading
		$("#marker-title").next("i").fadeOut();
	});
}		


// Marqueur positionnable
// Chargement de google map
$.ajax({
    url: "https://maps.googleapis.com/maps/api/js?key="+ google_map_key +"&callback=init_map",
    dataType: 'script',
	success: function()
	{ 		
		// Si pas de carte et de lat lng on se positionne au centre de la france
		if(!$("#lat").val() && !$("#lng").val()) {// Centre de la france
			$("#lat").val(46.64);
			$("#lng").val(2.51);
		}
		
		// Ouverture de la carte
		init_map(true);		
		
		// Positionne la marqueur
		move_marker(latlng);

		// Si changement dans input lat lng on reposition le marker
		$("#lat, #lng").keyup(function() {
			marker.setPosition(new google.maps.LatLng($("#lat").val(), $("#lng").val()));
		});			


		// Recherche de position a partir d'une adresse
		// Lance la recherche après un temps et un nombre de caratère minimum
		var timer = null;
		$("#marker-title").keyup(function() 
		{
			if($(this).val().length > 3)// Plus de 3 caractères
			{
				if(timer != null) clearTimeout(timer);
				timer = setTimeout(position_search, '500');		
			}
		});

	},
    async: true
});

// Corrige un bug avec le menu editable
$("#google-map").css("transform","none");
