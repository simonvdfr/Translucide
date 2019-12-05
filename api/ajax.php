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
			$(function() {	
				if(callback) eval(callback + "()");// S'il y a un callback à exécuter
			});
		</script>
		<?
	break;


	case "internal-login":// Connexion avec un login/passe interne au site
		
		// @todo: si la page est appelée directement (ajax.php), charger un fond et charger la dialog
		?>
		<div id="dialog-connect" title="<?_e("Log in");?>">

			<?if($_REQUEST['msg']){?>
			<div class="mas mtn pat ui-state-highlight"><?=htmlspecialchars($_REQUEST['msg']);?></div>
			<?}?>

			<form id="internal-login" class="mts small">

				<input type="hidden" id="nonce" value="<?=nonce("nonce");?>">

				<div class="mbs"><input type="email" id="email" placeholder="<?_e("My email");?>" required class="w100"><span class="wrapper big bold">@</span></div>

				<input type="password" id="password" placeholder="<?_e("My password");?>" required class="w100"><i class="fa fa-lock wrapper bigger"></i>

				<button class="bt internal fr mrn mtm pat">
					<?_e("Log in")?>
					<i class="fa fa-key"></i>
				</button>

			</form>		
		</div>

		<script>
		// S'il y a une fonction de callback
		callback = <?if($_REQUEST['callback']){ echo'"'.encode($_REQUEST['callback'], "_").'"';} else echo"null";?>;

		$(function()
		{
			// Update les nonces dans la page courante pour éviter de perdre le nonce
			$("#nonce").val('<?=$_SESSION['nonce']?>');

			// Login
			$("#internal-login").submit(function(event) 
			{
				event.preventDefault();

				// Icône de chargement
				$("#dialog-connect .bt .fa").removeClass("fa-key").addClass("fa-spin fa-cog");
				
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
				.fa-arrows-cw { display: none; }
				.fa-logout { display: none; }
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
				
				<div id="logout" class="fr" title="<?_e("Log out")?>"><i class="fa fa-fw fa-logout big"></i></div>

				<?if(@$_SESSION['auth']['edit-user']) {?>
				<div id="add-user" class="fr prs" title="<?_e("Add user")?>"><i class="fa fa-fw fa-user-plus"></i></div>
				<div id="list-user" class="fr prs" title="<?_e("List of user")?>"><i class="fa fa-fw fa-users"></i></div>
				<div id="profil" class="fr prs" title="<?_e("My profil")?>"><i class="fa fa-fw fa-user big vam"></i></div>
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
		<?

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
			if($res['state'] == "active") $state = "ok";
			elseif($res['state'] == "moderate") $state = "eye";
			elseif($res['state'] == "email") $state = "mail";
			elseif($res['state'] == "blacklist") $state = "lock";
			elseif($res['state'] == "deactivate") $state = "cancel";

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

		if(isset($GLOBALS['connect'])) $GLOBALS['connect']->close();

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

			<h3 class="medium man mbs"><?=$h3?></h3>

			<input type="hidden" id="uid" value="<?=@$res['id']?>">

			<div class="scroll">

				<div class="mbt">
					<label class="w100p tr mrt" for="state"><?_e("State")?></label> 
					<? if(@$_SESSION['auth']['edit-user']){?>
						<select id="state">
							<option value="active"><?_e("Active")?></option>
							<option value="moderate"><?_e("Moderate")?></option>
							<option value="email"><?_e("User email")?></option>
							<option value="blacklist"><?_e("Blacklist")?></option>
							<option value="deactivate"><?_e("Deactivate")?></option>
						</select>
						<script>$('#user #state option[value="<?=@$res['state']?>"]').prop('selected', true);</script>
					<?}else{?>
						<?_e(@$res['state'])?>
					<?}?>
				</div>

				<div class="mbs" style="max-height: 100px;">
					<label class="w100p tr mrt" for="auth"><?_e("Authorization")?></label>
					<select id="auth" multiple <?=(!@$_SESSION['auth']['edit-admin']?"disabled":"");?>>
						<?
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
				<input type="text" id="email-fake" class="none">
				<input type="password" id="password-fake" class="none">

				<div class="mbt"><label class="w100p tr mrt bold" for="name"><?_e("Name")?></label> <input type="text" id="name" value="<?=@$res['name']?>" maxlength="60" class="w60 bold"></div>

				<div class="mbt"><label class="w100p tr mrt" for="email"><?_e("Mail")?></label> <input type="email" id="email" value="<?=@$res['email']?>" maxlength="100" class="w60"></div>

				<div class="mbs nowrap">
					<label class="w100p tr mrt" for="password_new"><?_e("Password")?></label>
					<input type="password" id="password_new" class="w50" autocomplete="new-password">

					<a href="javascript:if($('#user-profil #password_new').attr('type') == 'password') $('#user-profil #password_new').attr('type','text'); else $('#user-profil #password_new').attr('type','password'); void(0);" title="<?_e("See password");?>"><i class="fa fa-fw fa-eye vam"></i></a>

					<a href="javascript:$('#user-profil #password_new').make_password();" title="<?_e("Suggest a password");?>"><i class="fa fa-fw fa-arrows-cw vam"></i></a>
				</div>


				<?
				// Si il y a des méta/infos complementaire pour cette utilisateur
				if(is_array($GLOBALS['user_info'])) 
				{		
					?>
					<div class="info mbs"><?

						$info = json_decode($res['info'], true);

						foreach($GLOBALS['user_info'] as $cle => $val)
						{
							?><div class="mbt"><label class="w100p tr mrt" for="<?=$cle?>"><?_e($val)?></label> <input type="text" id="info[<?=$cle?>]" value="<?=@$info[$cle]?>" class="w60"></div><?
						}
						
					?></div><?
				}
				?>

				<?if(isset($res['date_update'])){?><div class="mbt small"><label class="w100p tr mrt"><?_e("Updated the")?></label> <?=$res['date_update']?></div><?}?>
				<?if(isset($res['date_insert'])){?><div class="mbt small"><label class="w100p tr mrt"><?_e("Add the")?></label> <?=$res['date_insert']?></div><?}?>			

				<?if(isset($_REQUEST['uid']) and $_REQUEST['uid'] != $_SESSION['uid']){?><a id="del" class="fl"><i class="fa fa-fw fa-trash big vab"></i></a><?}?>

				<button id="save-user" class="fr mat small">
					<span><?=($_GET['mode'] == "add-user"? _e("Add") : ($uid ? _e("Save") : _e("Register")))?></span>
					<i class="fa fa-fw fa-<?=($uid?"floppy":"plus")?> big white"></i>
				</button>

			</div>
		
		</form>

		<script>
		user_tosave = function() {
			$("#save-user i").removeClass("fa-spin fa-cog").addClass("fa-floppy"); // Affiche l'icône disant qu'il faut sauvegarder sur le bt save
			$("#save-user").removeClass("saved").addClass("to-save");// Changement de la couleur de fond du bouton pour indiquer qu'il faut sauvegarder
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
				$("#save-user i").removeClass("fa-floppy").removeClass("fa-plus").addClass("fa-spin fa-cog");
				
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
					<?
					if(!$connect->error){
						if(@$_REQUEST['uid']){?>// Update réussit

							$("#save-user i").removeClass("fa-cog fa-spin").addClass("fa-ok");// Si la sauvegarde réussit on change l'icône du bt
							$("#save-user").removeClass("to-save").addClass("saved");// Si la sauvegarde réussit on met la couleur verte

						<?}
						elseif($insert_user){?>// Ajout d'un utilisateur

							$("#user .load #uid").val("<?=$insert_user?>");// On met l'id de l'utilisateur dans le input pour le mode save

							$("#save-user i").removeClass("fa-cog fa-spin").addClass("fa-ok");// Si la sauvegarde réussit on change l'icône du bt
							$("#save-user").removeClass("to-save").addClass("saved");// Si la sauvegarde réussit on met la couleur verte
							
							<?if(isset($_SESSION['auth']['edit-user'])){?>// Peut éditer les users

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
			elseif($connect->error){?>
				<script>
					error("<?=$connect->error;?>");
				</script>
			<?}
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