<?
include_once("../config.php");// Les variables si on ajax
include_once("../api/function.php");// Les fonctions si on ajax
include_once("../api/db.php");// Connexion à la db

$lang = get_lang();// Sélectionne la langue
load_translation('api');// Chargement des traductions du système

//@todo quand on modifie un tag on change le nom du tag dans toutes les metas

// MET À JOUR LES TAGS DE L'ARTICLE
if(isset($_GET['mode']) and $_GET['mode'] == "tag")
{
	login('high', 'edit-article');// Vérifie que l'on a le droit d'éditer les contenus
	
	$connect->query("DELETE FROM ".$table_meta." WHERE id='".(int)$_REQUEST['id']."' AND type='tag'");

	if(isset($_REQUEST['tags'])) 
	{
		$tags = explode(",", trim($_REQUEST['tags']));

		$i = 1;
		while(list($cle, $val) = each($tags)) {
			if(isset($val) and $val != "") {
				$connect->query("INSERT INTO ".$table_meta." SET id='".(int)$_REQUEST['id']."', type='tag', cle='".encode($val)."', val='".addslashes(trim($val))."', ordre='".$i."'");
				$i++;
			}
		}		
	}
	
	if($connect->error) echo $connect->error;

	exit;
}
// MET À JOUR LES INFORMATION DU TAG
if(isset($_GET['mode']) and $_GET['mode'] == "tag-info")
{	
	login('high', 'edit-article');// Vérifie que l'on a le droit d'éditer les contenus

	if(isset($_REQUEST['tag-info'])) 
	{
		$connect->query("DELETE FROM ".$table_meta." WHERE type='tag-info' AND cle='".encode($_REQUEST['tag'])."'");
		
		// Supprime les url avec le domaine pour faciliter le transport du site
		$_REQUEST['tag-info'] = str_replace($GLOBALS['home'], "", $_REQUEST['tag-info']);

		$tag_info = json_encode($_REQUEST['tag-info'], JSON_UNESCAPED_UNICODE);

		$connect->query("INSERT INTO ".$table_meta." SET type='tag-info', cle='".encode($_REQUEST['tag'])."', val='".addslashes($tag_info)."'");
	}
	
	if($connect->error) echo $connect->error;

	exit;
}
// SAUVEGARDE L'ARBO DES TAGS
elseif(isset($_GET['mode']) and $_GET['mode'] == "save-tag-tree") 
{
	login('high', 'edit-article');// Vérifie que l'on a le droit d'éditer les contenus

	$tags = json_encode($_REQUEST['tags'], JSON_UNESCAPED_UNICODE);

	$connect->query("DELETE FROM ".$table_meta." WHERE type='tags'");

	$connect->query("INSERT INTO ".$table_meta." SET type='tags', cle='".$lang."', val='".addslashes($tags)."'");

	if($connect->error) echo $connect->error;
	else 
	{
		?>
		<script>
			$(function()
			{
				$("#save-tag i").removeClass("fa-cog fa-spin").addClass("fa-check");// Si la sauvegarde réussit on change l'icône du bt
				$("#save-tag").removeClass("to-save").addClass("saved");// Si la sauvegarde réussit on met la couleur verte
			});
		</script>
		<?
	}

	exit;
}
// ARBRE DES TAGS
elseif(isset($_GET['mode']) and $_GET['mode'] == "tag-tree")
{
	$lang = get_lang();// Sélectionne  la langue
	load_translation('api');// Chargement des traductions du système
	add_translation(array(
		"Add Item" => array("fr" => "Ajouter un élément")
	));

	?>
	<style>
		#tag .fa-plus-circle { cursor: pointer;	}
		#tag .fa-arrows { cursor: move;	}
		#tag ol { 
			list-style: none;
			margin: 0;
		}
		#tag ol > li { padding: 0.2rem; }
	</style>
	<div class="absolute">

		<div class="tooltip slide-up fire mas pas mod">

			<ol id="tag-tree" class="pan man mbs">
				<?
				$sql_filter_tag = null;

				function tag_line($value)
				{
					global $sql_filter_tag;

					if(is_array($value)) $echo = $value['id'];
					else $echo = $value;

					echo'
					<li>
						<div>
							<i class="fa fa-arrows mrt grey small"></i>
							<input type="text" name="tag" placeholder="tag" value="'.htmlspecialchars($echo).'">
							<i class="fa fa-plus-circle mlt"></i>
						</div>';

						// Si des enfants on imbrique et on boucle
						if(isset($value['children'])) {
							echo'<ol>';
							while(list($cle, $value_children) = each($value['children'])) tag_line($value_children);
							echo'</ol>';
						}

					echo'
					</li>';

					// Tag disponible et non ajouté récemment
					if(isset($value['id'])) $sql_filter_tag .= "'".encode($value['id'])."',";
				}

				// On regarde s'il y a un arbre de tag déjà défini
				$sel_tag_tree = $connect->query("SELECT * FROM ".$table_meta." WHERE type='tags' LIMIT 1");
				$res_tag_tree = $sel_tag_tree->fetch_assoc();
				if($res_tag_tree['val'])
				{
					$tag_tree = json_decode($res_tag_tree['val'], true);
					while(list($cle, $val) = each($tag_tree)) tag_line($val);								
				}
				
				// Liste les tags non classé
				$sel_tag = $connect->query("SELECT * FROM ".$table_meta." WHERE type='tag' ".($sql_filter_tag ? "AND cle NOT IN (".trim($sql_filter_tag, ",").")" : "")." GROUP BY cle");
				if($connect->error) echo $connect->error;
				while($res_tag = $sel_tag->fetch_assoc()) {
					tag_line($res_tag['val']);
				}
				?>
			</ol>

			<button id="save-tag" class="small fr mts mlt"><?_e("Save")?> <i class='fa fa-fw fa-save big'></i></button>
			<button id="add-tag" class="small fr mts o50 ho1 t5"><?_e("Add Item")?> <i class='fa fa-fw fa-plus big'></i></button>

		</div>

	</div>

	<script>
		$(function()
		{
			// AUTOCOMPLETE
			// Donnée pour l'autocomplete lors de la saisie dans le champs tags
			get_available_tags = function() {
				var available_tags = [];
				$("#admin-bar #tag-tree li input").each(function() {
					available_tags.push($(this).val());
				});
				return available_tags;
			}

			function split(val) { return val.split(/,\s*/); }
		    function extractLast(term) { return split(term).pop(); }

			$("#admin-bar #tags").on("keydown", function(event) {				
				// don't navigate away from the field on tab when selecting an item
				if(event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete("instance").menu.active)
					event.preventDefault();	
			})
			.autocomplete({
				minLength: 0,
				source: function(request, response) {
					response($.ui.autocomplete.filter(get_available_tags(), extractLast(request.term)));// delegate back to autocomplete, but extract the last term
				},
				open: function(event, ui) {// Masque l'arbre des tags si elle est save
					if(!$("#admin-bar #tag button.to-save").length && !$("#admin-bar #tag button i.fa-spin").length)
					$("#admin-bar #tag .absolute").fadeOut("fast", function(){ close_tag = false; });
				},
				close: function(event, ui) {// On peut ré-ouvrir l'arbre des tags
					close_tag = true;
				},
				focus: function() {
					return false;// prevent value inserted on focus
				},
				select: function(event, ui) {
					var terms = split(this.value);

					// remove the current input
					terms.pop();

					// add the selected item
					terms.push(ui.item.value);

					// add placeholder to get the comma-and-space at the end
					terms.push("");

					this.value = terms.join(", ");

					return false;
				}
			});


			// Affichage du bouton de sauvegarde des tags en rouge
			tagtosave = function() {	
				$("#save-tag i").removeClass("fa-spin fa-cog").addClass("fa-save");// Affiche l'icône disant qu'il faut sauvegarder sur le bt save	
				$("#save-tag").removeClass("saved").addClass("to-save");// Changement de la couleur de fond du bouton pour indiquer qu'il faut sauvegarder
			}


			// Onlick on ajoute à la liste le tag
			$("#admin-bar #tag li .fa-plus-circle").on("click", function(event) {
				event.preventDefault();

				if($("#admin-bar #tag #tags").val()) var add_tag = $("#admin-bar #tag #tags").val() + ", " + $(this).prev().val();
				else var add_tag = $(this).prev().val();

				$("#admin-bar #tag #tags").val(add_tag);

				tosave();
			});


			// Rend les tags triable
			$("#admin-bar #tag-tree").nestedSortable({
				handle: 'div',
				items: 'li',
				opacity: .6,
				change: function(){
					tagtosave();
				}
			});


			// Ajoute un tag à l'arbo des tags
			$("#admin-bar #add-tag").click(function() 
			{	
				// Copie le dernier tag
				$("#admin-bar #tag-tree").append($("#admin-bar #tag-tree li").last().prop('outerHTML'));

				// Re-init la valeur du tag copier
				$("#admin-bar #tag-tree li input").last().val("");

				$("#admin-bar #tag-tree li").last().attr("id", "");

				tagtosave();
				
				// Prend l'id le plus elever
				/*var max = $.map($("#tag-tree li"), function(elem) {
					return parseInt(elem.id.match(/\d+/));
				}).sort(function(a, b) {
					return(b-a); //reverse sort
				});
				max = max[0] + 1;

				// Change l'id du tag copier
				$("#admin-bar #tag-tree li").last().attr("id", "tag-"+max);*/
			});	


			// Capture des actions au clavier
			$("#admin-bar #tag-tree input").on("keydown", function(event) 
			{				
				// Caractères texte, entrée, supp, backspace => A sauvegarder
				if(String.fromCharCode(event.which).match(/\w/) || event.keyCode == 13 || event.keyCode == 46 || event.keyCode == 8) tagtosave();			
			});


			// Sauvegarde les tags
			$("#admin-bar #save-tag").click(function() 
			{	
				// Animation sauvegarde en cours (loading)
				$("#save-tag i").removeClass("fa-save").addClass("fa-spin fa-cog");

				// Place les tags dans les data-tag pour la sauvegarde
				$("#admin-bar #tag-tree li").attr("id", function(){ return "tag-" + $("input", this).val() });

				// Envoi de l'arbre de tag
				$.ajax({
					url: path+"plugin/tag.php?mode=save-tag-tree",
					data: {"id": id, "tags": $("#admin-bar #tag-tree").nestedSortable("toHierarchy"), "nonce": $("#nonce").val()},
					success: function(html){
						// Affichage/exécution du retour
						$("body").append(html);
					}
				});
			});
		});	
	</script>
	<?
	exit;
}
?>