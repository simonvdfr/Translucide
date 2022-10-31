<?php
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


// Fixe la langue
if(strstr($_SERVER['SERVER_NAME'], 'domaine.com')) 
	$lang = $_SESSION['lang'] = 'en';
else
	$lang = $_SESSION['lang'] = 'fr';

// Langue alternative si une traduction n'existe pas
$GLOBALS['lang_alt'] = 'en';


// Définition de la zone horaire
date_default_timezone_set('Europe/Paris');

// Langue des dates .UTF8
if($lang == 'fr') setlocale(LC_ALL, 'fr_FR.utf8', 'fra');
else setlocale(LC_ALL, 'en_US.utf8');


// Serveur local ou online ? DEV || PROD
if(
	$_SERVER['SERVER_ADDR'] == '127.0.0.1' or
	strpos($_SERVER['SERVER_ADDR'], '::1') !== false)
	$dev = true;
else 
	$dev = false;


// Variables de la base de données
$GLOBALS['db_prefix'] = '';
$GLOBALS['db_charset'] = 'utf8mb4';// utf8 => classique || utf8mb4 => pour les emoji mac

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


// VARIABLES SITES
$GLOBALS['language'] = array('fr');

// charge le fichier translation.php dans le dossier du theme
$GLOBALS['theme_translation'] = false;


$GLOBALS['theme'] = '';


$GLOBALS['sitename'] = null;


if($dev)// Dev local
	$GLOBALS['scheme'] = '';
else 
	$GLOBALS['scheme'] = '';


if($dev)// Dev local
	$GLOBALS['domain'] = '';
else 
	$GLOBALS['domain'] = '';


$GLOBALS['path'] = '';

$GLOBALS['replace_path'] = '';// "/" Pour les chemins des média sur les sites avec dossier dans les url (filtre)


$GLOBALS['email_contact'] = '';


// false => noindex, nofollow | true => index, follow
$GLOBALS['online'] = false;

// Heure de fermeture du site (strtotime) = 20:00-06:00 +1 day
$GLOBALS['offline'] = null;


// Utilisation de librairie minifier
if($dev)// Dev local
	$GLOBALS['min'] = '';
else 
	$GLOBALS['min'] = '';//.min


// Générer une page en statique html
$GLOBALS['static'] = false;
$GLOBALS['static_dir'] = '';


// Vérifie l'état d'écoconception des images
$GLOBALS['img_check'] = true;

// Conversion vers le webp autorisé
$GLOBALS['towebp'] = true;


// Ecoindex
$GLOBALS['ecoindex'] = true;


// Cache sur les fichiers du CMS
$GLOBALS['cache'] = "";


// Include
$GLOBALS['function'] = '';// fonction du theme
$GLOBALS['after_get_tag'] = '';// Action avant d'afficher l'header


// https://developers.facebook.com/apps/
$GLOBALS['facebook_api_id'] = '';

// https://analytics.google.com/analytics/web/
$GLOBALS['google_analytics'] = '';

// https://search.google.com/search-console
$GLOBALS['google_verification'] = '';

// https://plausible.io
$GLOBALS['plausible'] = '';// $GLOBALS['domain']


// Toolbox
$GLOBALS['toolbox'] = array(
	//"h2",
	//"h3",
	//"h4",
	//"h5",
	//"h6",
	"bold",
	"italic",
	//"underline",
	//"superscript",
	//"fontSize",
	//"color",
	//"p",
	//"blockquote",
	//"q", => A finir
	//"highlight",
	//"insertUnorderedList",
	//"justifyLeft",
	//"justifyCenter",
	//"justifyRight",
	//"justifyFull",
	//"InsertHorizontalRule",
	//"viewsource",
	//"icon",
	"media",
	//"figure",
	//"video",// Lecture dans le site
	//"videoLink",// Lien vers youtube
	//"lang",
	//"anchor",
	//"bt",
	"link"
);

// Nombre de couleur custom dans la css color-x
$GLOBALS['nbcolor'] = 0;


// Clé hash pour les cryptages
$GLOBALS['pub_hash'] = '';
$GLOBALS['priv_hash'] = '';

// Nom de boucle de hashage du mdp
$GLOBALS['pwd_hash_loop'] = '';


// Niveau de sécurité du système de login // medium : token en session | high : ajout du token dans la base (multilog impossible)
$GLOBALS['security'] = 'medium';


// Vérifie que les fichiers uploadés ne contiennent pas des caractères susceptibles d'être des codes exécutables pour des hacks => A utiliser si compte public actif
$GLOBALS['file_check_hack'] = false;


// Temps d'expiration des sessions de connexion
$GLOBALS['session_expiration'] = 60*60*24*30;


// Compte public autorisé
$GLOBALS['public_account'] = false;

// Statue d'activation par défaut des comptes utilisateur
$GLOBALS['default_state'] = 'active';// moderate / mail / active / deactivate
$GLOBALS['mail_moderate'] = true;

// Niveaux d'authentification par défaut des comptes utilisateur
$GLOBALS['default_auth'] = 'edit-public';// add-media-public

// Info supplémentaire sur l'utilisateur
$GLOBALS['user_info'] = null;

// Niveaux d'authentification possible
$GLOBALS['auth_level'] = array(
	'edit-admin' => 'Managing admins',
	'edit-user' => 'Managing users',

	//'edit-config' => 'Edit Config',// A codé une admin de la config

	'edit-nav' => 'Edit menu',

	//'edit-header' => 'Edit header',// Pas utilisée pour le moment
	//'edit-footer' => 'Edit footer',// Pas utilisée pour le moment

	'add-media' => 'Send Files',

	//'edit-media' => 'Edit Files',// Pas utilisée pour le moment

	// Pour que les utilisateurs puissent ajouter du contenu au site
	//'add-media-public' => 'Public file',
	//'edit-public' => 'Public content',
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


// Pour des noms de modele plus explicite dans le select
$GLOBALS['tpl_name'] = array(
	"home" => "Page accueil",
	"article" => "Actualité",
	"event" => "Évènement agenda",
	"article-liste" => "Liste actualités - Agenda",
);


// Type de contenu ajoutable dans le menu
$GLOBALS['add_menu'] = array(
	//"article",
	"page"
);


// Bouton en bas en layer
$GLOBALS['bt_edit'] = true;
$GLOBALS['bt_top'] = false;


// Raccourci clavier pour une administration rapide
$GLOBALS['shortcut'] = false;


// Type mime supporté pour l'upload
$GLOBALS['mime_supported'] = array(
	'image/jpg',
	'image/jpeg',
	'image/pjpeg',
	'image/png',
	'image/x-png',
	'image/gif',
	'image/webp',
	'image/x-icon',
	'image/svg',
	'image/svg+xml',
	//'video/webm',
	//'video/mp4',
	'application/pdf',
	'application/zip',
	'application/x-zip-compressed',
	//'text/plain'
);


// Variables tailles images
$GLOBALS['max_image_size'] = '1920x1080';
$GLOBALS['jpg_quality'] = 90;
$GLOBALS['png_quality'] = 9;
$GLOBALS['webp_quality'] = 90;
$GLOBALS['img_green'] = '100';//ko
$GLOBALS['img_warning'] = '400';//ko
$GLOBALS['imgs_green'] = '800';//ko
$GLOBALS['imgs_warning'] = '1000';//ko
$GLOBALS['imgs_num'] = '15';// nombre d'image max


// On peut voir les dossiers dans la librairie des médias
$GLOBALS['list_media_dir'] = false;

// Nom du dossier média
$GLOBALS['media_dir'] = 'media';


// Favicon navigateur
$GLOBALS['favicon'] = '';

// Librairie d'icons spécifiques à la template
$GLOBALS['icons'] = '';// $GLOBALS['scheme'].$GLOBALS['domain'].$GLOBALS['path']."api/icons/icons.min.css"


// Utilisation de global.css ? à supprimer à termes (06/01/2021)
$GLOBALS['global.css'] = false;


// Url de la css du thème
$GLOBALS['style.css'] = null;


// Librairie externe
$GLOBALS['jquery'] = '//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js';

$GLOBALS['jquery_ui'] = '//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js';

$GLOBALS['jquery_ui_css'] = '//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.min.css';// cupertino flick smoothness base 


// Url pour faire un lien vers un tutoriel externe
$GLOBALS['tutoriel'] = null;


// Filtre url autorisé
$GLOBALS['filter_auth'] = array('page', 'user');


// Sécurité / défaut
$id = $title = $description = $image = $tag = null;
$mode = $uid = $error = $robots = $robots_data = $close = null;
$GLOBALS['filter'] = array();
$GLOBALS['translation'] = array();
$GLOBALS['content'] = array();
$GLOBALS['editkey'] = 1;
$GLOBALS['home'] = $GLOBALS['scheme'].$GLOBALS['domain'].$GLOBALS['path'];
$GLOBALS['root'] = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['path'].'theme/'.$GLOBALS['theme'].($GLOBALS['theme']?'/':'');


// Numéro de la page en cours
if(isset($_REQUEST['page'])) $page = (int)$_REQUEST['page'];
else $page = 1;

// Nombre d'entré par page
$num_pp = 20;
?>
