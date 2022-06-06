<?php if(!$GLOBALS['domain']) exit;?>

<?php include('theme/'.$GLOBALS['theme'].'/mdl/hero.php');?>

<section id="breadcrumb" class="flex justify-center py-16">
	<?php include('theme/'.$GLOBALS['theme'].'/mdl/breadcrumb.php');?>
</section>

<section id="article-list" class="p-36">

	<div class="article-filter">
		<?php
		// Liste les tags pour filtrer la page
		$i = 1;
		$sel_tag_list = $connect->query("SELECT distinct encode, name FROM ".$table_tag." WHERE zone='".$res['url']."' ORDER BY ordre ASC, encode ASC");
		//echo $connect->error;

		if($sel_tag_list->num_rows) _e("Tags : ");

		while($res_tag_list = $sel_tag_list->fetch_assoc()) {
			if($i > 1) echo', ';
			echo'<a href="'.make_url($res['url'], array($res_tag_list['encode'], 'domaine' => true)).'" class="color-blue no-decoration dash">'.$res_tag_list['name'].'</a>';
			$i++;
		}
		?>
	</div>

	<nav role="navigation" aria-label="<?php _e("Filter by")?>" class="flex wrap space jcc tc ptl pbm">
		<ul class="unstyled pln"><?php
			// Liste les tags pour filtrer la page
			$i = 1;
			$sel_tag_list = $connect->query("SELECT distinct encode, name FROM ".$table_tag." WHERE zone='".$res['url']."' GROUP BY encode, name ORDER BY encode ASC");
			//echo $connect->error;

			while($res_tag_list = $sel_tag_list->fetch_assoc()) {
				echo'<li class="inline prs"><a href="'.make_url($res['url'], array($res_tag_list['encode'], 'domaine' => true)).'" class="bt-tag">'.$res_tag_list['name'].'</a></li>';
				$i++;
			}
			?>
		</ul>
	</nav>

	<div class="article-list sm:grid md:grid-cols-2 lg:grid-cols-3 gap-36 sm:mx-20 mx-8 py-36 animation delay-1 fade-in">
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
		".$tt.".zone = 'tags' AND
		".$tt.".encode = '".$tag."'
	)";

	$sql.=" WHERE ".$tc.".lang='".$lang."' ".$sql_state." AND";

		$sql.=" ".$tc.".type='article'";

		$sql.=" LIMIT ".$start.", ".$num_pp;

	$sel_article = $connect->query($sql);

	$num_total = $connect->query("SELECT FOUND_ROWS()")->fetch_row()[0];// Nombre total d'articles

	while($res_article = $sel_article->fetch_assoc())
	{
		// Affichage du message pour dire si l'article est invisible ou pas
		if($res_article['state'] != "active") $state = " <span class='deactivate p-8'>".__("Article d&eacute;sactiv&eacute;")."</span>";
		else $state = "";

		$content_article = json_decode($res_article['content'], true);

		$date = explode("-", explode(" ", $res_article['date_insert'])[0]);
		?>

		<div class="article-card shadow border-rounded my-16 md:my-24">

			<article>

				<div class="article-post-content">
					<div class="article-post-title">
						<h3 class="mb-0 no-decoration"><a href="<?=make_url($res_article['url'], array("domaine" => true));?>" class="no-decoration"><?=$res_article['title']?></a><?=$state?></h3>
					</div>
					<div class="article-post-img mt-24 mb-36">
						<div class="entry-img">
							<a href="<?=make_url($res_article['url']);?>" class="no-decoration">
								<!-- Chercher variable pour appeler la photo publiée dans l'article !-->
								<img src="<?=(isset(parse_url(@$content_article['img-article'])['scheme'])?'':$GLOBALS['home']).@$content_article['img-article']; ?>">
							</a>
						</div>
						<div class="entry-date">
							<span class="day"><?=$date[2]?></span>
							<span class="month"><?=trim(utf8_encode(strftime("%h", mktime(0, 0, 0, $date[1], 10))),".")?></span>
							<span class="year"><?=$date[0]?></span>
						</div>
					</div>
				</div>
				<?php if(isset($content_article['texte'])) echo word_cut($content_article['texte'], '180')."...";?>
				<div class="entry-more mt-24">
					<a href="<?=make_url($res_article['url'], array("domaine" => true));?>" class="btn btn--line border-rounded text-bold no-decoration"><?php _e("Lire l'article")?> <i class="icon moon-arrow-right no-decoration ml-8"></i></a>
				</div>

			</article>
		</div>
		<?php
	}
	page($num_total, $page);
	?>
	</div>
</section>
