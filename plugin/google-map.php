<?hidden_label(__("Information pour la google map : "))?>
<?hidden('marker-title', 'vat mat')?>
<?hidden('lat', 'vat mat')?>
<?hidden('lng', 'vat mat')?>

<style>
	#google-map {
		height: 100%;
		min-height: 400px;
	}
</style>

<div id="google-map" class="animation fade-in"></div>

<script>
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
			scrollwheel: zoomable || false,
			navigationControl: true,
			mapTypeControl: false,
			scaleControl: true,
			draggable: true,
			zoom: 17,
			styles: [
				{"featureType":"administrative","stylers":[{"visibility":"off"}]},
				{"featureType":"poi","stylers":[{"visibility":"simplified"}]},
				{"featureType":"road","stylers":[{"visibility":"simplified"}]},
				{"featureType":"water","stylers":[{"visibility":"simplified"}]},
				{"featureType":"transit","stylers":[{"visibility":"simplified"}]},
				{"featureType":"landscape","stylers":[{"visibility":"simplified"}]},
				{"featureType":"road.highway","stylers":[{"visibility":"off"}]},
				{"featureType":"road.local","stylers":[{"visibility":"on"}]},
				{"featureType":"road.highway","elementType":"geometry","stylers":[{"visibility":"on"}]},
				{"featureType":"water","stylers":[{"color":"#84afa3"},{"lightness":52}]},
				{"stylers":[{"saturation":-77}]},
				{"featureType":"road"}]
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


$(document).ready(function()
{		
	// Marqueur positionnable
	edit.push(function() {
		
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
	});


	// Recherche de position a partir d'une adresse
	edit.push(function() {
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
	});

	// Corrige un bug avec le menu editable
	$("#google-map").css("transform","none");

});
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?=$GLOBALS['google_map']?>&callback=init_map"></script>