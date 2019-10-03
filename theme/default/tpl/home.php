<?if(!$GLOBALS['domain']) exit;?>

<style>
	.link:hover .bt { background-color: #78cfd6; color: #fff; }
	.link:hover .bt.bg-color { 		
		background-color: #fff;
		color: #78cfd6;
		border-color: #78cfd6;
	}
</style>


<section class="mw960p mod center mbl">

	<div class="tc mbn">
		<?h1('titre')?>
		<?h2('sstitre', 'pbl')?>
	</div>

	<div class="link w50 fl tc animation slide-left">
		<article>
			<div><?media('media-2', '130')?></div>
			<h3 class="mbn tdn"><a <?href("lien-2")?>><?span('titre-2')?></a></h3>
			<?txt('txt-2','w50 center')?>
			<div class="bt"><?_e("Lire plus")?></div>
		</article>
	</div>

	<div class="link w50 fl tc animation slide-right">
		<article>
			<div><?media('media-3', '130')?></div>
			<h3 class="mbn tdn"><a <?href("lien-3")?>><?span('titre-3')?></a></h3>
			<?txt('txt-3','w50 center')?>
			<div class="bt"><?_e("Lire plus")?></div>
		</article>
	</div>

</section>


<section class="mw960p mod center mbl">

	<?h2('titre-4', 'tc mbt mtt ptm')?>

	<article class="pbm mtl mod">

		<!-- image : 470 x 320 -->
		<div class="plm fl w40 animation slide-left"><a <?href('lien-6')?>><?media('image-6', '470')?></a></div>

		<div class="pll fr w60">

			<div class="animation slide-right">
				<?h3('titre-5','mbn')?>
				<?txt('txt-5')?>
			</div>

			<div class="mtl animation slide-right">
				<?h3('titre-6','mbn')?>
				<?txt('txt-6')?>
			</div>

		</div>


	</article>

	<article class="ptm mod" style="border-top: solid 1px #eee;">

			<div class="prs mtl ptm fl w60 tr animation slide-left">
				<?h3('titre-7','mbn')?>
				<?txt('txt-7')?>
			</div>

		<!-- image : 470 x 320 -->
		<div class="prl fr w40 animation slide-right"><a <?href('lien-map')?>><?media('image-7', '470')?></a></div>


	</article>

</section>


<!-- Module -->
<section class="mw960p mod center mbl">

	<?h2('titre-module', 'tc mbt mtt ptm')?>

		<div class="flex mbm tc">

			<!-- .module pour bien identifier que ce sont les elements à dupliquer et a sauvegardé -->
			<ul id="partenaire" class="module unstyled pan auto">
			<?
			// nom du module "partenaire" = id du module, et au début des id des txt() media() ...
			$module = module("partenaire");
			foreach($module as $key => $val)
			{
				?>
				<li>
					<div class='img fl mrl'><?media("partenaire-img-".$key, array('size' => '250x250'));?></div>
					<div class="pal">« <?txt("partenaire-text-".$key, array("tag" => "span"));?> »</div>
				</li>
				<?
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

		<?h2('titre-event', 'tc')?>

		<div class="fl w50 tr no-small-screen">
			<span class="editable-event" id="img-illu-event"><?media('media-event','425')?></span>
		</div>

		<div class="fl w50 mts">
			<?
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

							<div class="bold bt bg-color"><?_e("Lire")?></div>
						</div>

					</article>

				</div>
				<?
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