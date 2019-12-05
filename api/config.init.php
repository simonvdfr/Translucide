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
setlocale(LC_ALL, 'fr_FR.utf8', 'fra');//.UTF8


// Serveur local ou online ? DEV || PROD
if(
	strpos($_SERVER['SERVER_ADDR'], '::1') !== false or
	strpos($_SERVER['SERVER_ADDR'], '127.0') !== false)
	$dev = true;
else 
	$dev = false;


// Variables de la base de données
$GLOBALS['db_prefix'] = '';
$GLOBALS['db_charset'] = 'utf8';

$GLOBALS['table_content'] = $GLOBALS['tc'] = $GLOBALS['db_prefix'].'content';
$GLOBALS['table_meta'] = $GLOBALS['tm'] = $GLOBALS['db_prefix'].'meta';
$GLOBALS['table_tag'] = $GLOBALS['tt'] = $GLOBALS['db_prefix'].'tag';
$GLOBALS['table_user'] = $GLOBALS['tu'] = $GLOBALS['db_prefix'].'user';

if($dev) {// Dev local
	$GLOBALS['db_server'] = '';
	$GLOBALS['db_user'] = '';
	$GLOBALS['db'] = '';
	$GLOBALS['db_pwd'] = '';
}
else {
	$GLOBALS['db_server'] = '';
	$GLOBALS['db_user'] = '';
	$GLOBALS['db'] = '';
	$GLOBALS['db_pwd'] = '';
}


// Variables sites
$GLOBALS['language'] = array('fr');

$GLOBALS['function'] = '';// Include fonction du theme

$GLOBALS['theme'] = '';

if($dev)// Dev local
	$GLOBALS['scheme'] = '';
else 
	$GLOBALS['scheme'] = '';

if($dev)// Dev local
	$GLOBALS['domain'] = '';
else 
	$GLOBALS['domain'] = '';


$GLOBALS['path'] = '';

$GLOBALS['replace_path'] = "";// "/" Pour les chemins des média sur les sites avec dossier dans les url (filtre)


$GLOBALS['email_contact'] = '';

$GLOBALS['online'] = false;


// Utilisation de librairie minifier
if($dev)// Dev local
	$GLOBALS['min'] = '';
else 
	$GLOBALS['min'] = '';//.min


// https://developers.facebook.com/apps/
$GLOBALS['facebook_api_id'] = '';

// https://analytics.google.com/analytics/web/
$GLOBALS['google_analytics'] = '';

// https://search.google.com/search-console
$GLOBALS['google_verification'] = '';


// Clé hash pour les cryptages
$GLOBALS['pub_hash'] = '';
$GLOBALS['priv_hash'] = '';

// Niveau de sécurité du système de login // medium : token en session | high : ajout du token dans la base (multilog impossible)
$GLOBALS['security'] = 'medium';

// Nom de boucle de hashage du mdp
$GLOBALS['pwd_hash_loop'] = '';

// Temps d'expiration des sessions de connexion
$GLOBALS['session_expiration'] = 60*60*24*30;


// Compte public autorisé
$GLOBALS['public_account'] = false;

// Statue d'activation par défaut des comptes utilisateur
$GLOBALS['default_state'] = 'active';// moderate / mail / active / deactivate
$GLOBALS['mail_moderate'] = true;

// Niveaux d'authentification par défaut des comptes utilisateur
$GLOBALS['default_auth'] = 'add-media-public,edit-public';

// Info supplémentaire sur l'utilisateur
$GLOBALS['user_info'] = null;

// Niveaux d'authentification possible
$GLOBALS['auth_level'] = array(
	'edit-admin' => 'Managing admins',
	'edit-user' => 'Managing users',

	'edit-config' => 'Edit Config',

	'edit-nav' => 'Edit menu',
	'edit-header' => 'Edit header',
	'edit-footer' => 'Edit footer',

	'add-media' => 'Send Files',
	'edit-media' => 'Edit Files',

	'add-media-public' => 'Public file',
	'edit-public' => 'Public content',
);


// Type de contenu ajoutable
$GLOBALS['add_content'] = array(
	//"product" => ["fa" => "fa-basket", "tpl" => "product"],
	"article" => ["fa" => "fa-rss", "tpl" => "article"],
	//"event" => ["fa" => "fa-calendar-empty", "tpl" => "event"],
	//"video" => ["fa" => "fa-video", "tpl" => "video"],
	//"media" => ["fa" => "fa-file-pdf", "tpl" => "fichier"],
	"page" => ["fa" => "fa-doc-text", "tpl" => "page"]
);


// Toolbox
$GLOBALS['toolbox'] = array(
	//"h2",
	//"h3",
	//"h4",
	"bold",
	"italic",
	//"underline",
	//"superscript",
	//"fontSize",
	//"insertUnorderedList",
	//"justifyLeft",
	//"justifyCenter",
	//"justifyRight",
	//"justifyFull",
	//"InsertHorizontalRule",
	//"viewsource",
	//"icon",
	"media",
	//"anchor",
	//"bt",
	"link"
);


// Type mime supporté pour l'upload
$GLOBALS['mime_supported'] = array(
	'image/jpg',
	'image/jpeg',
	'image/pjpeg',
	'image/png',
	'image/x-png',
	'image/gif',
	'image/x-icon',
	'application/pdf',
	'application/zip',
	'application/x-zip-compressed',
	'text/plain'
);


// Variables tailles images
$GLOBALS['max_image_size'] = '1920x1080';
$GLOBALS['jpg_quality'] = 90;
$GLOBALS['png_quality'] = 9;


// On peut voir les dossiers dans la librairie des médias
$GLOBALS['media_dir'] = false;


// Cache sur les styles
$GLOBALS['cache'] = "";


// Favicon navigateur
$GLOBALS['favicon'] = '';

// Icone pour mobile / fav
$GLOBALS['touch_icon'] = '';

// Librairie d'icons spécifiques à la template
$GLOBALS['icons'] = '';// $GLOBALS['scheme'].$GLOBALS['domain'].$GLOBALS['path']."api/icons/icons.min.css"


// Librairie externe
$GLOBALS['jquery'] = '//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js';

$GLOBALS['jquery_ui'] = '//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js';

$GLOBALS['jquery_ui_css'] = '//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.min.css';// cupertino flick smoothness base 


// Filtre url autorisé
$GLOBALS['filter_auth'] = array('page', 'user');


// Sécurité / défaut
$id = $title = $description = $image = $tag = null;
$mode = $uid = $error = $robots = $robots_data = null;
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