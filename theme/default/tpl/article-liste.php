<?php  if(!$GLOBALS['domain']) exit; ?>

<style>
	.content article { border-left: 0.2em solid #35747f; }
</style>


<section class="mw960p mod center mtm mbl">


	<?php h1('title', 'mbn tc')?>


	<div class="mts tc italic">
		<?php 
		// Liste les tags pour filtrer la page
		$i = 1;
		$sel_tag_list = $connect->query("SELECT distinct encode, name FROM ".$table_tag." WHERE zone='".$res['url']."' ORDER BY ordre ASC, encode ASC");
		//echo $connect->error;

		if($sel_tag_list->num_rows) _e("Catégories : ");

		while($res_tag_list = $sel_tag_list->fetch_assoc()) {
			if($i > 1) echo', ';
			echo'<a href="'.make_url($res['url'], array($res_tag_list['encode'], 'domaine' => true)).'" class="color tdn dash">'.$res_tag_list['name'].'</a>';
			$i++;
		}
		?>
	</div>

	<div class="mod">
		<div class="fl"><?php media('img', '130')?></div>

		<div class="fl mlm"><?php txt('description')?></div>
	</div>


	<?php 
	// Si on n'a pas les droits d'édition des articles on affiche uniquement ceux actifs
	if(!@$_SESSION['auth']['edit-article']) $sql_state = "AND state='active'";
	else $sql_state = "";

	// Navigation par page
	$num_pp = 5;

	if(isset($GLOBALS['filter']['page'])) $page = (int)$GLOBALS['filter']['page']; else $page = 1;

	$start = ($page * $num_pp) - $num_pp;


	// Construction de la requete
	$sql ="SELECT SQL_CALC_FOUND_ROWS ".$tc.".id, ".$tc.".* FROM ".$tc;

	// Si filtre tag
	if(isset($tag))
	$sql.=" RIGHT JOIN ".$tt."
	ON
	(
		".$tt.".id = ".$tc.".id AND
		".$tt.".zone = 'actualites' AND
		".$tt.".encode = '".$tag."'
	)";

	$sql.=" WHERE (".$tc.".type='article') AND ".$tc.".lang='".$lang."' ".$sql_state."
	ORDER BY ".$tc.".date_insert DESC
	LIMIT ".$start.", ".$num_pp;

	$sel_fiche = $connect->query($sql);

	$num_total = $connect->query("SELECT FOUND_ROWS()")->fetch_row()[0];// Nombre total de fiche

	while($res_fiche = $sel_fiche->fetch_assoc())
	{
		// Affichage du message pour dire si l'article est invisible ou pas
		if($res_fiche['state'] != "active") $state = " <span class='deactivate pat'>".__("Article d&eacute;sactiv&eacute;")."</span>";
		else $state = "";

		$content_fiche = json_decode($res_fiche['content'], true);

		$date = explode("-", explode(" ", $res_fiche['date_insert'])[0]);
		?>
		<article class="mod plm mrm mtl mbm">

			<!--
			<div class="date fl mrm prm up bold big tc">
				<div class="bigger"><?=$date[2]?></div>
				<div><?=trim(utf8_encode(strftime("%h", mktime(0, 0, 0, $date[1], 10))),".")?></div>
			</div>
			-->

			<h2 class="mts up bigger"><a href="<?=make_url($res_fiche['url'], array("domaine" => true));?>" class="tdn"><?=$res_fiche['title']?></a><?=$state?></h2>

			<?php if(isset($content_fiche['texte'])) echo word_cut($content_fiche['texte'], '350')."...";?>

			<div class="fr mtm"><a href="<?=make_url($res_fiche['url'], array("domaine" => true));?>" class="bt bg-color bold"><?php _e("Lire l'article")?></a></div>

		</article>
		<?php 
	}

	page($num_total, $page);

?>
</section>