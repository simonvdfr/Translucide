<?php if(!$GLOBALS['domain']) exit; ?>

<?php include('theme/'.$GLOBALS['theme'].'/mdl/hero-article.php');?>

<section id="breadcrumb" class="sm:flex align-items-center justify-between text-center px-36 py-16">
	<?php include('theme/'.$GLOBALS['theme'].'/mdl/breadcrumb.php');?>
	<?php include('theme/'.$GLOBALS['theme'].'/mdl/form-search.php');?>
</section>

<section id="post" class="p-36">

	<div class="md:flex flex-row flex-no-wrap justify-content ">
		<!-- Contenu de l'article -->
		<article class="post-content md:mr-36">

			<!-- Image -->
			<div class="post-img">
				<!-- Date événement -->
				<?php
				if(stristr($res['tpl'], 'event'))
				{
					if(@$GLOBALS["content"]["aaaa-mm-jj"])
					{
						echo '<div class="post-event"><p class="text-bold color-blue mb-0"> Le ';
						if($lang == 'eu') echo str_replace('-', '/', $GLOBALS["content"]["aaaa-mm-jj"]);
						else echo date_lang($GLOBALS["content"]["aaaa-mm-jj"]);

						if(@$GLOBALS["content"]["start-time"]){
							echo ' '.__("de").' '.date_format(date_create($GLOBALS["content"]["start-time"]), 'H:i');
						}

						if(@$GLOBALS["content"]["end-time"]){
							echo ' '.__("à").' '.date_format(date_create($GLOBALS["content"]["end-time"]), 'H:i');
						}

						echo '</p></div>';
					}

					input("aaaa-mm-jj", array("type" => "hidden", "autocomplete" => "off", "class" => "meta text-center"));

					if(stristr($res['tpl'], 'event'))
					{
						input('start-time', array("type" => "hidden", "autocomplete" => "off", "class" => "meta text-center"));
						input('end-time', array("type" => "hidden", "autocomplete" => "off", "class" => "meta text-center"));
					}
				}
			?>
			<?php media('img-event', array('class' => 'mt-36', 'lazy' => true)); ?>

			</div>

			<div class="post-text mt-36">
				<?php txt('texte')?>
			</div>

			<?php if($res['tpl'] == "event-form") include 'contact.php';?>

			<!-- Tags -->
			<div class="entry entry-tag mt-36 py-36">
				<h4 class="float-left mr-8"><?php _e("Tags")?> :</h4>
				<?php tag('tags')?>
			</div>
			<script>
			if(!$(".editable-tag").text()) $("#tags").prev("h3").hide();
			else $("#tags").addClass("mb-24");
			</script>

		</article>

		<?php include('theme/'.$GLOBALS['theme'].'/mdl/sidebar.php');?>

	</div>

</section>

<script>
$(function()
{
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
});
</script>
