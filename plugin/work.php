<?if(!$work) $work = "work";?>

<ul id="<?=$work?>">
<?
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
		"Add a block" : {"fr" : "Ajouter un bloc"}
	});

	add_work = function() {

//@todo : pas de tooltips sur les champs editable, peux-etre relancer la fonction editable
		
		if($("#<?=$work?> li").size() > 1) li = $("#<?=$work?> li:last-child").prev().find(".editable");
		else li = $("#<?=$work?> li:last-child .editable");

		// Crée un id unique
		id = parseInt(li.attr("id").split("-").pop()) + 1;
		
			console.log(id);

		// Crée un block
		$("#<?=$work?> li:last-child").clone().prependTo("#<?=$work?>").show("400", function() {
			// Vide le titre et change l'id
			$("h2 div", this).attr("id", "<?=$work?>-titre-" + id);

			// Vide l'image et change l'id
			$(".editable-img img", this).attr({
				id: "<?=$work?>-img-" + id,
				src: ""
			});

			// Rends déplaçables le nouveau bloc

		});

		// Relance les events images
		$(".editable-img").off(".editable-img");
		editable_img_event();
	}


	$(document).ready(function()
	{		
		// Masque le bloc duplicable vide de défaut
		$("#<?=$work?> li:last-child").hide();

		// Rends déplaçables les blocs
		//$("#<?=$work?>").sortable();

		// Action si on lance le mode d'edition
		edit.push(function() {
			// Ajoute le bouton pour dupliquer le bloc vide de défaut
			$("#<?=$work?>").after("<a href='javascript:add_work();' class='add-elem'><i class='fa fa-fw fa-plus-square-o'></i> "+__("Add a block")+"</a>");
		});
	});
</script>