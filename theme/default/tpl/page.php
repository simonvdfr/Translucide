<?if(!$GLOBALS['domain']) exit;?>

<style>
	section[data-id='fond'] {
		background-position: top right;
  		background-repeat: no-repeat;
	}
</style>

<section class="mw960p mod center mtl mbl" <?bg("fond")?>>

	<h1 class="mbn tc up color"><?txt('title')?></h1>
	<h3 class="mbn tc big color-alt"><?txt('sstitre')?></h3>

	<article class="tj ptm pll prl mod">

		<?if($res['tpl'] == "page-double-haut" or $res['tpl'] == "page-double-haut-encart"){?>
			<div class="grid space-xl clear">
				<div class="">
					<span class="animation r90 fire">
						<?media('visuel-gauche')?>
					</span>
					<h2 class="tc"><?txt('titre-gauche')?></h2>
					<?txt('texte-gauche')?>
				</div>
				<div class="">
					<span class="animation slide-up">
						<?media('visuel-droite')?>
					</span>
					<h2 class="tc"><?txt('titre-droite')?></h2>
					<?txt('texte-droite')?>
				</div>
			</div>
		<?}?>


		<?txt('texte')?>


		<?if(strstr($res['tpl'], 'encart')){?>
				</article>
			</section>

			<section class="encart mbl ptm pbm bg-color">
				<article class="mw960p mod center white animation fade-in pam pll prl">
					<h2 class="tc biggest mtm white">
						<?txt('texte-encart')?>
					</h2>
				</article>
			</section>

			<section class="mw960p mod center mbl">
				<article class="tj pll prl">
					<h2 class="tc color"><?txt('titre-article')?></h2>
					<h3 class="tc big mtn"><?txt('sstitre-article')?></h3>
		<?}?>

	</article>

</section>
