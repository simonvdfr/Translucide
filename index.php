<?
@include_once("config.php");// Variables
include_once("api/function.php");// Fonctions

// Fonctions du theme
if($GLOBALS['function']) 
	include_once($_SERVER["DOCUMENT_ROOT"].$GLOBALS['path']."theme/".$GLOBALS['theme'].$GLOBALS['function']);

include_once("api/db.php");// Connexion à la db


// Pour éviter le duplicate avec index.php
if(stristr($_SERVER['REQUEST_URI'], 'index.php')){
	header("Status: 301 Moved Permanently");
	header("Location: ".str_ireplace("index.php", "", $_SERVER['REQUEST_URI']));
	exit;
}


// Sélectionne la langue
$lang = get_lang();

load_translation('api');// Chargement des traductions du système
load_translation('theme');// Chargement des traductions du thème


// CONTENU

// On récupère les données de la page
$sel = $connect->query("SELECT * FROM ".$table_content." WHERE url='".get_url()."' AND lang='".$lang."' LIMIT 1");
if($connect->error) {
	header($_SERVER['SERVER_PROTOCOL']." 503 Service Unavailable");
	exit($connect->error);
}
else $res = $sel->fetch_assoc();// On récupère les données de la page


// Pas de page existante
if(!$res) 
{
	// On regarde si une template 404 est définie
	$sel = $connect->query("SELECT * FROM ".$table_content." WHERE url='404' AND lang='".$lang."' AND state='active' LIMIT 1");
	$res = $sel->fetch_assoc();
	
	// On force un header 404
	header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");

	// Si pas de template
	if(!$res) $res['title'] = $msg = __("404 error : page not found");

	$robots = "noindex, nofollow";
}
else// Une page existe
{
	// On verifie l'url pour eviter les duplicates : si erreur = redirection
	if(($_SERVER['REQUEST_SCHEME']?$_SERVER['REQUEST_SCHEME']."://":$GLOBALS['scheme']).$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] != make_url($res['url'], array_merge($GLOBALS['filtre'], array("domaine" => true))))
	{
		header($_SERVER['SERVER_PROTOCOL']." 301 Moved Permanently");		
		header("location: ".make_url($res['url'], array_merge($GLOBALS['filtre'], array("domaine" => true))));
		exit;
	}

	if($res['state'] != "active")// Page non activé
	{
		// Si pas admin on affiche page en construction
		if(!$_SESSION['auth']['edit-'.$res['type']]) 
		{
			// On regarde si une template 503 est définie
			$sel = $connect->query("SELECT * FROM ".$table_content." WHERE url='503' AND lang='".$lang."' AND state='active' LIMIT 1");
			$res = $sel->fetch_assoc();

			header($_SERVER['SERVER_PROTOCOL']." 503 Service Unavailable");
				
			if(!$res) $res['title'] = $msg = __("Under Construction");
		}

		$robots = "noindex, follow";
	}
	else $robots = $GLOBALS['robots'];// Si la page est active elle est référençable (on utilise la config)
}


// Information pour les metas
$title = strip_tags($res['title']);
$description = (isset($res['description']) ? htmlspecialchars(strip_tags($res['description']), ENT_COMPAT) : "");

// Les contenus
if(isset($res['content']) and $res['content'] != '""') $GLOBALS['content'] = json_decode($res['content'], true);
else $GLOBALS['content'] = array();

// Image pour les réseaux sociaux
if(isset($GLOBALS['content']['og-image'])) $image = $GLOBALS['content']['og-image'];


// MENU DE NAVIGATION

// On récupère les données de la navigation
$sel_nav = $connect->query("SELECT * FROM ".$table_meta." WHERE type='nav' AND cle='".$lang."' LIMIT 1");
$res_nav = $sel_nav->fetch_assoc();

// Extraction du menu
if($res_nav['val']) $GLOBALS['nav'] = json_decode($res_nav['val'], true);
else $GLOBALS['nav'] = array();


// HEADER

// On récupère les données du header
$sel_header = $connect->query("SELECT * FROM ".$table_meta." WHERE type='header' AND cle='".$lang."' LIMIT 1");
$res_header = $sel_header->fetch_assoc();

// Ajout des données du footer
if($res_header['val']) $GLOBALS['content'] = @array_merge($GLOBALS['content'], json_decode($res_header['val'], true));


// FOOTER

// On récupère les données du footer
$sel_footer = $connect->query("SELECT * FROM ".$table_meta." WHERE type='footer' AND cle='".$lang."' LIMIT 1");
$res_footer = $sel_footer->fetch_assoc();

// Ajout des données du footer
if($res_footer['val']) $GLOBALS['content'] = @array_merge($GLOBALS['content'], json_decode($res_footer['val'], true));



header('Content-type: text/html; charset=UTF-8');

?><!DOCTYPE html>
<html lang="<?=$lang;?>">
<head>
	
	<meta charset="utf-8">

	<title><?=$title;?></title>

	<?if($description){?><meta name="description" content="<?=$description;?>"><?}?>

	<meta name="robots" content="<?=$robots;?>">

	<meta property="og:site_name" content="<?=utf8_encode($GLOBALS['sitename']);?>">
	<meta property="og:title" content="<?=$title;?>">
	<meta property="og:type" content="website">
	<?if(isset($res['url'])){?><meta property="og:url" content="<?=make_url($res['url'], array("domaine" => true))?>"><?}?>
	
	<?if($description){?><meta property="og:description" content="<?=$description;?>"><?}?>
	<?if($image){?><meta property="og:image" content="<?=$GLOBALS['home'].$image;?>"><?}?>

	<?if($GLOBALS['facebook_api_id']){?><meta property="fb:app_id" content="<?=$GLOBALS['facebook_api_id'];?>"><?}?>
	
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<?if($GLOBALS['google_page']){?><link href="<?=$GLOBALS['google_page'];?>" rel="publisher" /><?}?>


	<?if($GLOBALS['icons']){?><link rel="stylesheet" href="<?=$GLOBALS['icons']?>"><?}?>


	<link rel="stylesheet" href="<?=$GLOBALS['path']?>api/global<?=$GLOBALS['min']?>.css?">	


	<link rel="stylesheet" href="<?=$GLOBALS['path']?>theme/<?=$GLOBALS['theme']?>style<?=$GLOBALS['min']?>.css">	


	<link rel="shortcut icon" type="image/x-icon" href="<?=$GLOBALS['path']?>media/favicon.ico">


	<script src="<?=$GLOBALS['jquery']?>"></script>

	<script src="<?=$GLOBALS['path']?>api/lucide.init<?=$GLOBALS['min']?>.js"></script>


	<script>
		<? if($GLOBALS['google_analytics']) { ?>
		// Google Analytics
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		ga('create', '<?=$GLOBALS['google_analytics'];?>', 'auto');
		ga('send', 'pageview');
		<? } ?>


		<? if(isset($GLOBALS['facebook_sdk']) and $GLOBALS['facebook_api_id']) { ?>
		// Facebook
		(function(d, s, id){
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) {return;}
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v2.7&cookie=true&appId=<?=$GLOBALS['facebook_api_id'];?>";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
		<? } ?>
		
						
		<?if(isset($_COOKIE['autoload_edit']) and $_SESSION['auth']['edit-page']){?>// Si demande l'autoload du mode édition et si admin
			$(function(){
				edit_launcher();
				$("a.bt.fixed.edit").fadeOut();				
			});
			<?
			// Supprime le cookie qui demande de charger automatiquement l'admin
			@setcookie("autoload_edit", "", time() - 3600, $GLOBALS['path'], $GLOBALS['domain']);
		}?>			

		state = "<?=(isset($res['state'])?$res['state']:"")?>";
		permalink = "<?=(isset($res['url'])?$res['url']:"")?>";
		type = "<?=(isset($res['type'])?$res['type']:"")?>";
		path = "<?=$GLOBALS['path']?>";
	</script>

	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

</head>
<body>


<?
include_once("theme/".$GLOBALS['theme']."header.php");


echo"<div class='content".(isset($res['tpl']) ? " tpl-".encode($res['tpl']) : "")."'>";


	if(isset($res['tpl'])) // On a une page
	{
		include("theme/".$GLOBALS['theme']."tpl/".$res['tpl'].".php");// On charge la template du thème pour afficher le contenu
	}
	else // Pas de contenu a chargé
	{
		echo"<div class='pal tc'>".$msg."</div>";
	}


echo"</div>";


include_once("theme/".$GLOBALS['theme']."footer.php");
?>

<script>console.log("<?=benchmark()?>")</script>
</body>
</html>
<? $connect->close(); ?>