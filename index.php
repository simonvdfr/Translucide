<?php
@include_once("config.php");// Variables
include_once("api/function.php");// Fonctions
include_once("api/db.php");// Connexion à la db


// Pour éviter le duplicate avec index.php
if(stristr($_SERVER['REQUEST_URI'], 'index.php')){
	header("Status: 301 Moved Permanently");
	header("Location: ".str_ireplace("index.php", "", $_SERVER['REQUEST_URI']));
	exit;
}


// On ajax une page ?
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
	$ajax = true;
else
	$ajax = false;



// Sélectionne la langue
$GLOBALS['lang'] = get_lang();

load_translation('api');// Chargement des traductions du système
if(@$GLOBALS['theme_translation']) load_translation('theme');// Chargement des traductions du theme



/********** CONTENU **********/

// On récupère les données de la page
$get_url = $connect->real_escape_string(get_url());
$sel = $connect->query("SELECT * FROM ".$table_content." WHERE url='".$get_url."' AND lang='".$lang."' LIMIT 1");
if($connect->error) {
	header($_SERVER['SERVER_PROTOCOL']." 503 Service Unavailable");
	exit($connect->error);
}
else $res = $sel->fetch_assoc();// On récupère les données de la page



/********** TAGS **********/

// Construction de l'ajout du contenu tag/cat, si filter et la racine de l'url pas dans les filtres autorisés
if(isset($GLOBALS['filter']) and count($GLOBALS['filter']) > 0 and !in_array($get_url, $GLOBALS['filter_auth'])) 
{
	$filter_one = array_keys($GLOBALS['filter'])[0];

	// Si tag et pas uniquement home + filtre autorisé
	if(isset($GLOBALS['filter'][$filter_one]) and !in_array($filter_one, $GLOBALS['filter_auth']))
	{
		$tag = encode($GLOBALS['filter'][array_keys($GLOBALS['filter'])[0]]);

		// On rapatrie les infos du tag
		$sel_tag_info = $connect->query("SELECT * FROM ".$table_meta." WHERE type='tag-info' AND cle='".$tag."' LIMIT 1");
		$res_tag_info = $sel_tag_info->fetch_assoc();

		// Il n'y a pas d'infos sur le tag
		if(!@$res_tag_info['val'])
		{
			// On rapatrie simplement le nom du tag, pour le fil d'ariane par exemple
			$sel_tag = $connect->query("SELECT * FROM ".$table_tag." WHERE zone='".$res['url']."' AND encode='".$tag."' LIMIT 1");
			$res_tag = $sel_tag->fetch_assoc();

			// Si tag n'existe pas => page 404
			if(!$res_tag['name']) $res = null;
		}
	}
}



/********** ACTION après la récupération des données du tag **********/
if(@$GLOBALS['after_get_tag']) include_once($GLOBALS['root'].$GLOBALS['after_get_tag']);



/********** UNE PAGE EXISTE **********/
$robots_data = '';

if($res) 
{
	// Si on veut que le CMS soit en https dans la config on vérifie le statut d'origine de l'url
	if(strpos($GLOBALS['scheme'], 'https') !== false) 
	{
		// Verif si https dans l'url
		if(strpos(@$_SERVER['SCRIPT_URI'], 'https') !== false or $_SERVER['REQUEST_SCHEME'] == 'https') 
			$http = "https://";
		else 
			$http = "http://";
	}
	else $http = $GLOBALS['scheme'];


	// On verifie l'url pour eviter les duplicates : si erreur = redirection
	if($http.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] != make_url($res['url'], array_merge($GLOBALS['filter'], array("domaine" => true))))
	{
		header($_SERVER['SERVER_PROTOCOL']." 301 Moved Permanently");		
		header("location: ".make_url($res['url'], array_merge($GLOBALS['filter'], array("domaine" => true))));
		exit;
	}


	$robots_data = @$res['robots'];// paramètre des robots propriétaire à la page courante


	if($res['state'] != "active")// Page non activé
	{
		// Si pas admin on affiche page en construction
		if(!@$_SESSION['auth']['edit-'.$res['type']]) 
		{
			// On regarde si une template 503 est définie
			$sel_503 = $connect->query("SELECT * FROM ".$table_content." WHERE url='503' AND lang='".$lang."' AND state='active' LIMIT 1");
			$res_503 = $sel_503->fetch_assoc();
			if($res_503['id']) $res = $res_503;
			else {
				$res = null;
				$res['title'] = $msg = __("Under Construction");
				$res['state'] = 'deactivate';
			}

			header($_SERVER['SERVER_PROTOCOL']." 503 Service Unavailable");
		}

		$robots = "noindex, follow";
	}
	else// Si la page est active elle est référençable (on utilise la config ou les param de la page)
	{
		if(@$GLOBALS['online'] === false) $robots = 'noindex, nofollow';// Offline
		elseif(@$res['robots']) $robots = $robots_data;// Online + paramètre déterminé
		else $robots = 'index, follow';// Online + pas de paramètre
	} 
}
else/********** PAS DE PAGE EXISTANTE **********/
{
	// On regarde si une template 404 est définie
	$sel = $connect->query("SELECT * FROM ".$table_content." WHERE url='404' AND lang='".$lang."' AND state='active' LIMIT 1");
	$res = $sel->fetch_assoc();

	// Si pas de template
	if(!$res) {
		$res['title'] = $msg = __("404 error : page not found");
		$res['description'] = "";
	}

	// On force un header 404
	header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");

	$robots = "noindex, follow";
}



/********** ID DE LA PAGE **********/
if(isset($res['id'])) $GLOBALS['id'] = $res['id']; else $GLOBALS['id'] = null;


/********** LES CONTENUS **********/
if(isset($res['content']) and $res['content'] != '')  $GLOBALS['content'] = json_decode($res['content'], true);
else $GLOBALS['content'] = array();

// Si pas de titre/title H1 on met le title de la page/produit
if(!isset($GLOBALS['content']['title'])) $GLOBALS['content']['title'] = $res['title'];



/********** METAS HEAD **********/

// SI TAG ajout au meta
if(isset($res_tag_info['val']))// Il y a des infos sur le tag
{
	// Récupère les informations des tags et écrase celle du contenu
	$GLOBALS['content'] = @array_merge($GLOBALS['content'], json_decode($res_tag_info['val'], true));

	// Ecrase les données meta
	if(isset($GLOBALS['content']['title'])) $res['title'] = $GLOBALS['content']['title'];
	if(isset($GLOBALS['content']['description'])) $res['description'] = htmlspecialchars(strip_tags($GLOBALS['content']['description'], ENT_COMPAT));
	if(isset($GLOBALS['content']['img'])) $GLOBALS['content']['og-image'] = $GLOBALS['content']['img'];
}
elseif(isset($res_tag['name']))// Si il y a juste le nom du tag
{
	$res['title'] = $GLOBALS['content']['title'] = $res_tag['name'];
	$res['description'] = $GLOBALS['content']['description'] = "";
}
elseif(in_array($get_url, $GLOBALS['filter_auth']) and isset($GLOBALS['filter'])) {// Si url dans filtre autorisé on ajoute le mot cle dans le title
	$res['title'] = $res['title'] ." " . str_replace("-", " " , @array_keys($GLOBALS['filter'])[0]);
}



// SI CONTENU
// Information pour les metas du head
$title = strip_tags($res['title']);
$description = (isset($res['description']) ? htmlspecialchars(strip_tags($res['description']), ENT_COMPAT) : "");

// Image pour les réseaux sociaux
if(isset($GLOBALS['content']['og-image'])) $image = $GLOBALS['content']['og-image'];
elseif(isset($GLOBALS['content']['alaune'])) $image = $GLOBALS['content']['alaune'];
elseif(isset($GLOBALS['content']['visuel']) or isset($GLOBALS['content']['visuel-1'])) 
{
	if(isset($GLOBALS['content']['visuel'])) $image = $GLOBALS['content']['visuel'];
	else $image = $GLOBALS['content']['visuel-1'];

	// Si image plus grande (zoom)
	$parse_url = parse_url($image);
	parse_str($parse_url['query'], $get);
	if(isset($get['zoom'])) $image = $get['zoom'];
} 



// Si pas ajax on charge toute la page
if(!$ajax)
{
	/********** RÉCUPÉRATION DES DONNÉES META : NAV | HEADER |FOOTER **********/
	
	$sel_meta = $connect->query("SELECT * FROM ".$tm." WHERE type IN ('nav','header','footer') AND cle='".$lang."' LIMIT 3");
	while($res_meta = $sel_meta->fetch_assoc())
	{
		if(isset($res_meta['val']))
		{
			// Si menu de navigation
			if($res_meta['type'] == 'nav') $GLOBALS['nav'] = json_decode($res_meta['val'], true);
			// Si contenu du header ou footer
			else $GLOBALS['content'] = @array_merge($GLOBALS['content'], json_decode($res_meta['val'], true));
		}
	}

	// Si pas de nav
	if(!isset($GLOBALS['nav'])) $GLOBALS['nav'] = array();



	header('Content-type: text/html; charset=UTF-8');

	?><!DOCTYPE html>
	<html lang="<?=$lang;?>">
	<head>
		
		<meta charset="utf-8">

		<title><?=$title;?></title>
		<?php if($description){?><meta name="description" content="<?=$description;?>"><?php }?>

		<meta name="robots" content="<?=$robots;?>" data="<?=$robots_data;?>">

		<meta name="viewport" content="width=device-width, initial-scale=1">

		<meta property="og:title" content="<?=$title;?>">
		<meta property="og:type" content="website">
		<?php if(isset($res['url'])){?>
		<meta property="og:url" content="<?=make_url($res['url'], array_merge($GLOBALS['filter'], array("domaine" => true)))?>">
		<link rel="canonical" href="<?=make_url($res['url'], array_merge($GLOBALS['filter'], array("domaine" => true)))?>">
		<?php }?>
		<?php if($description){?><meta property="og:description" content="<?=$description;?>"><?php }?>
		<?php if($image){?><meta property="og:image" content="<?=$GLOBALS['home'].$image;?>"><?php }?>
		<meta property="article:published_time" content="<?=date(DATE_ISO8601, strtotime(@$res['date_insert']));?>">

		<?php if(@$GLOBALS['facebook_api_id']){?><meta property="fb:app_id" content="<?=$GLOBALS['facebook_api_id'];?>"><?php }?>
		<?php if(@$GLOBALS['google_verification']){?><meta name="google-site-verification" content="<?=$GLOBALS['google_verification'];?>" /><?php }?>


		<?php if(!isset($GLOBALS['global.css']) or @$GLOBALS['global.css'] == true){?><link rel="stylesheet" href="<?=$GLOBALS['path']?>api/global<?=$GLOBALS['min']?>.css?<?=$GLOBALS['cache']?>"><?php }?>

		<link rel="stylesheet" href="<?=$GLOBALS['path']?>theme/<?=$GLOBALS['theme'].($GLOBALS['theme']?"/":"")?>style<?=$GLOBALS['min']?>.css?<?=$GLOBALS['cache']?>">	

		<?php if(@$GLOBALS['icons']){?><link rel="stylesheet" href="<?=$GLOBALS['icons']?>"><?php }
		else{?>
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
		</style>
		<?php }?>

		<?php if(@$GLOBALS['favicon']){?><link rel="shortcut icon" type="image/x-icon" href="<?=$GLOBALS['favicon']?>"><?php }?>

		<script src="<?=$GLOBALS['jquery']?>"></script>

		<script src="<?=$GLOBALS['path']?>api/lucide.init<?=$GLOBALS['min']?>.js?<?=$GLOBALS['cache']?>"></script>


		<?php if(@$GLOBALS['plausible']) { ?>
		<script async defer data-domain="<?=@$GLOBALS['plausible']?>" src="https://plausible.io/js/plausible.js"></script>
		<?php }?>


		<script>
			
			<?php if(@$GLOBALS['google_analytics']) { ?>
			// Si Analytics pas desactivé
			if(get_cookie('analytics') != "desactiver") 
			{
				// Google Analytics
				google_analytics = '<?=$GLOBALS['google_analytics'];?>';
				(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
				})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
				ga('create', google_analytics, 'auto');
				ga('send', 'pageview');
			}
			<?php }


			if(@$GLOBALS['facebook_api_id']) { ?>
			// Facebook
			(function(d, s, id){
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) {return;}
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v2.7&cookie=true&appId=<?=$GLOBALS['facebook_api_id'];?>";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
			<?php } 


			if(isset($_COOKIE['autoload_edit']) and $_SESSION['auth']['edit-page']){?>
				// Si demande l'autoload du mode édition et si admin
				$(function(){
					edit_launcher();
					$("a.bt.fixed.edit").fadeOut();				
				});
				<?php
				// Supprime le cookie qui demande de charger automatiquement l'admin
				@setcookie("autoload_edit", "", time() - 3600, $GLOBALS['path'], $GLOBALS['domain']);
			}?>		


			// Variables
			id = "<?=$id?>";
			state = "<?=@$res['state']?>";
			permalink = "<?=@$res['url']?>";
			type = "<?=@$res['type']?>";
			tpl = "<?=@$res['tpl']?>";
			tag = "<?=encode(@$tag)?>";
			path = "<?=$GLOBALS['path']?>";
			theme = "<?=$GLOBALS['theme']?>";
			media_dir = "<?=$GLOBALS['media_dir']?>";
			<?=((!isset($GLOBALS['bt_edit']) or $GLOBALS['bt_edit'] == true)?'bt_edit = true;':'')?>
			<?=((!isset($GLOBALS['bt_top']) or $GLOBALS['bt_top'] == true)?'bt_top = true;':'')?>
			<?=(@$dev?'dev = true;':'')?>

		</script>

	</head>
	<body>
	<?php


	include_once('theme/'.$GLOBALS['theme'].($GLOBALS['theme']?'/':'').'header.php');


	echo'<main class="content'.(isset($res['tpl'])?' tpl-'.encode($res['tpl']):'').'">';
}



if(isset($res['tpl'])) // On a une page
{
	include('theme/'.$GLOBALS['theme'].($GLOBALS['theme']?'/':'').'tpl/'.$res['tpl'].'.php');// On charge la template du thème pour afficher le contenu
}
else // Pas de contenu a chargé
{
	echo'<div class="pal tc">'.$msg.'</div>';
}



// Si pas ajax on charge toute la page
if(!$ajax)
{
	echo'</main>';


	include_once('theme/'.$GLOBALS['theme'].($GLOBALS['theme']?'/':'').'/footer.php');
	?>


	<div class="responsive-overlay"></div>


	<script>console.log("<?=benchmark()?>")</script>


	<noscript>
		<style>
			/* Si pas de Javascript on affiche les contenus invisibles en attente d'animation */
			.animation { opacity: 1 !important; transform: translate3d(0, 0, 0) !important;	}
		</style>
	</noscript>


	</body>
	</html>
	<?php
}
else {?><script>console.log("<?=benchmark()?>")</script><?php }

$connect->close();
?>