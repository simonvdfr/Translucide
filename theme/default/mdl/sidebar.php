<?php if(!$GLOBALS['domain']) exit;?>


	<aside id="sidebar" class="sidebar-content justify-items-center md:py-36 animation slide-right">

		<!-- Categories -->
		<!-- A terme créer une liste de catégories sans utiliser editable-tag mais quelque chose comme editable-cat
		<div class="widget widget-cat">

			<div class="widget-title">
				<h3><?php _e("Catégories")?></h3>
			</div>

			<div class="widget-content">
				<?php tag('news')?>
			</div>

			<script>
			if(!$(".editable-tag").text()) $("#news").prev("h3").hide();
			else $("#news").addClass("mb-24");
			</script>

		</div>-->


		<!-- Liste des autres articles -->
		<?php
		$sel_article = $connect->query("SELECT * FROM ".$table_content." WHERE type='article' AND lang='".$lang."' AND state='active' AND id!='".$res['id']."' ORDER BY date_insert DESC LIMIT 0, 3");
		if($sel_article->num_rows)
		{?>
			<div class="widget widget-art">
				<div class="widget-title">
					<h3><?php _e("Last Articles")?></h3>
				</div>

				<div class="widget-content">
					<ul class="is-unstyled p-0">
					<?php
					while($res_article = $sel_article->fetch_assoc())
					{
						?>
						<li><a href="<?=make_url($res_article['url']);?>" class="no-decoration"><i class="icon-li icon moon-rss float-left mr-8"></i> <?=$res_article['title']?></a></li>
						<?php
					}
					?>
					</ul>
				</div>
			</div>
		<?php }?>

		<!-- Liste des autres évènements -->
		<?php
		$sel_article = $connect->query("SELECT * FROM ".$table_content." WHERE type='event' AND lang='".$lang."' AND state='active' AND id!='".$res['id']."' ORDER BY date_insert DESC LIMIT 0, 3");
		if($sel_article->num_rows)
		{?>
			<div class="widget widget-event">
				<div class="widget-title">
					<h3><?php _e("Derniers Évènements")?></h3>
				</div>

				<div class="widget-content">
					<ul class="unstyled p-0">
					<?php

					while($res_article = $sel_article->fetch_assoc())
					{
						?>
						<li class="mb-16 ml-16"><a href="<?=make_url($res_article['url']);?>" class="no-decoration"><i class="icon-li icon moon-calendar float-left mr-8"></i> <?=$res_article['title']?></a></li>
						<?php
					}
					?>
					</ul>
				</div>
			</div>
		<?php }?>

		<!-- Tags -->
		<div class="widget widget-tag">

			<div class="widget-title">
				<h3><?php _e("Tags")?></h3>
			</div>

			<div class="widget-content">
				<?php tag('tags')?>
			</div>

			<script>
			if(!$(".editable-tag").text()) $("#tags").prev("h3").hide();
			else $("#tags").addClass("mb-24");
			</script>

		</div>

	</aside>
