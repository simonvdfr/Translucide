<?php if(!$GLOBALS['domain']) exit;?>

<style>
	.link:hover .bt { background-color: #78cfd6; color: #fff; }
	.link:hover .bt.bg-color { 		
		background-color: #fff;
		color: #78cfd6;
		border-color: #78cfd6;
	}
</style>


<section class="mw960p mod center mtm mbl">

	<div class="tc mbn">
		<?php h1('titre')?>
		<?php h2('sstitre', 'pbl')?>
	</div>

	<div class="link w50 fl tc animation slide-left">
		<article>
			<div><?php media('media-2', '130')?></div>
			<h3 class="mbn tdn"><a <?php href("lien-2")?>><?php span('titre-2')?></a></h3>
			<?php txt('txt-2','w50 center block')?>
			<div class="bt mts"><?php _e("Lire plus")?></div>
		</article>
	</div>

	<div class="link w50 fl tc animation slide-right">
		<article>
			<div><?php media('media-3', '130')?></div>
			<h3 class="mbn tdn"><a <?php href("lien-3")?>><?php span('titre-3')?></a></h3>
			<?php txt('txt-3','w50 center block')?>
			<div class="bt mts"><?php _e("Lire plus")?></div>
		</article>
	</div>

</section>


<section class="mw960p mod center mbl">

	<?php h2('titre-4', 'tc mbt mtt ptm')?>

	<article class="pbm mtl mod">

		<div class="plm fl w40 animation slide-left"><a <?php href('lien-6')?>><?php media('image-6', '470')?></a></div>

		<div class="pll fr w60">

			<div class="animation slide-right">
				<?php h3('titre-5','mbn')?>
				<?php txt('txt-5')?>
			</div>

			<div class="mtl animation slide-right">
				<?php h3('titre-6','mbn')?>
				<?php txt('txt-6')?>
			</div>

		</div>


	</article>

	<article class="ptm mod" style="border-top: solid 1px #eee;" <?bg("bg", 'lazy')?>>

		<div class="prs mtl ptm fl w60 tr animation slide-left">
			<?php h3('titre-7','mbn')?>
			<?php txt('txt-7', array('lazy' => true))?>
		</div>

		<div class="prl fr w40 animation slide-right"><a <?php href('lien-map')?>><?php media('image-7', '470')?></a></div>

	</article>

</section>


<!-- Module -->
<section class="mw960p mod center mbl">

	<?php h2('titre-module', 'tc')?>

	<div class="flex">

		<!-- .module pour bien identifier que ce sont les elements à dupliquer et a sauvegardé -->
		<ul id="partenaire" class="module unstyled pan auto tc">
		<?php
		// nom du module "partenaire" = id du module, et au début des id des txt() media() ...
		$module = module("partenaire");
		foreach($module as $key => $val)
		{
			?>
			<li class="fl">
				<div><?php media("partenaire-img-".$key, array('size' => '250x250', 'lazy' => true));?></div>
				<div class="pam">« <?php txt("partenaire-text-".$key, array("tag" => "span"));?> »</div>
			</li>
			<?php
		}
		?>
		</ul>

	</div>

</section>



<!-- Event -->
<style>
	.event {
		margin-left: -2.5em;
		border-radius: 0.5em;
	}
	.event .date {
		border-radius : 100%;
		margin: 0rem 2rem;
		padding: 1rem 2rem;

		background-color: white;
		border-color: #35747f;
		color: #35747f;
	}
</style>
<section>

	<div class="mw960p mod center mbl">

		<?php h2('titre-event', 'tc')?>

		<div class="fl w50 tr no-small-screen">
			<span class="editable-event" id="img-illu-event"><?php media('media-event','425')?></span>
		</div>

		<div class="fl w50 mts">
			<?php 
			$sel_event = $connect->query("SELECT * FROM ".$table_content." WHERE type='event' AND lang='".$lang."' AND state='active' ORDER BY date_insert DESC LIMIT 0, 3");
			while($res_event = $sel_event->fetch_assoc())
			{
				$date = explode("-", explode(" ", $res_event['date_insert'])[0]);

				?>
				<div class="link event pts pbs mtm mbm animation slide-right">

					<article>

						<!--Picot
						<div class="picto fl">
							<?php
							$res_picto = ('article' == $res_event['type']) ? 'picto-actu.png' : 'picto-evenement.png';
							?>
							<img src="/media/tpl/<?=$res_picto?>" alt="picto <?=$res_event['type']?>">
						</div>-->

						<div class="date bold bt bg-color fl up big tc">
							<div><?=$date[2]?></div>
							<div><?=trim(utf8_encode(strftime("%h", mktime(0, 0, 0, $date[1], 10))),".")?></div>
						</div>

						<div>
							<h2 class="bold mod up bigger man nowrap tdn"><a href="<?=make_url($res_event['url']);?>" class="tdn"><?=$res_event['title']?></a></h2>

							<div class="bold bt bg-color"><?php _e("Lire")?></div>
						</div>

					</article>

				</div>
				<?php 
			}
			?>
		</div>

	</div>
</section>
<!-- Fin Event -->



<script>
$(function()
{
	// Met le lien sur zone la box et supprime le lien sur le h2
	$(".link article").wrapInner(function() {
		return "<a href='"+ $("a", this).attr("href") +"'"+ ($(this).attr("class")?" class='"+ $(this).attr("class") +"'":"")+ ($(this).attr("title") ? " title='"+ $(this).attr("title") +"'":"") +" />";
	}).children(0).unwrap();
	//$(".link article").contents().unwrap();

	// Mode admin
	edit.push(function() {
		// Supprime l'action de click sur le lien
		$(".link a").on("click", function(event) { event.preventDefault(); });
	});
});
</script>