<?
// benchmark
$GLOBALS['microtime'] = microtime(true);

// Force Config PHP
//ini_set('error_reporting','6135');
//error_reporting(E_ALL);
ini_set('magic_quotes_gpc', 'off');
ini_set('display_errors', 'On');
ini_set('allow_url_include', 'off');
ini_set('default_charset', 'UTF-8');

if(!isset($_SESSION))
{
	// Pour aider safari qui ne gère pas les cookies en iframe/cross domain
	ini_set('session.use_cookies', 1);       // Use cookies to store session.
	ini_set('session.use_only_cookies', 1);  // Force cookies for session (phpsessionID forbidden in URL)
	ini_set('session.use_trans_sid', false); // Prevent php to use session ID in URL if cookies are disabled.

	if(!isset($cron)) session_start();
}

// Définition de la zone horaire
date_default_timezone_set('Europe/Paris');

// Langue des dates
setlocale(LC_ALL, 'fr_FR', 'fra');//.UTF8


// Variables de la base de données
$GLOBALS['db_prefix'] = "";
$GLOBALS['db_charset'] = "utf8";

$GLOBALS['table_content'] = $GLOBALS['tc'] = $GLOBALS['db_prefix']."content";
$GLOBALS['table_meta'] = $GLOBALS['tm'] = $GLOBALS['db_prefix']."meta";
$GLOBALS['table_user'] = $GLOBALS['tu'] = $GLOBALS['db_prefix']."user";
$GLOBALS['table_shop'] = $GLOBALS['ts'] = $GLOBALS['db_prefix']."shop";

if(isset($_SERVER['WINDIR'])) {// Dev local
	$GLOBALS['db_server'] = "";
	$GLOBALS['db_user'] = "";
	$GLOBALS['db'] = "";
	$GLOBALS['db_pwd'] = "";
}
else {
	$GLOBALS['db_server'] = "";
	$GLOBALS['db_user'] = "";
	$GLOBALS['db'] = "";
	$GLOBALS['db_pwd'] = "";
}


// Variables sites
$GLOBALS['language'] = array("fr");

$GLOBALS['function'] = "";// Include fonction du theme

$GLOBALS['theme'] = "";

$GLOBALS['sitename'] = "";

if(isset($_SERVER['WINDIR']))// Dev local
	$GLOBALS['scheme'] = "";
else 
	$GLOBALS['scheme'] = "";

if(isset($_SERVER['WINDIR']))// Dev local
	$GLOBALS['domain'] = "";
else 
	$GLOBALS['domain'] = "";

$GLOBALS['path'] = "";

$GLOBALS['robots'] = "noindex, nofollow";

$GLOBALS['email_contact'] = "";


// Utilisation de librairie minifier
if(isset($_SERVER['WINDIR']))// Dev local
	$GLOBALS['min'] = "";
else 
	$GLOBALS['min'] = "";//.min


// https://developers.facebook.com/apps/
$GLOBALS['facebook_api_id'] = "";
$GLOBALS['facebook_api_secret'] = "";
$GLOBALS['facebook_page'] = "";// https://www.facebook.com/***
$GLOBALS['facebook_jssdk'] = false;

// https://console.developers.google.com/apis/credentials/oauthclient => Application Web
$GLOBALS['google_api_id'] = "";
$GLOBALS['google_api_secret'] = "";
$GLOBALS['google_map'] = "";
$GLOBALS['google_analytics'] = "";
$GLOBALS['google_page'] = "";// https://plus.google.com/***

// https://developer.yahoo.com/apps/
$GLOBALS['yahoo_api_id'] = "";
$GLOBALS['yahoo_api_secret'] = "";

// https://account.live.com/developers/applications/create
$GLOBALS['microsoft_api_id'] = "";
$GLOBALS['microsoft_api_secret'] = "";


// Clé hash pour les cryptages
$GLOBALS['pub_hash'] = "";
$GLOBALS['priv_hash'] = "";

// Niveau de sécurité du système de login // medium : token en session | high : ajout du token dans la base (multilog impossible)
$GLOBALS['security'] = "medium";

// Nom de boucle de hashage du mdp
$GLOBALS['pwd_hash_loop'] = "";

// Temps d'expiration des sessions de connexion
$GLOBALS['session_expiration'] = 60*60*24*30;


// Compte public autorisé
$GLOBALS['public_account'] = false;

// Statue d'activation par défaut des comptes utilisateur
$GLOBALS['default_state'] = "active";// moderate / mail / active / deactivate

// Niveaux d'authentification par défaut des comptes utilisateur
$GLOBALS['default_auth'] = "add-media-public,edit-public";

// Info supplémentaire sur l'utilisateur
$GLOBALS['meta_user'] = null;

// Niveaux d'authentification possible
$GLOBALS['auth_level'] = array(
	"edit-admin",
	"edit-user",
	"edit-config",
	"edit-nav",
	"edit-header",
	"edit-footer",
	"add-media",
	"add-page",
	"add-article",
	"add-event",
	"add-product",
	"edit-media",
	"edit-page",
	"edit-article",
	"edit-event",
	"edit-product",
	"add-media-public",
	"edit-public"
);


// Type de contenu ajoutable
$GLOBALS['add-content'] = array(
	//"product" => ["fa" => "fa-shopping-cart", "tpl" => "product"],
	//"article" => ["fa" => "fa-feed", "tpl" => "article"],
	//"event" => ["fa" => "fa-calendar-o", "tpl" => "event"],
	//"video" => ["fa" => "fa-video-camera", "tpl" => "video"],
	//"media" => ["fa" => "fa-file-pdf-o", "tpl" => "fichier"],
	"page" => ["fa" => "fa-file-text-o", "tpl" => "page"]
);


// Toolbox
$GLOBALS['toolbox'] = array(
	//"h2",
	//"h3",
	"bold",
	"italic",
	//"underline",
	//"superscript",
	//"insertUnorderedList",
	//"justifyLeft",
	//"justifyCenter",
	//"justifyRight",
	//"justifyFull",
	//"InsertHorizontalRule",
	//"viewsource",
	"icon",
	"media",
	//"anchor",
	"link"
);


// Type mime supporté pour l'upload
$GLOBALS['mime_supported'] = array(
	"image/jpg",
	"image/jpeg",
	"image/pjpeg",
	"image/png",
	"image/x-png",
	"image/gif",
	"image/x-icon",
	"application/pdf",
	"application/zip",
	"application/x-zip-compressed",
	"text/plain"
);


// Variables tailles images
$GLOBALS['max_image_size'] = "1920x1080";
$GLOBALS['jpg_quality'] = 90;
$GLOBALS['png_quality'] = 9;


// Animation pour l'ouverture / fermeture de la dialogue des medias
$GLOBALS['animation_dialog'] = true;


// Icone pour mobile
$GLOBALS['touch_icon'] = "";


// Cache sur les styles
$GLOBALS['cache'] = "";


// Librairie d'icons spécifiques à la template
$GLOBALS['icons'] = "";// $GLOBALS['scheme'].$GLOBALS['domain'].$GLOBALS['path']."api/icons/icons.min.css"


// Librairie externe
$GLOBALS['font_awesome'] = "https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css";

$GLOBALS['jquery'] = "//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js";

$GLOBALS['jquery_ui'] = "//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js";

$GLOBALS['jquery_ui_css'] = "//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.min.css";// cupertino flick smoothness base 


// Filtre url autorisé
$GLOBALS['filter_auth'] = array("page", "user");


// Sécurité / défaut
$id = null;
$title = null;
$description = null;
$image = null;
$mode = null;
$uid = null;
$error = null;
$GLOBALS['filter'] = array();
$GLOBALS['translation'] = array();
$GLOBALS['content'] = array();
$GLOBALS['editkey'] = 1;
$GLOBALS['home'] = $GLOBALS['scheme'].$GLOBALS['domain'].$GLOBALS['path'];


// Numéro de la page en cours
if(isset($_REQUEST['page'])) $page = (int)$_REQUEST['page'];
else $page = 1;

// Nombre d'entré par page
$num_pp = 20;
?>