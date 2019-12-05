<?
include_once("../config.php");// Les variables
include_once("function.php");// Fonction

$lang = get_lang();// Sélectionne  la langue
load_translation('api');// Chargement des traductions du système

switch($_GET['mode'])
{
	default:	
	break;

	case "edit":// Lancement du mode édition du contenu de la page
				
		unset($_SESSION['nonce']);// Pour éviter les interférences avec un autre nonce de session
		
		login('high', 'edit-'.($_GET['type']?encode($_GET['type']):"page"));// Vérifie que l'on a le droit d'éditer les contenus
		
		// Si on doit recharger la page avant de lancer le mode édition
		if(isset($_REQUEST['callback']) and $_REQUEST['callback'] == "reload_edit")
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
			
			<link rel="stylesheet" href="<?=$GLOBALS['jquery_ui_css']?>">


			<!-- Barre du haut avec bouton sauvegarder et option -->			
			<div id="admin-bar" class="none">

				<div id="user" class="fl pat"><i class="fa fa-fw fa-user-circle bigger" title="<?_e("Show user info")?>"></i></div>
				
				<!-- list/bars -->
				<div id="list-content" class="fl pat"><i class="fa fa-menu vam" title="<?_e("List of contents")?>"></i></div>

				<div id="meta-responsive" class="fl mat none small-screen"><i class="fa fa-fw fa-pencil bigger" title="<?_e("Page title")?>"></i></div>

				<div id="meta" class="fl mat w30 no-small-screen">

					<input type="text" id="title" value="" placeholder="<?_e("Page title")?>" title="<?_e("Page title")?>" maxlength="70" class="w100 bold">

					<div class="w50">
						<div class="tooltip slide-left fire pas mas mlt">

							<div class="small">
								<?_e("Description for search engines")?>

								<div class="fr">
									<input type="checkbox" id="noindex"> <label for="noindex" class="mrs" title="<?_e("Les moteurs de recherche ne référencent pas cette page")?>">noindex</label>
									<input type="checkbox" id="nofollow"> <label for="nofollow" title="<?_e("Empêche les liens d'être suivis par les robots et de transmettre de la popularité")?>">nofollow</label>
								</div>
							</div>
							<input type="text" id="description" value="" maxlength="160" class="w100">

							<div class="small mtm"><?_e("Formatted web address")?></div>
							<div class="grid">
								<input type="text" id="permalink" value="" placeholder="<?_e("Permanent link: 'home' if homepage")?>" maxlength="60" class="w50 mrm">
								
								<span id="ispage" class="none"><input type="checkbox" id="homepage"> <label for="homepage" class="mrs"><?_e("Home page")?></label></span>

								<label id="refresh-permalink"><i class="fa fa-fw fa-arrows-cw"></i><?_e("Regenerate address")?></label>
							</div>

							<div class="mod mtm">

								<div class="fl mrl">
									<div class="small"><?_e("Type of page")?></div>
									<div>
										<select id="type">
											<?
											foreach($GLOBALS['add_content'] as $cle => $array)
											{
												if(isset($_SESSION['auth']['add-'.$cle]))
													echo'<option value="'.$cle.'">'.__($cle).'</option>';
											}
											?>
										</select>
									</div>
								</div>
								
								<div class="fl mrl">
									<div class="small"><?_e("Template")?></div>
									<div>
										<select id="tpl">
											<?
											$scandir = array_diff(scandir($_SERVER['DOCUMENT_ROOT'].$GLOBALS['path']."theme/".$GLOBALS['theme'].($GLOBALS['theme']?"/":"")."tpl/"), array('..', '.'));
											//while(list($cle, $filename) = each($scandir)) PHP 7.2
											foreach($scandir as $cle => $filename)
											{			
												$filename = pathinfo($filename, PATHINFO_FILENAME);
												echo'<option value="'.$filename.'">'.$filename.'</option>';
											}
											?>	
										</select>
									</div>
								</div>	

								<div class="fl">
									<div class="small"><?_e("Creation date")?></div>
									<div>
										<input type="text" id="date-insert" class="w150p">
									</div>
								</div>

							</div>

							
							<div class="small mtm"><?_e("Image on social networks")?></div>
							<div class=""><span class="editable-media" id="og-image"><img src=""></span></div>
							
						</div>
					</div>

				</div>		

				<div id="close" class="fr mrt bigger" title="<?_e("Close the edit mode")?>"><i class="fa fa-fw fa-cancel vatt"></i></div>

				<button id="save" class="fr mat small" title="<?_e("Save")?>"><span class="no-small-screen"><?_e("Save")?></span> <i class="fa fa-fw fa-floppy big"></i></button>

				<button id="del" class="fr mat small o50 ho1 t5" title="<?_e("Delete")?>"><span class="no-small-screen"><?_e("Delete")?></span> <i class="fa fa-fw fa-trash big"></i></button>

				<div class="fr mat mrs switch o50 ho1 t5"><input type="checkbox" id="state-content" class="none"><label for="state-content" title="<?_e("Activation status")?>"><i></i></label></div>

			</div>


			<script>				
				// Update les nonces dans la page courante pour éviter de perdre le nonce
				$("#nonce").val('<?=$_SESSION['nonce']?>');

				<?
				// Outil dispo dans la toolbox pour les contenus
				if($GLOBALS['toolbox'])
				foreach($GLOBALS['toolbox'] as $cle => $val) { echo'toolbox_'.$val.' = true;'; }
				?>
			
				// Chargement de Jquery UI
				$.ajax({
			        url: "<?=$GLOBALS['jquery_ui']?>",
			        dataType: 'script',
			        cache: true,
					success: function()
					{ 		
						// Chargement de la css d'edition		
						$("body").append("<link rel='stylesheet' href='<?=$GLOBALS['path']?>api/lucide.css'>");
						
						// Affichage de la barre d'admin
						$("#admin-bar").show();				

						// Ajoute la marge haute
						$("body").addClass("body-margin-top");

						// Si Jquery UI bien charger on charge la lib qui rend le contenu éditable		
						var script = document.createElement('script');
						script.src = path+"api/lucide.edit.js?<?=$GLOBALS['cache']?>";
						document.body.appendChild(script);		

					},
			        async: true
			    });				
			</script>
			<?
		}

	break;


	case "add-content":// Dialog pour ajouter une page

		unset($_SESSION['nonce']);// Pour éviter les interférences avec un autre nonce de session

		login('medium');

		// @todo metre en none, chaché les options avancé (permalien, regen, home)

		// Dialog : titre, template, langue
		?>
		<link rel="stylesheet" href="<?=$GLOBALS['jquery_ui_css']?>">

		<link rel="stylesheet" href="<?=$GLOBALS['path']?>api/lucide.css?0.1">


		<div class="dialog-add" title="<?_e("Add content")?>">
			
			<input type="hidden" id="nonce" value="<?=nonce("nonce");?>">

			<ul class="small">
				<?
				foreach($GLOBALS['add_content'] as $cle => $array)
				{
					if(isset($_SESSION['auth']['add-'.$cle])){
						echo'<li data-filter="'.$cle.'" data-tpl="'.$array['tpl'].'"><a href="#add-'.$cle.'"><i class="fa '.$array['fa'].'"></i> <span>'.__("Add ".$cle).'</span></a></li>';
					}
				}
				?>
			</ul>					

			<div class="none">
				<?
				reset($GLOBALS['add_content']);
				foreach($GLOBALS['add_content'] as $cle => $array)
				{
					if(isset($_SESSION['auth']['add-'.$cle])) echo'<div id="add-'.$cle.'"></div>';
				}
				?>
			</div>
			

			<div>

				<div class="mas">
					<input type="text" id="title" placeholder="<?_e("Title")?>" maxlength="60" class="w60 bold">
					
					<select id="tpl" required class="w30">
						<option value=""><?_e("Select template")?></option>
						<?
						$scandir = array_diff(scandir($_SERVER['DOCUMENT_ROOT'].$GLOBALS['path']."theme/".$GLOBALS['theme'].($GLOBALS['theme']?"/":"")."tpl/"), array('..', '.'));
						foreach($scandir as $cle => $filename)
						{			
							$filename = pathinfo($filename, PATHINFO_FILENAME);
							echo"<option value=\"".$filename."\">".$filename."</option>";
						}
						?>					
					</select>
				</div>

				<div class="mas mtm">
					<input type="text" id="permalink" placeholder="<?_e("Permanent link")?>" maxlength="60" class="w50 mrm">
					<label for="homepage" class="mrs mtn none"><input type="checkbox" id="homepage"> <?_e("Home page")?></label>
					<label id="refresh-permalink" class="mtn"><i class="fa fa-fw fa-arrows-cw"></i><?_e("Regenerate address")?></label>
				</div>

			</div>


			<script>
			$(function()
			{
				// Update les nonces dans la page courante pour éviter de perdre le nonce
				$("#nonce").val('<?=$_SESSION['nonce']?>');			

				// Au click sur un onglet
				$(".dialog-add ul li").click(function(event) {
					var filter = $(this).data("filter");

					// Affiche ou masque le bt permalink home
					if(filter == "page") $("label[for='homepage']").show();
					else $("label[for='homepage']").hide();

					// Force la template du type
					$(".dialog-add #tpl").val($(this).data("tpl"));

					// Reconstruit le permalink
					refresh_permalink(".dialog-add");
				});

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

				// Chargement de Jquery UI
				$.ajax({
			        url: "<?=$GLOBALS['jquery_ui']?>",
			        dataType: 'script',
			        cache: true,
			        async: true,
					success: function()// Si Jquery UI bien charger on ouvre la dialog
					{				
						// Fermeture de la dialog de connexion
						$("#dialog-connect").dialog("close");

						// Création de la dialog d'ajout
						$(".dialog-add").dialog({
							modal: true,
							width: "60%",
							buttons: {
								"OK": function() 
								{								
									// Dans quel onglet on se situe
									type = $(".ui-tabs-nav .ui-state-active").data("filter");

									if(!$(".dialog-add #tpl").val()) error(__("Thank you to select a template"));
									else {
										$.ajax({
											type: "POST",
											url: path + "api/ajax.admin.php?mode=insert",
											data: {
												"title": $(".dialog-add #title").val(),
												"tpl": $(".dialog-add #tpl").val(),
												"permalink": $(".dialog-add #permalink").val(),
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

								// Template sélectionnée par défaut
								$(".dialog-add #tpl").val($(".ui-dialog ul li[aria-selected='true']").data("tpl"));
							},
							close: function() {
								$(".dialog-add").remove();					
							}
						});
					}
			    });	
				
			});
			</script>

		</div>
		<?				
	break;


	case "insert":// Crée une nouvelle page

		include_once("db.php");// Connexion à la db

		$type = encode($_POST['type']);

		login('high', 'add-'.$type);// Vérifie que l'on a le droit d'ajouter une page

		// @todo verifier que le permalink est bien enregister si il est diff du titre

		$url = (encode($_POST['permalink']) ? encode($_POST['permalink']) : encode($_POST['title']));

		if($url) 
		{
			// Ajoute la page
			$sql = "INSERT ".$table_content." SET ";
			$sql .= "title = '".addslashes($_POST['title'])."', ";
			$sql .= "tpl = '".addslashes($_POST['tpl'])."', ";
			$sql .= "url = '".$url."', ";
			$sql .= "lang = '".$lang."', ";
			$sql .= "type = '".$type."', ";
			$sql .= "user_insert = '".(int)$_SESSION['uid']."', ";
			$sql .= "date_insert = NOW() ";
			
			$connect->query($sql);
			
			if($connect->error)// Si il y a une erreur
				echo htmlspecialchars($sql)."\n<script>error(\"".htmlspecialchars($connect->error)."\");</script>";

			else // Sauvegarde réussit
			{
				// Pose un cookie pour demander l'ouverture de l'admin automatiquement au chargement
				setcookie("autoload_edit", "true", time() + 60*60, $GLOBALS['path'], $GLOBALS['domain']);
				
				?>
				<script>
				$(function()
				{		
					// Redirection vers la page crée
					document.location.href = "<?=make_url($url, array("domaine" => true));?>";
				});
				</script>
				<?
			}
		}
		else 
			echo"<script>error(\"".__("No permanent link for content")."\");</script>";	

	break;


	case "update":// Sauvegarde du contenu éditable de la page

		include_once("db.php");// Connexion à la db
		
		//highlight_string(print_r($_POST, true)); exit;

		$type = ($_POST['type']?encode($_POST['type']):"page");// Type de contenu

		login('high', 'edit-'.$type);// Vérifie que l'on peut éditer une page
		
		// PREPARATION POUR LE CONTENU ET NAVIGATION
		// On récupère les données de la page pour comparaison
		$sel = $connect->query("SELECT * FROM ".$table_content." WHERE url='".get_url($_POST['url'])."' AND lang='".$lang."' LIMIT 1");
		$res = $sel->fetch_assoc();		
		
		// Si le titre à changer et que l'on n'est pas sur le home on change l'URL de la page
		if($res['url'] != encode($_POST['permalink']) or (encode($_POST['title']) and !encode($_POST['permalink']))) 
		{
			if(!encode($_POST['permalink']) and encode($_POST['title'])) 
				$change_url = encode($_POST['title']);
			elseif(!encode($_POST['permalink']) and !encode($_POST['title'])) 
				$change_url = $type."-".$res['id'];
			else 
				$change_url = encode($_POST['permalink']);
		}


		// Check si la page a bien une url par sécuritée
		if((isset($change_url) and $change_url == "") or get_url($_POST['url']) == "")
			exit("<script>error(\"".__("No permanent link for content")."\");</script>");
	

		// MENU DE NAVIGATION
		if(isset($_POST['nav']))
		{
			// On regarde s'il y a déjà des données
			$sel_nav = $connect->query("SELECT * FROM ".$table_meta." WHERE type='nav' AND cle='".$lang."' LIMIT 1");
			$res_nav = $sel_nav->fetch_assoc();	
			
			// On remplace le chemin absolut du site par la clé : home (utilise pour éviter les bug lors des mises en lignes)
			array_walk($_POST['nav'], 
				function(&$key) { 	
					$key['href'] = str_replace($GLOBALS['home'], "", $key['href']);// Supprime les url avec le domaine pour faciliter le transport du site

					// Si vide ou raçine path on est sur la home
					if($key['href'] == "" or $key['href'] == $GLOBALS['path']) $key['href'] = "home";
				}
			);

			// Si on change d'url (permalink) on change dans le menu le lien correspondant
			if(isset($change_url)) {
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
			if($res_nav['type']) $sql = "UPDATE"; else $sql = "INSERT INTO";
			$sql .= " ".$table_meta." SET ";
			$sql .= "id = '0', ";
			$sql .= "type = 'nav', ";
			$sql .= "cle = '".$lang."', ";
			$sql .= "val = '".addslashes($json_nav)."' ";
			if($res_nav['type']) $sql .= "WHERE type='nav' AND cle='".$lang."' LIMIT 1";
			
			$connect->query($sql);

			// Si il y a une erreur
			if($connect->error)
				echo htmlspecialchars($sql)."\n<script>error(\"".htmlspecialchars($connect->error)."\");</script>";
		}


		// HEADER
		if(isset($_POST['header']))
		{
			// On regarde s'il y a déjà des données
			$sel_header = $connect->query("SELECT * FROM ".$table_meta." WHERE type='header' AND cle='".$lang."' LIMIT 1");
			$res_header = $sel_header->fetch_assoc();	
			
			// Supprime les url avec le domaine pour faciliter le transport du site
			$_POST['header'] = str_replace($GLOBALS['home'], @$GLOBALS['replace_path'], $_POST['header']);
			
			// On  encode les données
			$json_header = json_encode($_POST['header'], JSON_UNESCAPED_UNICODE);
			
			// Insert ou update ?
			if($res_header['type']) $sql = "UPDATE"; else $sql = "INSERT INTO";
			$sql .= " ".$table_meta." SET ";
			$sql .= "id = '0', ";
			$sql .= "type = 'header', ";
			$sql .= "cle = '".$lang."', ";
			$sql .= "val = '".addslashes($json_header)."' ";
			if($res_header['type']) $sql .= "WHERE type='header' AND cle='".$lang."' LIMIT 1";
			
			$connect->query($sql);

			// Si il y a une erreur
			if($connect->error)
				echo htmlspecialchars($sql)."\n<script>error(\"".htmlspecialchars($connect->error)."\");</script>";
		}

				
		// FOOTER
		if(isset($_POST['footer']))
		{
			// On regarde s'il y a déjà des données
			$sel_footer = $connect->query("SELECT * FROM ".$table_meta." WHERE type='footer' AND cle='".$lang."' LIMIT 1");
			$res_footer = $sel_footer->fetch_assoc();		

			// Supprime les url avec le domaine pour faciliter le transport du site
			$_POST['footer'] = str_replace($GLOBALS['home'], @$GLOBALS['replace_path'], $_POST['footer']);
			
			// On  encode les données
			$json_footer = json_encode($_POST['footer'], JSON_UNESCAPED_UNICODE);
			
			// Insert ou update ?
			if($res_footer['type']) $sql = "UPDATE"; else $sql = "INSERT INTO";
			$sql .= " ".$table_meta." SET ";
			$sql .= "id = '0', ";
			$sql .= "type = 'footer', ";
			$sql .= "cle = '".$lang."', ";
			$sql .= "val = '".addslashes($json_footer)."' ";
			if($res_footer['type']) $sql .= "WHERE type='footer' AND cle='".$lang."' LIMIT 1";
			
			$connect->query($sql);

			// Si il y a une erreur
			if($connect->error)
				echo htmlspecialchars($sql)."\n<script>error(\"".htmlspecialchars($connect->error)."\");</script>";
		}

		
		// TAG ajout au tag
		if(!isset($_POST['tag-info']) and isset($_POST['tag']))
		{
			$tag_key = encode(key($_POST['tag']));

			// SUPP APRES TEST SUR LA NOUVELLE TABLE TAG
			//$connect->query("DELETE FROM ".$table_meta." WHERE id='".(int)$_POST['id']."' AND type='tag'");
			$connect->query("DELETE FROM ".$table_tag." WHERE id='".(int)$_POST['id']."' AND zone='".$tag_key."'");

			// split les tags en fonction du séparateur
			$tags = explode((@$_POST['tag-separator']?trim($_POST['tag-separator']):","), trim($_POST['tag'][$tag_key]));

			$i = 1;
			foreach($tags as $cle => $val) {
				if(isset($val) and $val != "") {			
					//echo $val."/".htmlentities($val)."/".encode($val)."\n";		
					// SUPP APRES TEST SUR LA NOUVELLE TABLE TAG
					//$connect->query("INSERT INTO ".$table_meta." SET id='".(int)$_POST['id']."', type='tag', cle='".encode($val)."', val='".addslashes(trim($val))."', ordre='".$i."'");
					$connect->query("INSERT INTO ".$table_tag." SET id='".(int)$_POST['id']."', zone='".$tag_key."', encode='".encode($val)."', name='".addslashes(trimer($val))."', ordre='".$i."'");
					$i++;
				}
			}
			
			if($connect->error)	echo "<script>error(\"".htmlspecialchars($connect->error)."\");</script>";
		}

		
		// TAG-INFO ajout au meta les informations d'une page tag
		if(isset($_POST['tag-info']) and isset($_POST['tag'])) 
		{
			$tag = html_entity_decode($_POST['tag']);// Pour un titre/url sans html encodé

			$tag_url = encode(key($GLOBALS['filter']));// Permalink du tag

			// Supprime les infos du tag
			$connect->query("DELETE FROM ".$table_meta." WHERE type='tag-info' AND (cle='".encode($tag)."' OR cle='".$tag_url."')");
			
			// Supprime les url avec le domaine pour faciliter le transport du site
			$_POST['tag-info'] = str_replace($GLOBALS['home'], @$GLOBALS['replace_path'], $_POST['tag-info']);

			// Insertion des infos du tag
			$tag_info = json_encode($_POST['tag-info'], JSON_UNESCAPED_UNICODE);
			$connect->query("INSERT INTO ".$table_meta." SET type='tag-info', cle='".encode($tag)."', val='".addslashes($tag_info)."'");
			if($connect->error)	echo "<script>error(\"".htmlspecialchars($connect->error)."\");</script>";


			// Update les tags des contenus
			// SUPP APRES TEST SUR LA NOUVELLE TABLE TAG
			//$connect->query("UPDATE ".$table_meta." SET cle='".encode($tag)."', val='".addslashes($tag)."' WHERE type='tag' AND cle='".$tag_url."'");
			$connect->query("UPDATE ".$table_tag." SET encode='".encode($tag)."', name='".addslashes($tag)."' WHERE zone='".encode($_POST['permalink'])."' AND encode='".$tag_url."'");
			if($connect->error)	echo "<script>error(\"".htmlspecialchars($connect->error)."\");</script>";


			// Update le menu global tags

			// Contenu global tags dans la page courante ?
			if(@$_POST['global']['tags']) $global_tags = $_POST['global']['tags'];
			else
			{
				// Sinon on regarde s'il y a un menu global tags
				$sel_tags = $connect->query("SELECT * FROM ".$table_meta." WHERE type='global' AND cle='tags' LIMIT 1");
				$res_tags = $sel_tags->fetch_assoc();

				$global_tags = $res_tags['val'];
			}

			if(@$global_tags and @$tag_url and encode(@$tag))
			{
				// Changement Url
				$global_tags = str_replace('/'.$tag_url.'"', '/'.encode($tag).'"', $global_tags);

				// Changement Texte du lien
				$global_tags = preg_replace('/(\/'.encode($tag).'".*?>).*?(<\/a>)/', '$1'.$_POST['tag'].'$2', $global_tags);

				if($_POST['global']['tags']) $_POST['global']['tags'] = $global_tags;
				elseif($res_tags['val'])
				{
					// Update
					$connect->query("UPDATE ".$table_meta." SET val='".addslashes($global_tags)."' WHERE type='global' AND cle='tags'");
					if($connect->error)	echo "<script>error(\"".htmlspecialchars($connect->error)."\");</script>";
				}
			}	

			// Si changement de l'url on la change dans le navigateur
			if(encode($tag) != $tag_url)		
				$change_url = make_url(get_url($_POST['url']), array($tag, 'absolu' => true));
		}
		

		// META
		// Ajout des données aux meta liée à un contenu
		if(isset($_POST['meta']) and $_POST['meta'] != "") 
		{
			$i = 1;
			foreach($_POST['meta'] as $cle => $val) 
			{
				// Supprime la meta
				$connect->query("DELETE FROM ".$table_meta." WHERE id='".(int)$_POST['id']."' AND type='".encode($cle)."'");

				// Ajoute la meta si elle contient une variable
				if(isset($val) and $val != "")
				{
					$connect->query("INSERT INTO ".$table_meta." SET id='".(int)$_POST['id']."', type='".encode($cle)."', cle='".addslashes(trim($val))."', val='', ordre='".$i."'");
					$i++;
				}
			}		
			
			if($connect->error)	echo "<script>error(\"".htmlspecialchars($connect->error)."\");</script>";
		}	


		// CONTENU GLOBAL
		// Ajout aux meta de contenu en commun à plusieur page
		if(isset($_POST['global']) and $_POST['global'] != "") 
		{
			foreach($_POST['global'] as $cle => $val)
			{
				$connect->query("DELETE FROM ".$table_meta." WHERE type='global' AND cle='".encode($cle)."'");

				if(isset($val) and $val != "") {
					$val = str_replace($GLOBALS['home'], '', $val);// Supprime le domaine des urls

					$connect->query("INSERT INTO ".$table_meta." SET type='global', cle='".encode($cle)."', val='".addslashes(trim($val))."'");
				}
			}

			if($connect->error)	echo "<script>error(\"".htmlspecialchars($connect->error)."\");</script>";
		}	


		// CONTENU
		//@todo: verif si c'est la bonne technique pour evité l'ecrasement des donnée de la page si page tag
		if(!isset($_POST['tag-info']))// On verifie que l'on est pas sur une page tag
		{
			// Supprime les url avec le domaine pour faciliter le transport du site
			$_POST['content'] = (isset($_POST['content']) ? str_replace($GLOBALS['home'], '', $_POST['content']) : "");

			// Encode le contenu
			if(isset($_POST['content']) and $_POST['content'] != "") 
				$json_content = json_encode($_POST['content'], JSON_UNESCAPED_UNICODE);
			else 
				$json_content = "";


			// Sauvegarde les contenus
			$sql = "UPDATE ".$table_content." SET ";

			//@todo ajouter un check si un content n'existe pas déjà avec ce nom. si existe on incremente (check en boucle)
			if(isset($change_url)) $sql .= "url = '".$change_url."', ";

			$sql .= "title = '".addslashes($_POST['title'])."', ";
			$sql .= "description = '".addslashes($_POST['description'])."', ";
			$sql .= "content = '".addslashes($json_content)."', ";
			$sql .= "robots = '".addslashes(@$_POST['robots'])."', ";
			$sql .= "state = '".addslashes($_POST['state'])."', ";
			$sql .= "type = '".$type."', ";
			$sql .= "tpl = '".addslashes($_POST['tpl'])."', ";
			$sql .= "user_update = '".(int)$_SESSION['uid']."', ";
			$sql .= "date_update = NOW(), ";
			$sql .= "date_insert = '".addslashes(date('Y-m-d H:i:s', strtotime($_POST['date-insert'])))."' ";
			$sql .= "WHERE url = '".get_url($_POST['url'])."' AND lang = '".$lang."'";
			$connect->query($sql);

			//echo $sql;
		}

		
		if($connect->error)// S'il y a une erreur		
			echo htmlspecialchars($sql)."\n<script>error(\"".htmlspecialchars($connect->error)."\");</script>";
		
		else // Sauvegarde réussit
		{
			?>
			<script>
			$(function()
			{
				document.title = "<?=addslashes($_POST['title']);?>";

				<?if(isset($change_url)){?>					
					window.history.replaceState({}, document.title, "<?=make_url($change_url);?>");//history.state	
				<?}?>

				$("#save i").removeClass("fa-cog fa-spin").addClass("fa-ok");// Si la sauvegarde réussit on change l'icône du bt
				$("#save").removeClass("to-save").addClass("saved");// Si la sauvegarde réussit on met la couleur verte
			});
			</script>
			<?
		}

	break;


	case "delete":// Supprime le contenu

		include_once("db.php");// Connexion à la db

		//highlight_string(print_r($_POST, true)); exit;

		$type = ($_POST['type']?encode($_POST['type']):"page");// Type de contenu

		login('high', 'edit-'.$type);// Vérifie que l'on a le droit d'éditer le type de contenu


		// SUPPRIME LA PAGE
		$connect->query("DELETE FROM ".$table_content." WHERE url = '".get_url($_POST['url'])."' AND lang = '".$lang."'");

		// SUPPRIME LES TAGS LIÉES
		$connect->query("DELETE FROM ".$table_tag." WHERE id='".(int)$_POST['id']."'");


		if(isset($_POST['medias']))
		{
			// Supprime les url avec le domaine pour la suppression locale
			$_POST['medias'] = str_replace($GLOBALS['home'], "", $_POST['medias']);

			// On a demandé la SUPPRESSION DES FICHIERS liées au contenu
			//while(list($cle, $media) = each($_POST['medias'])) PHP 7.2
			foreach($_POST['medias'] as $cle => $media) {
				// strtok : Supprime les arguments après l'extension (timer...)
				unlink($_SERVER['DOCUMENT_ROOT'].$GLOBALS['path'].utf8_decode(strtok($media, "?")));
			}
		}


		if($connect->error) echo $connect->error."\nSQL:\n".$sql;// S'il y a une erreur
		else // Suppression réussit
		{
			?>
			<script>
			$(function()
			{		
				// Message page supprimé
				light("<?_e("Page deleted, redirecting")?> <i class='fa fa-cog fa-spin mlt'></i>");

				// Redirection vers la page d'accueil
				setTimeout(function(){ document.location.href = "<?=$GLOBALS['home'];?>"; }, 2000);
			});
			</script>
			<?
		}

	break;


	case "list-content":// Liste les contenus du site

		include_once("db.php");// Connexion à la db

		login('medium');// Vérifie que l'on a le droit d'éditer une page

		$type = null;

		echo'<div class="dialog-list-content" title="'.__("List of contents").'"><ul class="mtn mbs pls">';

		$sel = $connect->query("SELECT title, state, type, tpl, url, date_update FROM ".$GLOBALS['table_content']." WHERE 1 ORDER BY FIELD(type, 'page', 'article', 'product'), type ASC, title ASC");//date_update DESC
		while($res = $sel->fetch_assoc()) 
		{
			if($res['type'] != $type) echo (isset($type)?'</ul></li>':'').'<li'.(isset($type)?' class="mtm"':'').'><b>'.ucfirst($res['type']).'</b><ul>';

			echo'<li title="'.$res['date_update'].' - '.$res['tpl'].'"><a href="'.make_url($res['url'], array("domaine" => true)).'">'.($res['title']?$res['title']:__("Under Construction")).'</a>'.($res['state'] == "active" ? "":" <i class='fa fa-eye-off' title='".__("Deactivate")."'></i>").'</li>';

			$type = $res['type'];
		}

		echo"</ul></div>";

	break;


	case "make-permalink":// Construit un permalink

		//@todo Vérifier qu'il n'y a pas déjà un contenu avec la même URL
	
		login('medium', 'edit-'.($_POST['type']?encode($_POST['type']):"page"));// Vérifie que l'on a le droit d'éditer une page

		echo encode($_POST['title']);

	break;


	case "links":// Suggère des pages existante

		include_once("db.php");// Connexion à la db

		login('medium');// Vérifie que l'on a le droit d'éditer une page

		$term = $connect->real_escape_string($_GET["term"]);

		$sql = "SELECT id, title, type, url FROM ".$GLOBALS['table_content']." WHERE title LIKE '%".$term."%' OR url LIKE '%".$term."%'";
		if(!$term) $sql .= " ORDER BY date_update DESC"; else $sql .= " ORDER BY title ASC";
		$sql .= " LIMIT 50";

		$sel = $connect->query($sql);
		while($res = $sel->fetch_assoc()) {
			$data[] = array(
				'id' => $res['id'],
				'label' => $res['title'],
				'type' => $res['type'],
				'value' => make_url($res['url'], array("absolu" => true))//, array("domaine" => true)
			);
		}

		header("Content-Type: application/json; charset=utf-8");

		echo json_encode($data);

	break;


	case "add-nav":// Liste les pages absente du menu
		
		login('medium', 'edit-nav');// Vérifie que l'on est admin

		$menu = array();

		// Nettoyage et conversion du menu existant
		if(isset($_REQUEST['menu']))
		//while(list($cle, $val) = each($_REQUEST['menu'])) PHP 7.2
		foreach($_REQUEST['menu'] as $cle => $val)
		{
			// Si c'est un lien vers la home
			if($val == $GLOBALS['home'] or $val == $GLOBALS['path'])
				$menu[] = "home";
			else {
				// Supprime l'url root du site
				$val = str_replace($GLOBALS['home'], "", $val);

				$menu[] = $connect->real_escape_string($val);
			}
		}

		// Liste les pages abs du menu
		$sql = "SELECT * FROM ".$table_content." WHERE type = 'page' AND url NOT IN ('".implode("','", $menu)."') ORDER BY title ASC";
		//echo $sql."<br>";

		$sel = $connect->query($sql);
		while($res = $sel->fetch_assoc()) {
			echo"<li><div class='dragger'></div><a href=\"".$res['url']."\">".$res['title']."</a></li>";
		}

	break;


	case "dialog-media":// Affichage des médias
		
		login('medium', 'add-media');// Vérifie que l'on est admin

		//echo "_POST:<br>"; highlight_string(print_r($_POST, true));
		
		// Titre spécifique si la destination est une image cropé, forcé sur la largeur ...
		// Onglet : Locale / FB / Insta / Flicker
		// Option de tri : Par date (defaut) / par nom / par taille

		//@todo: si pas de source on utilise une autre fonction d'insertion ou on renvoie un élément complet d'image <img>

		//["image/jpg","image/jpeg","image/png","image/gif"];
		//highlight_string(print_r($tab_img, true));
		?>

		<div class="dialog-media" title="<?_e("Media Library")?>">

			<input type="hidden" id="dialog-media-target" value="<?=htmlspecialchars($_REQUEST['target'])?>">
			<input type="hidden" id="dialog-media-source" value="<?=htmlspecialchars($_REQUEST['source'])?>">
			<input type="hidden" id="dialog-media-width" value="<?=htmlspecialchars($_REQUEST['width'])?>">
			<input type="hidden" id="dialog-media-height" value="<?=htmlspecialchars($_REQUEST['height'])?>">
			<input type="hidden" id="dialog-media-dir" value="<?=htmlspecialchars($_REQUEST['dir'])?>">
			
			<!-- Chargement du moteur de recherche des médias -->
			<input type="text" id="recherche-media" placeholder="<?_e("Search")?>" class="mrl">

			<ul class="small">

				<li data-filter="all"><a href="#media" title="<?_e("Media")?>"><i class="fa fa-doc"></i> <span><?_e("Media")?></span></a></li>

				<!-- <li data-filter="file"><a href="api/ajax.admin.php?mode=media&filter=file" title="<?_e("Files")?>"><i class="fa fa-file-text-o"></i> <span><?_e("Files")?></span></a></li> -->	

				<!-- <li data-filter="image"><a href="api/ajax.admin.php?mode=media&filter=image" title="<?_e("Images")?>"><i class="fa fa-picture-o"></i> <span><?_e("Images")?></span></a></li> -->

				<li data-filter="resize"><a href="api/ajax.admin.php?mode=media&filter=resize" title="<?_e("Resized")?>"><i class="fa fa-resize-small"></i> <span><?_e("Resized")?></span></a></li>


				<?if(isset($_REQUEST['dir']) and $_REQUEST['dir']){?>
				<li data-filter="dir"><a href="api/ajax.admin.php?mode=media&filter=dir&dir=<?=urlencode($_REQUEST['dir']);?>" title="<?_e("Specific")?>"><i class="fa fa-file"></i> <span><?_e("Specific")?></span></a></li>
				<?}?>

				<!-- <li data-filter="video"><a href="api/ajax.admin.php?mode=media&filter=video" title="<?_e("Videos")?>"><i class="fa fa-film"></i> <span><?_e("Videos")?></span></a></li>

				<li data-filter="audio"><a href="api/ajax.admin.php?mode=media&filter=audio" title="<?_e("Audios")?>"><i class="fa fa-volume-up"></i> <span><?_e("Audios")?></span></a></li> -->

			</ul>
			
			<div id="media">
				<?
				$_GET['mode'] = "media";

				include("ajax.admin.php");
				?>
			</div>

			<script>
			index = $("[data-filter='dir']").index();

			add_container = function(file) {
				// Crée un id unique
				now += 1;
				var id = "dialog-media-"+ now;
				
				// Type de fichier
				var mime = file.type.split("/");
				
				// Switch sur le 1er onglet avec tous les médias
				if($(".ui-tabs-nav .ui-state-active").data("filter") != "dir")
					$(".dialog-media").tabs("option", "active", 0);

				// Option de resize à afficher ?
				if(!$("#dialog-media-width").val() && !$("#dialog-media-height").val())
					var resize = "<a class='resize' title=\"<?_e("Get resized image");?>\"><i class='fa fa-fw fa-resize-small bigger'></i></a>";
				else 
					var resize = "";

				//@todo voir l'utilité de metre le data-media dans le li à ce niveau vu que c'est juste un bloc vide pour upload

				// Crée un block vide pour y ajouter le media // $(".ui-state-active").attr("aria-controls") // + ($(".ui-state-active").attr("data-filter") == "resize" ? "resize/":"")
				var container = "<li class='pat mat tc uploading' id='"+ id +"' data-media=\"media/" + $("#dialog-media-dir").val() + file.name +"\" data-dir=\""+ $("#dialog-media-dir").val() +"\" data-type='"+ mime[0] +"'>";

					if(mime[0] == "image") 
						container += "<img src=''>" + resize;
					else 
						container += "<div class='file'><i class='fa fa-fw fa-doc mega'></i><div>"+ file.name +"</div></div>"

					container += "<div class='infos'></div>";

					container += "<a class='supp hidden' title=\""+__("Delete file")+"\"><i class='fa fa-fw fa-trash bigger'></i></a>";

				container += "</li>";

				$(".dialog-media [aria-hidden='false'] .add-media").after(container);


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
					get_img(id, $('#resize-tool .fa-resize-full').hasClass('checked'));
				}
			}


			$(function()
			{
				// Pour la construction d'id unique
				now = new Date().getTime();

				// Switch sur l'onglet spécifique si il existe
				if($("[data-filter='dir']").length)
					$(".dialog-media").tabs("option", "active", $("[data-filter='dir']").index());

				// On demande une version redimensionnée de l'image
				$(".dialog-media").on("click", ".resize", function(event)
				{
					event.stopPropagation();

					var id = $(this).parent().attr("id");
					var top = $(this).parent().offset().top;
					var left = $(this).offset().left;

					// Highlight l'image choisie
					$(this).parent().addClass("select");

					// Boîte à outils resize
					resize_tool = "<div id='resize-tool' class='toolbox'>";
						resize_tool+= __("Width") +": <input type='text' id='resize-width' class='w50p'> ";
						resize_tool+= __("Height") +": <input type='text' id='resize-height' class='w50p'>";
						resize_tool+= "<a href=\"javascript:$('#resize-tool .fa-resize-full').toggleClass('checked');void(0);\"><i class='fa fa-fw fa-resize-full'></i>"+ __("Zoom link") +"</a> ";
						resize_tool+= "<button onclick=\"resize_img('"+id+"')\"><i class='fa fa-fw fa-cog'></i> "+ __("Add") +"</button>";
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
							url: "api/ajax.admin.php?mode=del-media",
							data: {
								"file": $("#"+id).attr("data-media"),
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
				$(".dialog-media").on("click", "li:not(.add-media)", function(event)
				{
					var id = $(this).attr("id");

					if($(this).attr("data-type") == "image") get_img(id);// Si c'est une image
					else if($(this).attr("data-type") == "dir")// Si c'est un dossier
					{
						// Onglet ou on se trouve
						var id_parent = $(this).parent().parent().attr('id');

						// On inject le contenu du dossier
						$.ajax({
							type: "POST",
							url: path+"api/ajax.admin.php?mode=media&inject=true&filter=dir&dir="+$(this).attr("data-dir"),
							data: {
								//"dir": dir,
								"nonce": $("#nonce").val()
							},
							success: function(html)
							{ 	
								$("#"+id_parent).html(html);
							}
						});

					}
					else get_file(id);// Si c'est uu fichier
				});


				// Init variable d'upload
				source_queue = [];// @todo: voir si on les re-active
				file_queue = [];
				if(typeof uploading === "undefined") uploading = false;

				// Si on choisit des images pour l'upload avec le bouton
				$("#add-media").change(function()
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
				$("body").off(".editable").off(".editable-media");
				$(".editable-media").off(".editable-media");


				// On drag&drop des médias dans la fenêtre
				$("body")
					.on({
					"dragover.dialog-media": function(event) {// Highlight les zones on hover
						event.preventDefault();
						event.stopPropagation();					
						$(".ui-widget-overlay").addClass("body-dragover");
						$(".add-media").addClass("dragover");
					},
					"dragleave.dialog-media": function(event) {// Clean les highlight on out
						event.stopPropagation();
						$(".ui-widget-overlay").removeClass("body-dragover");
						$(".add-media").removeClass("dragover");
					},
					"drop.dialog-media": function(event) {// On lache un fichier sur la zone
						event.preventDefault();  
						event.stopPropagation();
						$(".ui-widget-overlay").removeClass("body-dragover");
						$(".add-media").removeClass("dragover");
						
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


				// Moteur de recherche dans les médias
				var timer = null;
				$("#recherche-media").on("keyup", function(event) {
					var recherche = $(this).val();

					// Filtre les li
					if(recherche)// Si on a une recherche
					{
						if(timer != null) clearTimeout(timer);
						timer = setTimeout(function() {
							timer = null;

							console.log("recherche"+recherche);

							$(".dialog-media [aria-hidden='false'] li").addClass("none");// Masque tous les Li
							$(".dialog-media [aria-hidden='false'] li[title*='"+recherche+"']").removeClass("none");// Affiche les li qui contiennent le mot dans le title
							$window.trigger("scroll")// Force le chargement des images
							
						}, '500');
					}
					else $(".dialog-media [aria-hidden='false'] li").removeClass("none");// Re-affiche tous les médias
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
		
		login('medium', 'add-media');// Vérifie que l'on est admin

		//echo"_POST";print_r($_POST);echo"_GET";print_r($_GET);

		$subfolder = null;
		if(isset($_GET['filter']) and  $_GET['filter'] == "resize") $subfolder .= "resize/";
		if(isset($_GET['filter']) and  $_GET['filter'] == "dir" and isset($_GET['dir'])) $subfolder .= $_GET['dir'].'/';

		$dir = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['path']."media/". $subfolder;

		// Le dossier existe
		if(is_dir($dir))
		{
			$scandir = array_diff(scandir($dir), array('..', '.'));// Nettoyage

			$i = 1;
			// Crée un tableau avec les fichiers du dossier et infos complètes
			foreach($scandir as $cle => $filename)
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

						$file_infos['0'] = $file_infos['1'] = "";
					}
					
					// Type mime
					list($type, $ext) = explode("/", $file_infos['mime']);
					
					// Pour le tri
					if(!isset($_GET['order']) or $_GET['order'] == 'time') $order = $stat['mtime'];// Tri par défaut
					elseif($_GET['order'] == 'size') $order = $stat['size'];
					elseif($_GET['order'] == 'name') $order = $filename;

					// Filtre le tableau en fonction du type mime choisi
					if(
						(!isset($_GET['filter']) or $_GET['filter'] == "resize" or $_GET['filter'] == "dir") or
						$_GET['filter'] == $type or
						($_GET['filter'] == "file" and $type != "image" and $type != "video" and $type != "audio")		
					) 
					{					
						// $i pour être sûr d'incrémenter le tableau
						$tab_file[$order.$i] = array("filename" => $filename, "size" => $stat['size'], "time" => $stat['mtime'], "width" => $file_infos['0'], "height" => $file_infos['1'], "mime" => $file_infos['mime']);
					}

					$i++;
				}
				elseif(is_dir($dir.$filename)) $is_dir[] = $filename;
			}			
		}

		// Tri du tableau
		if(!isset($sort)) {								
			if(!isset($_GET['order']) or $_GET['order'] == 'time') $sort = 'DESC';// Tri par défaut
			elseif($_GET['order'] == 'size') $sort = 'DESC';
			elseif($_GET['order'] == 'name') $sort = 'ASC';
		}
		
		?>
		<ul class="unstyled pan man smaller"><?

			// @todo ajouter la possiblitée de remonter dans l'arbo, jusqu'au dossier courant de l'onglet
			// Si on navige dans un dossier on n'affiche pas l'upload
			if(!isset($_GET['inject']))
			{
			?>	
			<li class="add-media pas mat tc big" onclick="document.getElementById('add-media').click();">
				<i class="fa fa-upload biggest pbs"></i><br>
				<?_e("Drag and drop a file here or click me");?>
				<input type="file" id="add-media" style="display: none" multiple>
			</li>
			<?
			}

			// Si il y a des dossier
			if(isset($is_dir) and is_array($is_dir) and count($is_dir) and @$GLOBALS['media_dir'])
			{
				foreach($is_dir as $cle => $val)
				{
					echo'<li 
					class="pat mat tc"
					title="'.utf8_encode($val).'"
					id="dialog-media-dir-'.encode((isset($_GET['filter'])?$_GET['filter']:'')).'-'.$cle.'"
					data-media="media/'.$subfolder.utf8_encode($val).'"
					data-dir="'.trim($subfolder,'/').utf8_encode($val).'"
					data-type="dir"
					>
						<div class="file"><i class="fa fa-fw fa-folder-empty mega"></i><div>'.utf8_encode($val).'</div></div>
					</li>';
				}
			}

			// S'il y a des fichiers dans la biblio
			if(isset($tab_file))
			{
				uksort($tab_file, 'strnatcmp');// Tri ascendant
				if($sort == 'DESC') $tab_file = array_reverse($tab_file, true);// Tri Descendant
							
				$i = 1;
				// Affiche les fichiers en fonction du tri
				foreach($tab_file as $cle => $val)
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
								default: $fa = "doc"; break;
								case"zip": $fa = "file-archive"; break;
								case"msword": $fa = "file-word"; break;
								case"vnd.ms-excel": $fa = "file-excel"; break;
								case"vnd.ms-powerpoint": $fa = "file-powerpoint"; break;
								case"pdf": $fa = "file-pdf"; break;
							}
						break;
						case"text": 
							switch($ext)
							{
								default: $fa = "doc"; break;
								case"plain": $fa = "doc-text"; break;
								case"html": $fa = "file-code"; break;
							}
						break;
						case"video": $fa = "video"; break;
						case"audio": $fa = "volume-up"; break;
					}
					
					// Infos sur le fichier
					if($val['width'] and $val['height']) $info = $val['width']."x".$val['height']."px";
					else $info = pathinfo($val['filename'], PATHINFO_EXTENSION);
					
					// Affichage du fichier '.($i>=20?'none':'').'
					echo'<li 
						class="pat mat tc"
						title="'.utf8_encode($val['filename']).' | '.date("d-m-Y H:i:s", $val['time']).' | '.$val['mime'].'"
						id="dialog-media-'.encode((isset($_GET['filter'])?$_GET['filter']:'')).'-'.$i.'"
						data-media="media/'.$subfolder.utf8_encode($val['filename']).'"
						data-dir="'.trim($subfolder,'/').'"
						data-type="'.$type.'"
					>';

						if($type == "image") {
							$src = $GLOBALS['path'].'media/'.$subfolder.$val['filename'];
							echo'<img src="'.($i<=20?$src:'').'"'.($i>20?' data-lazy="'.$src.'"':'').'>';
							echo'<a class="resize" title="'.__("Get resized image").'"><i class="fa fa-fw fa-resize-small bigger"></i></a>';
						}
						else echo'<div class="file"><i class="fa fa-fw fa-'.$fa.' mega"></i><div>'.utf8_encode($val['filename']).'</div></div>';

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
			$(function()
			{
				if($("#dialog-media-width").val() || $("#dialog-media-height").val()) $(".dialog-media .resize").remove();

				// Pour bien prendre en compte les images en lazyload injecté fraichement dans la dom
				$animation = $(".animation, [data-lazy]");

				/*
				// @todo SUPP car maintenant on utilise le lazyload dans lucide.ini.js
				// LAZY LOAD IMAGE Dialog media : Charge les medias au scroll
				$window.on("scroll resize", function ()
				{
			    	$.each($(".dialog-media img"), function() 
			    	{
			    		var $element = $(this);
			    		var element_top = $element.offset().top;

			    		// Si l'image est dans data=lazy mais n'est pas chargé et que le li est visible
						if($element.data("lazy") && !$element.attr("src") && $element.parent().css("display") != "none") {
							if(element_top <= window_bottom) $element.attr("src", $(this).data("lazy"));
						}
					});
				});
				*/

				$window.trigger("scroll");// Force le lancement pour les lazyload des images déjà dans l'ecran
				
			});
		</script>
		<?
	break;

	
	case "del-media":// Supprime un fichier

		login('medium', 'add-media');// Vérifie que l'on est admin

		return unlink($_SERVER['DOCUMENT_ROOT'].$GLOBALS['path'].utf8_decode(strtok($_REQUEST['file'], "?")));
		
	break;


	case "get-img":// Renvoi une image et la resize si nécessaire

		login('medium', 'add-media');// Vérifie que l'on est admin

		if(@$_POST['dir']) $dir = encode($_POST['dir'], "-", array('/','_'));
		else $dir = null;
		
		// On supprime les ? qui pourrait gêner à la récupération de l'image
		$file = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['path'].strtok($_POST['img'], "?");
		
		// Resize l'image ou simple copie
		echo resize($file, (int)$_POST['width'], (int)$_POST['height'], $dir, (@$_POST['crop'] == 'true'?'crop':null));

	break;


	case "add-media":// Envoi d'une image sur le serveur et la resize si nécessaire (upload)
			
		login('medium', 'add-media');// Vérifie que l'on est admin

		//echo "_POST:<br>"; highlight_string(print_r($_POST, true));
		//echo "_FILES:<br>"; highlight_string(print_r($_FILES, true));
		// @todo: Vérifier qu'il n'y a pas déjà un fichier qui a le même nom sur le serveur, si oui => alert pour overwrite
		// @todo: Proposer l'option crop (si w&h spécifié) / resize (si aucune des w&h ne sont pas spécifiés)
		
		// Si la taille du fichier est supérieure a la taille limitée par le serveur
		if($_FILES['file']['error'] == 1)
			exit('<script>error("'.__("The file exceeds the send size limit of ") . ini_get("upload_max_filesize").'");</script>');

		// Récupération de l'extension
		$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
	
		// Hack protection : contre les doubles extensions = Encode le nom de fichier + supprime l'extension qui ne passe pas l'encode et l'ajoute après
		$filename = encode(basename($_FILES['file']['name'], ".".$ext)).".".strtolower($ext);

		// @todo trouver la bonne regex qui permet de n'avoir qu'un seul point
		// 2ème passe avec une whitelist pour supp tous les autres caractères indésirables et n'avoir qu'un seul point (pour l'ext)
		//$filename = preg_replace("([^a-z0-9\.\-_]|[\.]{2,})", "", $_FILES['file']['name']);
		// /^[a-z0-9]+\.[a-z]{3,4}$/  /[^a-z0-9\._-]+/  ([^a-z0-9\.\-_]|[\.]{2,})  [a-zA-Z0-9]{1,200}\.[a-zA-Z0-9]{1,10}

		if(@$_POST['dir']) $dir = encode($_POST['dir'], '-', array('/','_'));
		else $dir = null;

		$src_file = 'media/'. ($dir?$dir.'/':'') . $filename;
		$root_file = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['path'] . $src_file;
		
		// Check le type mime côté serveur
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$file_infos['mime'] = finfo_file($finfo, $_FILES['file']['tmp_name']);
		finfo_close($finfo);

		// Vérifie que le type mime est supporté (Hack protection : contre les mauvais mimes types) 
		// + Le fichier tmp ne contient pas de php ou de javascript
		if(file_check('file'))
		{
			@mkdir(dirname($root_file), 0705, true);

			// Upload du fichier
			if(move_uploaded_file($_FILES['file']['tmp_name'], $root_file))
			{
				// Type mime
				list($type, $ext) = explode("/", $file_infos['mime']);

				// Si c'est une image
				if($type == "image")
				{
					// Resize l'image si besoin 
					// SUPP ?? (On ajoute le path du site pour gerer l'édition dans les sous catégories) $GLOBALS['path']. => maintenant ça se passe dans le edit.js
					echo img_process($root_file,
							$dir,
							(int)$_POST['width'],
							(int)$_POST['height'],
							(isset($_POST['resize'])?$_POST['resize']:'')
						);
				}		
				else 
					echo $src_file;// Retourne l'url du fichier original		
			}			
		}
		//else echo $file_infos['mime'];

	break;



	case "dialog-icon":// Affichage des médias
		
		login('medium', 'edit-page');// Vérifie que l'on est admin

		// @todo: ajouter une recherche en js (qui masque)
		?>

		<div class="dialog-icon" title="<?_e("Icon Library")?>">

			<input type="hidden" id="dialog-icon-target" value="<?=(isset($_GET['target']) ? htmlspecialchars($_GET['target']) : "");?>"><!-- SUPP ?? -->
			<input type="hidden" id="dialog-icon-source" value="<?=htmlspecialchars(isset($_GET['target']) ? $_GET['source'] : "")?>">
			
			<input type="text" class="search w20 mbs" placeholder="<?_e("Search")?>" value="">

			<?
			//$pattern = '/\.([\w-]+):before\s*{\s*content:\s*(["\']\\\w+["\']);?\s*}/';
			//$pattern = '/\.(fa-(?:\w+(?:-)?)+):before\s*{\s*content:\s*"\\\\(.+)";?\s*}/';
			//$pattern = '/\\.(fa-\\w+):before{content:"(\\\\\w+)"}/';	
			//$pattern = '/\\.(fa-(?:\\w+(?:-)?)+):before{content:"(\\\\\\w+)"}/';	
			//$pattern = '/\\.(fa-(?:[a-z-]*)):before{content:"(\\\\\\w+)"}/';	
			$pattern = "/\\.(fa-(?:[a-z-]*)):before{content:'(\\\\\\w+)'}/";	

			// Url du fichier qui contient les icônes
			if($GLOBALS['icons']) $file = $GLOBALS['icons'];
			else $file = $GLOBALS['scheme'].$GLOBALS['domain'].$GLOBALS['path']."api/global.min.css";


			// On récupère le contenu du fichier css qui contient les icones
			$content = curl($file);

			// Nécessite allow_url_include
    		//$content = file_get_contents($file);
			

			// On extrait seulement les icônes
			preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
			//highlight_string(print_r($subject, true));
			
			// On crée un tableau propre
			foreach($matches as $match){ $list[$match[1]] = $match[2];	}			

			?>
			<ul id="icon" class="unstyled pan man smaller">	
			<?
				// S'il y a des fichiers dans la biblio
				if(isset($list))
				{
					//uksort($list, 'strnatcmp');// Tri Ascendant
					//if($sort == 'DESC') $list = array_reverse($list, true);// Tri Descendant
					
					//while(list($cle, $val) = each($list)) PHP 7.2
					foreach($list as $cle => $val)
					{						
						echo"<li class='pat fl' title=\"".substr($cle, 3)."\"><i class='fa fa-fw biggest ".$cle."' id='".trim($val, '\\')."'></i></li>";
					}
				}
			?>
			</ul>

			<script>
			$(function()
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



	case "tags":// Liste les tags pour l'auto-complete
		
		include_once("db.php");// Connexion à la db

		login('medium');

		$sel_tag = $connect->query("SELECT distinct encode, name FROM ".$table_tag." WHERE zone='".encode($_POST['zone'])."' ORDER BY ordre ASC, encode ASC");
		while($res_tag = $sel_tag->fetch_assoc()) {
			$tab_tag[] = $res_tag['name'];
		}	

		header("Content-Type: application/json; charset=utf-8");

		echo json_encode($tab_tag);//JSON_UNESCAPED_UNICODE

	break;
}


// Fermeture de la connexion
if(isset($GLOBALS['connect'])) @$GLOBALS['connect']->close();
?>