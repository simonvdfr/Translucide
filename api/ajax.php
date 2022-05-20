<?php
@include_once("config.init.php");// Les variables par défaut


// Chemin de la config en fonction d'ou on appel
if(strstr($_SERVER['SCRIPT_FILENAME'], 'theme/')) $dir_conf = explode('theme/', $_SERVER['SCRIPT_FILENAME'])[0];
else $dir_conf = explode('api/ajax.php', $_SERVER['SCRIPT_FILENAME'])[0];


@include($dir_conf."config.php");// Les variables ../config.php || $_SERVER['DOCUMENT_ROOT']."/config.php"
@include_once("function.php");// Fonction


$lang = get_lang();// Sélectionne la langue
load_translation('api');// Chargement des traductions du système
if(@$GLOBALS['theme_translation']) load_translation('theme');// Chargement des traductions du theme


switch($_GET['mode'])
{
	default:
	case "login":// Check le login interne

		login();

		?>
		<script>
			$(function() {
				if(callback) eval(callback + "()");// S'il y a un callback à exécuter
			});
		</script>
		<?php
	break;


	case "internal-login":// Connexion avec un login/passe interne au site

		// @todo: si la page est appelée directement (ajax.php), charger un fond et charger la dialog
		?>
		<div id="dialog-connect" title="<?php _e("Log in");?>">

			<?php if($_REQUEST['msg']){?>
			<div class="m-16 mt-0 p-8 ui-state-highlight"><?=htmlspecialchars($_REQUEST['msg']);?></div>
			<?php }?>

			<form id="internal-login" class="mt-16 text-smaller">

				<input type="hidden" id="nonce" value="<?=nonce("nonce");?>">

				<p class="mb-24"><?_e("All fields are mandatory")?></p>

				<label for="email">
					<?php _e("My email");?>
					<i><?_e("Expected format" )?> : myname@domain.com</i>
				</label>
				<div class="mb-24"><input type="email" id="email" autocomplete="email" required><span class="wrapper text-bold" aria-hidden="true">@</span></div>

				<label for="password"><?php _e("My password");?></label>
				<div class="mb-24"><input type="password" id="password" autocomplete="current-password" required><i class="icon moon-lock wrapper" aria-hidden="true"></i></div>

				<button class="bg-light float-right mr-0 mt-24 p-8">
					<?php _e("Log in")?>
					<i class="icon moon-log-in" aria-hidden="true"></i>
				</button>

			</form>
		</div>

		<script>
		// S'il y a une fonction de callback
		callback = <?php if($_REQUEST['callback']){ echo'"'.encode($_REQUEST['callback'], "_").'"';} else echo"null";?>;

		$(function()
		{
			// Update les nonces dans la page courante pour éviter de perdre le nonce
			$("#nonce").val('<?=$_SESSION['nonce']?>');


			// Message d'erreur en cas de mauvaise saisie du mail. Pour l'accessibilité
			var email = document.getElementById("email");
			email.addEventListener("invalid", function() {
				email.setCustomValidity("<?_e("Invalid email")?>. <?_e("Expected format")?> : dupont@exemple.com")
			}, false);
			email.addEventListener("input", function() {
				email.setCustomValidity("");
			}, false);


			// Login
			$("#internal-login").submit(function(event)
			{
				event.preventDefault();

				// Icône de chargement
				$("#dialog-connect .btn .icon").removeClass("moon-key").addClass("icon-spin moon-settings");

				// Désactive le submit
				$("#internal-login input[type='submit']").attr("disabled", true);
				$("#internal-login").off("submit");

				$.ajax(
				{
					type: "POST",
					url: "<?=$GLOBALS['path']?>api/ajax.php?mode=login",
					data: {
						email: $("#internal-login #email").val(),
						password: $("#internal-login #password").val(),
						callback: callback,
						nonce: $("#internal-login #nonce").val()
					}
				})
				.done(function(html) {
					// On ferme la dialog
					$("#dialog-connect").dialog("close");

					// On exécute le retour
					$("body").append(html);
				});
			});
		});
		</script>
		<?php
	break;


	case "quick-view-user":// AFFICHAGE RAPIDE D'UN COMPTE UTILISATEUR

		?>
		<!DOCTYPE html>
		<html lang="<?=$lang;?>">
		<head>
			<meta charset="utf-8">
			<title><?=__("User profile")." ".(int)$_REQUEST['uid'];?></title>
			<meta name="robots" content="noindex, nofollow">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<link rel="stylesheet" href="<?=$GLOBALS['jquery_ui_css'];?>">
			<link rel="stylesheet" href="assets/css/style<?=$GLOBALS['min']?>.css?">
			<link rel="stylesheet" href="assets/css/custom<?=$GLOBALS['min']?>.css?">
			<script src="<?=$GLOBALS['jquery'];?>"></script>
			<script src="<?=$GLOBALS['jquery_ui'];?>"></script>
			<script src="assets/js/custom.init<?=$GLOBALS['min']?>.js"></script>

			<!-- Appel du js supplémentaire pour les options spécifiques au thème -->
			<?php if(file_exists($_SERVER['DOCUMENT_ROOT'].$GLOBALS['sous-dossier']."theme/".$GLOBALS['theme'].($GLOBALS['theme']?"/":"")."admin.init.js")) {?>
					<script src="<?=$GLOBALS['path']?>theme/<?=$GLOBALS['theme'].($GLOBALS['theme']?"/":"")?>admin.init.js"></script>
			<?php }?>
		</head>

		<body>

			<input type="hidden" id="nonce" value="<?=nonce("nonce");?>">

			<div id="admin-bar" class="mt-24">
				<div id="user">
					<center><i class="icon icon-spin moon-settings mt-36"></i></center>
				</div>
			</div>

			<script>
			path = "<?=$GLOBALS['path']?>";

			$(function()
			{
				// Injection du la fiche user
				$.ajax({ url: "<?=$GLOBALS['path']?>api/ajax.php?mode=user&uid=<?=(int)$_REQUEST['uid']?>&callback=reload", data: { nonce: $("#nonce").val() } })
					.done(function(html) {
						$("#user").html(html);

						// Execution des fonctions d'edition des plugins
						$(edit).each(function(key, funct){
							funct();
						});
					});
			});
			</script>

		</body>
		</html>
		<?php

	break;


	case "user":// AFFICHAGE DE L'INTERFACE DE GESTION DES UTILISATEURS

		// @todo: ajouter les checks sur mail, password, et aussi mode non admin
		// @todo encadrer le tout d'un formulaire pour avoir un onchange simple, et aussi metre en place le ajax qui affiche la progression de sauvegarde
		// @todo si appel direct de la page on include dans le body générique

		include_once("db.php");// Connexion à la db

		login('medium');

		?>
		<div class="absolute">
			<div class="tooltip slide-left p-16 m-16 ml-8">

				<div id="logout" class="float-right" title="<?php _e("Log out")?>"><i class="icon moon-log-out"></i></div>

				<?php if(@$_SESSION['auth']['edit-user']) {?>
				<div id="add-user" class="float-right pr-16" title="<?php _e("Add user")?>"><i class="icon moon-user-plus"></i></div>
				<div id="list-user" class="float-right pr-16" title="<?php _e("List of user")?>"><i class="icon moon-users"></i></div>
				<div id="profil" class="float-right pr-16" title="<?php _e("My profil")?>"><i class="icon moon-user"></i></div>
				<?php }?>

				<div class="load">
					<?php
					$_GET['mode'] = "profil";
					include("ajax.php");
					?>
				</div>

			</div>
		</div>

		<script>
		$(function()
		{
			$("#profil").click(function() {// Voir mon profil
				$.ajax({ url: "<?=$GLOBALS['path']?>api/ajax.php?mode=profil", data: { nonce: $("#nonce").val() } }).done(function(html) { $("#user .load").html(html); });
			});

			$("#add-user").click(function() {// Ajouter un utilisateur
				$.ajax({ url: "<?=$GLOBALS['path']?>api/ajax.php?mode=add-user", data: { nonce: $("#nonce").val() } }).done(function(html) { $("#user .load").html(html); });
			});

			$("#list-user").click(function() {// Liste des utilisateurs
				$.ajax({ url: "<?=$GLOBALS['path']?>api/ajax.php?mode=list-user", data: { nonce: $("#nonce").val() } }).done(function(html) { $("#user .load").html(html); });
			});

			$("#admin-bar #logout").click(function() {
				logout();
			});
		});
		</script>
		<?php

		// Pas de mysql close car déjà close dans le include ajax.php mode profil

	break;


	case "del-user":// SUPPRESSION D'UN COMPTE

		include_once("db.php");// Connexion à la db

		login('high', 'edit-user');

		if($_REQUEST['uid'] != $_SESSION['uid'])
		{
			if($connect->query("DELETE FROM ".$table_user." WHERE id='".(int)$_REQUEST['uid']."'"))
			{
				// Supprime les métas //@todo migration supp au long terme (12/11/2018)
				//$connect->query("DELETE FROM ".$table_meta." WHERE id='".(int)$_REQUEST['uid']."' AND type='user_info'");

				$msg = __("User deleted")." ".(int)$_REQUEST['uid'];
			}
			else
				$msg = $connect->error;
		}


	case "list-user":// LISTE LES UTILISATEURS

		include_once("db.php");// Connexion à la db

		login('medium', 'edit-user');

		if(!isset($_POST['search']) and !isset($_POST['page']))
		{
			?>
			<h3 class="medium m-0 mb-16"><?php _e("List of user")?></h3>

			<div class="mb-16"><input type="text" class="search w70" placeholder="<?php _e("Search")?>" value=""></div>

			<ul class="is-unstyled p-0 m-0">
			<?php
		}

		$num_pp = 10;

		$start = ($page * $num_pp) - $num_pp;

		$search = (isset($_POST['search']) ? $connect->real_escape_string($_POST['search']) : "");

		$sql = "SELECT SQL_CALC_FOUND_ROWS ".$table_user.".id, ".$table_user.".* FROM ".$table_user." ";

		$sql .= "WHERE 1 ";

		// L'utilisateur n'a pas les droits admins donc il ne peut pas éditer les fiches des administrateurs
		//if(!$_SESSION['auth']['edit-admin']) $sql .= "AND FIND_IN_SET('edit-admin', auth)=0 ";
		// @todo verifier que ça marche !!
		if(!$_SESSION['auth']['edit-admin']) $sql .= "AND auth NOT LIKE '%edit-admin%' ";

		if($search)
		{
			$sql .= "AND ";
			$sql .= "id LIKE '%".$search."%' OR ";
			$sql .= "state LIKE '%".$search."%' OR ";
			$sql .= "auth LIKE '%".$search."%' OR ";
			$sql .= "name LIKE '%".$search."%' OR ";
			$sql .= "email LIKE '%".$search."%' OR ";
			//$sql .= "oauth LIKE '%".$search."%' OR ";

			$sql = trim($sql, 'OR ')." ";
		}

		$sql .= "ORDER BY date_insert DESC ";

		$sql .= "LIMIT ".$start.", ".$num_pp;

		//echo $sql."<br>";

		$sel = $connect->query($sql);

		$num_total = $connect->query("SELECT FOUND_ROWS()")->fetch_row()[0];

		while($res = $sel->fetch_assoc())
		{
			if($res['state'] == "active") $state = "ok";
			elseif($res['state'] == "moderate") $state = "eye";
			elseif($res['state'] == "email") $state = "mail";
			elseif($res['state'] == "blacklist") $state = "lock";
			elseif($res['state'] == "deactivate") $state = "cancel";

			echo"
			<li class='pl-8 pr-8' onclick=\"select_user('".$res['id']."');\">
				<label><i class='icon moon-".$state."' title=\"".__($res['state'])."\"></i></label>
				<label class='text-bold p-8'>".$res['name']."</label>
				<label class='text-smaller'>".$res['email']."</label>
			</li>";
		}

		// Si on n'a pas affiché tous les résultats on affiche la navigation par page
		if($num_total > ($page * $num_pp)) echo"<li class='next text-smaller' onclick=\"next_users('".($page + 1)."');\">".__("Next")."</li>";

		if(!isset($_POST['search']) and !isset($_POST['page']))
		{
			?>
			</ul>

			<script>
			// Navigation par page (on charge les résultats suivants)
			next_users = function(page)
			{
				$("#user .next").slideUp("normal", function(){ $(this).remove() });// Supprime le bouton next

				$.ajax({
					type: "POST",
					url: "<?=$GLOBALS['path']?>api/ajax.php?mode=list-user",
					data: { page: page, search: $("#user .search").val(), nonce: $("#nonce").val() }
				})
				.done(function(html) {
					// Animation sur l'ouverture des nouveaux résultats
					var next_users = $(html).hide();
					$("#user .load ul").append(next_users);
					next_users.slideDown("normal");
				})
				.fail(function() {
					error(__("Error"));
				});

				return false;
			}

			// Voir un profil
			select_user = function(uid) {
				$.ajax({ url: "<?=$GLOBALS['path']?>api/ajax.php?mode=profil", data: { uid: uid, nonce: $("#nonce").val() } }).done(function(html) { $("#user .load").html(html); });
			}

			// Recherche un utilisateur
			search_user = function()
			{
				timer = null;

				// loading
				$("#user .search").after("<i class='icon icon-spin moon-settings' style='position: relative; left: -15px; color: rgba(117, 137, 140, 0.5);'></i>");

				$.ajax({
						type: "POST",
						url: "<?=$GLOBALS['path']?>api/ajax.php?mode=list-user",
						data: { search: $("#user .search").val(), nonce: $("#nonce").val() }
					})
					.done(function(html) {
						$("#user .load ul").html(html);
						$("#user .search").next("i").fadeOut();
					})
					.fail(function() {
						error(__("Error"));
					});
			}

			$(document).ready(function()
			{
				<?php if(isset($msg)) {?>
					// S'il y a un message
					light("<?=$msg?>");
				<?php }?>

				// Recherche avec timer
				var timer = null;
				$("#user .search").keyup(function()
				{
					if(timer != null) clearTimeout(timer);
					timer = setTimeout(search_user, '500');
				});
			});
			</script>
			<?php
		}

		if(isset($GLOBALS['connect'])) $GLOBALS['connect']->close();

	break;


	case "add-user":// AJOUTER UN UTILISATEUR PAR L'ADMIN

		include_once("db.php");// Connexion à la db

		login('high', 'edit-user');


	case "profil":// AFFICHAGE DU FORMULAIRE UTILISATEUR

		// @todo ajouter une icône a coté du picto de state pour re-envoyer le mail d'activation à l'utilisateur / btn pour passer l'utilisateur en 'active' si en mode 'moderate'
		// @todo: autocomplet sur les champs de connexion d'api tiers (fb, g+...)

		include_once("db.php");// Connexion à la db

		if($_GET['mode'] != "add-user")
		{
			// Si l'utilisateur a affiché est diff de l'utilisateur en cours on vérifie que l'on est admin
			if(isset($_REQUEST['uid']) and $_REQUEST['uid'] != $_SESSION['uid'])
				login('medium', 'edit-user');
			else
				login('medium');

			$uid = (isset($_REQUEST['uid']) ? $_REQUEST['uid'] : $_SESSION['uid']);

			// Récupérationd des données de base de l'utilisateur
			$sel = $connect->query("SELECT * FROM ".$table_user." WHERE id='".(int)$uid."' LIMIT 1");
			$res = $sel->fetch_assoc();

			//@todo migration supp au long terme (12/11/2018)
			if(!@$GLOBALS['user_info_in_table_user']) {
				// Récupération des infos sur l'utilisateur
				/*$sel_meta = $connect->query("SELECT * FROM ".$table_meta." WHERE id='".(int)$uid."' AND type='user_info' LIMIT 1");
				$res_meta = $sel_meta->fetch_assoc();*/
			}

			$array_auth = explode(",", $res['auth']);// Les autorisations

			$oauth = json_decode($res['oauth'], true);// Les api tiers

			// On vérifie que l'on a le droit d'éditer les utilisateurs admin si fiche admin
			if(array_search("edit-admin", $array_auth) !== false) login('medium', 'edit-admin');
		}

		if($_GET['mode'] == "add-user") $h3 = __("Add user");
		elseif($_SESSION['uid'] == $res["id"]) $h3 = __("Your profile")." ".$res["id"];
		else $h3 = __("Profile")." ".$res["id"];

		?>
		<form id="user-profil">

			<h3 class="medium m-0 mb-16"><?=$h3?></h3>

			<input type="hidden" id="uid" value="<?=@$res['id']?>">

			<div class="grid grid-cols-3 gap-8 scroll">

				<div class="text-right mb-8">
					<label class="mr-8" for="state"><?php _e("State")?></label>
				</div>
				<div class="col-span-2 text-left">
					<?php if(@$_SESSION['auth']['edit-user']){?>
						<select id="state">
							<option value="active"><?php _e("Active")?></option>
							<option value="moderate"><?php _e("Moderate")?></option>
							<option value="email"><?php _e("User email")?></option>
							<option value="blacklist"><?php _e("Blacklist")?></option>
							<option value="deactivate"><?php _e("Deactivate")?></option>
						</select>
						<script>$('#user #state option[value="<?=@$res['state']?>"]').prop('selected', true);</script>
					<?php }else{?>
						<?php _e(@$res['state'])?>
					<?php }?>
				</div>

				<div class="text-right mb-16">
					<label class="mr-8" for="auth"><?php _e("Authorization")?></label>
				</div>

				<div class="col-span-2 text-left">
					<select id="auth" multiple <?=(!@$_SESSION['auth']['edit-admin']?"disabled":"");?>>
						<?php
						// Droit de base
						foreach($GLOBALS['auth_level'] as $cle => $val)	{
							echo'<option value="'.$cle.'">'.__($val).'</option>';
						}

						// Droit contenu
						foreach($GLOBALS['add_content'] as $cle => $array)
						{
							echo'<option value="add-'.$cle.'">'.__("Add ".$cle).'</option>';
							echo'<option value="edit-'.$cle.'">'.__("Edit ".$cle).'</option>';
						}
						?>
					</select>
					<script>
					$.each("<?=@$res['auth']?>".split(','), function(cle, val){
						$('#user #auth option[value="'+ val +'"]').prop('selected', true);
					});
					</script>
				</div>

				<!-- Désactive l'autocomplet du navigateur -->
				<input type="text" id="email-fake" class="hidden">
				<input type="password" id="password-fake" class="hidden">

				<div class="text-right mb-16">
					<label class="mr-8 text-bold" for="name"><?php _e("Name")?></label>
				</div>

				<div class="col-span-2 text-left">
					<input type="text" id="name" value="<?=@$res['name']?>" maxlength="100">
				</div>

				<div class="text-right mb-16">
					<label class="mr-8" for="email"><?php _e("Mail")?></label>
				</div>

				<div class="col-span-2 text-left">
					<input type="email" id="email" value="<?=@$res['email']?>" maxlength="100">
				</div>

				<div class="text-right mb-16">
					<label class="mr-8" for="password_new"><?php _e("Password")?></label>
				</div>

				<div class="col-span-2 text-left">
					<input type="password" id="password_new" class="float left" autocomplete="new-password">
					<a href="javascript:if($('#user-profil #password_new').attr('type') == 'password') $('#user-profil #password_new').attr('type','text'); else $('#user-profil #password_new').attr('type','password'); void(0);" title="<?php _e("See password");?>" class="no-decoration"><i class="icon moon-eye align-middle"></i></a>
					<a href="javascript:$('#user-profil #password_new').make_password();" title="<?php _e("Suggest a password");?>" class="no-decoration"><i class="icon moon-refresh-cw align-middle"></i></a>
					<a href="javascript:send_password();" title="<?php _e("Send password by mail");?>" class="no-decoration" id="send-password"><i class="icon moon-mail align-middle"></i></a>
				</div>

			</div>

			<div class="grid grid-cols-2">

				<?php
				// Si il y a des méta/infos complementaire pour cette utilisateur
				if(is_array(@$GLOBALS['user_info']))
				{
					?>
					<div class="info text-center mb-16"><?php

						$info = json_decode($res['info'], true);

						foreach($GLOBALS['user_info'] as $cle => $val)
						{
							?><div class="mb-8"><label class="text-right mr-8" for="<?=$cle?>"><?php _e($val)?></label> <input type="text" id="info[<?=$cle?>]" value="<?=@$info[$cle]?>"></div><?php
						}

					?></div><?php
				}
				?>

				<?php if(isset($res['date_insert'])){?><div class="mb-8 text-smaller"><label class="text-left"><?php _e("Add the")?></label> <?=$res['date_insert']?></div><?php }?>
				<?php if(isset($res['date_update'])){?><div class="mb-8 text-smaller"><label class="text-left"><?php _e("Updated the")?></label> <?=$res['date_update']?></div><?php }?>

			</div>

				<?php if(isset($_REQUEST['uid']) and $_REQUEST['uid'] != $_SESSION['uid']){?><a id="del" class="float-left"><i class="icon moon-trash vab"></i></a><?php }?>

				<div class="col-span-full text-right ma-8">
					<button id="save-user" class="text-smaller">
						<span><?=($_GET['mode'] == "add-user"? _e("Add") : ($uid ? _e("Save") : _e("Register")))?></span>
						<i class="icon moon-<?=($uid?"save":"plus")?>"></i>
					</button>
				</div>

		</form>

		<script>
			user_tosave = function() {
				$("#save-user i").removeClass("icon-spin moon-settings").addClass("moon-save"); // Affiche l'icône disant qu'il faut sauvegarder sur le btn save
				$("#save-user").removeClass("saved").addClass("to-save");// Changement de la couleur de fond du bouton pour indiquer qu'il faut sauvegarder
			}

			send_password = function(){
				if(confirm("Envoyer un nouveau mot de passe à "+ $("#user-profil #email").val() +" ?"))
				{
					$("#send-password .icon").removeClass("moon-mail").addClass("icon-spin moon-settings");

					// Envoi du mail
					$.ajax({
						type: "POST",
						url: "<?=$GLOBALS['path']?>api/ajax.php?mode=send-password",
						data: {
							uid: $("#user-profil #uid").val(),
							email: $("#user-profil #email").val(),
							nonce: $("#nonce").val()
						}
					})
					.done(function(html) {
						$("#send-password .icon").removeClass("icon-spin moon-settings").addClass("moon-mail");

						// On exécute le retour
						$("body").append(html);
					});
				}
			}

			$(function()
			{
				// On focus on select le contenu
				$("#user .search_user_id").focus(function() {
					$(this).select();
				});

				// Recherche d'un utilisateur sur un api tiers
				$("#user #facebook, #user #google").autocomplete({
					source: function(request, response) {

						var selector = this.element.attr('id');

						$("#user #"+selector).after("<i class='icon icon-spin moon-settings' style='position: absolute; right: 30px; color: rgba(117, 137, 140, 0.5);'></i>");// Loading

						// Chargement des résultats
						$.ajax({
							url: "<?=$GLOBALS['path']?>api/ajax.php?mode=get-external-uid",
							dataType: "json",
							data: {
								search: request.term,
								api: selector,
								nonce: $("#nonce").val()
							},
							success: function(data) {
								response(data);
							},
							complete: function() {
								$("#user #"+selector).next("i").fadeOut();// Close loading
							}
						});
					},
					minLength: 3,
					delay: 500
				}).each(function() {
					$(this).autocomplete("instance")._renderItem = function(ul, item) {
						if(item.img) return $("<li>").append("<a class='block mod'><img src='"+ item.img +"' width='30' class='fl'>"+ item.label +"</a>").appendTo(ul);
					}
				});

				// Si on click sur supprimer
				$("#user .load #del").click(function(event) {
					event.preventDefault();
					if(confirm(__("Delete user")+" "+ $("#uid").val() +" ?")) {
						$.ajax({
							url: "<?=$GLOBALS['path']?>api/ajax.php?mode=del-user",
							data: { uid: $("#uid").val(), nonce: $("#nonce").val() }
						}).done(function(html) {
							$("#user .load").html(html);
						});
					}
				});


				// Si le contenu change, on change le statut du bouton sauvegarder
				$("#user .load input").keyup(function() { user_tosave(); });
				$("#user .load select").change(function() { user_tosave(); });


				$("#user-profil").submit(function(event)
				{
					event.preventDefault();

					// Animation sauvegarde en cours (loading)
					$("#save-user i").removeClass("moon-save").removeClass("moon-plus").addClass("icon-spin moon-settings");

					data = {};

					data["nonce"] = $("#nonce").val();

					// Contenu des input
					$(document).find("#user .load input, #user .load select").each(function() {
						data[$(this).attr("id")] = $(this).val();
					});

					// On sauvegarde en ajax les contenus éditables
					$.ajax({
						type: "POST",
						url: "<?=$GLOBALS['path']?>api/ajax.php?mode=save-user",
						data: data
					})
					.done(function(html) {
						$("body").append(html);
					})
					.fail(function() {
						error(__("Error"));
					});
				});
			});
		</script>
		<?php

		if(isset($GLOBALS['connect'])) $GLOBALS['connect']->close();

	break;


	case "save-user":// CREATION D'UN COMPTE | SAUVEGARDER DES INFOS UTILISATEUR

		//@todo : ajouter un captcha pour éviter les spam d'ajout d'utilisateur. si admin pas de check

		if($_SESSION['nonce'] == $_REQUEST['nonce'])
		{
			include_once("db.php");// Connexion à la db

			$uid = $insert_user = $insert_info = $logout = null;

			// Vérifie que l'on est admin si les utilisateurs publics ne peuvent pas créé de compte
			if(!@$_REQUEST['uid'] and !$GLOBALS['public_account'])
				login('high', 'edit-user');
			elseif(@$_REQUEST['uid'])
			{
				// Si l'utilisateur est différent de nous on vérifie que l'on est admin
				if($_REQUEST['uid'] != $_SESSION['uid']) login('high', 'edit-user');
				else login('high');

				// Récupère les données sur l'utilisateurs
				$sel = $connect->query("SELECT * FROM ".$table_user." WHERE id='".(int)$_REQUEST['uid']."' LIMIT 1");
				$res = $sel->fetch_assoc();

				// Si on édite les droits d'un utilisateur
				if(isset($_POST['auth']))
				{
					// Si les droits d'accès ont changé on déconnecte l'utilisateur pour qu'il recrée son cookie auth
					if($_REQUEST['uid'] != $_SESSION['uid'] and $res['auth'] != implode(",", $_POST['auth']))
						$logout = true;
					// Si on change nos propres droits on recrée le cookie auth (on doit avoir les droits d'édition user)
					elseif($_REQUEST['uid'] == $_SESSION['uid'] and $res['auth'] != implode(",", $_POST['auth']) and isset($_SESSION['auth']['edit-user']))
						token($_REQUEST['uid'], null, implode(",", $_POST['auth']));
				}
			}

			// Nettoyage du email
			$_POST['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

			// Hashage du pwd avec le salt unique
			$password_new = null;
			$hashed_password = null;
			if(@$_POST['password_new']) {
				list($hashed_password, $unique_salt) = hash_pwd($_POST['password_new']);
				$password_new = $_POST['password_new'];
			}

			// Suppression des caractères indésirable pour la sécurité et des espaces de début et fin
			$_POST = array_map("secure_value", $_POST);

			// Sécurisation supplémentaire
			$_POST = array_map(function($value) use($connect) {
				if(is_array($value)) {
					foreach($value as $cle => $val) $value[$cle] = $connect->real_escape_string($val);
				}
				else $value = $connect->real_escape_string($value);
				return $value;
			}, $_POST);


			// UPDATE / INSERT INFOS DE CONNEXION
			if(@$_REQUEST['uid'])
				$sql = "UPDATE ".$GLOBALS['table_user']." SET ";
			else
				$sql = "INSERT INTO ".$GLOBALS['table_user']." SET ";

			// État d'activation
			if(isset($_SESSION['auth']['edit-user']) and isset($_POST['state']))
				$sql .= "state = '".encode($_POST['state'])."', ";
			elseif(!@$_REQUEST['uid'])
				$sql .= "state = '".addslashes($GLOBALS['default_state'])."', ";

			// Droit d'accès
			if(isset($_SESSION['auth']['edit-admin']) and isset($_POST['auth'])) {
				$auth = $connect->real_escape_string(implode(",", $_POST['auth']));
				$sql .= "auth = '".$auth."', ";
			}
			elseif(!@$_REQUEST['uid'])
				$sql .= "auth = '".addslashes($GLOBALS['default_auth'])."', ";

			$name = $connect->real_escape_string($_POST['name']);
			$sql .= "name = '".$name."', ";

			$email = $connect->real_escape_string($_POST['email']);
			$sql .= "email = '".$email."', ";

			// Si informations supplémentaires sur l'utilisateur
			if(isset($_POST['info']) and is_array($_POST['info'])) {
				$info = $connect->real_escape_string(json_encode($_POST['info'], JSON_UNESCAPED_UNICODE));
				$sql .= "info = '".$info."', ";
			}

			// Mot de passe
			if($hashed_password) {
				$sql .= "password = '".addslashes($hashed_password)."', ";
				$sql .= "salt = '".addslashes($unique_salt)."', ";

				// Création du token light
				if($GLOBALS['security'] != 'high' and @$_REQUEST['uid'])
					$sql .= "token = '".addslashes(token_light((int)$_REQUEST['uid'], $unique_salt))."', ";
			}
			else if($logout) $sql .= "token = '', ";// Déconnecte l'utilisateur

			// Token d'api externe
			if(isset($_POST['oauth'])) {
				$oauth = $connect->real_escape_string(json_encode($_POST['oauth'], JSON_UNESCAPED_UNICODE));
				$sql .= "oauth = '".$oauth."', ";
			}

			$sql .= "date_update = NOW() ";

			if(isset($_SESSION['auth']['edit-user']) and isset($_POST['date_insert']))
			{
				$date_insert = $connect->real_escape_string($_POST['date_insert']);
				$sql .= ", date_insert = '".$date_insert."' ";
			}

			if(@$_REQUEST['uid'])
				$sql .= "WHERE id = '".(int)$_REQUEST['uid']."'";
			else
				$sql .= ", date_insert = NOW() ";

			// Exécution de la requête
			$connect->query($sql);

			//echo "_POST<br>"; highlight_string(print_r($_POST, true));
			//echo $sql;


			// Pas d'erreur sur les infos de connexion
			if(!$connect->error)
			{
				// Id de l'utilisateur crée
				if($connect->insert_id) $insert_user = $uid = $connect->insert_id;
				elseif(@$_REQUEST['uid']) $uid = (int)$_REQUEST['uid'];

				if($uid)
				{
					// ENVOI DU MAIL À L'ADMIN : default_state = moderate
					if($GLOBALS['default_state'] == "moderate" and $insert_user and !$_POST['state'] and $GLOBALS['mail_moderate'])
					{
						// Pour le garder secret
						unset($_POST['password_new'], $_POST['password_confirm']);

						// Sujet
						$subject = "[".utf8_encode(htmlspecialchars($_SERVER['HTTP_HOST']))."] ".__("New user to activate")." ".htmlspecialchars($_POST['email']);

						// Lien vers la fiche admin pour activation
						$message = "<br><a href='".make_url("", array("domaine" => true))."api/ajax.php?mode=quick-view-user&uid=".$uid."' target='_blank'>".__("User profile")."</a><br>";

						$message .= "<pre>";
						$message .= print_r($_POST, true);
						$message .= "</pre><br>-------------------------------------------------------<br>";

						$message .= "IP du Visiteur : ".getenv("REMOTE_ADDR")."<br>";
						$message .= "Host : ".gethostbyaddr($_SERVER["REMOTE_ADDR"])."<br>";
						$message .= "User Agent : ".getenv("HTTP_USER_AGENT")."<br>";
						$message .= "IP du Serveur : ".getenv("SERVER_ADDR")."<br>";

						$header="Content-type:text/html; charset=utf-8\r\nFrom:".($_POST['email'] ? htmlspecialchars($_POST['email']) : $GLOBALS['email_contact']);

						mail($GLOBALS['email_contact'], $subject, stripslashes($message), $header);
					}


					// Pour l'auto-login
					if($password_new) $_POST['password'] = $password_new;


					// @todo: ajouter l'envoi de mail à l'user si public_account = true dans conf (hash de verif = id + date crea + global hash).
				}

				?>
				<script>
				$(function()
				{
					<?php
					if(!$connect->error){
						if(@$_REQUEST['uid']){?>// Update réussit

							$("#save-user i").removeClass("moon-settings icon-spin").addClass("moon-check");// Si la sauvegarde réussit on change l'icône du btn
							$("#save-user").removeClass("to-save").addClass("saved");// Si la sauvegarde réussit on met la couleur verte

						<?php }
						elseif($insert_user){?>// Ajout d'un utilisateur

							$("#user .load #uid").val("<?=$insert_user?>");// On met l'id de l'utilisateur dans le input pour le mode save

							$("#save-user i").removeClass("moon-settings icon-spin").addClass("moon-check");// Si la sauvegarde réussit on change l'icône du btn
							$("#save-user").removeClass("to-save").addClass("saved");// Si la sauvegarde réussit on met la couleur verte

							<?php if(isset($_SESSION['auth']['edit-user'])){?>// Peut éditer les users

								$("#save-user span").html("<?php _e("Save")?>");

							<?php }else{?>// Inscription

								$("#save-user span").html("<?php _e("Account created")?>");

								// @todo: bouton de sauvegarde readonly (pour éviter re-submit) + message si validation par mail/admin requise

							<?php }?>

						<?php }
					}
					else {?>
						error("<?=$connect->error;?>");
					<?php }?>
				});
				</script>
				<?php
			}
			elseif($connect->error){?>
				<script>
					error("<?=$connect->error;?>");
				</script>
			<?php }
		}

		// Supp ?? car include parfois
		//if(isset($GLOBALS['connect'])) $GLOBALS['connect']->close();

	break;


	case "check-email":// Check en ajax si le mail est conforme, mx existant, et pas déjà dans la base

		// @todo: check mx : nous n'avons pas réussi à vérifier si votre fournisseur de mail fonctionne correctement

		if($_SESSION['nonce'] == $_REQUEST['nonce'])
		{
			$_POST['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

			if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))// Format valide
			{
				// Si le mail est celui de la personne connecter OU admin, pas besoin de check l'existance
				if(@$_SESSION['email'] == $_POST['email'] OR @$_SESSION['auth']['edit-user']) echo "true";
				else
				{
					include_once("db.php");
					$email = $GLOBALS['connect']->real_escape_string($_POST['email']);
					$sel = $GLOBALS['connect']->query("SELECT id FROM ".$GLOBALS['table_user']." WHERE email='".$email."' LIMIT 1");

					if($res = $sel->fetch_assoc())// Déjà dans la base
						echo "existing account";
					else
						echo "true";
				}
			}
			else echo "false mail";
		}
		else echo "false nonce";

		if(isset($GLOBALS['connect'])) $GLOBALS['connect']->close();

	break;

	case "check-password":// Check en ajax si le password est un minimum sécurisé
	break;

	case "make-password":// Crée un password aléatoirement
		if($_SESSION['nonce'] == $_REQUEST['nonce']) echo make_pwd(mt_rand(8,12));
	break;

	case "send-password":// Crée un password aléatoirement & l'envoi par mail à l'utilisateur
		if($_SESSION['nonce'] == $_REQUEST['nonce'] and @$_REQUEST['uid'] and @$_REQUEST['email'])
		{
			login('high', 'edit-user');

			$pwd = make_pwd(mt_rand(8,12));
			list($hashed_password, $unique_salt) = hash_pwd($pwd);

			$sql = "UPDATE ".$GLOBALS['table_user']." SET ";
			$sql .= "password = '".addslashes($hashed_password)."', ";
			$sql .= "salt = '".addslashes($unique_salt)."', ";
			$sql .= "token = '', ";// Déconnecte l'utilisateur
			$sql .= "date_update = NOW() ";
			$sql .= "WHERE id = '".(int)$_REQUEST['uid']."'";

			$connect->query($sql);

			// Mail avec le mdp
			$subject = "[".utf8_encode(htmlspecialchars($_SERVER['HTTP_HOST']))."] ".__("New Password");
			$message = "Bonjour,<br><br>Voici votre nouveau mot de passe pour vous connecter au site ".utf8_encode(htmlspecialchars($_SERVER['HTTP_HOST']))." : ".($pwd);
			$header="Content-type:text/html; charset=utf-8\r\nFrom:".$GLOBALS['email_contact'];

			mail($_REQUEST['email'], $subject, stripslashes($message), $header);
		}
	break;

	case "get-external-uid":// Cherche l'id d'un utilisateur sur une api tiers

		// @todo si pas le token tiers on ajoute un élément de retour dans le tableau pour ouvrir la boite de login (relog, sans pour autant changer le token maison)
		// @todo: faire : yahoo et microsoft

		if($_SESSION['access_token_external'][$_REQUEST['api']])
		{
			header('Content-Type: application/json');

			switch($_REQUEST['api'])
			{
				case "facebook":
					$response = json_decode(curl("https://graph.facebook.com/search?q=".urlencode($_REQUEST['search'])."&type=user&limit=50&access_token=".$_SESSION['access_token_external'][$_REQUEST['api']]), true);

					//highlight_string(print_r($response, true));

					//while(list($cle, $val) = each($response['data'])) PHP 7.2
					foreach($response['data'] as $cle => $val) {
						$json[] = array(
							'value' => $val['id'],
							'label' => $val['name'],
							'img' => "https://graph.facebook.com/".$val['id']."/picture"
						);
					}
				break;

				case "google":
					// maxResults
					$response = json_decode(curl("https://www.googleapis.com/plus/v1/people/?query=".urlencode($_REQUEST['search'])."&access_token=".$_SESSION['access_token_external'][$_REQUEST['api']]), true);

					//highlight_string(print_r($response, true));

					//while(list($cle, $val) = each($response['items'])) PHP 7.2
					foreach($response['items'] as $cle => $val) {
						$json[] = array(
							'value' => $val['id'],
							'label' => $val['displayName'],
							'img' => $val['image']['url']
						);
					}
				break;
			}

			echo json_encode($json);
		}
		else echo"[{\"value\":\" \", \"label\":\"".ucfirst(encode($_REQUEST['api']))." ".__("Connection required")."\"}]";

	break;



	case "logout":
		logout();
	break;
}
?>
