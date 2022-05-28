<? if(!$GLOBALS['domain']) exit;
function highlight($txt, $search)
{
	return $txt;
}
?>

<?php include('theme/'.$GLOBALS['theme'].'/mdl/hero.php');?>
<?php include('theme/'.$GLOBALS['theme'].'/mdl/breadcrumb.php');?>

<section id="page" class="layout-maxed">

	<div class="page-content px-16 py-36">
		<?php

		// Traitement des mots-clés de recherche
		if(!@$_POST['search'] and $GLOBALS['filter'] and key($GLOBALS['filter']) != 'page')// GET (filter)
		{
			$_POST['search'] = strip_tags(str_replace("-", " " , array_keys($GLOBALS['filter'])[0]));
		}
		else if(@$_POST['search'])// POST (sumbit form)
		{
			// Pour l'url
			$GLOBALS['filter'][] = encode(strip_tags(@$_POST['search']));

			// Pour l'affichage et garder les accents avec la nav par page
			$_SESSION['search'] = $_POST['search'] = strip_tags(@$_POST['search']);
		}
		else
		{
			// Page de recherche sans mots-clés
			$_SESSION['search'] = $_POST['search'] = '';
		}


		// Si on n'a pas les droits d'édition des articles on affiche uniquement ceux actifs
		if(!@$_SESSION['auth']['edit-article']) $sql_state = "AND state='active'";
		else $sql_state = "";

		// Navigation par page
		$num_pp = 10;

		if(isset($GLOBALS['filter']['page'])) $page = (int)$GLOBALS['filter']['page']; else $page = 1;

		$start = ($page * $num_pp) - $num_pp;


		// Construction de la requête
		$sql ="SELECT SQL_CALC_FOUND_ROWS ".$tc.".id, ".$tc.".* FROM ".$tc;


		$sql.=" WHERE url!='search' AND lang='".$lang."' ".$sql_state." ";


		if(@$_POST['search'] or $GLOBALS['filter'])
		{
			// Si plusieurs arguments
			$sql .= "AND (";
			$searches = explode(" ", @$_POST['search']);
			foreach($searches as $cle => $val)
			{
				$search = trim($connect->real_escape_string($val));

				if($cle > 0) $sql.= " AND ";

				$sql.= "(title LIKE '%".$search."%' OR content LIKE '%".$search."%')";
			}
			$sql .= ")";
		}


		$sql.=" ORDER BY ".$tc.".date_insert DESC
		LIMIT ".$start.", ".$num_pp;


		//echo $sql;
		//echo"<br>filter after : ";print_r($GLOBALS['filter']);


		$sel_fiche = $connect->query($sql);


		$num_total = $connect->query("SELECT FOUND_ROWS()")->fetch_row()[0];// Nombre total de fiche
		?>


	<p class="text-center">
		<?php
		echo $num_total.' '.__("result").($num_total>1?'s':'');
		if(@$_SESSION['search']) echo ' '.__("for")." <strong>".htmlspecialchars(@$_SESSION['search'])."</strong>";
		?>
	</p>


	<?php
	while($res_fiche = $sel_fiche->fetch_assoc())
	{
		$texte = null;

		// Affichage du message pour dire si l'article est invisible ou pas
		if($res_fiche['state'] != "active") $state = " <span class='deactivate' title=\"".__("Disabled page")."\"><i class='icon moon-eye-off' aria-hidden='true'></i></span>";
		else $state = "";

		$content_fiche = json_decode($res_fiche['content'], true);

		?>
		<article class="mt-24">

			<h2><a href="<?=make_url($res_fiche['url'], array("domaine" => true));?>" class="no-decoration"><?php echo highlight($res_fiche['title'], @$_POST['search'])?></a><?php echo $state?></h2>

			<?php
			if(isset($content_fiche['description'])) $texte = $content_fiche['description'];
			elseif(isset($content_fiche['texte'])) $texte = $content_fiche['texte'];

			if(isset($texte))
				echo '<p class="mb-0">'.highlight(word_cut($texte, '350', '...', '<br><i><div>'), @$_POST['search']).'</p>';
			?>

			<a href="<?=make_url($res_fiche['url'], array("domaine" => true));?>" class="btn btn--line btn--small border-rounded text-bold float-right mt-20 no-decoration" aria-label="<?php _e("Lire")?> <?php echo $res_fiche['title']?>"><?php _e("Lire")?></a>

		</article>
		<?php
	}?>
	</div>


	<div class="text-center mt-24"><?php page($num_total, $page, array('aria-label'=>__("Browsing by page")));?></div>


</section>
