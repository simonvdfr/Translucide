<?php
if(!$GLOBALS['domain']) exit;
if(!@$GLOBALS['content']['titre']) $GLOBALS['content']['titre'] = $GLOBALS['content']['title'];
?>

<style>
	aside { border-left: 0.2em solid #78cfd6; }
</style>


<section class="mw960p mod center mtm mbl">

	<?php h1('titre', 'tc up')?>
	<?php h2('sstitre', 'tc')?>


	<article class="fl w80 prl pbm tj">

		<?php txt('texte')?>

		<?php if($res['tpl'] == "event-formulaire") include 'contact.php';?>

	</article>


	<aside class="fr w20 plt animation slide-right">

		<?php 
		// Date évènement
		if(stristr($res['tpl'], 'event'))
		{
			?><div class="tc mbm"><?php 
			if(@$GLOBALS["content"]["aaaa-mm-jj"])
			{
				//@todo faire une transformation de la date en une ligne au lieu du explode
				$date_debut = explode("-", $GLOBALS["content"]["aaaa-mm-jj"]);
				echo'<h3 class="big tc mtn mbt">'.__("Début de l'événement").'</h3>'.$date_debut['2'].'/'.$date_debut['1'].'/'.$date_debut['0'].'<br>';
			}

			input("aaaa-mm-jj", array("type" => "hidden", "class" => "meta tc"));

			?></div><?php 
		}?>

		<!-- Tag -->
		<div class="tc">

			<h3 class="big tc mtn mbt"><?php _e("Catégories")?></h3>

			<?php tag('actualites')?>

			<script>
			if(!$(".editable-tag").text()) $("#actualites").prev("h3").hide();
			else $("#actualites").addClass("mbm");
			</script>

		</div>


		<!-- Liste des autres articles -->
		<?php 
		$sel_article = $connect->query("SELECT * FROM ".$table_content." WHERE type='article' AND lang='".$lang."' AND state='active' AND id!='".$res['id']."' ORDER BY date_insert DESC LIMIT 0, 3");
		if($sel_article->num_rows)
		{?>
		<h3 class="big tc mtn mbt"><?php _e("Derniers Articles")?></h3>

		<ul class="unstyled pan">
		<?php 		
		while($res_article = $sel_article->fetch_assoc())
		{
			?>
			<li class="medium mbs mls"><a href="<?=make_url($res_article['url']);?>" class="tdn"><i class="fa-li fa fa-fw fa-rss fl mrt"></i> <?=$res_article['title']?></a></li>
			<?php 
		}
		?>
		</ul>
		<?php }?>


		<!-- Liste des autres évènements -->
		<?php 
		$sel_article = $connect->query("SELECT * FROM ".$table_content." WHERE type='event' AND lang='".$lang."' AND state='active' AND id!='".$res['id']."' ORDER BY date_insert DESC LIMIT 0, 3");
		if($sel_article->num_rows)
		{?>
			<h3 class="big tc ptm mbt"><?php _e("Derniers Évènements")?></h3>

			<ul class="unstyled pan">
			<?php 
			
			while($res_article = $sel_article->fetch_assoc())
			{
				?>
				<li class="medium mbs mls"><a href="<?=make_url($res_article['url']);?>" class="tdn"><i class="fa-li fa fa-fw fa-calendar-empty fl mrt"></i> <?=$res_article['title']?></a></li>
				<?php 
			}
			?>
			</ul>
		<?php }?>


	</aside>

</section>

<script>
	// Action si on lance le mode d'edition
	edit.push(function()
	{
		// DATEPIKER pour la date de l'event
		$.datepicker.setDefaults({
	        altField: "#datepicker",
	        monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
	        dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
	        dateFormat: 'yy-mm-dd',
	        firstDay: 1
	    });
		$("#aaaa-mm-jj").datepicker();
	});
</script>