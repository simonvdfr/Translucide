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


// Variables de la base de données
$GLOBALS['db_server'] = "";
$GLOBALS['db_user'] = "";
$GLOBALS['db'] = "";
$GLOBALS['db_pwd'] = "";
$GLOBALS['db_prefix'] = "";
$GLOBALS['db_charset'] = "utf8";

$GLOBALS['table_content'] = $GLOBALS['db_prefix']."content";
$GLOBALS['table_meta'] = $GLOBALS['db_prefix']."meta";
$GLOBALS['table_user'] = $GLOBALS['db_prefix']."user";


// Variables sites
$GLOBALS['language'] = array("fr");

$GLOBALS['theme'] = "";

$GLOBALS['sitename'] = "";

$GLOBALS['scheme'] = "";
$GLOBALS['domain'] = "";
$GLOBALS['path'] = "";

$GLOBALS['robots'] = "index, follow";

$GLOBALS['email_contact'] = "";


// https://developers.facebook.com/apps/
$GLOBALS['facebook_api_id'] = "";
$GLOBALS['facebook_api_secret'] = "";
$GLOBALS['facebook_page'] = "";// https://www.facebook.com/***

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


// Compte public autorisé
$GLOBALS['public_account'] = false;

// Statue d'activation par défaut des comptes utilisateur
$GLOBALS['default_state'] = "active";// moderate / mail / active / deactivate

// Niveaux d'authentification par défaut des comptes utilisateur
$GLOBALS['default_auth'] = "edit-public,upload-public";

// Info supplémentaire sur l'utilisateur
$GLOBALS['meta_user'] = null;

// Niveaux d'authentification possible
$GLOBALS['auth_level'] = array("edit-admin", "edit-user", "edit-config", "edit-nav", "edit-header", "edit-footer", "upload-file", "add-page", "add-article", "add-product", "edit-page", "edit-article", "edit-product",  "edit-public", "upload-public");

// Niveau de sécurité du système de login // medium : token en session | high : ajout du token dans la base (multilog impossible)
$GLOBALS['security'] = "medium";

// Nom de boucle de hashage du mdp
$GLOBALS['pwd_hash_loop'] = "";

// Temps d'expiration des sessions de connexion
$GLOBALS['session_expiration'] = 60*60*24*30;


// Type mime supporté pour l'upload
$GLOBALS['mime_supported'] = array(
	"image/jpg", "image/jpeg", "image/pjpeg", "image/png", "image/x-png", "image/gif", "image/x-icon",
	"application/pdf", "application/zip", "text/plain"
);


// Variables tailles images
$GLOBALS['max_image_size'] = "1920x1080";
$GLOBALS['jpg_quality'] = 90;
$GLOBALS['png_quality'] = 9;


// Librairie externe
$GLOBALS['font_awesome'] = "https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css";
$GLOBALS['jquery'] = "//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js";
$GLOBALS['jquery_ui'] = "//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js";
$GLOBALS['jquery_ui_css'] = "//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.min.css";// cupertino flick smoothness base 


// Sécurité / défaut
$title = null;
$description = null;
$image = null;
$mode = null;
$uid = null;
$error = null;
$GLOBALS['filtre'] = array();
$GLOBALS['translation'] = array();
$GLOBALS['content'] = array();
$GLOBALS['editkey'] = 1;
$GLOBALS['home'] = $GLOBALS['scheme'].$GLOBALS['domain'].$GLOBALS['path'];


// Navigation par page
$num_pp = 10;
if(isset($_REQUEST['page'])) $page = (int)$_REQUEST['page']; else $page = 1;

?>