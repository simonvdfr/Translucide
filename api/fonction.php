<?
/********** BENCHMARK **********/
// Mesure le temps de traitement Php
function benchmark() {
	return (microtime(true) - $GLOBALS['microtime']);
}


/********** URL **********/
// Nettoie et encode les mots
function encode($value, $separator = "-", $pass = null) 
{
	$tofind = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñß@\’\"'_-&()=/*+$!:;,.\²~#?§µ%£°{[|`^]}¤€<>";
    $replac = "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynnba                                         ";
	
	// Si on doit laisser certains caractères
	if(count($pass)) {
		foreach($pass as $char){
			$strpos = strpos($tofind, $char);
			$tofind[$strpos] = "";
			$replac[$strpos] = "";
		}
	}

	$value = strtolower(strtr(utf8_decode($value), $tofind, $replac));// Supp les caractères indésirables
	$value = preg_replace("/ /", $separator, preg_replace("/ {2,}/", $separator, trim($value)));// Supp les espaces et remplace par des tirés {1,}

	return $value;
}

// Récupère l'url rewriter
function get_url($url_source = null)
{
	// Si pas d'url forcé on donne l'url en cours complète
	if(!$url_source) $url_source = ($_SERVER['REQUEST_SCHEME']?$_SERVER['REQUEST_SCHEME']:"http")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

	// Parse l'url pour ne garder que la partie rewrite sans le chemin de base du site
	$parse_url = parse_url($url_source);
	$path = ltrim($parse_url['path'], $GLOBALS['path']);

	// Si l'url est vide : url = home
	if(!encode($path)) $url = "home"; 
	else
	{
		// Si il y a des filtres dans l'url
		if(strstr($parse_url['path'], "/")) {
			$explode_path = explode("/", $path);

			$url = $explode_path[0];// Url raçine
			unset($explode_path[0]);// Supp la racine des filtres

			while(list($cle, $dir) = each($explode_path)) {
				$explode_dir = explode("_", $dir);	
				$GLOBALS["filtre"][encode($explode_dir[0])] = encode(preg_replace("/^".$explode_dir[0]."_/", "", $dir), "-", array(".","_"));
			}
		}
		else $url = encode($path);
	}

	return $url;
}

// Retourne l'url rewriter
function make_url($url, $filtre = array())
{
	if(is_array($filtre))
	{
		// Force le domaine
		if($filtre['domaine']) $domaine = $filtre['domaine'];
		unset($filtre['domaine']);

		// Création des dossier dans l'url en fonction des filtres
		while(list($cle, $val) = each($filtre)) {
			if($cle == "page" and $val == 1) unset($filtre['page']);// Si Page == 1 on ne l'affiche pas dans l'url
			elseif($val) $dir .= "/".encode($cle)."_".encode($val, "-", array(".","_"));
		}
	}

	if($url == "home") 
	{
		$url = $GLOBALS['path'];

		if($domaine) $url = $GLOBALS['home'];
	}
	else {
		$url = encode($url);

		if($domaine) $url = $GLOBALS['home'] . $url;

		if($dir) $url = trim($url, "/") . $dir;
	}	

	return $url;
}


/********** LANGUAGE **********/
// Sélectionne la langue
function get_lang($lang = '')
{		
	// Si la langue est déjà dans la session
	if($_SESSION['lang']) {
		$lang = $_SESSION['lang'];		
	}
	elseif(!$lang) // Si pas de langue on prend la 1er langue du navigateur
	{
		preg_match_all('~([\w-]+)(?:[^,\d]+([\d.]+))?~', strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']), $matches, PREG_SET_ORDER);
		$explode = explode("-", $matches[0][1]);
		$lang = $explode[0];
	}
	
	// Si la langue de l'utilisateur n'existe pas pour les contenus de ce site on charge la langue par défaut
	if(!in_array($lang, $GLOBALS['language'])) $lang = $GLOBALS['language'][0];

	// Création du cookie avec la langue. Utile pour le js
	setcookie("lang", $lang, time() + $GLOBALS['session_expiration'], $GLOBALS['path'], $GLOBALS['domain']);

	$GLOBALS['lang'] = $_SESSION['lang'] = $_COOKIE['lang'] = $lang;	

	return $lang;
}

// Charge une traduction
function load_translation($id)
{	
	switch ($id) {
		case "api": $translation_file = "api/translation.php"; break;
		case "theme": $translation_file = "theme/".$GLOBALS['theme']."translation.php"; break;
		default: $translation_file = "plugin/".$id."/translation.php"; break;
	}

	// On récupère le fichier de traduction
	@include($_SERVER['DOCUMENT_ROOT'].$GLOBALS['path'].$translation_file);

	// Ajoute la traduction au tableau des traductions
	if($add_translation) add_translation($add_translation);
}

// Ajoute la traduction
function add_translation($add_translation)
{	
	// On met toutes les clés en minuscule pour une recherche insensible à la case
	$add_translation = array_change_key_case($add_translation, CASE_LOWER);
	
	// On ajoute la nouvelle traduction au tableau de toutes les traductions
	$GLOBALS['translation'] = array_merge($GLOBALS['translation'], $add_translation);
}

// Retourne une traduction
function __($txt)
{
	// Si une traduction existe
	if($GLOBALS['translation'][mb_strtolower($txt)][$GLOBALS['lang']]) 
		$txt = $GLOBALS['translation'][mb_strtolower($txt)][$GLOBALS['lang']];

	return utf8_encode($txt);
}

// Affichage d'une traduction
function _e($txt)
{
	echo __($txt);
}


/********** CONTENT **********/
// Contenu texte
function txt($key = null, $placeholder = null, $tag = "div", $readonly = false)
{
	$key = ($key ? $key : "txt-".$GLOBALS['editkey']);

	echo"<".$tag." class='editable".($readonly?" readonly":"")."' id='".encode($key)."'".($placeholder?" placeholder=\"".utf8_encode($placeholder)."\"":"").">".(isset($GLOBALS['content'][$key]) ? $GLOBALS['content'][$key] : "")."</".$tag.">";

	$GLOBALS['editkey']++;
}

// Contenu image
function img($key = null, $size = null, $zoom = null)
{
	$key = ($key ? $key : "img-".$GLOBALS['editkey']);

	if($size) $size = explode("x", $size);

	echo"<span class='editable-img'><img src=\"".(isset($GLOBALS['content'][$key]) ? $GLOBALS['home'].$GLOBALS['content'][$key] : "")."\"".(isset($size[0])?" width='".$size[0]."'":"")."".(isset($size[1])?" height='".$size[1]."'":"")." atl=\"\" class='".((isset($size[0]) and isset($size[1]))?"crop":"")."".($zoom?" zoom":"")."' id='".encode($key)."'></span>";

	$GLOBALS['editkey']++;
}

// Image de fond de bloc
function bg($key = null)
{
	$key = ($key ? $key : "bg-".$GLOBALS['editkey']);

	echo" id='".encode($key)."' data-editable='bg' data-bg=\"".(isset($GLOBALS['content'][$key]) ? $GLOBALS['home'].$GLOBALS['content'][$key] : "")."\"";

	$GLOBALS['editkey']++;
}

// Contenu champ checkbox
function checkbox($key = null)
{
	$key = ($key ? $key : "checkbox-".$GLOBALS['editkey']);

	echo"<i class='editable-checkbox fa fa-fw ".($GLOBALS['content'][$key] == true ? "fa-check yes" : "fa-times no")."' id='".encode($key)."'></i>";
	
	$GLOBALS['editkey']++;
}

// Contenu champ select
function select($key = null, $option = null, $placeholder = null)
{
	$key = ($key ? $key : "select-".$GLOBALS['editkey']);

	$option_decode = json_decode($option, true);

	if($option_decode[$GLOBALS['content'][$key]]) {
		$selected_key = $GLOBALS['content'][$key];
		$selected_option = $option_decode[$GLOBALS['content'][$key]];
	}
	else  {
		$selected_key = key($option_decode);
		$selected_option = $option_decode[$selected_key];
	}

	echo"<span id='".encode($key)."' class='editable-select' data-option='".$option."' data-selected=\"".$selected_key."\">".$selected_option."</span>";
	
	$GLOBALS['editkey']++;
}

// Contenu champ hidden
function hidden($key = null, $class = null)
{
	$key = ($key ? $key : "hidden-".$GLOBALS['editkey']);

	echo"<input type='hidden' id='".encode($key)."' value=\"".(isset($GLOBALS['content'][$key]) ? $GLOBALS['content'][$key] : "")."\" class='editable-hidden ".$class."'>";
	
	$GLOBALS['editkey']++;
}

// Label visible qu'en mode édition
function hidden_label($content = null, $class = null)
{
	echo"<label class='none ".$class."'>".$content."</label>";
}


// Lien éditable // @todo: A SUPP ??
function href($key = null)
{
	$key = ($key ? $key : "href-".$GLOBALS['editkey']);
	echo" href=\"".(isset($GLOBALS['content'][$key]) ? $GLOBALS['content'][$key] : "")."\" id='".encode($key)."' data-editable='href'";
	$GLOBALS['editkey']++;
}

// Icône fonts // @todo: A SUPP ??
function ico($key = null)
{
	$key = ($key ? $key : "ico-".$GLOBALS['editkey']);
	echo"<i class='editable fontawesome-icon circle-yes icon-eur'></i>";
	$GLOBALS['editkey']++;
}

// Slideshow // @todo: A SUPP ??
function slideshow($key = null)
{
	$key = ($key ? $key : "slide-".$GLOBALS['editkey']);
	echo"<span class='editable' id='".encode($key)."'>".(isset($GLOBALS['content'][$key]) ? $GLOBALS['content'][$key] : "")."</span>";
	$GLOBALS['editkey']++;
}


/********** SÉCURISATION **********/
function secure_value($value) {

	// htmlentities htmlspecialchars
	if(is_array($value)) {
		while(list($cle, $val) = each($value)) $value[$cle] = trim(htmlspecialchars($val, ENT_QUOTES));
	}
	else $value = trim(htmlspecialchars($value, ENT_QUOTES));

	return $value;
}


/********** Connexion **********/
function curl($url, $params = null) 
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	if($params) curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params, null, '&'));
	$return = curl_exec($curl);
	$getinfo = curl_getinfo($curl);
	curl_close($curl);

	//@todo: si retour erreur : faire un message d'erreur
	//highlight_string(print_r($getinfo, true));

	return $return;
}

// Crypte le mot de passe
function hash_pwd($pwd, $salt = null) 
{				
	// Création du salt unique a cet utilisateur char(16)
	if(!$salt) $unique_salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));// @todo: peut-etre remplacer cette fonction par make_pwd
	else $unique_salt = $salt;
		
	// Boucle pour encoder x fois le pwd avec le salt unique
	for($i = 0; $i < $GLOBALS['pwd_hash_loop']; $i++) {
		$pwd = hash('sha256', $pwd . $unique_salt . $GLOBALS['priv_hash']);
	} 

	if($salt) 
		return $pwd;// Retour pour comparaison
	else 
		return array($pwd, $unique_salt);// Retour pour stockage
}

// Crée un password
function make_pwd($length = 12, $special_chars = false, $extra_special_chars = false)
{
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

	if($special_chars) $chars .= "!@#%^&*()";//$ <= Créé parfois un bug : crée une variable php

	if($extra_special_chars) $chars .= "-_ []{}<>~`+=,.;:/?|";

	$password = "";

	for($i = 0; $i < $length; $i++) { $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1); }

	return $password;
}

// Un hash/nonce pour faire des signatures. Evite les cross-site request forgery CSRF
function nonce($session = null)
{
	$nonce = hash("sha256", uniqid(mt_rand(), true));

	if($session) $_SESSION[$session] = $nonce;

	return $nonce;
}

function ip()// Retourne l'adresse IP du client (utilisé pour empêcher le détournement de cookie de session)
{
    $ip = $_SERVER['REMOTE_ADDR'];

    // Ensuite, nous utilisons plusieurs en-têtes HTTP pour empêcher le détournement de session des utilisateurs derrière le même proxy
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $ip.'_'.$_SERVER['HTTP_X_FORWARDED_FOR'];
    if(isset($_SERVER['HTTP_CLIENT_IP'])) $ip = $ip.'_'.$_SERVER['HTTP_CLIENT_IP'];

    return $ip;
}

// Création d'un token
function token($uid, $email = null, $auth = null) // @todo: Vérif l'intérêt de mettre le mail et pas le name ou rien
{
	// Si la fonction de memorisation de connexion de l'utilisateur et coché
	if($_POST['rememberme']) {
		setcookie("rememberme", encode($_POST['rememberme']), 0, $GLOBALS['path'], $GLOBALS['domain']);
		$_COOKIE['rememberme'] = encode($_POST['rememberme']);
	}

	// Date d'expiration (si on ne mémorise pas l'utilisateur on crée une session de 30min
	$time = time() + ($_COOKIE['rememberme'] == "false" ? (30*60) : $GLOBALS['session_expiration']);

	// Id de l'utilisateur
	$_SESSION['uid'] = (int)$uid;
	
	// Nom de l'utilisateur
	if($email) $_SESSION['email'] = $email;

	// Cookie+Session pour connaitre les autorisations utilisateur
	if($auth) {
		$array_auth = explode(",", $auth);
		while(list($cle, $val) = each($array_auth)) { $_SESSION['auth'][$val] = true; }
		setcookie("auth", encode($auth, ",", array("_")), $time, $GLOBALS['path'], $GLOBALS['domain']);
	}
	
	// Date d'expiration du login
	$_SESSION['expires'] = $time;
	
	// Faire en sorte que le token soit plus complet et autonome sans trop de variable dans la session
	$_SESSION['token'] = $token = hash("sha256", $_SESSION['uid'] . $_SESSION['expires'] . ip() . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['SERVER_NAME'] . $GLOBALS['pub_hash']);
	
	// Niveau de sécurité élever, on enregistre le token dans la bdd
	if($GLOBALS['security'] == 'high') 
	{
		if(!$GLOBALS['connect']) include_once("db.php");// Connexion à la db

		$GLOBALS['connect']->query("UPDATE ".$GLOBALS['table_user']." SET token='".$token."' WHERE id='".(int)$uid."'");
	}

	return $token;
}

// Création d'un token light (utile lors des changements de mot de passe et donne la possibiliter le log sur plusieurs machines)
function token_light($uid, $salt)
{	
	$_SESSION['token_light'] = $token_light = hash("sha256", $salt . $uid . $GLOBALS['pub_hash']);

	return $token_light;
}

// Vérifie si le token est bon
function token_check($token)
{	
	if($token == hash("sha256", $_SESSION['uid'] . $_SESSION['expires'] . ip() . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['SERVER_NAME'] . $GLOBALS['pub_hash']) and time() < $_SESSION['expires'])
	{
		// On update la date d'expiration de la session
		token($_SESSION['uid'], $_SESSION['email']);

		return true;
	}
	else 
		return false;
}

// Connexion au site avec le système interne de login+password
function login($level = 'low', $auth = null, $quiet = null)
{	
	//////// Le level détermine le niveau de vérification pour des taches plus ou moins sensible
	// low : Vérif juste s'il y a un token dans la session
	// medium : Check le contenu du token
	// high : Check le token, et s'il est identique dans bdd (config : security=high ou token_light)

	// Vérifie que la personne qui a posté le formulaire a bien la variable de session de protection contre les CSRF
	$csrf = false;
	if(isset($_SESSION['nonce']) and $_SESSION['nonce'] != $_REQUEST['nonce']) $csrf = true;
	
	// Pas de hack on vérifie l'utilisateur
	if(!$csrf) 
	{
		// On se log avec le formulaire donc on check password & mail
		if(isset($_POST['email']) and isset($_POST['password']))
		{
			if(!$GLOBALS['connect']) include_once("db.php");// Connexion à la db

			// Supprime l'ancienne session
			session_regenerate_id(true);

			// Nettoyage du mail envoyé
			$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
			$email = $GLOBALS['connect']->real_escape_string($email);

			// Extraction des données de l'utilisateur
			$sel = $GLOBALS['connect']->query("SELECT * FROM ".$GLOBALS['table_user']." WHERE email='".$email."' ".($level == 'low' ? "" : "AND state='active'")." LIMIT 1");
			$res = $sel->fetch_assoc();

			if($res['email']) 
			{						
				// Création d'un token maison
				if($res['password'] == hash_pwd($_POST['password'], $res['salt']))
				{

					if(isset($auth) and !empty(array_diff(explode(",", $auth), explode(",", $res['auth']))))// Vérifie les auth d'utilisateur si c'est demandée 
					{
						$msg = __("Bad credential");
						logout();
					}
					else if($token = token($res['id'], $res['email'], $res['auth']))// Tout est ok on crée le token
					{
						// Création d'un token light : permet une vérif au changement de mdp et permet log sur plusieurs machines
						if($GLOBALS['security'] != 'high') {							
							$token_light = token_light($res['id'], $res['salt']);
							$GLOBALS['connect']->query("UPDATE LOW_PRIORITY ".$GLOBALS['table_user']." SET token='".$token_light."' WHERE id='".$res['id']."'");
						}
						
						// On est logé !
						return true;
					}
				}
				else $msg = __("Connection error")." 2";
			}
			else $msg = __("Unknown user");
		}
		// Sinon on vérifie la validité du token et s'il n'a pas expiré
		elseif(isset($_SESSION['token']))
		{			
			if($level == 'medium' and $GLOBALS['security'] != 'high')// Vérification mode moyen
			{
				if(!token_check($_SESSION['token']))// Vérification du contenu du token
				{
					$msg = __("Connection error")." 3";
					logout();
				}
				else if(isset($auth))// Vérifie les autorisations utilisateur dans la bdd si c'est demandée
				{
					if(!$GLOBALS['connect']) include_once("db.php");// Connexion à la db

					// Extraction des données de l'utilisateur
					$sel = $GLOBALS['connect']->query("SELECT auth FROM ".$GLOBALS['table_user']." WHERE id='".(int)$_SESSION['uid']."' AND state='active' LIMIT 1");
					$res = $sel->fetch_assoc();

					if(!empty(array_diff(explode(",", $auth), explode(",", $res['auth']))))
					{
						$msg = __("Bad credential");
						logout();
					}
					else return true;
				}
				else return true;
			}
			else if(($GLOBALS['security'] == 'high' or $level == 'high') and token_check($_SESSION['token']))// Comparaison avec le token dans la bdd
			{
				if(!$GLOBALS['connect']) include_once("db.php");// Connexion à la db

				session_regenerate_id(true);// Supprime l'ancienne session

				$sel = $GLOBALS['connect']->query("SELECT auth, token FROM ".$GLOBALS['table_user']." WHERE id='".(int)$_SESSION['uid']."' AND state='active' LIMIT 1");
				$res = $sel->fetch_assoc();
				
				if(isset($auth) and !empty(array_diff(explode(",", $auth), explode(",", $res['auth']))))// Vérifie les autorisations
				{
					$msg = __("Bad credential");
					logout();
				}
				elseif($GLOBALS['security'] == 'high' and $res['token'] == $_SESSION['token']) return true;// Sécurité haute forcée dans la config
				elseif($level == 'high' and $res['token'] == $_SESSION['token_light']) return true;// Verification du token light (changement de pwd...)
				else 
				{
					$msg = __("Connection error")." 4";
					logout();
				}
			}
			else return true;
		}
		else {
			//$msg = __("No token");
			logout();
		}
	}
	else {
		$msg = __("Nonce error");
		logout();
	}


	// Si pas de token ou si le login échoue on lance la dialog de connexion et exit l'action courante
	if(!isset($_SESSION['token']) and !$quiet)
	{
		?>
		<script>
			// Ouverture de la dialog de connexion
			$(document).ready(function()
			{
				//$(".ui-dialog-content").dialog("close");// On ferme les dialogs en cours

				if(typeof tosave == 'function') tosave();// Mode : A sauvegarder
				
				// On ferme la dialog de connexion s'il y en a une d'ouvert
				$("#dialog-connect").dialog("close");
				
				// On ouvre la dialog de choix du système de login et affiche une erreur
				$.ajax(
					{
						url: "<?=$GLOBALS['path']?>api/ajax.php?mode=select-login-mode", 
						data: {
							callback: "<?=encode($_REQUEST['callback'], "_")?>",
							msg: "<?=htmlspecialchars($msg);?>"
						}
					})
					.done(function(html){
						$("body").append(html);	
						
						// Effet sur la dialog
						$("#dialog-connect").dialog({
							modal: true,
							minHeight: 0,
							show: {effect: "fadeIn"},
							//hide: {effect: "fadeOut"},// Bug collateral : empèche la re-ouverture rapide de la dialog de connexion
							close: function() {
								$("#dialog-connect").remove();
							}
						});
					});
			});
		</script>
		<?

		exit;
	}
}

function logout($redirect = null)
{
	// Supprime les variables de session de connexion
    unset($_SESSION['token'], $_SESSION['uid'], $_SESSION['expires'], $_SESSION['nonce'], $_SESSION['auth'], $_COOKIE['auth'], $_SESSION['state']);// session_destroy();
	
	// Supprime le cookie d'autorisation user
	@setcookie("auth", "", time() - 3600, $GLOBALS['path'], $GLOBALS['domain']);
	
	// Supprime le cookie de memorisation de l'utilisateur
	@setcookie("rememberme", "", time() - 3600, $GLOBALS['path'], $GLOBALS['domain']);

	// Si redirection
	if($redirect == "login") {
		header("Location: ajax.php");
		exit;
	}
	elseif($redirect == "home") {
		header("Location: ".$GLOBALS['home']);
		exit;
	}
}



/********** IMAGE **********/

function resize($source_file, $new_width = null, $new_height = null, $dest_dir = null, $option = null)
{	
	// Supprime les arguments après l'extension (timer...)
	$source_file = explode("?", $source_file)[0];

	// Récupération des informations de l'image source
	list($source_width, $source_height, $type, $attr) = getimagesize($source_file);

	if(!$source_width and !$source_height) exit(__("Size of source file unspecified"));

	// Récupération de l'extension
	$source_ext = pathinfo($source_file, PATHINFO_EXTENSION);

	// file_name : on récup le nom du fichier, on lui supp l'extension (qui ne passe pas l'encode), on l'encode
	$root_dir = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['path'];	
	$file_name = encode(basename(basename($source_file), ".".$source_ext));
	
	// Si image à réduire ou à forcer
	if(($new_width and $source_width > $new_width) or ($new_height and $source_height > $new_height) or $option)
	{		
		// Dossier final d'image redimensionnée
		$dir = ($dest_dir ? $dest_dir : "media/");

		// Crée les dossiers
		@mkdir($root_dir.$dir, 0705, true);

		// On crée une image avec l'image source en fonction de son type
		switch($type) {
			case 1: $source_img = imagecreatefromgif($source_file); break;
			case 2: $source_img = imagecreatefromjpeg($source_file); break;
			case 3: $source_img = imagecreatefrompng($source_file); break;
			default: exit(__("Unsupported file type")); break;
		}  
			
		// Callage de l'image
		$x = $y = 0;

		if($new_width and $new_height)// On redimensionne dans tous les sens
		{
			$ratio_width = $source_width / $new_width;
			$ratio_height = $source_height / $new_height;

			if($ratio_width > 1 or $ratio_height > 1)// Taille maximale dépassée dans un sens ?
			{
				if($option == "crop")
				{
					if($ratio_width < $ratio_height) {
						$dest_width = $new_width;
						$dest_height = $source_height / $ratio_width;
					}
					else {
						$dest_width = $source_width / $ratio_height;				
						$dest_height = $new_height;				
					}
					
					// Positionnement de l'image cropé
					$x = ($new_width - $dest_width) / 2;
					$y = ($new_height - $dest_height) / 5;// Paramètre pour callé en hauteur le crop (2 à l'origine)
				}
				else// Si pas crop on resize la taille la plus grande
				{
					if($ratio_width < $ratio_height) {
						$dest_width = $new_width = $source_width / $ratio_height;				
						$dest_height = $new_height;				
					}
					else {
						$dest_width = $new_width;	
						$dest_height = $new_height = $source_height / $ratio_width;		
					}
				}
			}
			else// Image carrée
			{
				$dest_width = $new_width;
				$dest_height = $new_height;
			}

		}
		elseif($new_width and !$new_height)// On force la largeur => on calcule la nouvelle hauteur
		{ 
			$new_width = $dest_width = $new_width;
			$new_height = $dest_height = $new_width * $source_height / $source_width;
		}
		elseif(!$new_width and $new_height)// On force la hauteur => on calcule la nouvelle largeur
		{
			$new_width = $dest_width = $new_height * $source_width / $source_height;
			$new_height = $dest_height = $new_height;
		}
		
		// Création de l'image vide de base pour y coller l'image finale
		$final_img = imagecreatetruecolor($new_width, $new_height);
		
		// S'il y a une transparence on la conserve
		switch($type) {
			case 1: // Gif
				imagecolortransparent($final_img, imagecolorallocatealpha($final_img, 0, 0, 0, 127));
			case 3: // Png + Gif
				imagealphablending($final_img, false);
				imagesavealpha($final_img, true);
			break;
		}  
		
		// On copie et resize l'image dans l'image de base finale
		imagecopyresampled($final_img, $source_img, $x, $y, 0, 0, $dest_width, $dest_height, $source_width, $source_height);
		
		// Libère la mémoire
		imagedestroy($source_img);

		// Si l'image n'a pas la bonne orientation (consomme pas mal de mémoire)
		switch ($option) {
		  case 3: $deg = 180; break;
          case 6: $deg = 270; break;
          case 8: $deg = 90; break;
		}
		if($deg) $final_img = imagerotate($final_img, $deg, 0);
		
		// Ajoute la taille de la nouvelle image en supprimant l'ancienne si besoin
		preg_match("/(-[0-9]+x[0-9]+)$/", $file_name, $matches);
		$file_name = str_replace($matches[0], "", $file_name);
		$file_name_ext = $file_name."-".round($new_width)."x".round($new_height).".".$source_ext;

		// Création de l'image finale dans le bon type		
		switch($type) {
			case 1: imagegif($final_img, $root_dir.$dir.$file_name_ext); break;
			case 2: imagejpeg($final_img, $root_dir.$dir.$file_name_ext, $GLOBALS['jpg_quality']); break;
			case 3: imagepng($final_img, $root_dir.$dir.$file_name_ext); break;// $GLOBALS['png_quality']
		}		
		
		imagedestroy($final_img);// Libère la mémoire	
	}
	else// Copie l'image si elle est plus petite ou à la bonne taille
	{
		$dir = "media/";
		$file_name_ext = $file_name.".".$source_ext;
		
		@mkdir($root_dir.$dir, 0705, true);// Crée les dossiers

		copy($source_file, $root_dir.$dir.$file_name_ext);
	}

	return $dir.$file_name_ext."?".time();// Time pour forcer le refresh
}


function img_process($root_file, $dest = "media/", $des_resize = "media/resize/", $new_width = null, $new_height = null, $resize = null)
{
	// Valeur par défaut
	$option = null;
	$src_file = $dest.basename($root_file)."?".time();

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
			$option = $exif['Orientation'];
		}
	}

	// Image trop grande (> global) pour le web : on la redimensionne
	if($source_width > $max_width or $source_height > $max_height or $option) 
	{
		$src_file = resize($root_file, $max_width, $max_height, $dest, $option);// Redimensionne sans crop

		unlink($root_file);// Supprime l'image originale puisque l'on ne garde que la maxsize

		$root_file = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['path'].explode("?", $src_file)[0];// La maxsize devient l'image root (explode: supp le timer)
	}
	

	// L'interface a demandé un redimensionnement ?
	if($resize and (($new_width and $source_width > $new_width) or ($new_height and $source_height > $new_height)))
	{
		return resize($root_file, $new_width, $new_height, $des_resize, $resize);// Redimensionne

		//unlink($root_file);// Si on a redimensionné on supp l'image de base
	}
	else
		return $src_file;// Retourne l'url du fichier original si pas de redimensionnement	
}
?>