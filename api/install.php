<?
include_once("config.init.php");// Les variables par défaut
include_once("function.php");// Fonction

$lang = get_lang();// Sélectionne  la langue
load_translation('api');// Chargement des traductions du système

switch(@$_GET['mode'])
{
	default:// FORMULAIRE de configuration

		//@todo: ajouter la possibilité de récup notre propre id fb, google, yah, ms (mode silencieux de login tiers)
		//@todo: voir pour utiliser ce fichier également en ajax pour édit la config par la suite
		//@todo: Ajouter un lien pour test les connexions tierses
		//@todo: donner les URL à rentrer dans les applications tierses
		//@todo: ajouter un droit d'édition light de la config (nom du site, code analytics, mail contact...) ou visible par tous les éditeurs de contenu ?
		//@todo: Vérif le cas ou pas de fichier conf existe
		//@todo: Vérif le cas ou fichier conf exist
		// highlight_string(print_r($_SERVER, true));

		// Pour éviter les problèmes de cache qui appèlerais un fichier inexistant
		// cas du favicon.ico qui crée une 404 qui charge donc l'install et crée un nouveau nonce
		// @todo: SUPP car crée un bug sur certaine config apache => http2 ?
		/*if(isset($_SERVER['REDIRECT_URL'])) {
			header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
			exit("<h1>404 error : page not found</h1>");
		}*/

		// Verifie que l'on execute bien depuis index.php
		// Evite d'avoir d'autre chargement de la config (ex: favicon.ico inexistant qui charge la conf une 2ème fois))
		// Si url de redirection existe, elle doit etre = au nom du script executé qui appel l'install = index.php
		if(isset($_SERVER['REDIRECT_URL']) and $_SERVER['REDIRECT_URL'] != $_SERVER['SCRIPT_NAME']) exit;

		// Si on appelle directement le fichier depuis le dossier api/ => exit
		if(strstr($_SERVER['SCRIPT_NAME'], 'install.php')) exit;

		// Charge la config maison si elle existe depuis les 2 chemins possibles
		@include_once("config.php");// Si chargement en include
		@include_once("../config.php");// Si chargement depuis le dossier api dans l'url

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

			"Start installation" => array("fr" => "Lancer l'installation"),

			"Configuration already created" => array("fr" => "Configuration déjà crée")
		);

		add_translation($add_translation);


		// On vérifie si la configuration est déjà créée / normalement plus utile car on bloque plus haut le chargement de install.php directement dans l'url
		if($GLOBALS['db_server'] or $GLOBALS['db_user'] or $GLOBALS['db']) exit('<h1>'.__('Configuration already created').'</h1>');
	

		// Chemin complet du site
		$scheme_domain_path = "";
		if($GLOBALS['scheme'] and $GLOBALS['domain'] and $GLOBALS['path'])
			$scheme_domain_path = $GLOBALS['home'];
		else {
			if(isset($_SERVER['REQUEST_SCHEME'])) $scheme_domain_path .= $_SERVER['REQUEST_SCHEME']."://";
			else $scheme_domain_path .= "http://";
			
			$scheme_domain_path .= $_SERVER['SERVER_NAME'];

			//@todo vérif car crée un bug sur les install en sous domaine
			//$scheme_domain_path .= str_replace(basename($_SERVER['REQUEST_URI']) , "", $_SERVER['REQUEST_URI']);
			$scheme_domain_path .= $_SERVER['REQUEST_URI'];
		}

		// Nom du site
		if(isset($GLOBALS['sitename'])) $sitename =  utf8_encode($GLOBALS['sitename']);
		else 
		{
			$parse_url = parse_url($scheme_domain_path);
			// Si dossier
			if($parse_url['path'] != '/') $sitename = ucfirst(trim($parse_url['path'],'/'));
			else// Si juste domaine
			{
				$domains = explode('.', $_SERVER['SERVER_NAME']);
				$sitename = ucfirst($domains[count($domains)-2]);
			}
		}


		header('Content-type: text/html; charset=UTF-8');

		?><!DOCTYPE html>
		<html lang="<?=$lang;?>">
		<head>
			
			<meta charset="utf-8">

			<title><?_e("Site Installation");?></title>

			<meta name="robots" content="noindex, nofollow">

			<meta name="viewport" content="width=device-width, initial-scale=1">

			<link rel="shortcut icon" type="image/x-icon" href="about:blank">
			<!-- Pour eviter de charger un ico 404 qui recharge la config -->

			<link rel="stylesheet" href="<?=$GLOBALS['jquery_ui_css'];?>">

			<link rel="stylesheet" href="api/global.css?">

			<style>
				@font-face {
					font-family: 'FontAwesome';
					src:  url('<?=$GLOBALS['path']?>api/icons/icons.eot?<?=$GLOBALS['cache']?>');
					src:  
						url('<?=$GLOBALS['path']?>api/icons/icons.eot?<?=$GLOBALS['cache']?>#iefix') format('embedded-opentype'),
						url('<?=$GLOBALS['path']?>api/icons/icons.woff2?<?=$GLOBALS['cache']?>') format('woff2'),
						url('<?=$GLOBALS['path']?>api/icons/icons.woff?<?=$GLOBALS['cache']?>') format('woff'),
						url('<?=$GLOBALS['path']?>api/icons/icons.ttf?<?=$GLOBALS['cache']?>') format('truetype'),
						url('<?=$GLOBALS['path']?>api/icons/icons.svg?<?=$GLOBALS['cache']?>#icons') format('svg');
					font-weight: normal;
					font-style: normal;
				}
				
				body { background-color: #75898c; }
				.layer { box-shadow: 0 0 60px rgba(53, 116, 127, 0.3) inset, 0 0 5px rgba(0, 0, 0, 0.3);	}
				.layer:after { display: none; }

				label { 
					text-align: right;
					padding-right: 1rem;
					cursor: default;
				}

				@media screen and (max-width: 640px) 
				{
					.w80 { width: 95%; }
					.w10, .w20, .w30, .w50, .w60 { width: 90%; }

					label { 
						display: block;
						text-align: left;
					}
				}

				.bt.fixed.top { display: none !important; }
			</style>

			<script src="<?=$GLOBALS['jquery'];?>"></script>

			<script src="<?=$GLOBALS['jquery_ui'];?>"></script>

			<script src="api/lucide.init.js"></script>

			<script>
				path = "";

				submittable = function() {
					// Icône de chargement
					$("#setup button i").removeClass("fa-spin");
					
					// Active le submit
					$("#setup button").attr("disabled", false);
				}

				$(function()
				{
					// Setup
					$("#setup").submit(function(event) 
					{
						event.preventDefault();

						// Icône de chargement
						$("#setup button i").addClass("fa-spin");
						
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
							url: "api/install.php?mode=start",
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

			<div class="w80 center">

				<h2 class="tc"><?_e("Site Installation");?></h2>

				<div class="layer mod pam mbm">

					<form id="setup">

						<input type="hidden" id="nonce" name="nonce" value="<?=nonce("nonce");?>" class="w100">

						<ul class="unstyled">

							<li><label class="w30"><?_e("Address database");?></label> <input type="text" id="db_server" value="<?=$GLOBALS['db_server'];?>" placeholder="localhost" required class="w60 vatt"></li>
							
							<li><label class="w30"><?_e("Name of the data base");?></label> <input type="text" id="db" value="<?=$GLOBALS['db'];?>" required class="w60 vatt"></li>
							
							<li><label class="w30"><?_e("MySQL Username");?></label> <input type="text" id="db_user" value="<?=$GLOBALS['db_user'];?>" placeholder="root" required class="w60 vatt"></li>
							
							<li>
								<label class="w30"><?_e("MySQL User Password");?></label> <input type="password" id="db_pwd" value="<?=$GLOBALS['db_pwd'];?>" class="w60 vatt">
								<a href="javascript:void(0);" onclick="if($('#db_pwd').attr('type') == 'password') $('#db_pwd').attr('type','text'); else $('#db_pwd').attr('type','password');" tabindex="-1"><i class="fa fa-fw fa-eye mts vam"></i></a>
							</li>
							
							<li><label class="w30"><?_e("Table Prefix");?></label> <input type="text" id="db_prefix" value="<?=$GLOBALS['db_prefix'];?>" placeholder="tl_" class="w10 vatt"></li>


							<li class="mtm"><label class="w30 bold"><?_e("Name of the site");?></label> <input type="text" id="sitename" value="<?=$sitename;?>" class="w60 vatt"></li>
							<li>
								<label class="w30"><?_e("Site theme");?></label>

								<select id="theme" class="vatt">
								<?
								// Un thème dans la racine
								if(file_exists("theme/header.php")) echo"<option value=\"\"".($GLOBALS['theme'] == "" ? " selected":"").">/</option>";
								
								// Des dossiers de thème
								$scandir = array_diff(scandir("theme/"), array('..', '.', 'tpl'));
								foreach($scandir as $cle => $file) { 
									if(is_dir("theme/".$file)) echo"<option value=\"".$file."\"".($GLOBALS['theme'] == $file ? " selected":"").">".$file."</option>";
								}							
								?>					
								</select>
								
							</li>
							<li><label class="w30"><?_e("Site Location");?></label> <input type="text" id="scheme_domain_path" value="<?=$scheme_domain_path;?>" required class="w60 vatt"></li>


							<li class="mtm">
								<label class="w30 bold"><i class="fa fa-fw fa-globe"></i> <?_e("Administrator email");?></label> <input type="email" id="email_contact" value="<?=$GLOBALS['email_contact'];?>" required maxlength="100" class="w60 vatt">					
							</li>
							<li>
								<label class="w30 bold"><i class="fa fa-fw fa-key"></i> <?_e("Administrator password");?></label>
								<input type="password" id="password" required class="w60 vatt">

								<a href="javascript:void(0);" onclick="if($('#password').attr('type') == 'password') $('#password').attr('type','text'); else $('#password').attr('type','password');" tabindex="-1"><i class="fa fa-fw fa-eye mts vam"></i></a>

								<!-- <a href="javascript:void(0);" onclick="$('#setup #password').make_password();" title="<?_e("Suggest a password");?>"><i class="fa fa-fw fa-arrows-cw mts vam"></i></a> -->

							</li>

							<!-- 
							<li class="mtl bold"><?_e("Option");?></li>

							<li><label class="w30"><i class="fa fa-fw fa-line-chart"></i> <?_e("Google analytics code");?></label> <input type="text" id="google_analytics" placeholder="UA-00000000-1" class="w20 vatt"></li>
							 -->
						</ul>

						<button class="fr mam bold"><?_e("Start installation");?> <i class="fa fa-fw fa-cog"></i></button>

					</form>

				</div>

			</div>

		</body>
		</html>
		<?

		exit;

	break;



	case "start":// CRÉATION / Mise à jour des données de configuration
		
		// Chemin des fichiers de config
		$config_sample_file = "config.init.php";
		$config_final_file = "../config.php";

		@include_once($config_final_file);

		// Vérification du nonce et si la config n'est pas déjà créée
		if($_SESSION['nonce'] == @$_REQUEST['nonce'] and (!$GLOBALS['db_server'] or !$GLOBALS['db_user'] or !$GLOBALS['db']))
		{			
			// Traduction de la page d'installation
			$add_translation = array(
				"Table already exists" => array("fr" => "La table existe déjà"),
				"User already exists : update password" => array("fr" => "L'utilisateur existe déjà : mise à jour du mot de passe"),
				"Wrong email" => array("fr" => "Mauvais email"),
				"Successful installation ! Redirection to homepage ..." => array("fr" => "Installation réussie ! Redirection vers la page d'accueil ...")
			);

			add_translation($add_translation);

			
			if(@$_POST['db_server'] and @$_POST['db_user'] and @$_POST['db'])
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
					$GLOBALS['table_tag'] = addslashes($_POST['db_prefix']."tag");
					$GLOBALS['table_meta'] = addslashes($_POST['db_prefix']."meta");
					$GLOBALS['table_user'] = addslashes($_POST['db_prefix']."user");
										
					// Vérification de l'existence des base de données
					if($GLOBALS['connect']->query("SELECT id FROM ".$GLOBALS['table_content'])){// Table déjà existante
						?><script>light("<?_e("Table already exists")?> : content");</script><?
					}
					else {// Création de la base de données
						$GLOBALS['connect']->query("
							CREATE TABLE IF NOT EXISTS `".$GLOBALS['table_content']."` (
								`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
								`state` varchar(20) NOT NULL DEFAULT 'deactivate',
								`lang` varchar(8) NOT NULL,
								`robots` varchar(18) DEFAULT NULL,
								`type` varchar(20) NOT NULL DEFAULT 'page',
								`tpl` varchar(80) NOT NULL,
								`url` varchar(60) DEFAULT NULL,
								`title` varchar(70) NOT NULL,
								`description` varchar(160) DEFAULT NULL,
								`content` longtext,
								`user_update` bigint(20) UNSIGNED DEFAULT NULL,
								`date_update` datetime DEFAULT NULL,
								`user_insert` bigint(20) UNSIGNED NOT NULL,
								`date_insert` datetime NOT NULL,
								PRIMARY KEY (`id`),
								UNIQUE KEY `url` (`url`),
								KEY `state` (`state`),
								KEY `type` (`type`),
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
						?><script>light("<?_e("Table already exists")?> : meta");</script><?
					}
					else {// Création de la base de données
						$GLOBALS['connect']->query("
							CREATE TABLE IF NOT EXISTS `".$GLOBALS['table_meta']."` (
								`id` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
								`type` varchar(32) NOT NULL,
								`cle` varchar(255) NOT NULL DEFAULT '',
								`val` text,
								`ordre` smallint(6) NOT NULL DEFAULT '0',
								PRIMARY KEY (`id`,`type`,`cle`),
								KEY `type` (`type`,`cle`),
								KEY `ordre` (`ordre`)
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
					if($GLOBALS['connect']->query("SELECT id FROM ".$GLOBALS['table_tag'])){// Table déjà existante
						?><script>light("<?_e("Table already exists")?> : tag");</script><?
					}
					else {// Création de la base de données
						$GLOBALS['connect']->query("
							CREATE TABLE IF NOT EXISTS `".$GLOBALS['table_tag']."` (
								`id` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
								`zone` varchar(32) NOT NULL,
								`encode` varchar(255) NOT NULL DEFAULT '',
								`name` text NOT NULL,
								`ordre` smallint(6) NOT NULL DEFAULT '0',
								PRIMARY KEY (`id`,`zone`,`encode`),
								KEY `type` (`zone`,`encode`),
								KEY `ordre` (`ordre`)
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
						?><script>light("<?_e("Table already exists")?> : user");</script><?
					}
					else {// Création de la base de données
						$GLOBALS['connect']->query("
							CREATE TABLE IF NOT EXISTS `".$GLOBALS['table_user']."` (
								`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
								`state` varchar(20) NOT NULL DEFAULT 'active',
								`auth` varchar(255) NOT NULL,
								`name` varchar(60) DEFAULT NULL,
								`email` varchar(100) NOT NULL,
								`info` text,
								`password` char(64) DEFAULT NULL,
								`salt` char(16) DEFAULT NULL,
								`token` varchar(255) DEFAULT NULL COMMENT 'token light',
								`oauth` text COMMENT 'Token api externe',
								`date_update` datetime DEFAULT NULL,
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


					// Droit d'edition de base
					$auth = null;
					foreach($GLOBALS['add_content'] as $cle => $val) $auth.=',add-'.$cle.',edit-'.$cle;

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
						if(!$GLOBALS['pwd_hash_loop']) $GLOBALS['pwd_hash_loop'] = $_POST['pwd_hash_loop'] = mt_rand(60536, 65536);

						// Email pour le login automatique
						$_POST['email'] = $email;
						
						// Vérifie que l'utilisateur n'existe pas déjà
						$sel = $GLOBALS['connect']->query("SELECT id FROM ".addslashes($_POST['db_prefix'])."user WHERE email='".$email."' AND state='active' LIMIT 1");
						if($res = $sel->fetch_assoc())// User déjà existant : on update ses données
						{			
							$uid = $res['id'];

							// Création de la requête
							$sql = "UPDATE ".addslashes($_POST['db_prefix'])."user SET ";
							$sql .= "state = 'active', ";
							$sql .= "auth = '".addslashes(implode(",", array_keys($GLOBALS['auth_level'])) . $auth)."', ";// Donne tous les droits
							
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
							
							$sql .= "auth = '".addslashes(implode(",", array_keys($GLOBALS['auth_level'])) . $auth)."', ";// Donne tous les droits

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
							else $uid = $GLOBALS['connect']->insert_id;
						}



						// ECRITURE DE LA CONFIGRATION
												
						// Ouverture du fichier config. Si pas de config on prend le sample
						if(file_exists($config_final_file)) $config_file = file($config_final_file);
						else $config_file = file($config_sample_file);

						// Séparation des données du chemin du site
						$parse_url = parse_url($_POST['scheme_domain_path']);
						$_POST['scheme'] = $parse_url['scheme']."://";
						$_POST['domain'] = $GLOBALS['domain'] = $parse_url['host'];
						$_POST['path'] = $GLOBALS['path'] = $parse_url['path'];

						// Formate le nom du site
						$_POST['sitename'] = htmlspecialchars(stripslashes($_POST['sitename']));

						// Cache du jour de l'install
						$_POST['cache'] = $GLOBALS['cache'] = date("Ymd");

						// On parcourt le fichier config
						foreach($config_file as $line_num => $line) 
						{
							// On récupère la clé de la variable en cours
							preg_match("/GLOBALS\[\'([a-z_]+)\'\]/", $line, $match);

							if(isset($match[1])) $key = $match[1]; else $key = "";
							
							// Changement de la ligne et ajout de la nouvelle variable
							if(isset($key) and isset($_POST[$key])) 
								$config_file[$line_num] = "\$GLOBALS['".$key."'] = \"".utf8_decode($_POST[$key])."\";\r\n";							
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



						// AJOUTE LA PAGE D'ACCUEIL
						// Vérifie qu'il n'y a pas déjà une page home
						$sel = $GLOBALS['connect']->query("SELECT id FROM ".addslashes($_POST['db_prefix'])."content WHERE url='home' LIMIT 1");
						$res = $sel->fetch_assoc();
						if(!$res['id'])// Page non existante : on la crée
						{	
							// Ajout de la page d'accueil
							$sql = "INSERT ".addslashes($_POST['db_prefix'])."content SET ";
							$sql .= "title = '".addslashes(utf8_decode($_POST['sitename']))."', ";
							$sql .= "tpl = 'home', ";
							$sql .= "url = 'home', ";
							$sql .= "lang = '".$GLOBALS['language'][0]."', ";
							$sql .= "type = 'page', ";
							$sql .= "user_insert = '".(int)$uid."', ";
							$sql .= "date_insert = NOW() ";
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
							else
							// Pose un cookie pour demander l'ouverture de l'admin automatiquement au chargement
							setcookie("autoload_edit", "true", time() + 60*60, $_POST['path'], $_POST['domain']);
						}



						// LOGIN AUTOMATIQUE
						login();



						// MESSAGE DE BIENVENUE et d'information qu'il faut créé la page d'accueil du site
						?>
						<script>
							light("<?_e("Successful installation ! Redirection to homepage ...")?>");
							setTimeout(function(){
								 $("#error, #highlight").slideUp("slow").fadeOut(function() {
									window.location.reload();// window.location = window.location.href;
								 });
							}, 3000);
						</script>
						<?

					}
				}
			}
		}

		exit;

	break;

}
?>