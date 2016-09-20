<?
if($_GET['mode'] == "setup-update") include_once("config.init.php");// Les variables par défaut
@include_once(($_GET['mode'] == "setup" ? "" : "../")."config.php");// Les variables mais avec if au cas où  on lance depuis l'install
include_once("fonction.php");// Fonction

$lang = get_lang();// Sélectionne  la langue
load_translation('api');// Chargement des traductions du système

switch($_GET['mode'])
{
	default:	
	break;


	case "edit":// Lancement du mode édition du contenu de la page
				
		unset($_SESSION['nonce']);// Pour éviter les interférences avec un autre nonce de session
		
		login('high', 'edit_content');// Vérifie que l'on a le droit d'éditer les contenus
		
		// Si on doit recharger la page avant de lancer le mode édition
		if($_REQUEST['callback'] == "reload_edit")
		{
			// Pose un cookie pour demander l'ouverture de l'admin automatiquement au chargement
			setcookie("autoload_edit", "true", time() + 60*60, $GLOBALS['path'], $GLOBALS['domain']);
			?>
			<script>
			reload();
			</script>
		<?}
		else 
		{				
			// JS pour mettre en mode édit les contenus et ajout d'un nonce pour signer les formulaires
			?>
			<input type="hidden" name="nonce" id="nonce" value="<?=nonce("nonce");?>">

			<link rel="stylesheet" href="api/lucide.css?0.1">

			<script>				
				// Update les nonces dans la page courante pour éviter de perdre le nonce
				$("#nonce").val('<?=$_SESSION['nonce']?>');
			
				// Lib qui rend le contenu éditable
				var script = document.createElement('SCRIPT');
				script.src = "api/lucide.edit.js?0.1";
				document.body.appendChild(script);
			</script>
			<?
		}

	break;


	case "adminbar":// @todo mettre ici la barre d'administration pour y afficher les infos venant de la base (langue, template dispo, état...)
		// SUPP ???

	break;


	case "add-page":// Dialog pour ajouter une page

		unset($_SESSION['nonce']);// Pour éviter les interférences avec un autre nonce de session

		login('medium', 'add_page');// Vérifie que l'on a le droit d'ajouter une page

		// Dialog : titre, template, langue
		?>
		<div class="dialog-add" title="<?_e("Add a page")?>">

			<input type="hidden" id="nonce" value="<?=nonce("nonce");?>">

			<div class="">
				<input type="text" id="title" placeholder="<?_e("Page title")?>" maxlength="60" class="w60 bold">

				<select id="tpl" required class="w30">
					<option value=""><?_e("Select page template")?></option>
					<?
					$scandir = array_diff(scandir($_SERVER['DOCUMENT_ROOT'].$GLOBALS['path']."theme/".$GLOBALS['theme']."tpl/"), array('..', '.'));
					while(list($cle, $filename) = each($scandir))				
					{			
						$filename = pathinfo($filename, PATHINFO_FILENAME);
						echo"<option value=\"".$filename."\">".$filename."</option>";
					}
					?>					
				</select>
			</div>

			<div class="mtt">
				<input type="text" id="permalink" placeholder="<? _e("Permanent link: 'home' if homepage"); ?>" maxlength="60" class="w50 mrm">
				<label for="homepage" class="mrs mtn"><input type="checkbox" id="homepage"> <? _e("Home page"); ?></label>
				<label id="refresh-permalink" class="mtn"><i class="fa fa-fw fa-refresh"></i><? _e("Regenerate address"); ?></label>
			</div>

			<script>
			$(document).ready(function()
			{
				// Update les nonces dans la page courante pour éviter de perdre le nonce
				$("#nonce").val('<?=$_SESSION['nonce']?>');

				// Changement au click de la checkbox homepage
				$(".dialog-add #homepage").change(function() {
					if(this.checked) $(".dialog-add #permalink").val("home");
					else refresh_permalink(".dialog-add");
				});

				// Click refresh permalink
				$(".dialog-add #refresh-permalink").click(function() {
					refresh_permalink(".dialog-add");
				});

				// Création du permalink lors de la saisie du title
				var timer = null;
				$(".dialog-add #title").keyup(function() 
				{
					if(timer != null) clearTimeout(timer);

					timer = setTimeout(function() {
						timer = null;
						refresh_permalink(".dialog-add");
					}, '500');
				});

				
				// Fermeture de la dialog de connexion
				$("#dialog-connect").dialog("close");
			});
			</script>

		</div>
		<?				
	break;


	case "insert":// Crée une nouvelle page

		include_once("db.php");// Connexion à la db
		
		login('high', 'add_page');// Vérifie que l'on a le droit d'ajouter une page

		$url = (encode($_POST['permalink']) ? encode($_POST['permalink']) : encode($_POST['title']));

		// Ajoute la page
		$sql = "INSERT ".$table_content." SET ";
		$sql .= "title = '".addslashes($_POST['title'])."', ";
		$sql .= "tpl = '".addslashes($_POST['tpl'])."', ";
		$sql .= "url = '".$url."', ";
		$sql .= "lang = '".$lang."', ";
		$sql .= "user_insert = '".(int)$_SESSION['uid']."', ";
		$sql .= "date_insert = NOW() ";
		$connect->query($sql);
		
		if($connect->error) echo $connect->error."\nSQL:\n".$sql;// S'il y a une erreur
		else // Sauvegarde réussit
		{
			?>
			<script>
			$(document).ready(function()
			{
				document.location.href = "<?=make_url($url);?>";
			});
			</script>
			<?
		}

	break;


	case "update":// Sauvegarde du contenu éditable de la page

		include_once("db.php");// Connexion à la db
		
		login('high', 'edit_content');// Vérifie que l'on peut éditer une page

		//highlight_string(print_r($_POST['content'], true)); exit;
		
		// PREPARATION POUR LE CONTENU ET NAVIGATION
		// On récupère les données de la page pour comparaison
		$sel = $connect->query("SELECT * FROM ".$table_content." WHERE url='".get_url($_POST['url'])."' AND lang='".$lang."' LIMIT 1");
		$res = $sel->fetch_assoc();		
		
		// Si le titre à changer et que l'on n'est pas sur le home on change l'URL de la page
		if($res['url'] != encode($_POST['permalink']) or (encode($_POST['title']) and !encode($_POST['permalink']))) 
		{
			if(!encode($_POST['permalink']) and encode($_POST['title'])) $change_url = encode($_POST['title']);
			elseif(!encode($_POST['permalink']) and !encode($_POST['title'])) $change_url = "page-".$res['id'];
			else $change_url = encode($_POST['permalink']);
		}


		// MENU DE NAVIGATION
		if(isset($_POST['nav']))
		{
			// On regarde s'il y a déjà des données
			$sel_nav = $connect->query("SELECT * FROM ".$table_meta." WHERE type='nav' AND cle='".$lang."' LIMIT 1");
			$res_nav = $sel_nav->fetch_assoc();	
			
			// On remplace le chemin absolut du site par la clé : home (utilise pour éviter les bug lors des mises en lignes)
			array_walk($_POST['nav'], 
				function(&$key) { 
					if($key['href'] == $GLOBALS['path']) $key['href'] = "home";
				}
			);

			// Si on change d'url (permalink) on change dans le menu le lien correspondant
			if($change_url) {
				array_walk($_POST['nav'], 
					function(&$key) { 
						global $res, $change_url;
						if($key['href'] == $res['url']) $key['href'] = $change_url;
					}
				);
			}

			// On  encode les données
			$json_nav = json_encode($_POST['nav'], JSON_UNESCAPED_UNICODE);
			
			// Insert ou update ?
			if($res_nav['id']) $sql = "UPDATE"; else $sql = "INSERT INTO";
			$sql .= " ".$table_meta." SET ";
			$sql .= "type = 'nav', ";
			$sql .= "cle = '".$lang."', ";
			$sql .= "val = '".addslashes($json_nav)."' ";
			if($res_nav['id']) $sql .= "WHERE id = '".$res_nav['id']."'";
			
			$connect->query($sql);

			if($connect->error) echo $connect->error."\nSQL:\n".htmlspecialchars($sql);// S'il y a une erreur
		}


		// HEADER
		if($_POST['header'])
		{
			// On regarde s'il y a déjà des données
			$sel_header = $connect->query("SELECT * FROM ".$table_meta." WHERE type='header' AND cle='".$lang."' LIMIT 1");
			$res_header = $sel_header->fetch_assoc();		
			
			// On  encode les données
			$json_header = json_encode($_POST['header'], JSON_UNESCAPED_UNICODE);
			
			// Insert ou update ?
			if($res_header['id']) $sql = "UPDATE"; else $sql = "INSERT INTO";
			$sql .= " ".$table_meta." SET ";
			$sql .= "type = 'header', ";
			$sql .= "cle = '".$lang."', ";
			$sql .= "val = '".addslashes($json_header)."' ";
			if($res_header['id']) $sql .= "WHERE id = '".$res_header['id']."'";
			
			$connect->query($sql);

			if($connect->error) echo $connect->error."\nSQL:\n".htmlspecialchars($sql);// Si il y a une erreur
		}

				
		// FOOTER
		if($_POST['footer']) 
		{
			// On regarde s'il y a déjà des données
			$sel_footer = $connect->query("SELECT * FROM ".$table_meta." WHERE type='footer' AND cle='".$lang."' LIMIT 1");
			$res_footer = $sel_footer->fetch_assoc();		
			
			// On  encode les données
			$json_footer = json_encode($_POST['footer'], JSON_UNESCAPED_UNICODE);
			
			// Insert ou update ?
			if($res_footer['id']) $sql = "UPDATE"; else $sql = "INSERT INTO";
			$sql .= " ".$table_meta." SET ";
			$sql .= "type = 'footer', ";
			$sql .= "cle = '".$lang."', ";
			$sql .= "val = '".addslashes($json_footer)."' ";
			if($res_footer['id']) $sql .= "WHERE id = '".$res_footer['id']."'";
			
			$connect->query($sql);

			if($connect->error) echo $connect->error."\nSQL:\n".htmlspecialchars($sql);// Si il y a une erreur
		}

		
		// CONTENU
		// Supprime les url avec le domaine pour faciliter le transport du site
		$_POST['content'] = str_replace($GLOBALS['scheme'].$GLOBALS['domain'].$GLOBALS['path'], "", $_POST['content']);

		// Encode le contenu
		$json_content = json_encode($_POST['content'], JSON_UNESCAPED_UNICODE);

		// Sauvegarde les contenus
		$sql = "UPDATE ".$table_content." SET ";
		if($change_url) $sql .= "url = '".$change_url."', ";
		$sql .= "title = '".addslashes($_POST['title'])."', ";
		$sql .= "description = '".addslashes($_POST['description'])."', ";
		$sql .= "content = '".addslashes($json_content)."', ";
		$sql .= "state = '".addslashes($_POST['state'])."', ";
		$sql .= "user_update = '".(int)$_SESSION['uid']."', ";
		$sql .= "date_update = NOW() ";
		$sql .= "WHERE url = '".get_url($_POST['url'])."' AND lang = '".$lang."'";
		$connect->query($sql);

		//echo $sql;
		
		if($connect->error) echo $connect->error."\nSQL:\n".htmlspecialchars($sql);// S'il y a une erreur
		else // Sauvegarde réussit
		{
			?>
			<script>
			$(document).ready(function()
			{
				document.title = "<?=addslashes($_POST['title']);?>";

				<?if($change_url){?>					
					window.history.replaceState(history.state, document.title, "<?=make_url($change_url);?>");					
				<?}?>

				$("#save i").removeClass("fa-cog fa-spin").addClass("fa-check");// Si la sauvegarde réussit on change l'icône du bt
				$("#save, #preview").removeClass("to-save").addClass("saved");// Si la sauvegarde réussit on met la couleur verte
			});
			</script>
			<?
		}

	break;


	case "make-permalink":// Construit un permalink

		login('medium', 'edit_content');// Vérifie que l'on a le droit d'éditer une page

		echo encode($_POST['title']);

	break;


	case "add-nav":// Liste les pages absente du menu
		
		login('medium', 'edit_nav');// Vérifie que l'on est admin

		$menu = array();

		// Nettoyage et conversion du menu existant
		if($_REQUEST['menu'])
		while(list($cle, $val) = each($_REQUEST['menu']))
		{
			// Si c'est un lien vers la home
			if($val == $GLOBALS['scheme'].$GLOBALS['domain'].$GLOBALS['path'] or $val == $GLOBALS['path'])
				$menu[] = "home";
			else 
				$menu[] = $connect->real_escape_string($val);
		}

		// Liste les pages abs du menu
		$sql = "SELECT * FROM ".$table_content." WHERE url NOT IN ('".implode("','", $menu)."') ORDER BY title ASC";
		//echo $sql."<br>";

		$sel = $connect->query($sql);
		while($res = $sel->fetch_assoc()) {
			echo"<li><div class='dragger'></div><a href=\"".$res['url']."\">".$res['title']."</a></li>";
		}

	break;


	case "dialog-media":// Affichage des médias
		
		login('medium', 'upload_file');// Vérifie que l'on est admin

		//echo "_POST:<br>"; highlight_string(print_r($_POST, true));
		
		// Titre spécifique si la destination est une image cropé, forcé sur la largeur ...
		// Onglet : Locale / FB / Insta / Flicker
		// Option de tri : Par date (defaut) / par nom / par taille

		//@todo: si pas de source on utilise une autre fonction d'insertion ou on renvoie un élément complet d'image <img>

		//["image/jpg","image/jpeg","image/png","image/gif"];
		//highlight_string(print_r($tab_img, true));
		?>

		<div class="dialog-media" title="<?_e("Media Library")?>">

			<input type="hidden" id="dialog-media-target" value="<?=htmlspecialchars($_GET['target'])?>">
			<input type="hidden" id="dialog-media-source" value="<?=htmlspecialchars($_GET['source'])?>">
			<input type="hidden" id="dialog-media-width" value="<?=htmlspecialchars($_GET['width'])?>">
			<input type="hidden" id="dialog-media-height" value="<?=htmlspecialchars($_GET['height'])?>">

			<ul class="small">
				<li data-filter="all"><a href="#media" title="<?_e("Media")?>"><i class="fa fa-files-o"></i> <span><?_e("Media")?></span></a></li>
				<li data-filter="image"><a href="api/ajax.admin.php?mode=media&filter=image" title="<?_e("Images")?>"><i class="fa fa-picture-o"></i> <span><?_e("Images")?></span></a></li>
				<li data-filter="resize"><a href="api/ajax.admin.php?mode=media&filter=resize" title="<?_e("Resized")?>"><i class="fa fa-compress"></i> <span><?_e("Resized")?></span></a></li>
				<li data-filter="file"><a href="api/ajax.admin.php?mode=media&filter=file" title="<?_e("Files")?>"><i class="fa fa-file-text-o"></i> <span><?_e("Files")?></span></a></li>				
				<!-- <li data-filter="video"><a href="api/ajax.admin.php?mode=media&filter=video" title="<?_e("Videos")?>"><i class="fa fa-film"></i> <span><?_e("Videos")?></span></a></li>
				<li data-filter="audio"><a href="api/ajax.admin.php?mode=media&filter=audio" title="<?_e("Audios")?>"><i class="fa fa-volume-up"></i> <span><?_e("Audios")?></span></a></li> -->
			</ul>
			
			<div id="media" class="test">
				<?
				$_GET['mode'] = "media";
				include("ajax.admin.php");
				?>
			</div>

			<script>
			add_container = function(file) {
				// Crée un id unique
				now += 1;
				var id = "dialog-media-"+ now;
				
				// Type de fichier
				var mime = file.type.split("/");
				
				// Switch sur le 1er onglet avec tous les médias
				$(".dialog-media").tabs("option", "active", 0);

				// Option de resize à afficher ?
				if(!$("#dialog-media-width").val() && !$("#dialog-media-height").val())
					var resize = "<a class='resize' title=\"<?_e("Get resized image");?>\"><i class='fa fa-fw fa-compress bigger'></i></a>";
				else 
					var resize = "";

				// Crée un block vide pour y ajouter le media // $(".ui-state-active").attr("aria-controls") // + ($(".ui-state-active").attr("data-filter") == "resize" ? "resize/":"")
				$("#media .add-file").after("<li class='pat mat tc uploading' id='"+ id +"' data-file=\"media/" + file.name +"\" data-type='"+ mime[0] +"'>"+ (mime[0] == "image"? "<img src=''>" + resize : "<div class='file'><i class='fa fa-fw fa-file-o mega'></i><div>"+ file.name +"</div></div>") +"<div class='infos'></div><a class='supp hidden' title=\""+__("Delete file")+"\"><i class='fa fa-fw fa-trash bigger'></i></a></li>");

				// Converti la date unix en date lisible
				var date = new Date();
				
				// Nom et Date de l'image dans le title
				$("#"+id).attr("title", file.name+" | "+date.getDate()+"-"+date.getMonth()+"-"+date.getFullYear()+" "+date.getHours()+":"+date.getMinutes()+":"+date.getSeconds());

				// Poids de l'image uploadée
				if(file.size >= 1048576) var filesize = Math.round(file.size / 1048576) + "Mo";
				else if(file.size >= 1024) var filesize = Math.round(file.size / 1024) + "Ko";
				else if(file.size < 1024) var filesize = file.size + "oct";

				// Si c'est une image
				if(mime[0] == "image")
				{
					// On crée un objet image pour s'assurer que l'image est bien chargée dans le browser pour avoir la largeur/hauteur		
					var image = new Image();
					image.onload = function() {// Image bien chargée dans le navigateur
						$("#"+id+" .infos").html(image.naturalWidth +"x"+ image.naturalHeight +"px - "+ filesize);// Largeur+Hauteur de l'image a uploader
						window.URL.revokeObjectURL(image.src);
					}					
					image.src = window.URL.createObjectURL(file);
				}
				else $("#"+id+" .infos").html(file.name.replace(/^.*\./, '') +" - "+ filesize);

				return id;
			}

			// Resize d'image avec lien
			resize_img = function(id)
			{
				if(!$("#resize-width").val() && !$("#resize-height").val())
				{
					$("#resize-width, #resize-height").css("border-color","red");
				}
				else 
				{
					$("#dialog-media-width").val($("#resize-width").val());
					$("#dialog-media-height").val($("#resize-height").val());
					get_img(id, $('#resize-tool .fa-expand').hasClass('checked'));
				}
			}


			$(document).ready(function()
			{
				// Pour la construction d'id unique
				now = new Date().getTime();


				// On demande une version redimensionnée de l'image
				$(".dialog-media").on("click", ".resize", function(event)
				{
					event.stopPropagation();

					var id = $(this).parent().attr("id");
					var top = $(this).parent().offset().top;
					var left = $(this).offset().left;

					// Boîte à outils resize
					resize_tool = "<div id='resize-tool' class='toolbox'>";
						resize_tool+= __("Width") +": <input type='text' id='resize-width' class='w50p'> ";
						resize_tool+= __("Height") +": <input type='text' id='resize-height' class='w50p'>";
						resize_tool+= "<a href=\"javascript:$('#resize-tool .fa-expand').toggleClass('checked');void(0);\"><i class='fa fa-fw fa-expand'></i>"+ __("Zoom link") +"</a> ";
						resize_tool+= "<button onclick=\"resize_img('"+id+"')\"><i class='fa fa-fw fa-cogs'></i> "+ __("Add") +"</button>";
					resize_tool+= "</div>";
			
					$(".ui-dialog").append(resize_tool);
					
					// On l'affiche et la positionne
					$("#resize-tool")
						.css("z-index", parseInt($(".ui-dialog").css("z-index")) + 1)
						.show()
						.offset({
							top: top - $(this).height() - 8,
							left: left
						});			
					
					// On affiche la taille de l'image originale dans les placeholder
					$("#resize-tool #resize-width").attr("placeholder", $(this).prev("img")[0].naturalWidth);
					$("#resize-tool #resize-height").attr("placeholder", $(this).prev("img")[0].naturalHeight);
				});


				// On supp une image
				$(".dialog-media").on("click", ".supp", function(event)
				{
					event.stopPropagation();

					if(confirm(__("Delete file")+" ?")) 
					{
						var id = $(this).parent().attr("id");
						
						$.ajax({
							url: "api/ajax.admin.php?mode=del-file",
							data: {
								"file": $("#"+id).attr("data-file"),
								"nonce": $("#nonce").val()
							},
							success: function(html){
								if(!html) 
									$("#"+id).hide("slide", 300);
								else 
									error(html);
							}
						});
					}
				});


				// On sélectionne un fichier
				$(".dialog-media").on("click", "li:not(.add-file)", function(event)
				{
					var id = $(this).attr("id");

					if($(this).attr("data-type") == "image") get_img(id);
					else get_file(id);
				});

				// Init variable d'upload
				source_queue = [];// @todo: voir si on les re-active
				file_queue = [];
				if(typeof uploading === "undefined") uploading = false;

				// Si on choisit des images pour l'upload avec le bouton
				$("#upload-file").change(function()
				{
					// Inverse le tableau pour l'afficher comme dans le dossier
					$.merge(uploads = [], this.files);

					// Rétablie le tableau dans le bon ordre si upload en cours
					if(source_queue.length > 0) source_queue.reverse();
					if(file_queue.length > 0) file_queue.reverse();

					$.each(uploads.reverse(), function(cle, file)
					{
						// Ajoute un contener pour le media uploader
						var id = add_container(file);										
						
						// Variables d'upload //, $(".ui-state-active").attr("data-filter")
						source_queue.push($("#"+id));
						file_queue.push(file);
					});

					// Inverse le tableau des variables pour up dans le bon ordre visuel
					source_queue.reverse();
					file_queue.reverse();
					
					// Lance le 1er upload
					if(!uploading) upload(source_queue.shift(), file_queue.shift());
				});


				// Pour éviter les highlight des zones draggables du fond
				$("body").off(".editable").off(".editable-img");
				$(".editable-img").off(".editable-img");

				// On drag&drop des médias dans la fenêtre
				$("body")
					.on({
					"dragover.dialog-media": function(event) {// Highlight les zones on hover
						event.preventDefault();
						event.stopPropagation();					
						$(".ui-widget-overlay").addClass("body-dragover");
						$(".add-file").addClass("dragover");
					},
					"dragleave.dialog-media": function(event) {// Clean les highlight on out
						event.stopPropagation();
						$(".ui-widget-overlay").removeClass("body-dragover");
						$(".add-file").removeClass("dragover");
					},
					"drop.dialog-media": function(event) {// On lache un fichier sur la zone
						event.preventDefault();  
						event.stopPropagation();
						$(".ui-widget-overlay").removeClass("body-dragover");
						$(".add-file").removeClass("dragover");
						
						// Upload du fichier dropé
						if(event.originalEvent.dataTransfer)
						{
							// Inverse le tableau pour l'afficher comme dans le dossier
							$.merge(uploads = [], event.originalEvent.dataTransfer.files);
							
							// Rétablie le tableau dans le bon ordre si upload en cours
							if(source_queue.length > 0) source_queue.reverse();
							if(file_queue.length > 0) file_queue.reverse();

							$.each(uploads.reverse(), function(cle, file)
							{
								// Ajoute un contener pour le média uploadé
								var id = add_container(file);										
								
								// Variables d'upload //, $(".ui-state-active").attr("data-filter")
								source_queue.push($("#"+id));
								file_queue.push(file);
							});

							// Inverse le tableau des variables pour up dans le bon ordre visuel
							source_queue.reverse();
							file_queue.reverse();							

							// Lance le 1er upload si pas d'upload en cours
							if(!uploading) upload(source_queue.shift(), file_queue.shift());							
						}
					}
				});
			});
			</script>
		</div>
		<?
	break;


	case "media":// Liste les images

		// @todo: Ajouter une recherche js comme dans la partie font awesome
		// @todo: mettre player html5 si vidéo ou audio pour avoir la preview et possibilité de jouer les médias en mode zoom
		// @todo: ajouter un bouton de nettoyage qui scanne les contenus et regarde si les fichiers sont utilisés
		
		login('medium', 'upload_file');// Vérifie que l'on est admin

		$dir = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['path']."media/".($_GET['filter'] == "resize" ? "resize/":"");
		
		// Le dossier existe
		if(is_dir($dir))
		{
			$scandir = array_diff(scandir($dir), array('..', '.'));// Nettoyage

			$i = 1;
			// Crée un tableau avec les fichiers du dossier et infos complètes
			while(list($cle, $filename) = each($scandir))				
			{				
				if($filename != "Thumbs.db" and $filename != ".htaccess" and !is_dir($dir.$filename))
				{			
					$stat = stat($dir.$filename);// size : poids, mtime : date de modification (timestamp)
					$file_infos = getimagesize($dir.$filename);// 0 : width, 1 : height

					// Si ce n'est pas une image
					if(!is_array($file_infos)) {
						$finfo = finfo_open(FILEINFO_MIME_TYPE);
						$file_infos['mime'] = finfo_file($finfo, $dir.$filename);
						finfo_close($finfo);
					}
					
					// Type mime
					list($type, $ext) = explode("/", $file_infos['mime']);
					
					// Pour le tri
					if(!$_GET['order'] or $_GET['order'] == 'time') $order = $stat['mtime'];// Tri par défaut
					elseif($_GET['order'] == 'size') $order = $stat['size'];
					elseif($_GET['order'] == 'name') $order = $filename;

					// Filtre le tableau en fonction du type mime choisi
					if(
						$_GET['filter'] == $type or
						($_GET['filter'] == "file" and $type != "image" and $type != "video" and $type != "audio") or 
						(!$_GET['filter'] or $_GET['filter'] == "resize")
					) 
					{					
						// $i pour être sûr d'incrémenter le tableau
						$tab_file[$order.$i] = array("filename" => $filename, "size" => $stat['size'], "time" => $stat['mtime'], "width" => $file_infos['0'], "height" => $file_infos['1'], "mime" => $file_infos['mime']);
					}

					$i++;
				}
			}			
		}

		// Tri du tableau
		if(!$sort) {								
			if(!$_GET['order'] or $_GET['order'] == 'time') $sort = 'DESC';// Tri par défaut
			elseif($_GET['order'] == 'size') $sort = 'DESC';
			elseif($_GET['order'] == 'name') $sort = 'ASC';
		}
		
		?>
		<ul class="unstyled pan man smaller">	
	
			<li class="add-file pas mat tc big" onclick="document.getElementById('upload-file').click();">
				<i class="fa fa-upload biggest pbs"></i><br>
				<?_e("Drag and drop a file here or click me");?>
				<input type="file" id="upload-file" style="display: none" multiple>
			</li>
			<?

			// S'il y a des fichiers dans la biblio
			if($tab_file)
			{
				uksort($tab_file, 'strnatcmp');// Tri ascendant
				if($sort == 'DESC') $tab_file = array_reverse($tab_file, true);// Tri Descendant
								
				$i = 1;
				// Affiche les fichiers en fonction du tri
				while(list($cle, $val) = each($tab_file)) 
				{				
					// Convertie la taille en mode lisible
					if($val['size'] >= 1048576) $val['size'] = round($val['size'] / 1048576) . "Mo";
					elseif($val['size'] >= 1024) $val['size'] = round($val['size'] / 1024) . "Ko";
					elseif($val['size'] < 1024) $val['size'] = $val['size'] . "oct";
					
					// Le type de fichier
					list($type, $ext) = explode("/", $val['mime']);
					switch($type)
					{
						default:						
							switch($ext)
							{
								default: $fa = "file-o"; break;
								case"zip": $fa = "file-archive-o"; break;
								case"msword": $fa = "file-word-o"; break;
								case"vnd.ms-excel": $fa = "file-excel-o"; break;
								case"vnd.ms-powerpoint": $fa = "file-powerpoint-o"; break;
								case"pdf": $fa = "file-pdf-o"; break;
							}
						break;
						case"text": 
							switch($ext)
							{
								default: $fa = "file-o"; break;
								case"plain": $fa = "file-text-o"; break;
								case"html": $fa = "file-code-o"; break;
							}
						break;
						case"video": $fa = "film"; break;
						case"audio": $fa = "volume-up"; break;
					}
					
					// Infos sur le fichier
					if($val['width'] and $val['height']) $info = $val['width']."x".$val['height']."px";
					else $info = pathinfo($val['filename'], PATHINFO_EXTENSION);
					
					// Affichage du fichier
					echo"<li class='pat mat tc' title=\"".utf8_encode($val['filename'])." | ".date("d-m-Y H:i:s", $val['time'])." | ".$val['mime']."\" id=\"dialog-media-".encode($_GET['filter'])."-".$i."\" data-file=\"media/".($_GET['filter'] == "resize"?"resize/":"").utf8_encode($val['filename'])."\" data-type=\"".$type."\">";

						if($type == "image") {
							echo"<img src=\"media/".($_GET['filter'] == "resize"?"resize/":"").$val['filename']."\">";
							echo"<a class='resize' title=\"".__("Get resized image")."\"><i class='fa fa-fw fa-compress bigger'></i></a>";
						}
						else echo"<div class='file'><i class='fa fa-fw fa-".$fa." mega'></i><div>".utf8_encode($val['filename'])."</div></div>";

						echo"						
						<div class='infos'>".$info." - ".$val['size']."</div>
						<a class='supp' title=\"".__("Delete file")."\"><i class='fa fa-fw fa-trash bigger'></i></a>
					</li>";
					
					$i++;
				}
			}

		?>
		</ul>

		<script>
			$(document).ready(function()
			{
				if($("#dialog-media-width").val() || $("#dialog-media-height").val()) $(".dialog-media .resize").remove();
			});
		</script>
		<?
	break;

	
	case "del-file":// Supprime un fichier

		login('medium', 'upload_file');// Vérifie que l'on est admin

		return unlink($_SERVER['DOCUMENT_ROOT'].$GLOBALS['path'].utf8_decode(strtok($_REQUEST['file'], "?")));
		
	break;


	case "get-img":// Renvoi une image et la resize si nécessaire

		login('medium', 'upload_file');// Vérifie que l'on est admin
		
		// On supprime les ? qui pourrait gêner à la récupération de l'image
		$file = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['path'].strtok($_POST['img'], "?");
		
		// Resize l'image ou simple copie
		echo resize($file, (int)$_POST['width'], (int)$_POST['height'], "media/resize/");

	break;


	case "upload-file":// Envoi d'une image sur le serveur et la resize si nécessaire
			
		login('medium', 'upload_file');// Vérifie que l'on est admin

		//echo "_POST:<br>"; highlight_string(print_r($_POST, true));
		//echo "_FILES:<br>"; highlight_string(print_r($_FILES, true));
		// @todo: Vérifier qu'il n'y a pas déjà un fichier qui a le même nom sur le serveur, si oui => alert pour overwrite
		// @todo: Proposer l'option crop (si w&h spécifié) / resize (si aucune des w&h ne sont pas spécifiés)
		
		// Récupération de l'extension
		$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
	
		// Hack protection : contre les doubles extensions = Encode le nom de fichier + supprime l'extension qui ne passe pas l'encode et l'ajoute après
		$filename = encode(basename($_FILES['file']['name'], ".".$ext)).".".strtolower($ext);

		// @todo trouver la bonne regex qui permet de n'avoir qu'un seul point
		// 2ème passe avec une whitelist pour supp tous les autres caractères indésirables et n'avoir qu'un seul point (pour l'ext)
		//$filename = preg_replace("([^a-z0-9\.\-_]|[\.]{2,})", "", $_FILES['file']['name']);
		// /^[a-z0-9]+\.[a-z]{3,4}$/  /[^a-z0-9\._-]+/  ([^a-z0-9\.\-_]|[\.]{2,})  [a-zA-Z0-9]{1,200}\.[a-zA-Z0-9]{1,10}

		$src_file = "media/".$filename;
		$root_file = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['path'].$src_file;
		
		// Check le type mime côté serveur
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$file_infos['mime'] = finfo_file($finfo, $_FILES['file']['tmp_name']);
		finfo_close($finfo);

		// Vérifie que le type mime est supporté (Hack protection : contre les mauvais mimes types)
		if(in_array($file_infos['mime'], $GLOBALS['mime_supported']))
		{
			// Le fichier tmp ne contient pas de php ou de javascript
			if(!preg_match("/<\?php|<scr/", file_get_contents($_FILES["file"]["tmp_name"])))
			{	
				@mkdir(dirname($root_file), 0705);

				// Upload du fichier
				if(move_uploaded_file($_FILES['file']['tmp_name'], $root_file))
				{
					// Type mime
					list($type, $ext) = explode("/", $file_infos['mime']);

					// Si c'est une image
					if($type == "image")
					{
						$force = null;

						// Taille de l'image uploadée
						list($source_width, $source_height, $type) = getimagesize($root_file);
						
						// Limite max de taille d'image pour l'upload global
						list($max_width, $max_height) = explode("x", $GLOBALS['max_image_size']);
						
						// On vérifie la bonne orientation de l'image jpeg
						if($type == 2) {// Exif ne fonctionne qu'avec les jpeg
							$exif = exif_read_data($root_file);
							if($exif['Orientation'] != 1) {
								$max_width = ($source_width > $max_width ? $max_width : $source_width);
								$max_height = ($source_height > $max_height ? $max_height : $source_height);
								$force = $exif['Orientation'];
							}
						}

						// Image trop grande (> global) pour le web : on la redimensionne
						if($source_width > $max_width or $source_height > $max_height or $force) 
						{
							$src_file = resize($root_file, $max_width, $max_height, "media/", $force);// Redimensionne

							unlink($root_file);// Supprime l'image originale puisque l'on ne garde que la maxsize

							$root_file = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['path'].explode("?", $src_file)[0];// La maxsize devient l'image root (explode: supp le timer)
						}
						

						// L'interface a demandé un redimensionnement ?
						$final_width = (int)$_POST['width'];
						$final_height = (int)$_POST['height'];
						if($_POST['resize'] and ($final_width and $source_width > $final_width) or ($final_height and $source_height > $final_height)) 
						{
							echo resize($root_file, $final_width, $final_height, "media/resize/");// Redimensionne

							//unlink($root_file);// Si on a redimensionné on supp l'image de base
						}
						else
							echo $src_file;// Retourne l'url du fichier original si pas de redimensionnement	
					}		
					else 
						echo $src_file;// Retourne l'url du fichier original		
				}
			}
		}

	break;



	case "dialog-icon":// Affichage des médias
		
		login('medium', 'edit_content');// Vérifie que l'on est admin

		// @todo: ajouter une recherche en js (qui masque)
		?>

		<div class="dialog-icon" title="<?_e("Icon Library")?>">

			<input type="hidden" id="dialog-icon-target" value="<?=htmlspecialchars($_GET['target'])?>"><!-- SUPP ?? -->
			<input type="hidden" id="dialog-icon-source" value="<?=htmlspecialchars($_GET['source'])?>">
			
			<input type="text" class="search w20 mbs" placeholder="<?_e("Search")?>" value="">

			<?
			//$pattern = '/\.([\w-]+):before\s*{\s*content:\s*(["\']\\\w+["\']);?\s*}/';
			//$pattern = '/\.(fa-(?:\w+(?:-)?)+):before\s*{\s*content:\s*"\\\\(.+)";?\s*}/';
			//$pattern = '/\\.(fa-\\w+):before{content:"(\\\\\w+)"}/';	
			$pattern = '/\\.(fa-(?:\\w+(?:-)?)+):before{content:"(\\\\\\w+)"}/';	
			
			// On récupère la css qui contient les icônes
			$subject = file_get_contents($GLOBALS['font_awesome']);
			
			// On extrait seulement les icônes
			preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER);
			//highlight_string(print_r($matches, true));
			
			// On crée un tableau propre
			foreach($matches as $match){ $icons[$match[1]] = $match[2];	}			

			?>
			<ul id="icon" class="unstyled pan man smaller">	
			<?
				// S'il y a des fichiers dans la biblio
				if($icons)
				{
					//uksort($icons, 'strnatcmp');// Tri Ascendant
					//if($sort == 'DESC') $icons = array_reverse($icons, true);// Tri Descendant
					
					while(list($cle, $val) = each($icons)) 
					{						
						echo"<li class='pat fl' title=\"".substr($cle, 3)."\"><i class='fa fa-fw biggest ".$cle."' id='".trim($val, '\\')."'></i></li>";
					}
				}
			?>
			</ul>

			<script>
			$(document).ready(function()
			{		
				// Recherche
				$(".dialog-icon .search").keyup(function() 
				{
					if($(this).val() == '') $("#icon li").show();
					else {
						$("#icon li").hide();
						$("#icon li[title*='"+$(this).val()+"']").show();
					}
				});

				// On selectionne une image
				$("#icon").on("click", "li", function(event)
				{
					var id = $("i", this).attr("id");
					
					// Effet
					$(".dialog-icon i").css("opacity","0.4");
					$("#"+id).css("opacity","1");

					// On ajoute l'icône
					exec_tool("insertIcon", id);

					// Fermeture de la dialog
					$(".dialog-icon").dialog("close");
				});
			});
			</script>
		</div>
		<?
	break;


	case "facebook-photos":// Liste des images que l'on a sur facebook
				
		//@todo: check si access token facebook disponible

		login('medium', 'edit_content');// Vérifie que l'on est admin

		// https://graph.facebook.com/me/albums
		// https://graph.facebook.com/id-album/photos?fields=source,name,id,link&access_token=
		// https://graph.facebook.com/id-album/picture > cover

		// url ultime qui renvoi tous les albums et les photos : https://graph.facebook.com/me/albums?fields=photos&access_token=CAAJ9CCDJ1kkBAGYMbiqGDHXCuYIEy4SMIWQ6GIJ4tMIlfuhtoLdjEOL323YEtOxpI95AshuDdxTxHFYXy4jPg8QYUgo5GJedRpnZBqzHSvbvm4nZBMsRlTs90Up4uZCGRha560lgsiH0IaysKx1VsDQLpm4dVrAQczMVYKvfWsVfCcZBuuQA
		
		// url ultime 2 :		https://graph.facebook.com/me/?fields=albums.fields%28id,name,cover_photo,photos.fields%28name,picture,source%29%29&access_token=CAAJ9CCDJ1kkBAGYMbiqGDHXCuYIEy4SMIWQ6GIJ4tMIlfuhtoLdjEOL323YEtOxpI95AshuDdxTxHFYXy4jPg8QYUgo5GJedRpnZBqzHSvbvm4nZBMsRlTs90Up4uZCGRha560lgsiH0IaysKx1VsDQLpm4dVrAQczMVYKvfWsVfCcZBuuQA

		// /me/albums  /me/photos/uploaded
		if($_SESSION['access_token_external'] and $_SESSION['login_api'] == "facebook")
		$response = json_decode(curl("https://graph.facebook.com/me/albums?&access_token=".$tab_token_response['access_token']), true);

		echo "response<br>"; highlight_string(print_r($response, true));

		//@todo: prévoir une navigation par page pour les albums et les photos
		//@todo: si album vide = on ne l'affiche pas

	break;


	
	// SETUP / INSTALL / CONFIG

	case "setup-update":// Mise à jour des données de configuration
		
		// Chemin des fichiers de config
		$config_sample_file = "config.init.php";
		$config_final_file = "../config.php";

		// Verification du nonce
		if($_SESSION['nonce'] == $_REQUEST['nonce'] and (!$GLOBALS['db_server'] or !$GLOBALS['db_user'] or !$GLOBALS['db']))
		{			
			// Traduction de la page d'installation
			$add_translation = array(
				"Table already exists" => array("fr" => "La table existe déjà"),
				"User already exists : update password" => array("fr" => "L'utilisateur existe déjà : mise à jour du mot de passe"),
				"Wrong email" => array("fr" => "Mauvais email"),
				"Successful installation ! create your homepage !" => array("fr" => "Installation réussie ! créer votre page d'accueil !")
			);

			add_translation($add_translation);

			
			if($_POST['db_server'] and $_POST['db_user'] and $_POST['db'])
			{
				
				// BASE DE DONNEE
				// Connexion à la bdd
				$GLOBALS['connect'] = @new mysqli(addslashes($_POST['db_server']), addslashes($_POST['db_user']), addslashes($_POST['db_pwd']), addslashes($_POST['db']));
				
				if ($GLOBALS['connect']->connect_errno) {// Erreur
					?>
					<script>
						submittable();
						error("<?=utf8_encode($GLOBALS['connect']->connect_error);?>");
					</script>
					<?
					exit;
				}
				else {// Réussite
					
					// Nom des tables
					$GLOBALS['table_content'] = addslashes($_POST['db_prefix']."content");
					$GLOBALS['table_meta'] = addslashes($_POST['db_prefix']."meta");
					$GLOBALS['table_user'] = addslashes($_POST['db_prefix']."user");
										
					// Vérification de l'existence des base de données
					if($GLOBALS['connect']->query("SELECT id FROM ".$GLOBALS['table_content'])){// Table déjà existante
						?>
						<script>
							light("<?_e("Table already exists")?> : content");
						</script>
						<?
					}
					else {// Création de la base de données
						$GLOBALS['connect']->query("
							CREATE TABLE IF NOT EXISTS `".$GLOBALS['table_content']."` (
								`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
								`state` varchar(20) NOT NULL DEFAULT 'deactivate',
								`lang` varchar(8) NOT NULL,
								`type` varchar(20) NOT NULL DEFAULT 'page',
								`tpl` varchar(80) NOT NULL,
								`url` varchar(60) NOT NULL,
								`title` varchar(60) NOT NULL,
								`description` varchar(160) NOT NULL,
								`content` longtext NOT NULL,
								`user_update` bigint(20) UNSIGNED NOT NULL,
								`date_update` datetime NOT NULL,
								`user_insert` bigint(20) UNSIGNED NOT NULL,
								`date_insert` datetime NOT NULL,
								PRIMARY KEY (`id`),
								KEY `state` (`state`),
								KEY `type` (`type`),
								KEY `url` (`url`),
								KEY `lang` (`lang`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;
						");

						if($GLOBALS['connect']->error) {
							?>
							<script>
								submittable();
								error("<?=utf8_encode($connect->error);?>");
							</script>
							<?
							exit;
						}
					}

					// Vérification de l'existence des base de données
					if($GLOBALS['connect']->query("SELECT id FROM ".$GLOBALS['table_meta'])){// Table déjà existante
						?>
						<script>
							light("<?_e("Table already exists")?> : meta");
						</script>
						<?
					}
					else {// Création de la base de données
						$GLOBALS['connect']->query("
							CREATE TABLE IF NOT EXISTS `".$GLOBALS['table_meta']."` (
								`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
								`type` varchar(20) NOT NULL,
								`cle` varchar(255) NOT NULL,
								`val` text NOT NULL,
								PRIMARY KEY (`id`) USING BTREE,
								KEY `type` (`type`),
								KEY `cle` (`cle`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;
						");

						if($GLOBALS['connect']->error) {
							?>
							<script>
								submittable();
								error("<?=utf8_encode($connect->error);?>");
							</script>
							<?
							exit;
						}
					}

					// Vérification de l'existence des base de données
					if($GLOBALS['connect']->query("SELECT id FROM ".$GLOBALS['table_user'])){// Table déjà existante
						?>
						<script>
							light("<?_e("Table already exists")?> : user");
						</script>
						<?
					}
					else {// Création de la base de données
						$GLOBALS['connect']->query("
							CREATE TABLE IF NOT EXISTS `".$GLOBALS['table_user']."` (
								`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
								`state` varchar(20) NOT NULL DEFAULT 'active',
								`auth` set('".implode("','", $GLOBALS['auth_level'])."') NOT NULL,
								`name` varchar(60) NOT NULL,
								`email` varchar(100) NOT NULL,
								`password` char(64) DEFAULT NULL,
								`salt` char(16) DEFAULT NULL,
								`oauth` text NOT NULL,
								`token` varchar(255) DEFAULT NULL,
								`date_update` datetime NOT NULL,
								`date_insert` datetime NOT NULL,
								PRIMARY KEY (`id`),
								UNIQUE KEY `email` (`email`),
								KEY `state` (`state`)								
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;
						");

						if($GLOBALS['connect']->error) {
							?>
							<script>
								submittable();
								error("<?=utf8_encode($connect->error);?>");
							</script>
							<?
							exit;
						}
					}
				
					
					// UTILISATEUR

					// Vérification de l'email
					$email = filter_input(INPUT_POST, 'email_contact', FILTER_SANITIZE_EMAIL);
					if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
						?>
						<script>
							submittable();
							light("<?_e("Wrong email")?>");
						</script>
						<?
						exit;
					}
					else {

						// Clean l'email pour éviter les injections
						$email = $GLOBALS['connect']->real_escape_string($email);

						// Crée un hash si pas déjà un chargé par le config maison
						if(!$GLOBALS['pub_hash']) $GLOBALS['pub_hash'] = $_POST['pub_hash'] = make_pwd(mt_rand(32, 64), true, true);
						if(!$GLOBALS['priv_hash']) $GLOBALS['priv_hash'] = $_POST['priv_hash'] = make_pwd(mt_rand(32, 64), true, true);
						if(!$GLOBALS['pwd_hash_loop']) $GLOBALS['pwd_hash_loop'] = $_POST['pwd_hash_loop'] = mt_rand(60536, 6536);

						// Email pour le login automatique
						$_POST['email'] = $email;
						
						// Vérifie que l'utilisateur n'existe pas déjà
						$sel = $GLOBALS['connect']->query("SELECT id FROM ".addslashes($_POST['db_prefix'])."user WHERE email='".$email."' AND state='active' LIMIT 1");
						if($res = $sel->fetch_assoc())// User déjà existant : on update ses données
						{						
							// Création de la requête
							$sql = "UPDATE ".addslashes($_POST['db_prefix'])."user SET ";
							$sql .= "state = 'active', ";
							$sql .= "auth = '".addslashes(implode(",", $GLOBALS['auth_level']))."', ";// Donne tous les droits
							
							list($password, $unique_salt) = hash_pwd($_POST['password']);

							if($password and $unique_salt) {
								$sql .= "password = '".addslashes($password)."', ";
								$sql .= "salt = '".addslashes($unique_salt)."', ";
								//if($GLOBALS['security'] != 'high') $sql .= "token = '".addslashes(token_light((int)$_REQUEST['uid'], $unique_salt))."', "; Voir si utile !??
							}

							$sql .= "date_update = NOW() ";

							$sql .= "WHERE id = '".$res['id']."'";

							// Exécution de la requête
							$GLOBALS['connect']->query($sql);

							if($GLOBALS['connect']->error) {
								?>
								<script>
									submittable();
									error("<?=utf8_encode($connect->error);?>");
								</script>
								<?
								exit;
							}

							?>
							<script>
								light("<?_e("User already exists : update password")?>");
							</script>
							<?
						}
						else {// Création de l'utilisateur admin avec tous les droits

							// Création de la requête
							$sql = "INSERT INTO ".addslashes($_POST['db_prefix'])."user SET ";
							$sql .= "state = 'active', ";
							$sql .= "auth = '".addslashes(implode(",", $GLOBALS['auth_level']))."', ";// Donne tous les droits

							$sql .= "email = '".addslashes($email)."', ";
							
							list($password, $unique_salt) = hash_pwd($_POST['password']);

							if($password and $unique_salt) {
								$sql .= "password = '".addslashes($password)."', ";
								$sql .= "salt = '".addslashes($unique_salt)."', ";
								//if($GLOBALS['security'] != 'high') $sql .= "token = '".addslashes(token_light((int)$_REQUEST['uid'], $unique_salt))."', "; Voir si utile !??
							}

							$sql .= "date_insert = NOW() ";

							// Exécution de la requête
							$GLOBALS['connect']->query($sql);

							if($GLOBALS['connect']->error) {
								?>
								<script>
									submittable();
									error("<?=utf8_encode($connect->error);?>");
								</script>
								<?
								exit;
							}
						}

						// Login automatique
						login();


						// ECRITURE DE LA CONFIGRATION
												
						// Ouverture du fichier config. Si pas de config on prend le sample
						if(file_exists($config_final_file)) $config_file = file($config_final_file);
						else $config_file = file($config_sample_file);

						// Séparation des données du chemin du site
						$parse_url = parse_url($_POST['scheme_domain_path']);
						$_POST['scheme'] = $parse_url['scheme']."://";
						$_POST['domain'] = $parse_url['host'];
						$_POST['path'] = $parse_url['path'];

						// On parcourt le fichier config
						foreach($config_file as $line_num => $line) 
						{
							// On récupère la clé de la variable en cours
							preg_match("/GLOBALS\[\'([a-z_]+)\'\]/", $line, $match);
							
							$key = $match[1];
							
							// Changement de la ligne et ajout de la nouvelle variable
							if(isset($_POST[$key])) $config_file[$line_num] = "\$GLOBALS['".$key."'] = \"".addcslashes($_POST[$key], "\\'")."\";\r\n";							
						}

						unset($line);

						// écriture dans le fichier config
						$fopen = fopen($config_final_file, 'w');
						foreach($config_file as $line) {
							fwrite($fopen, $line);
						}
						fclose($fopen);
						
						// Force les droits sur le fichier config
						chmod($config_final_file, 0666);


						// Message de bienvenue et d'information qu'il faut créé la page d'accueil du site
						?>
						<script>
							light("<?_e("Successful installation ! create your homepage !")?>");
							setTimeout(function(){
								 $(".ui-state-highlight").slideUp("slow").fadeOut(function() {
									 window.location.reload();// window.location = window.location.href;
								 });
							}, 5000);
						</script>
						<?

					}
				}
			}
		}

		exit;

	break;


	case "setup":// Formulaire de configuration

		//@todo: ajouter la possibilité de récup notre propre id fb, google, yah, ms (mode silencieux de login tiers)
		//@todo: voir pour utiliser ce fichier également en ajax pour édit la config par la suite
		//@todo: Ajouter un lien pour test les connexions tierses
		//@todo: donner les URL à rentrer dans les applications tierses
		//@todo: ajouter un droit d'édition light de la config (nom du site, code analytics, mail contact...) ou visible par tous les éditeurs de contenu ?
		//@todo: Vérif le cas ou pas de fichier conf existe
		//@todo: Vérif le cas ou fichier conf exist

		// Pour éviter les problèmes de cache qui appèlerais un fichier inexistant
		if($_SERVER['REDIRECT_URL']) {
			header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
			exit("<h1>404 error : page not found</h1>");
		}

		// Charge la config maison si elle existe
		@include("config.php");

		// Traduction de la page d'installation
		$add_translation = array(
			"Site Installation" => array("fr" => "Installation du site"),

			"Address database" => array("fr" => "Adresse de la base de données"),
			"Name of the data base" => array("fr" => "Nom de la base de données"),
			"MySQL Username" => array("fr" => "Nom d'utilisateur MySQL"),
			"MySQL User Password" => array("fr" => "Mot de passe de l'utilisateur MySQL"),
			"Table Prefix" => array("fr" => "Préfixe de table"),

			"Name of the site" => array("fr" => "Nom du site"),
			"Site theme" => array("fr" => "Thème du site"),

			"Site Location" => array("fr" => "Emplacement du site"),

			"Administrator email" => array("fr" => "Email administrateur"),
			"Administrator password" => array("fr" => "Mot de passe administrateur"),

			"Option" => array("fr" => "Option"),

			"Google analytics code" => array("fr" => "Code google analytics"),

			"System login third" => array("fr" => "Système de login tièrce"),

			"Id of the app facebook" => array("fr" => "Id de l'app facebook"),
			"Secret key of the app facebook" => array("fr" => "Clé secrete de l'app facebook"),

			"Id of the app google" => array("fr" => "Id de l'app google"),
			"Secret Key to google app" => array("fr" => "Clé secrete de l'app google"),

			"Id of the app yahoo" => array("fr" => "Id de l'app yahoo"),
			"Secret key to the app yahoo" => array("fr" => "Clé secrete de l'app yahoo"),

			"Id of the app microsoft" => array("fr" => "Id de l'app microsoft"),
			"Secret key of microsoft app" => array("fr" => "Clé secrete de l'app microsoft"),

			"Start installation" => array("fr" => "Lancer l'installation")
		);

		add_translation($add_translation);


		// Chemin complet du site
		$scheme_domain_path = "";
		if($GLOBALS['scheme'] and $GLOBALS['domain'] and $GLOBALS['path'])
			$scheme_domain_path = $GLOBALS['scheme'] . $GLOBALS['domain'] . $GLOBALS['path'];
		else {
			if($_SERVER['[REQUEST_SCHEME']) $scheme_domain_path .= $_SERVER['[REQUEST_SCHEME']."://";
			else $scheme_domain_path .= "http://";
			
			$scheme_domain_path .= $_SERVER['SERVER_NAME'];
			$scheme_domain_path .= $_SERVER['REQUEST_URI'];
		}

		// Nom du site
		$domains = explode('.', $_SERVER['SERVER_NAME']);
		$sitename = ($GLOBALS['sitename'] ? $GLOBALS['sitename'] : ucfirst($domains[count($domains)-2]));


		header('Content-type: text/html; charset=UTF-8');

		?><!DOCTYPE html>
		<html lang="<?=$lang;?>">
		<head>
			
			<meta charset="utf-8">

			<title><?_e("Site Installation");?></title>

			<meta name="robots" content="noindex, nofollow">

			<meta name="viewport" content="width=device-width, initial-scale=1">

			<link rel="stylesheet" href="<?=$GLOBALS['jquery_ui_css'];?>">
			<link rel="stylesheet" href="<?=$GLOBALS['font_awesome']?>">	
			<link rel="stylesheet" href="api/global.css?">

			<style>
			@media screen and (max-width: 640px) 
			{
				.w80 { width: 95%; }
				.w10, .w20, .w30, .w50, .w60 { width: 90%; }

				label { display: block; }
			}
			</style>

			<script src="<?=$GLOBALS['jquery'];?>"></script>
			<script src="<?=$GLOBALS['jquery_ui'];?>"></script>

			<script src="api/lucide.init.js"></script>

			<script>
				submittable = function() {
					// Icône de chargement
					$("#setup button i").removeClass("fa-spin fa-cog").addClass("fa-cogs");
					
					// Active le submit
					$("#setup button").attr("disabled", false);
				}

				$(document).ready(function()
				{
					// Setup
					$("#setup").submit(function(event) 
					{
						event.preventDefault();

						// Icône de chargement
						$("#setup button i").removeClass("fa-cogs").addClass("fa-spin fa-cog");
						
						// Désactive le submit
						$("#setup button").attr("disabled", true);

						// Variable
						var data = {};
						 $("input, select", $("#setup")).each(function(index) {
							data[$(this).attr("id")] = this.value;
						 })

						$.ajax(
						{ 
							type: "POST",
							url: "api/ajax.admin.php?mode=setup-update",
							data: data,
							success: function(html){ $("body").append(html); }
						});
					});
				});
			</script>

			<!--[if lt IE 9]>
				<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
			<![endif]-->

		</head>
		<body>

			<div class="center w80">

				<form id="setup">

					<input type="hidden" id="nonce" name="nonce" value="<?=nonce("nonce");?>">
					
					<h2><?_e("Site Installation");?></h2>

					<ul class="unstyled">

						<li><label class="w30"><?_e("Address database");?></label> <input type="text" id="db_server" value="<?=$GLOBALS['db_server'];?>" placeholder="localhost" required class="w60 vatt"></li>
						
						<li><label class="w30"><?_e("Name of the data base");?></label> <input type="text" id="db" value="<?=$GLOBALS['db'];?>" required class="w60 vatt"></li>
						
						<li><label class="w30"><?_e("MySQL Username");?></label> <input type="text" id="db_user" value="<?=$GLOBALS['db_user'];?>" placeholder="root" required class="w60 vatt"></li>
						
						<li>
							<label class="w30"><?_e("MySQL User Password");?></label> <input type="password" id="db_pwd" value="<?=$GLOBALS['db_pwd'];?>" class="w60 vatt">
							<a href="javascript:if($('#db_pwd').attr('type') == 'password') $('#db_pwd').attr('type','text'); else $('#db_pwd').attr('type','password'); void(0);"><i class="fa fa-fw fa-eye mts vam"></i></a>
						</li>
						
						<li><label class="w30"><?_e("Table Prefix");?></label> <input type="text" id="db_prefix" value="<?=$GLOBALS['db_prefix'];?>" placeholder="tl_" class="w10 vatt"></li>


						<li class="mtm"><label class="w30 bold"><?_e("Name of the site");?></label> <input type="text" id="sitename" value="<?=$sitename;?>" class="w60 vatt"></li>
						<li>
							<label class="w30"><?_e("Site theme");?></label>

							<select id="theme" required class="vatt">
							<?
							// Un thème dans la racine
							if(file_exists("theme/header.php")) echo"<option value=\"\"".($GLOBALS['theme'] == "" ? " selected":"").">/</option>";
							
							// Des dossiers de thème
							$scandir = array_diff(scandir("theme/"), array('..', '.', 'tpl'));
							while(list($cle, $file) = each($scandir)) { 
								if(is_dir("theme/".$file)) echo"<option value=\"".$file."/\"".($GLOBALS['theme'] == $file."/" ? " selected":"").">".$file."</option>";
							}							
							?>					
							</select>
							
						</li>
						<li><label class="w30"><?_e("Site Location");?></label> <input type="text" id="scheme_domain_path" value="<?=$scheme_domain_path;?>" required class="w60 vatt"></li>


						<li class="mtm">
							<label class="w30 bold"><i class="fa fa-fw fa-user-secret"></i> <?_e("Administrator email");?></label> <input type="email" id="email_contact" value="<?=$GLOBALS['email_contact'];?>" required maxlength="100" class="w60 vatt">					
						</li>
						<li>
							<label class="w30 bold"><i class="fa fa-fw fa-key"></i> <?_e("Administrator password");?></label>
							<input type="password" id="password" required class="w60 vatt">
							<a href="javascript:if($('#password').attr('type') == 'password') $('#password').attr('type','text'); else $('#password').attr('type','password'); void(0);"><i class="fa fa-fw fa-eye mts vam"></i></a>
							<a href="javascript:$('#setup #password').make_password();" title="<?_e("Suggest a password");?>"><i class="fa fa-fw fa-refresh mts vam"></i></a>
						</li>

						<!-- 
						<li class="mtl bold"><?_e("Option");?></li>

						<li><label class="w30"><i class="fa fa-fw fa-line-chart"></i> <?_e("Google analytics code");?></label> <input type="text" id="google_analytics" placeholder="UA-00000000-1" class="w20 vatt"></li>


						<li class="mtm bold"><?_e("System login third");?></li>

						<li class="mts">
							<label class="w30"><i class="fa fa-fw fa-facebook-f"></i> <?_e("Id of the app facebook");?></label> <input type="text" id="facebook_api_id" placeholder="" class="w60 vatt">
							<a href="https://developers.facebook.com/apps/" target="_blank"><i class="fa fa-fw fa-info-circle mts vam"></i></a>
						</li>
						<li><label class="w30"><?_e("Secret key of the app facebook");?></label> <input type="text" id="facebook_api_secret" placeholder="" class="w60 vatt"></li>

						<li class="mts">
							<label class="w30"><i class="fa fa-fw fa-google"></i> <?_e("Id of the app google");?></label> <input type="text" id="google_api_id" placeholder="" class="w60 vatt">
							<a href="https://console.developers.google.com/apis/credentials/oauthclient" target="_blank"><i class="fa fa-fw fa-info-circle mts vam"></i></a>
						</li>
						<li><label class="w30"><?_e("Secret Key to google app");?></label> <input type="text" id="facebook_api_secret" placeholder="" class="w60 vatt"></li>
						
						<li class="mts">
							<label class="w30"><i class="fa fa-fw fa-yahoo"></i> <?_e("Id of the app yahoo");?></label> <input type="text" id="yahoo_api_id" placeholder="" class="w60 vatt">
							<a href="https://developer.yahoo.com/apps/" target="_blank"><i class="fa fa-fw fa-info-circle mts vam"></i></a>
						</li>
						<li><label class="w30"><?_e("Secret key to the app yahoo");?></label> <input type="text" id="yahoo_api_secret" placeholder="" class="w60 vatt"></li>

						<li class="mts">
							<label class="w30"><i class="fa fa-fw fa-windows"></i> <?_e("Id of the app microsoft");?></label> <input type="text" id="microsoft_api_id" placeholder="" class="w60 vatt">
							<a href="https://account.live.com/developers/applications/create" target="_blank"><i class="fa fa-fw fa-info-circle mts vam"></i></a>
						</li>
						<li><label class="w30"><?_e("Secret key of microsoft app");?></label> <input type="text" id="microsoft_api_secret" placeholder="" class="w60 vatt"></li>
						 -->
					</ul>

					<button class="fr mam mbl bold"><?_e("Start installation");?> <i class="fa fa-fw fa-cogs"></i></button>

				</form>

			</div>

		</body>
		</html>
		<?

		exit;

	break;

}
?>