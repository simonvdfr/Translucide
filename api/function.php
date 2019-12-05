<?
/********** BENCHMARK **********/
// Mesure le temps de traitement Php
function benchmark() {
	return (microtime(true) - $GLOBALS['microtime']);
}



/********** URL / NAVIGATION **********/

// Trim tous type d'espaces
function trimer($value) 
{
	return trim(html_entity_decode($value), " \t\n\r\0\x0B\xC2\xA0");
}

// Nettoie et encode les mots
function encode($value, $separator = "-", $pass = null) 
{
	// Tableau des special chars PHP 7.2
	$from = str_split(utf8_decode("ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñß@\’\"'_-&()=/*+$!:;,.\²~#?§µ%£°{[|`^]}¤€<>"));
    $to = str_split("aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynnba                                         ");
	
	// Si on doit laisser certains caractères
	if(@count($pass)) {
		foreach($pass as $char){
			$strpos = strpos(implode($from), $char);
			$from[$strpos] = "";
			$to[$strpos] = "";
		}
	}

	$value = strtolower(strtr(utf8_decode($value), implode($from), implode($to)));// Supp les caractères indésirables

	$value = trimer($value, " \t\n\r\0\x0B\xC2\xA0");// Supprime les espaces et espaces insecable de début et fin
	$value = preg_replace('/ {2,}/', $separator, $value);// Remplace les double espaces
	$value = preg_replace('/ /', $separator, $value);// Remplace les espaces simple
	//$value = preg_replace('/\xa0/', $separator, $value);// Remplace les espaces insecable [\xc2\xa0]

	return $value;
}

// Récupère l'url rewriter
function get_url($url_source = null)
{
	// Si pas d'url forcé on donne l'url en cours complète
	if(!$url_source) 
		$url_source = (@$_SERVER['REQUEST_SCHEME'] ? $_SERVER['REQUEST_SCHEME'] : "http")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

	// Parse l'url pour ne garder que la partie rewrite sans le chemin de base du site
	$parse_url = parse_url($url_source);
	$path = preg_replace("/^".addcslashes($GLOBALS['path'], "/")."*/", "", $parse_url['path']);

	// Si l'url est vide : url = home
	if(!encode($path)) $url = "home"; 
	else
	{
		// Si il y a des filtres/page dans l'url
		if(strstr($parse_url['path'], "/") or strstr($parse_url['path'], "page_")) 
		{
			$explode_path = explode("/", $path);

			if(strstr($explode_path[0], "page_")) $url = "home";// Home si le premier element est la nav par page
			else
			{
				$url = $explode_path[0];// Url raçine
				unset($explode_path[0]);// Supp la racine des filtres si dossier
			}
			
			//while(list($cle, $dir) = each($explode_path)) PHP 7.2
			foreach($explode_path as $cle => $dir)
			{
				$dir = urldecode($dir);// Pour supprimer les %20 ..

				$explode_dir = explode("_", $dir);

				if($explode_dir[0])
					$GLOBALS['filter'][encode($explode_dir[0], "-", array(".","'"))] = encode(preg_replace("/^".$explode_dir[0]."_/", "", $dir), "-", array(".","'","@","_"));
			}
		}
		else $url = $path;
	}

	return encode($url);
}

// Retourne l'url rewriter
function make_url($url, $filter = array())
{
	$dir = "";

	if(is_array($filter))
	{
		// Force le domaine
		if(isset($filter['domaine'])) $domaine = $filter['domaine'];
		unset($filter['domaine']);	

		// Force le chemin absolu
		if(isset($filter['absolu'])) $absolu = $filter['absolu'];
		unset($filter['absolu']);

		// Création des dossier dans l'url en fonction des filtres
		//while(list($cle, $val) = each($filter)) PHP 7.2
		foreach($filter as $cle => $val)
		{
			if($cle == "page" and $val == 1)
				unset($filter['page']);// Si Page == 1 on ne l'affiche pas dans l'url
			elseif($val)
				$dir .= "/" . (($cle and $cle != $val) ? encode($cle)."_" : "") . encode($val, "-", array(".","'","@","_"));
		}
	}

	if($url == "home") 
	{
		$url = $GLOBALS['path'];

		if(isset($domaine)) $url = $GLOBALS['home'];
	}
	else {
		$url = encode($url, "-", array("#","/"));

		if(isset($domaine)) $url = $GLOBALS['home'] . ltrim($url, "/");
	}	

	// Si filtre ou page
	if($dir) $url = trim($url, "/") . $dir;

	// Si on demande le chemin absolu
	if(isset($absolu)) $url = $GLOBALS['path'] . $url;

	return $url;
}

// Navigation par page
function page($num_total, $page, $full = false)
{
	global $num_pp, $res;

	?>
	<ul class="page unstyled inbl man pan">		
	<?if($num_total > $num_pp)
	{
		$num_page = ceil($num_total/$num_pp);
		
		// Page 1
		?><li class="fl mrs"><a href="<?=make_url($res['url'], array_merge($GLOBALS['filter'], array("page" => "1", "domaine" => true)))?>" class="bt<?if($page == 1) echo" selected";?>">1</a></li><?

		if($num_page > 10 and $page >= 10 and !$full)// + de 10 page
		{
			?><li class="fl mrs mtt">...</li><?
			
			for($i = ($page - 1); $i <= ($page + 1) and $i < $num_page; $i++){?>
				<li class="fl mrs"><a href="<?=make_url($res['url'], array_merge($GLOBALS['filter'], array("page" => $i, "domaine" => true)))?>" class="bt<?if($page == $i) echo" selected";?>"><?=$i?></a></li>
			<?}
		}
		else// - de 10 page
		{
			for($i = 2; $i <= ($full?$num_page:10) and $i < $num_page; $i++){?>
				<li class="fl mrs"><a href="<?=make_url($res['url'], array_merge($GLOBALS['filter'], array("page" => $i, "domaine" => true)))?>" class="bt<?if($page == $i) echo" selected";?>"><?=$i?></a></li>
			<?}
		}

		if($num_page > 10 and $page < ($num_page - 2) and !$full) {?><li class="fl mrs">...</li><?}

		// Page final
		?><li class="fl mrs"><a href="<?=make_url($res['url'], array_merge($GLOBALS['filter'], array('page' => $num_page, "domaine" => true)))?>" class="bt<?if($page == $num_page) echo" selected";?>"><?=$num_page?></a></li><?

	}?>
	</ul>
	<?
}



/********** LANGUAGE **********/

// Sélectionne la langue
function get_lang($lang = '')
{		
	// Si la langue est déjà dans la session
	if(isset($_SESSION['lang'])) {
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

	return encode($lang);
}

// Charge une traduction
function load_translation($id)
{	
	switch ($id) {
		case "api": $translation_file = "api/translation.php"; break;
		case "theme": $translation_file = "theme/".$GLOBALS['theme'].($GLOBALS['theme']?"/":"")."translation.php"; break;
		default: $translation_file = "plugin/".$id."/translation.php"; break;
	}

	// On récupère le fichier de traduction
	@include($_SERVER['DOCUMENT_ROOT'].$GLOBALS['path'].$translation_file);

	// Ajoute la traduction au tableau des traductions
	if(isset($add_translation)) add_translation($add_translation);
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
function __($singulier, $pluriel = "", $num = 0)
{
	// Traduction direct dans la fonction
	if(is_array($singulier)) 
	{
		if(isset($singulier[key($singulier)][$GLOBALS['lang']]))// Une traduction dans la langue courante
			return $singulier[key($singulier)][$GLOBALS['lang']];
		else
			return key($singulier);//utf8_encode
	}
	else
	{
		if($num > 1) $txt = $pluriel; else $txt = $singulier;
		
		// Si une traduction existe
		if(isset($GLOBALS['translation'][mb_strtolower($txt)][$GLOBALS['lang']]))
			return $GLOBALS['translation'][mb_strtolower($txt)][$GLOBALS['lang']];
		else
			return $txt;//utf8_encode
	}
}

// Affichage d'une traduction
function _e($singulier, $pluriel = "", $num = 0)
{
	echo __($singulier, $pluriel, $num);
}



/********** CONTENT **********/

// Contenu texte
function txt($key = null, $filter = array())
{
	$key = ($key ? $key : "txt-".$GLOBALS['editkey']);

	// S'il y a une valeur pour le filter mais que != tableau => c'est une class
	if(!is_array($filter)) $filter = array("class" => $filter);

	// Si contenu global on rapatri le contenu depuis la table méta (Anciennement "universel")
	if(isset($filter['global']))
	{
		$sel = $GLOBALS['connect']->query("SELECT * FROM ". $GLOBALS['table_meta']." WHERE type='global' AND cle='".encode($key)."' LIMIT 1");
		$res = $sel->fetch_assoc();

		$GLOBALS['content'][$key] = $res['val'];
	}

	echo"<".(isset($filter['tag'])? $filter['tag'] : "div");

		echo" id='".encode($key)."'";

		echo" class='";
		if(isset($filter['editable'])) echo $filter['editable']; else echo"editable";
		if(isset($filter['class'])) echo" ".$filter['class'];
		if(isset($filter['global'])) echo" global";
		echo"'";

		if(isset($filter['placeholder'])) echo" placeholder=\"".$filter['placeholder']."\"";

		if(isset($filter['itemprop'])) echo" itemprop=\"".$filter['itemprop']."\"";

		if(isset($filter['dir'])) echo" data-dir='".$filter['dir']."'";// Desitation de stockage du fichier

	echo">";

	if(isset($GLOBALS['content'][$key])) {
		if(isset($filter['function'])) echo $filter['function']($GLOBALS['content'][$key]);
		else echo $GLOBALS['content'][$key];
	}
	elseif(isset($filter['default'])) echo $filter['default'];

	echo"</".(isset($filter['tag']) ? $filter['tag'] : "div").">";

	$GLOBALS['editkey']++;
}


// Fonction raccourcie
function h1($key = null, $filter = array())
{
	// S'il y a une valeur pour le filter mais que != tableau => c'est une class
	if(!is_array($filter)) $filter = array('class' => $filter);
	
	$filter['tag'] = __FUNCTION__;// Force le tag
	
	txt($key, $filter);// Appel de la fonction d'origine
}
function h2($key = null, $filter = array())
{
	// S'il y a une valeur pour le filter mais que != tableau => c'est une class
	if(!is_array($filter)) $filter = array('class' => $filter);
	
	$filter['tag'] = __FUNCTION__;// Force le tag
	
	txt($key, $filter);// Appel de la fonction d'origine
}
function h3($key = null, $filter = array())
{
	// S'il y a une valeur pour le filter mais que != tableau => c'est une class
	if(!is_array($filter)) $filter = array('class' => $filter);
	
	$filter['tag'] = __FUNCTION__;// Force le tag
	
	txt($key, $filter);// Appel de la fonction d'origine
}
function span($key = null, $filter = array())
{
	// S'il y a une valeur pour le filter mais que != tableau => c'est une class
	if(!is_array($filter)) $filter = array('class' => $filter);
	
	$filter['tag'] = __FUNCTION__;// Force le tag
	
	txt($key, $filter);// Appel de la fonction d'origine
}



// Contenu image/fichier
function media($key = null, $filter = array())
{
	$key = ($key ? $key : "file-".$GLOBALS['editkey']);

	// Si contenu global on rapatri le contenu depuis la table méta
	if(isset($filter['global']))
	{
		$sel = $GLOBALS['connect']->query("SELECT * FROM ". $GLOBALS['table_meta']." WHERE type='global' AND cle='".encode($key)."' LIMIT 1");
		$res = $sel->fetch_assoc();

		$GLOBALS['content'][$key] = $res['val'];
	}

	// S'il y a une valeur pour le filter mais != tableau => c'est la taille de l'image
	if(!is_array($filter)) $filter = array("size" => $filter);

	// Une taille est définie
	if(isset($filter['size'])) $size = explode("x", $filter['size']);

	// Nom du fichier
	if(isset($GLOBALS['content'][$key]) and $GLOBALS['content'][$key]!="") 
		$filename = $GLOBALS['home'].ltrim($GLOBALS['content'][$key], @$GLOBALS['replace_path']);
	else
		$filename = "";

	if($filename) 
	{
		// Extention du fichier
		$ext = pathinfo(explode("?", $filename)[0], PATHINFO_EXTENSION);

		// Recherche du type de fichier
		switch($ext)
		{	
			case"jpg": 
			case"jpeg":  
			case"png": 
			case"gif": 
				$img = true; 
			break;

			default: $fa = "doc"; break;

			case"zip": $fa = "file-archive"; break;
			case"msword": $fa = "file-word"; break;
			case"vnd.ms-excel": $fa = "file-excel"; break;
			case"vnd.ms-powerpoint": $fa = "file-powerpoint"; break;
			case"pdf": $fa = "file-pdf"; break;
		}
	}

	echo"<span";

		echo" id='".encode($key)."'";

		echo" class='";
		if(isset($filter['editable'])) echo $filter['editable']; else echo"editable-media";
		if(isset($filter['global'])) echo" global";
		//if(isset($size[0]) and isset($size[1])) echo" crop";
		if(isset($filter['crop'])) echo" crop";
		echo"'";

		if(isset($filter['class'])) echo" data-class='".$filter['class']."'";
		if(isset($filter['dir'])) echo" data-dir='".$filter['dir']."'";// Desitation de stockage du fichier
		if(isset($size[0])) echo" data-width='".$size[0]."'";
		if(isset($size[1])) echo" data-height='".$size[1]."'";
		if(isset($size[0]) or isset($size[1])) // @todo Vérifier si on met max-width ou width
			echo" style='".
			(isset($size[0])?'max-width:'.$size[0].'px;':'').
			(isset($size[1])?'max-height:'.$size[1].'px':'').
			"'";

		if(isset($filter['placeholder'])) echo" placeholder=\"".$filter['placeholder']."\"";

	echo">";

		if(isset($img))// C'est une image
		{
			// Si on veux voir la version grande au clic
			if(isset($filter['zoom'])) {
				$parse_url = parse_url($filename);
				parse_str($parse_url['query'], $get);
				if(@$get['zoom']) echo'<a href="'.$get['zoom'].'">';
			}

				echo"<img ";

				if(isset($filter['lazy']))
					echo"data-lazy=\"".$filename."\"";
				else 
					echo"src=\"".$filename."\"";

				if(isset($size[0]) or isset($size[1])) {
					echo" style='";
					if(isset($size[0])) echo"max-width: ".$size[0]."px;";
					if(isset($size[1])) echo"max-height: ".$size[1]."px;";
					echo"'";
				}

				// On met en data l'url de la version grande
				if(isset($filter['data-zoom'])) echo" data-zoom='".@$filter['data-zoom']."'";

				if(isset($filter['itemprop'])) echo" itemprop='".@$filter['itemprop']."'";

				// Image map
				if(isset($filter['usemap'])) echo" usemap='".@$filter['usemap']."'";

				// Texte ALT
				if(isset($GLOBALS['content'][$key.'-alt'])) echo' alt="'.$GLOBALS['content'][$key.'-alt'].'"';
				else echo' alt=""';

				echo" class='";					
					if(isset($filter['zoom'])) echo" zoom";
					if(isset($filter['class'])) echo" ".$filter['class'];
				echo"'>";

			// Fin lien zoom
			if(isset($filter['zoom'])) echo"</a>";
		}
		elseif($filename) // C'est un fichier
			echo"<a href=\"".$GLOBALS['content'][$key]."\" target='_blank'><i class='fa fa-fw fa-".$fa." mega' title=\"".$GLOBALS['content'][$key]."\"></i></a>";

	echo"</span>";

	$GLOBALS['editkey']++;
}

// Image de fond de bloc
function bg($key = null, $filter = array())
{
	$key = ($key ? $key : "bg-".$GLOBALS['editkey']);

	// Si contenu global on rapatri le contenu depuis la table méta
	if(isset($filter['global']))
	{
		$sel = $GLOBALS['connect']->query("SELECT * FROM ". $GLOBALS['table_meta']." WHERE type='global' AND cle='".encode($key)."' LIMIT 1");
		$res = $sel->fetch_assoc();

		$GLOBALS['content'][$key] = $res['val'];
	}
	
	// Si pas d'array et qu'il y a une variable c'est que c'est un lazyload
	if(!is_array($filter) and isset($filter)) $filter = array("lazy" => true);

	$url = (isset($GLOBALS['content'][$key]) ? $GLOBALS['home'].$GLOBALS['content'][$key] : "");

	echo" data-id='".encode($key)."' data-bg=\"".$url."\"";

	if(isset($filter['global'])) echo" data-global='true'";

	if(isset($filter['dir'])) echo" data-dir='".$filter['dir']."'";// Desitation de stockage du fichier

	// Si lazy load des images de fond
	if(isset($filter['lazy']))
		echo' data-lazy="bg"';
	else if($url)
		echo' style="background-image: url(\''.$url.'\')"';

	$GLOBALS['editkey']++;
}

// Bloc de contenu générique duplicable
function module($module = "module")
{
	// Extrait les données work du tableau des contenu
	$keys = array_keys($GLOBALS['content']);
	foreach($keys as $key)
	{
		if(preg_match("/^".$module."-/", $key) == 1)
		{
			// Récupère le denier chiffre (numéro d'occurance)
			preg_match('/(\d+)(?!.*\d)/', $key, $match);
			$num_module = @$match[1];

			// Récupère le type d'élément (txt, img, href, alt...) = dernier texte
			preg_match('/([a-z]+)(?!.*[a-z])/', $key, $match);
			$type_module = $match[1];

			// @todo supp = ancienne version qui ne marche pas avec les alt éditables
			// Supprime le préfix du nom du module en cours
			//$type_num_module = str_replace($module."-", "", $key);
			// Sépare les elements du nom du module
			//$exp_key = explode("-", $type_num_module);
			// Numéro de l'occurence du module
			//$num_module = $exp_key[(count($exp_key)-1)];
			// Nom distinctif du module (txt, img...)
			//$type_module = rtrim($type_num_module, "-".$num_module);

			// Si une variable dans la zone originale duplicable (0) on la raz par sécurité
			if($num_module == 0) $GLOBALS['content'][$key] = "";

			// Création du tableau avec les elements de modules
			$array_module[$module][$num_module][$type_module] = $GLOBALS['content'][$key];

			//echo $key." | ".$type_module."*".$num_module."*".($num_module == 0)." : ".$GLOBALS['content'][$key]."<br>";
		}
	}

	// Bloc vide pour l'ajout de nouveau élément (bloc duplicable)
	$array_module[$module][0]['titre'] = "";

	// Re-init le tableau
	reset($array_module[$module]);

	return $array_module[$module];
}

// Contenu champ checkbox
function checkbox($key = null, $filter = array())
{
	$key = ($key ? $key : "checkbox-".$GLOBALS['editkey']);

	// fa-check/fa-close => fa-ok/fa-cancel
	echo"<i class='".(isset($filter['editable'])?$filter['editable']:"editable-checkbox")." fa fa-fw ".((isset($GLOBALS['content'][$key]) and $GLOBALS['content'][$key] == true) ? "fa-ok yes" : "fa-cancel no") . (isset($filter['class'])?" ".$filter['class']:"")."' id='".encode($key)."'></i>";
	
	$GLOBALS['editkey']++;
}

// Contenu champ select
function select($key = null, $filter = array())
{
	$key = ($key ? $key : "select-".$GLOBALS['editkey']);

	if(!is_array($filter)) $filter = array("option" => $filter);

	$option_decode = json_decode($filter['option'], true);

	// inverse les clés et les valeurs
	if(@$filter['flip']) {
		$option_decode = array_flip($option_decode);
		$filter['option'] = json_encode($option_decode, JSON_UNESCAPED_UNICODE);
	}

	if(isset($GLOBALS['content'][$key]) and isset($option_decode[$GLOBALS['content'][$key]])) {
		$selected_key = $GLOBALS['content'][$key];
		$selected_option = $option_decode[$GLOBALS['content'][$key]];
	}
	else {
		$selected_key = key($option_decode);
		if($selected_key) $selected_option = $option_decode[$selected_key];
	}

	echo"<span id='".encode($key)."' class='".(isset($filter['editable'])?$filter['editable']:"editable-select") . (isset($filter['class'])?" ".$filter['class']:"")."' data-option='".str_ireplace("'",  "&apos;", $filter['option'])."' data-selected=\"".$selected_key."\">".@$selected_option."</span>";
	
	$GLOBALS['editkey']++;
}

// Contenu champ input
function input($key = null, $filter = null)
{
	$key = ($key ? $key : "input-".$GLOBALS['editkey']);

	if(!is_array($filter)) $filter = array("class" => $filter);
	if(!isset($filter['type'])) $filter['type'] = "text";

	echo'<input type="'.$filter['type'].'" id="'.encode($key).'"';


	echo' value="';

	if(isset($GLOBALS['content'][$key])) 
		if(@$filter['type'] == 'number') echo str_replace(',', '.', $GLOBALS['content'][$key]);
		else echo $GLOBALS['content'][$key]; 
	else 
		echo @$filter['default'];
	
	echo'"';


	echo' class="editable-input '.@$filter['class'].'"';

	if($filter['type'] == "checkbox" and @$GLOBALS['content'][$key] == true) echo' checked="checked"';

	if(isset($filter['placeholder'])) echo' placeholder="'.$filter['placeholder'].'"';
	
	if(@$filter['readonly']) echo' readonly';

	echo'>';

	// Si autocomplete
	if(isset($filter['autocomplete'])) {?>
		<script>
			edit.push(function() {	
				$("#<?echo encode($key)?>").autocomplete({
					source: <?echo'["'.implode('","', $filter['autocomplete']).'"]';?>,
					minLength: 0,
				}).focus(function () {
					$(this).autocomplete("search");
				});
			});
		</script>
	<?}
	
	$GLOBALS['editkey']++;
}

// Lien éditable
function href($key = null, $target = null)
{
	$key = ($key ? $key : "href-".$GLOBALS['editkey']);

	echo'href="'.(isset($GLOBALS['content'][$key]) ? $GLOBALS['content'][$key] : "").'" data-href="'.encode($key).'"';

	if($target == 'file' and strstr(@$GLOBALS['content'][$key], ".")) echo' target="_blank"';

	$GLOBALS['editkey']++;
}

// Tags
function tag($key = null, $filter = array())
{
	$key = encode($key ? $key : "tag");

	echo'<'
	.(isset($filter['tag'])?$filter['tag']:"div")
	.' id="'.$key.'" class="editable-tag'.(isset($filter['class'])?" ".$filter['class'] : '').'"'
	.(isset($filter['placeholder'])?' placeholder="'.$filter['placeholder'].'"' : '')
	.(isset($filter['separator'])?' data-separator="'.$filter['separator'].'"' : '')
	.(isset($filter['itemprop'])?' itemprop="'.$filter['itemprop'].'"' : '')
	.'>';

		$i = 1;
		//$sel_tag = $GLOBALS['connect']->query("SELECT * FROM ".$GLOBALS['table_meta']." WHERE id='".(int)$GLOBALS['id']."' AND type='tag' ORDER BY ordre ASC LIMIT 10");// SUPP APRES TEST SUR LA NOUVELLE TABLE TAG
		$sel_tag = $GLOBALS['connect']->query("SELECT * FROM ".$GLOBALS['table_tag']." WHERE id='".(int)$GLOBALS['id']."' AND zone='".$key."' ORDER BY ordre ASC LIMIT 10");
		while($res_tag = $sel_tag->fetch_assoc()) 
		{ 
			$GLOBALS['tags'][$res_tag['encode']] = $res_tag['name'];

			if($i > 1) echo (@$filter['separator']?$filter['separator']:', ');
			echo'<a href="'.make_url($key, array($res_tag['encode'], 'domaine' => true)).'" class="tdn">'.$res_tag['name'].'</a>';
			$i++;
		}

	echo'</'.(isset($filter['tag']) ? $filter['tag'] : "div").'>';
}



/********** SÉCURISATION **********/

function secure_value($value) {

	// htmlentities htmlspecialchars
	if(is_array($value)) {
		//while(list($cle, $val) = each($value)) PHP 7.2
		foreach($value as $cle => $val) $value[$cle] = trim(htmlspecialchars($val, ENT_QUOTES));
	}
	else $value = trim(htmlspecialchars($value, ENT_QUOTES));

	return $value;
}



/********** CONNEXION **********/

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

// Retourne l'adresse IP du client (utilisé pour empêcher le détournement de cookie de session)
function ip()
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
	if(isset($_POST['rememberme'])) {
		setcookie("rememberme", encode($_POST['rememberme']), 0, $GLOBALS['path'], $GLOBALS['domain']);
		$_COOKIE['rememberme'] = encode($_POST['rememberme']);
	}

	// Date d'expiration (si on ne mémorise pas l'utilisateur on crée une session de 30min
	$time = time() + ((isset($_COOKIE['rememberme']) and $_COOKIE['rememberme'] == "false") ? (30*60) : $GLOBALS['session_expiration']);

	// Id de l'utilisateur
	$_SESSION['uid'] = (int)$uid;
	
	// Nom de l'utilisateur
	if($email) $_SESSION['email'] = $email;

	// Cookie+Session pour connaitre les autorisations utilisateur
	if($auth) {
		$array_auth = explode(",", $auth);
		foreach($array_auth as $cle => $val) { $_SESSION['auth'][$val] = true; }
		setcookie("auth", encode($auth, ",", array("-")), $time, $GLOBALS['path'], $GLOBALS['domain']);
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
	// @todo verif si ce n'est pas ça qui crée un bug collatéral de perte de session
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
	if(isset($_SESSION['nonce']) and $_SESSION['nonce'] != @$_REQUEST['nonce']) $csrf = true;
	
	// Pas de hack on vérifie l'utilisateur
	if(!$csrf) 
	{
		// On se log avec le formulaire donc on check password & mail
		if(isset($_POST['email']) and isset($_POST['password']))
		{
			if(!isset($GLOBALS['connect'])) include_once("db.php");// Connexion à la db

			// Supprime l'ancienne session
			@session_regenerate_id(true);

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
				else {
					$msg = __("Password error");
					logout();
				} 
			}
			else {
				$msg = __("Unknown user");
				logout();
			}
		}
		// Sinon on vérifie la validité du token et s'il n'a pas expiré
		elseif(isset($_SESSION['token']))
		{			
			if($level == 'medium' and $GLOBALS['security'] != 'high')// Vérification mode moyen
			{
				if(!token_check($_SESSION['token']))// Vérification du contenu du token
				{
					$msg = __("Token error");
					logout();
				}
				else if(isset($auth))// Vérifie les autorisations utilisateur dans la bdd si c'est demandée
				{
					if(!isset($GLOBALS['connect'])) include_once("db.php");// Connexion à la db

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
				if(!isset($GLOBALS['connect'])) include_once("db.php");// Connexion à la db

				@session_regenerate_id(true);// Supprime l'ancienne session

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
					$msg = __("Connection error");
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
		<link rel="stylesheet" href="<?=$GLOBALS['jquery_ui_css']?>">

		<link rel="stylesheet" href="<?=$GLOBALS['path']?>api/lucide.css">

		<script>
			// Ouverture de la dialog de connexion
			$(function()
			{
				//$(".ui-dialog-content").dialog("close");// On ferme les dialogs en cours

				if(typeof tosave == 'function') tosave();// Mode : A sauvegarder
				
				// Chargement de Jquery UI
				$.ajax({
			        url: "<?=$GLOBALS['jquery_ui']?>",
			        dataType: 'script',
			        cache: true,
					success: function()// Si Jquery UI bien charger on charge la dialog de choix de login
					{ 						
						// On ferme la dialog de connexion s'il y en a une d'ouvert
						if($("#dialog-connect").length) $("#dialog-connect").dialog("close");
						
						// On ouvre la dialog de choix du système de login et affiche une erreur
						$.ajax({
							url: "<?=$GLOBALS['path']?>api/ajax.php?mode=internal-login", 
							data: {
								callback: "<?=(isset($_REQUEST['callback']) ? encode($_REQUEST['callback'], "_") : "")?>",
								msg: "<?=htmlspecialchars((isset($msg) ? $msg : ""));?>"
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
					},
			        async: true
			    });		

				
			});
		</script>
		<?
		exit;
	}
	elseif ($quiet == 'error') {?><script>$(function() {error("<?=$msg?>", 4000); });</script><?}
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

// Verifie que le fichier et supporter et pas de hack
function file_check($file)
{
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$file_infos['mime'] = finfo_file($finfo, $_FILES[$file]['tmp_name']);
	finfo_close($finfo);

	// Vérifie que le type mime est supporté (Hack protection : contre les mauvais mimes types) 
	if(in_array($file_infos['mime'], $GLOBALS['mime_supported'])) 
	{
		// Le fichier tmp ne contient pas de php ou de javascript
		if(!preg_match("/<\?php|<scr/", file_get_contents($_FILES[$file]['tmp_name']))) 
			return true;
		else 
			return false;
	}
	else return false;
}

// Redimentionne une image
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
	$root_dir = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['path'];
	$file_name = encode(basename(basename($source_file), "." . $source_ext));

	// Dossier final d'image redimensionnée
	$dir = ($dest_dir ? $dest_dir.'/' : '');

	// dir clean si media forcé
	$dir_clean = ltrim(str_replace('media', '', $dir), '/');

	// Si image à réduire ou à forcer
	if(($new_width and $source_width > $new_width) or ($new_height and $source_height > $new_height) or $option)
	{
		// Version original pour le zoom
		$zoom = 'media/' . $dir_clean . $file_name . '.' . $source_ext;

		// Si media dans dir on force ne met pas dans /resize/
		$dir = (strpos($dir, 'media') === 0 ? '' : 'media/resize/') . $dir;

		// Crée les dossiers
		@mkdir($root_dir . $dir, 0705, true);

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
					$y = ($new_height - $dest_height) / 3;// Paramètre pour callé en hauteur le crop (2 à l'origine)
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
		if(isset($deg)) $final_img = imagerotate($final_img, $deg, 0);
		
		// Ajoute la taille de la nouvelle image en supprimant l'ancienne si besoin
		preg_match("/(-[0-9]+x[0-9]+)$/", $file_name, $matches);
		if(isset($matches[0])) $file_name = str_replace($matches[0], "", $file_name);
		$file_name_ext = $file_name."-".round($new_width)."x".round($new_height).".".$source_ext;

		// Création de l'image finale dans le bon type		
		switch($type) {
			case 1: imagegif($final_img, $root_dir . $dir . $file_name_ext); break;
			case 2: imagejpeg($final_img, $root_dir . $dir . $file_name_ext, $GLOBALS['jpg_quality']); break;
			case 3: imagepng($final_img, $root_dir . $dir . $file_name_ext); break;// $GLOBALS['png_quality']
		}		
		
		imagedestroy($final_img);// Libère la mémoire	
	}
	else// Copie l'image si elle est plus petite ou à la bonne taille
	{
		$zoom = "";// Pas de zoom

		$dir = "media/" . $dir_clean;// @todo ajouter le dir (sans resize)
		$file_name_ext = $file_name.".".$source_ext;
		
		@mkdir($root_dir . $dir, 0705, true);// Crée les dossiers

		copy($source_file, $root_dir . $dir . $file_name_ext);
	}

	return $dir . $file_name_ext . "?" . ($zoom?"zoom=".$zoom."&":"") . time();// Time pour forcer le refresh
}

// Examine et traite une image
function img_process($root_file, $dest_dir = null, $new_width = null, $new_height = null, $resize = null)
{
	// Valeur par défaut
	$option = null;
	$dir = ($dest_dir ? 'media/'.$dest_dir : 'media');
	$src_file = $dir.'/'.basename($root_file).'?'.time();

	// Taille de l'image uploadée
	list($source_width, $source_height, $type) = getimagesize($root_file);
	
	// Limite max de taille d'image pour l'upload global
	list($max_width, $max_height) = explode("x", $GLOBALS['max_image_size']);
	
	// On vérifie la bonne orientation de l'image jpeg
	if($type == 2) {// Exif ne fonctionne qu'avec les jpeg
		$exif = @exif_read_data($root_file);
		if(isset($exif['Orientation']) and $exif['Orientation'] != 1) {
			$max_width = ($source_width > $max_width ? $max_width : $source_width);
			$max_height = ($source_height > $max_height ? $max_height : $source_height);
			$option = $exif['Orientation'];
		}
	}

	// Image trop grande (> global) pour le web : on la redimensionne
	if($source_width > $max_width or $source_height > $max_height or $option) 
	{
		// Redimensionne sans crop
		$src_file = resize($root_file, $max_width, $max_height, $dir, $option);

		// Supprime l'image originale puisque l'on ne garde que la maxsize
		unlink($root_file);

		// La maxsize devient l'image root (explode: supp le timer)
		$root_file = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['path'].explode("?", $src_file)[0];
	}
	

	// L'interface a demandé un redimensionnement ?
	if($resize and (($new_width and $source_width > $new_width) or ($new_height and $source_height > $new_height)))
	{
		return resize($root_file, $new_width, $new_height, $dest_dir, $resize);// Redimensionne

		//unlink($root_file);// Si on a redimensionné on supp l'image de base
	}
	else// Pas de redimensionnement
	{
		$dest_file = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['path'].explode("?", $src_file)[0];

		// Le fichier destination est demandé dans un endroit different du fichier source
		if($root_file != $dest_file)
		{
			// Si dossier destination on copie l'image dans la destination
			copy($root_file, $dest_file);

			// Et on supprime l'image source
			unlink($root_file);
		}

		// Retourne l'url du fichier
		return $src_file;
	}
}



/********** TEXTE **********/

// Coupe une phrase proprement
function word_cut($texte, $limit, $end = '', $tags = '') {//$tags = '<br><div>'  $end = '...'
	$texte = strip_tags($texte.' ', $tags);// texte sans html
	$word_cut = preg_replace('/\s+?(\S+)?$/u', '', substr($texte, 0, $limit));// /u > pour l'utf8
	if(strlen($word_cut) < strlen(trim($texte))) $word_cut .= $end;// Si coupure on ajoute une ponctuation à la fin
	return $word_cut;
}



/********** FONCTION DU THEME **********/
if(isset($GLOBALS['function']) and $GLOBALS['function'] != "") 
	include_once($_SERVER["DOCUMENT_ROOT"].$GLOBALS['path']."theme/".$GLOBALS['theme'].($GLOBALS['theme']?"/":"").$GLOBALS['function']);

?>