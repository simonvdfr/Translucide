<?
@include_once("../config.php");// Variables
include_once("../api/function.php");// Fonctions

include_once("../api/db.php");// Connexion à la db

//@todo:
// ajouter l'url canonique dans les meta
// ajouter le nom de la tpl dans le content addclass
// verif que le mode edit passe bien

// Sélectionne la langue
$lang = get_lang();

load_translation('api');// Chargement des traductions du système
load_translation('theme');// Chargement des traductions du thème


// CONTENU

// On récupère les données de la page
$sel = $connect->query("SELECT * FROM ".$table_content." WHERE url='".encode($_GET["url"])."' AND lang='".$lang."' LIMIT 1");
if($connect->error) {
	header($_SERVER['SERVER_PROTOCOL']." 503 Service Unavailable");
	exit($connect->error);
}
else $res = $sel->fetch_assoc();

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
elseif($res['state'] != "active")// Page non activé
{
	// Si pas admin on affiche page en construction
	if(!$_SESSION['auth']['edit_content']) 
	{
		// On regarde si une template 503 est définie
		$sel = $connect->query("SELECT * FROM ".$table_content." WHERE url='503' AND lang='".$lang."' AND state='active' LIMIT 1");
		$res = $sel->fetch_assoc();

		header($_SERVER['SERVER_PROTOCOL']." 503 Service Unavailable");
			
		if(!$res) $res['title'] = $msg = __("Under Construction");
	}

	$robots = "noindex, follow";
}
else// Si la page est active elle est référençable
{
	$robots = "index, follow";
}

// Information pour les metas
$title = strip_tags($res['title']);
$description = strip_tags($res['description']);

// Les contenus
if($res['content'] and $res['content'] != '""') $GLOBALS['content'] = json_decode($res['content'], true);
else $GLOBALS['content'] = array();


//header('Content-type: text/html; charset=UTF-8');

?>
<title><?=$title;?></title>

<?if($description){?><meta name="description" content="<?=strip_tags($description);?>" /><?}?>

<meta name="robots" content="<?=$robots;?>">

<meta property="og:site_name" content="<?=$GLOBALS['sitename'];?>" />
<meta property="og:title" content="<?=$title;?>" />
<meta property="og:type" content="website" />
<meta property="og:url" content="<?=$GLOBALS['scheme'].$GLOBALS['domain'];?>" />
<?if($description){?><meta property="og:description" content="<?=strip_tags($description);?>" /><?}?>
<?if($image){?><meta property="og:image" content="<?=$GLOBALS['scheme'].$GLOBALS['domain'].$image;?>" /><?}?>

<script>

	state = "<?=$res['state']?>";
	permalink = "<?=$res['url']?>";

	document.title = "<?=addslashes($title);?>";

	window.history.replaceState(history.state, document.title, "<?=make_url($res['url']);?>");	

	<? if($GLOBALS['google_analytics']) { ?>
	// Google Analytics
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	ga('create', '<?=$GLOBALS['google_analytics'];?>', 'auto');
	ga('send', 'pageview');
	<? } ?>

</script>

<?
//echo"<div class='content".($res['tpl']?" tpl-".encode($res['tpl']):"")."'>";

if($res['tpl']) // On a une page
{
	// On charge la template du thème pour afficher le contenu
	include("../theme/".$GLOBALS['theme'].($GLOBALS['theme']?"/":"")."tpl/".$res['tpl'].".php");
}
else // Pas de contenu a chargé
{
	echo"<div class='pal tc'>".$msg."</div>";
}

$connect->close();
?>
<script>console.log("<?=benchmark()?>")</script>