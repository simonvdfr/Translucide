<?
@include_once("../config.php");// Les variables
@include_once("function.php");// Fonction

$lang = get_lang();// Sélectionne la langue
load_translation('api');// Chargement des traductions du système

switch($_GET['mode'])
{		
	default:
	case "login":// Check le login interne

		login();
		
		?>
		<script>
		$(document).ready(function()
		{	
			// S'il y a un callback à exécuter
			if(callback) eval(callback + "()");
		});
		</script>
		<?

	break;
	
	case "select-login-mode":
		// @todo: si la page est appelée directement (ajax.php), charger un fond et charger la dialog
		?>
		<div id="dialog-connect" title="<?_e("Administrator Login");?>">

			<style>
				/* Font Awesome pour bt connexion */
				.loading:before {
					content: "\f013" !important;
					animation: fa-spin 2s infinite linear;

					border-right: none !important;
					padding-right: 0 !important;
				}
				.down:before {
					content: "\f0a7" !important;
					animation: bounce-light .35s ease 6 alternate;

					border-right: none !important;
					padding-right: 0 !important;
				}				
					@keyframes bounce-light {
						from { transform: translateY(0);}
						to { transform: translateY(-5px);}
					}
			</style>

			<script>
			// S'il y a une fonction de callback
			callback = <?if($_REQUEST['callback']){ echo'"'.encode($_REQUEST['callback'], "_").'"';} else echo"null";?>;

			// Connexion
			login = function(login_api) 
			{
				bt = $("#dialog-connect a.bt.connect."+login_api);
				
				// FadeOut les autres boutons
				$("#dialog-connect a.bt.connect:not(."+login_api+")").slideUp();
				
				if(login_api != 'internal')// On utilise une api tiers pour la connexion => popup
				{
					// Change l'icône en loading
					$(bt).addClass("loading");
					
					// Affichage du message
					$("#dialog-connect").append("<div class='tc small'>"+ __("Validate the connection in the popup") +"</div>");

					// Création d'un popup qui charge le site de connexion tierce
					width = 420;
					height = 510;
					window.open("<?=$GLOBALS['path']?>api/ajax.php?mode=external-login&login_api="+login_api, "popup_connect", "top="+((screen.height / 2) - (height / 2))+", left="+((screen.width / 2) - (width / 2))+", width="+width+", height="+height+", location=no, menubar=no, directories=no, status=no, scrollbars=auto");
				}
				else// On utilise le système de login interne
				{
					// Unbind le click
					$(bt).attr("href","javascript://").css("cursor","default");

					// Change l'icône en flèche vers le bas
					$(bt).addClass("down");

					// Supprime les css :hover
					$(bt).addClass("nohover");

					// Injection du formulaire de login en dessous du bt
					$.ajax({url: "<?=$GLOBALS['path']?>api/ajax.php?mode=internal-login"}).done(function(html) { $("#dialog-connect").append(html); });
				}
			};
			</script>		

			<?if($_REQUEST['msg']){?>
				<div class="mas mtn pat ui-state-highlight"><?=htmlspecialchars($_REQUEST['msg']);?></div>
			<?}?>

			<a href="javascript:login('internal');void(0);" class="bt connect internal"><?_e("Connection with");?> <?=($GLOBALS['sitename']);?></a>

			<?if($GLOBALS['facebook_api_secret']){?><a href="javascript:login('facebook');void(0);" class="bt connect facebook"><?_e("Connection with");?> Facebook</a><?}?>

			<?if($GLOBALS['google_api_secret']){?><a href="javascript:login('google');void(0);" class="bt connect google"><?_e("Connection with");?> Google</a><?}?>

			<?if($GLOBALS['yahoo_api_secret']){?><a href="javascript:login('yahoo');void(0);" class="bt connect yahoo"><?_e("Connection with");?> Yahoo</a><?}?>
			
			<?if($GLOBALS['microsoft_api_secret']){?><a href="javascript:login('microsoft');void(0);" class="bt connect microsoft"><?_e("Connection with");?> Hotmail - Microsoft</a><?}?>

		</div>
		<?
	break;


	case "internal-login":// Connexion avec un login/passe interne au site
		?>
		<form id="internal-login" class="mts none small">

			<input type="hidden" id="nonce" value="<?=nonce("nonce");?>">

			<div class="mbs"><input type="email" id="email" placeholder="<?_e("My email");?>" required class="w100"><span class="wrapper big bold">@</span></div>

			<input type="password" id="password" placeholder="<?_e("My password");?>" required class="w100"><i class="fa fa-lock wrapper bigger"></i>

			<button class="bt internal fr mrn mtm pat white">
				<?_e("Log in")?>
				<i class="fa fa-key"></i>
			</button>

		</form>		

		<script>
		$(document).ready(function()
		{
			// Update les nonces dans la page courante pour éviter de perdre le nonce
			$("#nonce").val('<?=$_SESSION['nonce']?>');

			// Affichage du formulaire de login interne
			$("#internal-login").slideDown("slow");

			// Login
			$("#internal-login").submit(function(event) 
			{
				event.preventDefault();

				// Icône de chargement
				$(bt).removeClass("down").addClass("loading");
				
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
		<?
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
			<link rel="stylesheet" href="<?=$GLOBALS['font_awesome']?>">	
			<link rel="stylesheet" href="global<?=$GLOBALS['min']?>.css?">
			<link rel="stylesheet" href="lucide.css?">
			<script src="<?=$GLOBALS['jquery'];?>"></script>
			<script src="<?=$GLOBALS['jquery_ui'];?>"></script>
			<script src="lucide.init<?=$GLOBALS['min']?>.js"></script>
			
			<!-- Appel du js supplémentaire pour les options spécifiques au thème -->
			<?if(file_exists($_SERVER['DOCUMENT_ROOT'].$GLOBALS['path']."theme/".$GLOBALS['theme'].($GLOBALS['theme']?"/":"")."admin.init.js")) {?>
					<script src="<?=$GLOBALS['path']?>theme/<?=$GLOBALS['theme'].($GLOBALS['theme']?"/":"")?>admin.init.js"></script>
			<?}?>

			<style>
				#user .absolute { width: 100%; }
				#user .tooltip { 
					max-width: 420px;
					margin: auto;
				}
				.fa-refresh { display: none; }
				.fa-sign-out { display: none; }
			</style>
		</head>
		<body>
			
			<input type="hidden" id="nonce" value="<?=nonce("nonce");?>">

			<div id="admin-bar" class="mtm">				
				<div id="user">				
					<center><i class="fa fa-spin fa-cog biggest mtl" style="position: absolute"></i></center>
				</div>
			</div>

			<script>
			path = "<?=$GLOBALS['path']?>";

			$(document).ready(function()
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
		<?

	break;


	case "user":// AFFICHAGE DE L'INTERFACE DE GESTION DES UTILISATEURS
		
		// @todo: ajouter les checks sur mail, password, et aussi mode non admin
		// @todo encadrer le tout d'un formulaire pour avoir un onchange simple, et aussi metre en place le ajax qui affiche la progression de sauvegarde
		// @todo si appel direct de la page on include dans le body générique

		include_once("db.php");// Connexion à la db

		login('medium');

		?>
		<div class="absolute">
			<div class="tooltip slide-left fire pas mas mlt mod">
				
				<div id="logout" class="fr" title="<?_e("Log out")?>"><i class="fa fa-fw fa-sign-out big"></i></div>

				<?if($_SESSION['auth']['edit-user']) {?>
				<div id="add-user" class="fr prs" title="<?_e("Add user")?>"><i class="fa fa-fw fa-user-plus"></i></div>
				<div id="list-user" class="fr prs" title="<?_e("List of user")?>"><i class="fa fa-fw fa-users"></i></div>
				<div id="profil" class="fr prs" title="<?_e("My profil")?>"><i class="fa fa-fw fa-id-card big vam"></i></div>
				<?}?>				

				<div class="load">
					<?
					$_GET['mode'] = "profil";
					include("ajax.php");
					?>
				</div>

			</div>
		</div>

		<script>
		$(document).ready(function()
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

			$("#logout").click(function() {	
				logout();
			});
		});
		</script>
		<?

	break;


	case "del-user":// SUPPRESSION D'UN COMPTE

		include_once("db.php");// Connexion à la db

		login('high', 'edit-user');

		if($_REQUEST['uid'] != $_SESSION['uid']) 
		{
			if($connect->query("DELETE FROM ".$table_user." WHERE id='".(int)$_REQUEST['uid']."'"))
			{
				// Supprime les métas
				$connect->query("DELETE FROM ".$table_meta." WHERE id='".(int)$_REQUEST['uid']."' AND type='user_info'");

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
			<h3 class="medium man mbs"><?_e("List of user")?></h3>

			<div class="mbs"><input type="text" class="search w70" placeholder="<?_e("Search")?>" value=""></div>			

			<ul class="unstyled pan man">
			<?
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
			if($res['state'] == "active") $state = "check";
			elseif($res['state'] == "moderate") $state = "eye";
			elseif($res['state'] == "email") $state = "envelope";
			elseif($res['state'] == "blacklist") $state = "lock";
			elseif($res['state'] == "deactivate") $state = "close";

			echo"
			<li class='plt prt' onclick=\"select_user('".$res['id']."');\">
				<label><i class='fa fa-fw fa-".$state."' title=\"".__($res['state'])."\"></i></label>
				<label class='bold pat'>".$res['name']."</label>
				<label class='small'>".$res['email']."</label>
			</li>";
		}

		// Si on n'a pas affiché tous les résultats on affiche la navigation par page
		if($num_total > ($page * $num_pp)) echo"<li class='next small' onclick=\"next_users('".($page + 1)."');\">".__("Next")."</li>";

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
				$("#user .search").after("<i class='fa fa-spin fa-cog' style='position: relative; left: -15px; color: rgba(117, 137, 140, 0.5);'></i>");

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
				<?if(isset($msg)) {?>
					// S'il y a un message
					light("<?=$msg?>");
				<?}?>

				// Recherche avec timer
				var timer = null;
				$("#user .search").keyup(function() 
				{
					if(timer != null) clearTimeout(timer);
					timer = setTimeout(search_user, '500');		
				});
			});
			</script>
			<?
		}			
	break;


	case "add-user":// AJOUTER UN UTILISATEUR PAR L'ADMIN
		
		include_once("db.php");// Connexion à la db

		login('high', 'edit-user');


	case "profil":// AFFICHAGE DU FORMULAIRE UTILISATEUR

		// @todo ajouter une icône a coté du picto de state pour re-envoyer le mail d'activation à l'utilisateur / bt pour passer l'utilisateur en 'active' si en mode 'moderate'
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

			// Récupération des infos sur l'utilisateur
			$sel_meta = $connect->query("SELECT * FROM ".$table_meta." WHERE id='".(int)$uid."' AND type='user_info' LIMIT 1");
			$res_meta = $sel_meta->fetch_assoc();

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

			<h3 class="medium man mbs"><?=$h3?></h3>

			<input type="hidden" id="uid" value="<?=(isset($res['id'])?$res['id']:"")?>">

			<div class="mbt">
				<label class="w100p tr mrt" for="state"><?_e("State")?></label> 
				<? if($_SESSION['auth']['edit-user']){?>
					<select id="state" class="fa-select">
						<option value="active">&#xf00c; <?_e("Active")?></option>
						<option value="moderate">&#xf06e; <?_e("Moderate")?></option>
						<option value="email">&#xf0e0; <?_e("User email")?></option>
						<option value="blacklist">&#xf023; <?_e("Blacklist")?></option>
						<option value="deactivate">&#xf00d; <?_e("Deactivate")?></option>
					</select>
					<script>$('#user #state option[value="<?=$res['state']?>"]').prop('selected', true);</script>
				<?}else{?>
					<?_e($res['state'])?>
				<?}?>
			</div>

			<div class="mbs" style="max-height: 100px;">
				<label class="w100p tr mrt" for="auth"><?_e("Authorization")?></label>
				<select id="auth" class="fa-select" multiple <?=(!$_SESSION['auth']['edit-admin']?"disabled":"");?>>
					<option value="edit-admin">&#xf21b; <?_e("Managing admins")?></option>
					<option value="edit-user">&#xf007; <?_e("Managing users")?></option>

					<option value="edit-config">&#xf013; <?_e("Edit Config")?></option>

					<option value="edit-nav">&#xf0ca; <?_e("Edit menu")?></option>
					<option value="edit-header">&#xf0a6; <?_e("Edit header")?></option>
					<option value="edit-footer">&#xf0a7; <?_e("Edit footer")?></option>

					<option value="add-media">&#xf093; <?_e("Send Files")?></option>
					<option value="edit-media">&#xf07b; <?_e("Edit Files")?></option>
					
					<?
					while(list($cle, $array) = each($GLOBALS['add-content']))
					{
						echo'<option value="add-'.$cle.'">&#xf0f6; '.__("Add ".$cle).'</option>';
						echo'<option value="edit-'.$cle.'">&#xf0f6; '.__("Edit ".$cle).'</option>';
					}
					?>

					<option value="add-media-public">&#xf114; <?_e("Public file")?></option>
					<option value="edit-public">&#xf0a1; <?_e("Public content")?></option>
				</select>
				<script>
				$.each("<?=$res['auth']?>".split(','), function(cle, val){ 
					$('#user #auth option[value="'+ val +'"]').prop('selected', true);
				});
				</script>					
			</div>
			
			<!-- Désactive l'autocomplet du navigateur -->
			<input type="text" id="email-fake" class="none">
			<input type="password" id="password-fake" class="none">

			<div class="mbt"><label class="w100p tr mrt bold" for="name"><?_e("Name")?></label> <input type="text" id="name" value="<?=(isset($res['name'])?$res['name']:"")?>" maxlength="60" class="w60 bold"></div>

			<div class="mbt"><label class="w100p tr mrt" for="email"><?_e("Mail")?></label> <input type="email" id="email" value="<?=(isset($res['email'])?$res['email']:"")?>" maxlength="100" class="w60"></div>

			<div class="mbs nowrap">
				<label class="w100p tr mrt" for="password"><?_e("Password")?></label>
				<input type="password" id="password" class="w50" autocomplete="new-password">

				<a href="javascript:if($('#user-profil #password').attr('type') == 'password') $('#user-profil #password').attr('type','text'); else $('#user-profil #password').attr('type','password'); void(0);" title="<?_e("See password");?>"><i class="fa fa-fw fa-eye vam"></i></a>

				<a href="javascript:$('#user-profil #password').make_password();" title="<?_e("Suggest a password");?>"><i class="fa fa-fw fa-refresh vam"></i></a>
			</div>

			<?if($GLOBALS['facebook_api_secret']){?><div class="mbt"><label class="w100p tr mrt" for="facebook"><?_e("Facebook id")?></label> <input type="text" id="oauth[facebook]" value="<?=$oauth['facebook']?>" class="w60 small search_user_id"></div><?}?>

			<?if($GLOBALS['google_api_secret']){?><div class="mbt"><label class="w100p tr mrt" for="google"><?_e("Google id")?></label> <input type="text" id="oauth[google]" value="<?=$oauth['google']?>" class="w60 small search_user_id"></div><?}?>

			<?if($GLOBALS['yahoo_api_secret']){?><div class="mbt"><label class="w100p tr mrt" for="yahoo"><?_e("Yahoo id")?></label> <input type="text" id="oauth[yahoo]" value="<?=$oauth['yahoo']?>" class="w60 small search_user_id"></div><?}?>

			<?if($GLOBALS['microsoft_api_secret']){?><div class="mbs"><label class="w100p tr mrt" for="microsoft"><?_e("Microsoft id")?></label> <input type="text" id="oauth[microsoft]" value="<?=$oauth['microsoft']?>" class="w60 small search_user_id"></div><?}?>

			<?
			// Si il y a des méta/infos complementaire pour cette utilisateur
			if(is_array($GLOBALS['meta_user'])) 
			{		
				?>
				<div class="meta mbs"><?
					
					if($res_meta['val']) $metas = json_decode($res_meta['val'], true);

					while(list($cle, $val) = each($GLOBALS['meta_user']))
					{
						?><div class="mbt"><label class="w100p tr mrt" for="<?=$cle?>"><?_e($val)?></label> <input type="text" id="meta[<?=$cle?>]" value="<?=$metas[$cle]?>" class="w60"></div><?
					}			
					
				?></div><?
			}
			?>

			<?if(isset($res['date_update'])){?><div class="mbt small"><label class="w100p tr mrt"><?_e("Updated the")?></label> <?=$res['date_update']?></div><?}?>
			<?if(isset($res['date_insert'])){?><div class="mbt small"><label class="w100p tr mrt"><?_e("Add the")?></label> <?=$res['date_insert']?></div><?}?>			

			<?if(isset($_REQUEST['uid']) and $_REQUEST['uid'] != $_SESSION['uid']){?><a id="del" class="fl"><i class="fa fa-fw fa-trash big vab"></i></a><?}?>

			<button id="save-user" class="fr mat small">
				<span><?=($_GET['mode'] == "add-user"? _e("Add") : ($uid ? _e("Save") : _e("Register")))?></span>
				<i class="fa fa-fw fa-<?=($uid?"save":"plus")?> big white"></i>
			</button>
		
		</form>

		<script>
		user_tosave = function() {
			$("#save-user i").removeClass("fa-spin fa-cog").addClass("fa-save"); // Affiche l'icône disant qu'il faut sauvegarder sur le bt save
			$("#save-user").removeClass("saved").addClass("to-save");// Changement de la couleur de fond du bouton pour indiquer qu'il faut sauvegarder
		}

		$(document).ready(function()
		{			
			// On focus on select le contenu
			$("#user .search_user_id").focus(function() {
				$(this).select();
			});

			// Recherche d'un utilisateur sur un api tiers
			$("#user #facebook, #user #google").autocomplete({
				source: function(request, response) {	
					
					var selector = this.element.attr('id');
					
					$("#user #"+selector).after("<i class='fa fa-spin fa-cog' style='position: absolute; right: 30px; color: rgba(117, 137, 140, 0.5);'></i>");// Loading
					
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
				$("#save-user i").removeClass("fa-save").removeClass("fa-plus").addClass("fa-spin fa-cog");
				
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
		<?
	break;


	case "save-user":// CREATION D'UN COMPTE | SAUVEGARDER DES INFOS UTILISATEUR
		
		//@todo : ajouter un captcha pour éviter les spam d'ajout d'utilisateur. si admin pas de check

		if($_SESSION['nonce'] == $_REQUEST['nonce'])
		{
			include_once("db.php");// Connexion à la db		

			$uid = $insert_user = $insert_meta = null;
			
			// Vérifie que l'on est admin si les utilisateurs publics ne peuvent pas créé de compte
			if(!$_REQUEST['uid'] and !$GLOBALS['public_account']) login('high', 'edit-user');
			elseif($_REQUEST['uid'])
			{
				// Si on l'utilisateur est différent de nous on vérifie que l'on est admin
				if($_REQUEST['uid'] != $_SESSION['uid']) login('high', 'edit-user');
				else login('high');

				$sel = $connect->query("SELECT * FROM ".$table_user." WHERE id='".(int)$_REQUEST['uid']."' LIMIT 1");
				$res = $sel->fetch_assoc();
			}

			// Nettoyage du email
			$_POST['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
			
			// Hashage du pwd avec le salt unique
			$password = null;
			if($_POST['password']) list($password, $unique_salt) = hash_pwd($_POST['password']);

			// Suppression des caractères indésirable pour la sécurité et des espaces de début et fin
			$_POST = array_map("secure_value", $_POST);

			// Sécurisation supplémentaire
			$_POST = array_map(function($value) use($connect) {
				if(is_array($value)) { while(list($cle, $val) = each($value)) $value[$cle] = $connect->real_escape_string($val); }
				else $value = $connect->real_escape_string($value);
				return $value; 
			}, $_POST);


			// UPDATE / INSERT INFOS DE CONNEXION
			if($_REQUEST['uid']) 
				$sql = "UPDATE ".$GLOBALS['table_user']." SET ";
			else 
				$sql = "INSERT INTO ".$GLOBALS['table_user']." SET ";
			
			// État d'activation
			if($_SESSION['auth']['edit-user'] and $_POST['state'])
				$sql .= "state = '".encode($_POST['state'])."', ";
			elseif(!$_REQUEST['uid']) 
				$sql .= "state = '".addslashes($GLOBALS['default_state'])."', ";
			
			// Droit d'accès
			if($_SESSION['auth']['edit-admin'] and $_POST['auth']) {
				$auth = $connect->real_escape_string(implode(",", $_POST['auth']));
				$sql .= "auth = '".$auth."', ";
			}
			elseif(!$_REQUEST['uid']) 
				$sql .= "auth = '".addslashes($GLOBALS['default_auth'])."', ";

			$name = $connect->real_escape_string($_POST['name']);			
			$sql .= "name = '".$name."', ";

			$email = $connect->real_escape_string($_POST['email']);
			$sql .= "email = '".$email."', ";
			
			// Mot de passe
			if($password) {
				$sql .= "password = '".addslashes($password)."', ";
				$sql .= "salt = '".addslashes($unique_salt)."', ";

				// Création du token light
				if($GLOBALS['security'] != 'high' and (int)$_REQUEST['uid']) $sql .= "token = '".addslashes(token_light((int)$_REQUEST['uid'], $unique_salt))."', ";
			}
			
			// Token d'api externe
			if(isset($_POST['oauth'])) {
				$oauth = $connect->real_escape_string(json_encode($_POST['oauth'], JSON_UNESCAPED_UNICODE));			
				$sql .= "oauth = '".$oauth."', ";
			}

			$sql .= "date_update = NOW() ";

			if($_REQUEST['uid'])
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
				elseif((int)$_REQUEST['uid']) $uid = (int)$_REQUEST['uid'];

				if($uid) 
				{
					// On regarde si il n'y a pas déjà des donnée dans la base
					$sel_meta = $connect->query("SELECT * FROM ".$GLOBALS['table_meta']." WHERE id='".(int)$uid."' AND type='user_info' LIMIT 1");
					$res_meta = $sel_meta->fetch_assoc();

					// AJOUT DES DONNÉE EN MÉTA
					if($uid and isset($_POST['meta']) and is_array($_POST['meta'])) {
						if($res_meta['id']) 
							$sql = "UPDATE ".$GLOBALS['table_meta']." SET ";
						else 
							$sql = "INSERT INTO ".$GLOBALS['table_meta']." SET ";
						
						$meta = $connect->real_escape_string(json_encode($_POST['meta'], JSON_UNESCAPED_UNICODE));
						$sql .= "val = '".$meta."' ";

						if($res_meta['id']) 
							$sql .= "WHERE id = '".(int)$uid."' AND type = 'user_info' LIMIT 1";
						else 
							$sql .= ", type = 'user_info', id = '".(int)$uid."'";
						
						$connect->query($sql);
						
						//echo "_POST['meta']<br>"; highlight_string(print_r($_POST['meta'], true));
						//echo $sql;

						if(!$connect->error) 
							if($connect->insert_id) $insert_meta = $connect->insert_id;
					}

 
					// ENVOI DU MAIL À L'ADMIN : default_state = moderate
					if($GLOBALS['default_state'] == "moderate" and $insert_user and !$_POST['state']) 
					{
						// Pour le garder secret
						$password = $_POST['password'];
						unset($_POST['password'], $_POST['password_confirm']);
						
						// Sujet
						$subject = "[".utf8_encode($GLOBALS['sitename'])."] ".__("New user to activate")." ".htmlspecialchars($_POST['email']);
						
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
						
						// Pour l'auto-login
						$_POST['password'] = $password;
					}


					// @todo: ajouter l'envoi de mail à l'user si public_account = true dans conf (hash de verif = id + date crea + global hash).
				}
			
				?>
				<script>
				$(document).ready(function()
				{
					<?
					if(!$connect->error){
						if($_REQUEST['uid']){?>// Update réussit

							$("#save-user i").removeClass("fa-cog fa-spin").addClass("fa-check");// Si la sauvegarde réussit on change l'icône du bt
							$("#save-user").removeClass("to-save").addClass("saved");// Si la sauvegarde réussit on met la couleur verte

						<?}
						elseif($insert_user){?>// Ajout d'un utilisateur

							$("#user .load #uid").val("<?=$insert_user?>");// On met l'id de l'utilisateur dans le input pour le mode save

							$("#save-user i").removeClass("fa-cog fa-spin").addClass("fa-check");// Si la sauvegarde réussit on change l'icône du bt
							$("#save-user").removeClass("to-save").addClass("saved");// Si la sauvegarde réussit on met la couleur verte
							
							<?if($_SESSION['auth']['edit-user']){?>// Peut éditer les users

								$("#save-user span").html("<?_e("Save")?>");

							<?}else{?>// Inscription

								$("#save-user span").html("<?_e("Account created")?>");

								// @todo: bouton de sauvegarde readonly (pour éviter re-submit) + message si validation par mail/admin requise 

							<?}?>

						<?}
					}
					else {?>
						error("<?=$connect->error;?>");
					<?}?>
				});
				</script>
				<?
			}
		}
	break;


	case "check-email":// Check en ajax si le mail est conforme, mx existant, et pas déjà dans la base

		// @todo: check mx : nous n'avons pas réussi à vérifier si votre fournisseur de mail fonctionne correctement

		if($_SESSION['nonce'] == $_REQUEST['nonce'])
		{
			$_POST['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

			if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
			{
				include_once("db.php");

				$email = $GLOBALS['connect']->real_escape_string($_POST['email']);

				$sel = $GLOBALS['connect']->query("SELECT id FROM ".$GLOBALS['table_user']." WHERE email='".$email."' LIMIT 1");

				if($res = $sel->fetch_assoc()) 
					echo "existing account";
				else 
					echo "true";
			}
			else echo "false mail";
		}
		else echo "false nonce";

	break;

	case "check-password":// Check en ajax si le password est un minimum sécurisé
	break;

	case "make-password":// Crée un password aléatoirement		
		if($_SESSION['nonce'] == $_REQUEST['nonce']) echo make_pwd(mt_rand(8,12));
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

					while(list($cle, $val) = each($response['data'])) {
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

					while(list($cle, $val) = each($response['items'])) {
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


	case "external-login":// external_token : utilisation de systèmes de connexion tierce, se déroule dans une Popup
		
		// @todo: ajouter un mode pour ajouter un login silencieux, juste pour avoir les tokens tiers
		// @todo: Twitter, Instagram, Flicker

		// Vérifie que l'on a sélectionné un système tiers
		if($_REQUEST['login_api']) $login_api = $_SESSION['login_api'] = encode($_REQUEST['login_api']);
		else exit(false);

		// Variable générique
		$redirect_uri = $GLOBALS['home']."api/ajax.php?mode=external-login&login_api=";

		if(!isset($_REQUEST["code"])) nonce('state');// CSRF protection

		
		
		// FACEBOOK params &scope=user_photos

		$get_code['facebook'] = "https://graph.facebook.com/oauth/authorize?client_id=".$GLOBALS['facebook_api_id']."&state=".$_SESSION['state']."&display=popup&redirect_uri=".urlencode($redirect_uri)."facebook";

		$token_return_type['facebook'] = "json";// url

		$get_token['facebook'] = "https://graph.facebook.com/oauth/access_token?client_id=".$GLOBALS['facebook_api_id']."&client_secret=".$GLOBALS['facebook_api_secret']."&code=".(isset($_REQUEST['code'])?$_REQUEST['code']:"")."&redirect_uri=".urlencode($redirect_uri)."facebook";

		$token_params['facebook'] = null;
		
		// https://graph.facebook.com/me/albums?fields=photos&access_token=
		$get_info['facebook'] = "https://graph.facebook.com/me?fields=id,name,picture&access_token=";
		$get_info_uid['facebook'] = "id";



		// GOOGLE params
		
		// Le scope plus.login permet de faire des recherches d'autre utilisateur
		$get_code['google'] = "https://accounts.google.com/o/oauth2/auth?scope=".urlencode("profile https://www.googleapis.com/auth/plus.login")."&state=".$_SESSION['state']."&response_type=code&client_id=".$GLOBALS['google_api_id']."&redirect_uri=".urlencode($redirect_uri)."google";// pour obtenir le code
		
		$token_return_type['google'] = "json";

		$get_token['google'] = "https://accounts.google.com/o/oauth2/token";// pour obtenir le token avec le code

		$token_params['google'] = array(
			"code" => (isset($_REQUEST['code'])?$_REQUEST['code']:""),
			"client_id" => $GLOBALS['google_api_id'],
			"client_secret" => $GLOBALS['google_api_secret'],
			"redirect_uri" => $redirect_uri."google",
			"grant_type" => "authorization_code"
		);

		$get_info['google'] = "https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token=";// pour obtenir id de l'utilisateur
		$get_info_uid['google'] = "id";



		// YAHOO params

		$get_code['yahoo'] = "https://api.login.yahoo.com/oauth2/request_auth?client_id=".$GLOBALS['yahoo_api_id']."&state=".$_SESSION['state']."&response_type=code&redirect_uri=".urlencode($redirect_uri)."yahoo";

		$token_return_type['yahoo'] = "json";

		$get_token['yahoo'] = "https://api.login.yahoo.com/oauth2/get_token";
		$get_token_uid['yahoo'] = "xoauth_yahoo_guid";

		$token_params['yahoo'] = array(
			"code" => (isset($_REQUEST['code'])?$_REQUEST['code']:""),
			"client_id" => $GLOBALS['yahoo_api_id'],
			"client_secret" => $GLOBALS['yahoo_api_secret'],
			"redirect_uri" => $redirect_uri."yahoo",
			"grant_type" => "authorization_code"
		);



		// MICROSOFT params

		$get_code['microsoft'] = "https://oauth.live.com/authorize?response_type=code&client_id=".$GLOBALS['microsoft_api_id']."&state=".$_SESSION['state']."&scope=wl.signin wl.basic&redirect_uri=".urlencode($redirect_uri)."microsoft";

		$token_return_type['microsoft'] = "json";
		
		//https://login.microsoftonline.com/common/oauth2/token
		$get_token['microsoft'] = "https://oauth.live.com/token";
		$get_token_uid['microsoft'] = "user_id";

		$token_params['microsoft'] = array(
			"code" => (isset($_REQUEST['code'])?$_REQUEST['code']:""),
			"client_id" => $GLOBALS['microsoft_api_id'],
			"client_secret" => $GLOBALS['microsoft_api_secret'],
			"redirect_uri" => $redirect_uri."microsoft",
			"grant_type" => "authorization_code"
		);
		
		// https://graph.microsoft.com/v1.0/me?access_token=
		$get_info['microsoft'] = "https://apis.live.net/v5.0/me?access_token=";


		
		// On ouvre l'URL tierse pour récupérer le code
		if($get_code[$login_api] and !isset($_REQUEST["code"])) {			
			header("Location: ".$get_code[$login_api]);
			exit;
		}


		
		// On a le code donc on va chercher le token
		if($login_api and $_REQUEST["code"] and $_SESSION['state'] and $_SESSION['state'] === $_REQUEST['state'])
		{
			// Plus besoin du state, on le supprime
			unset($_SESSION['state']);

			// Récupération du token
			$token_response = curl($get_token[$login_api], $token_params[$login_api]);
			
			// Extraction du token de la réponse
			if($token_return_type[$login_api] == "url") {			
				$tab_token_response = null;
				parse_str($token_response, $tab_token_response);
			}
			else {
				$tab_token_response = json_decode($token_response, true);
			}

			//echo"<br><br><strong>Response</strong> : "; highlight_string(print_r($tab_token_response, true));

			// On a un access_token
			if($tab_token_response['access_token'])
			{
				// On récupère le token tiers
				$_SESSION['access_token_external'][$login_api] = $tab_token_response['access_token'];

				// On récupère l'id tiers s'il se trouve dans le retour avec le access_token
				if(isset($get_token_uid[$login_api])) $uid = $tab_token_response[$get_token_uid[$login_api]];

				// Rapatriement des données de l'user (id, nom...)
				if($get_info[$login_api]) {
					$info_response = json_decode(curl($get_info[$login_api].$tab_token_response['access_token']), true);
					//echo"<br><br><strong>User info</strong> : "; highlight_string(print_r($info_response, true));

					// On récupère l'id tiers s'il se trouve dans les infos
					if($get_info_uid[$login_api]) $uid = $info_response[$get_info_uid[$login_api]];
				}

				// Si on a un access_token tiers on crée un token maison checkable facilement et avec une durée de vie plus longue
				if($uid)
				{
					if(!isset($GLOBALS['connect'])) include_once("db.php");
					
					// On vérifie l'utilisateur
					$uid = $connect->real_escape_string($uid);
					$sel = $connect->query("SELECT * FROM ".$table_user." WHERE oauth LIKE '%\"".$login_api."\":\"".$uid."\"%' AND state='active' LIMIT 1");
					$res = $sel->fetch_assoc();
					
					// L'utilisateur existe et est activé
					if($res['id'])
					{
						// Supprime l'ancienne session
						session_regenerate_id(true);

						if(token($res['id'], $res['email'], $res['auth']))// On crée le token maison
						{
							// Crée le token light pour vérifier si on a le bon mot de passe
							token_light($res['id'], $res['salt']);

							?>
							<script>
								// Quand l'utilisateur ferme la fenêtre ou le js
								window.onunload = function() 
								{
									// S'il y a une fonction de callback à lancer : typiquement l'edition
									if(window.opener.callback) {										
										eval("opener." + window.opener.callback + "()");
									}
								}	
								window.close();								
							</script>
							<?
						}
					}
					else $msg = __("Unknown user")." ".htmlspecialchars($uid);
				}
				else $msg = __("Unable to find the user number");
			}
			else $msg = __("Unable to find the access token");
		}
		else $msg = __("Connection error")." 1";

		if($msg) echo $msg;

		//highlight_string(print_r($_SESSION, true));
		//highlight_string(print_r($_REQUEST, true));

	break;
	
	case "logout":
		logout();
	break;
}
?>