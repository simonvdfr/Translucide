<?if(!$GLOBALS['domain']) exit;?>

<footer class="mod tc pal">

	<section class="mw960p center">

		<section class="w33 fl">
			
			<div class="mbm"><?media('footer-logo', '150')?></div>

			<h3 class="mbs medium"><?txt('footer-sstitre')?></h3>
			
			<div class="link-box gold"><?txt('footer-contact')?></div>

		</section>

		<section class="w33 fl">
			
			<h2 class="mbm medium up"><i class='fa fa-clock-o'></i> <?txt('footer-titre-heure')?></h2>
			
			<div class="bold"><?txt('footer-heure')?></div>

		</section>

		<section class="w33 fl">

			<h2 class="mbm medium up"><i class='fa fa-map-marker'></i> <?txt('footer-titre-adresse')?></h2>

			<div class="bold mbt"><?txt('footer-adresse')?></div>
			
			<div class="social bigger">
				<?txt('footer-social')?>
			</div>

		</section>

	</section>

	<?if($res['url'] == "home"){?><section class="mod w100 tc ptm"><?txt('webmaster')?></section><?}?>

</footer>

<script src="theme/<?=$GLOBALS['theme'];?>effect.js"></script>
