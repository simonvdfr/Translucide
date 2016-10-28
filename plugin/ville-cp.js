function autocomplete_ville(form, ville, cp, id)
{
	var cache_ville = {};

	$(form+" "+ville+", "+form+" "+cp).autocomplete({
		minLength: 2,
		source: function(request, response) {

			// Ajoute le type de recherche
			request.type = this.element.context.id;

			var term = request.term;
			if(term in cache_ville) {
				response(cache_ville[term]);
				return;
			}

			$.getJSON(path+"plugin/ville-cp.php", request, function(data, status, xhr) {
				cache_ville[term] = data;
				response(data);
			});
		},
		focus: function(event, ui) {
			$(form+" "+ville).val(ui.item.label);
			return false;
		},
		select: function(event, ui) {
			$(form+" "+ville).val(ui.item.label);
			$(form+" "+cp).val(ui.item.value);
			$(form+" "+id).val(ui.item.id);

			$(form+" "+ville)[0].setCustomValidity("");
			
			// Si on a trouvé une ville dans la base
			if($(form+" "+id).val() != "") $(form+" .fa-globe").fadeIn().attr("title", "G\u00e9olocalisation sur "+ ui.item.label+ " - "+ ui.item.value);

			return false;
		}
	})
	.autocomplete("instance")._renderItem = function(ul, item) {
		return $("<li>").append(item.label +" <font color='#808080'>"+ item.value + "</font>").appendTo(ul);
	};
}