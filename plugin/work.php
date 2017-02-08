<?
// @todo: lien au dessu du bloc
// @todo: edition du tooltips


if(!$work) $work = "work";

// Extrait les données work du tableau des contenu
$keys = array_keys($GLOBALS['content']);
foreach($keys as $key) {
	if(preg_match("/".$work."-/", $key) == 1) {
		$exp_key = explode("-", $key);
		$array_work[$exp_key[2]][$exp_key[1]] = $GLOBALS['content'][$key];
		//$array_work[$key] = $GLOBALS['content'][$key];
	}
}

// Bloc vide pour l'ajout de nouveau élément
$array_work[0]['titre'] = "";
?>


<ul id="<?=$work?>" class="work">
<?
// Affichage des éléments existant
while(list($key, $val) = each($array_work)) { 
	echo"
	<li class='animation slide-up mtl tc'>

		<h2 class='h4-like w100 mod mtn'><span class='editable' id='".$work."-titre-".(int)$key."'>".$array_work[$key]['titre']."</span></h2>

		<div class='w150p'><span class='editable-img'><img src=\"".$array_work[$key]['img']."\" width='150' id='".$work."-img-".(int)$key."'></span></div>

		<div class='none w100 mod pts'><span class='editable' id='".$work."-txt-".(int)$key."'>".$array_work[$key]['txt']."</span></div>

	</li>";
}
?>
</ul>


<script>
	add_translation({
		"Add a block" : {"fr" : "Ajouter un bloc"},
		"Move" : {"fr" : "D\u00e9placer"},
		"Remove" : {"fr" : "Supprimer"}
	});

	add_work = function()
	{
		// Crée un id unique
		id = parseInt($("#<?=$work?> li:first-child .editable").attr("id").split("-").pop()) + 1;
		
		// Crée un block
		$("#<?=$work?> li:last-child").clone().prependTo("#<?=$work?>").show("400", function() {
			// Vide le titre et change l'id
			$("h2 .editable", this).attr("id", "<?=$work?>-titre-" + id);

			// Vide l'image et change l'id
			$(".editable-img img", this).attr({
				id: "<?=$work?>-img-" + id,
				src: ""
			});

		// @todo voir pour changer l'event dans le edit.js pour qu'ils supp bien toutes les instances de .editable comme au dessu

			// Unbind les events toolbox
			$(".editable").off();

			// Bind la toolbox
			editable_event();
		});

		// Relance les events images
		$(".editable-img").off(".editable-img");
		editable_img_event();
	}


	// Rends déplaçables les blocs
	move_work = function() {

		// Change le style du bouton et l'action
		$(".work-bt .fa-arrows").css("transform","scale(.5)");

		// Désactive l'edition
		$(".editable-img").off(".editable-img");
		$(".editable").off();

		// Change l'action sur le lien 'move'
		$(".work-bt [href='javascript:move_work();']").attr("href","javascript:unmove_work();");

		// Les rend déplaçable
		$("#<?=$work?>").sortable();
	}

	// Désactive le déplacement des blocs
	unmove_work = function() {

		// Change le style du bouton et l'action
		$(".work-bt .fa-arrows").css("transform","scale(1)");

		// Change l'action sur le lien 'move'
		$(".work-bt [href='javascript:unmove_work();']").attr("href","javascript:move_work();");

		// Active l'edition
		editable_event();
		editable_img_event();

		// Les rend déplaçable
		$("#<?=$work?>").sortable("destroy");
	}

	$(document).ready(function()
	{		
		// Masque le bloc duplicable vide de défaut
		$("#<?=$work?> li:last-child").hide();
		
		// Action si on lance le mode d'edition
		edit.push(function() {

			// Désactive les animations pour rendre plus fluide les déplacements et l'edition
			$("#<?=$work?> .fire").css({
				"opacity": "1",
				"transform": "translate3d(0, 0, 0)"
			});
			$("#<?=$work?> .animation").removeClass("animation fire");

			// Ajoute le bouton pour dupliquer le bloc vide de défaut
			$("#<?=$work?>").after("<div class='work-bt'><a href='javascript:move_work();'><i class='fa fa-fw fa-arrows'></i> "+__("Move")+"</a> <a href='javascript:add_work();'><i class='fa fa-fw fa-plus-square-o'></i> "+__("Add a block")+"</a></div>");
			
			// Force le parent en relatif pour bien positionner les boutons d'ajout
			$(".work-bt").parent().addClass("relative");

			// Ajout de la suppresion au survole d'un bloc
			$("#<?=$work?> li").append("<a href='javascript:void(0)'><i class='fa fa-close absolute none' style='top: -5px; right: -5px;' title='"+ __("Remove") +"'></i></a>");

			// Supprime un bloc
			$("#<?=$work?> li .fa-close").fadeIn().on("click", function(event) {
				$(this).closest("li").fadeOut("slow", function() {
					this.remove();
				});
			});
		});
	});
</script>