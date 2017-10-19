<?if(!$GLOBALS['domain']) exit;?>

<footer class="mod tc pal">

	<section class="mw960p center">

		<section class="w33 fl">
			
			<div class="mbm"><?media('footer-logo', '150')?></div>

			<h3 class="mbs medium"><?txt('footer-sstitre')?></h3>
			
			<div class="link-box gold"><?txt('footer-contact')?></div>

		</section>

		<section class="w33 fl">
			
			<h2 class="mbm medium up"><i class='fa fa-clock'></i> <?txt('footer-titre-heure')?></h2>
			
			<div class="bold"><?txt('footer-heure')?></div>

		</section>

		<section class="w33 fl">

			<h2 class="mbm medium up"><i class='fa fa-location'></i> <?txt('footer-titre-adresse')?></h2>

			<div class="bold mbt"><?txt('footer-adresse')?></div>
			
			<div class="social bigger">
				<?txt('footer-social')?>
			</div>

		</section>

	</section>

	<?if(isset($res['url']) == "home"){?><section class="mod w100 tc ptm"><?txt('webmaster')?></section><?}?>

</footer>


<script>
$(function()
{
	// BG IMAGE SOUS LE HEADER
	if($(".under-header").length) {
		$(".under-header").css("margin-top", -$("header").outerHeight());// Calage
		$(".under-header").append("<div class='overlay'></div>");// Diminue l'opacité
		//$("header a").css("color", "#fff");// Lien en blanc
		/*$("header #header-logo, header .burger").css({// Couleur du logo inversé
			"-webkit-filter": "invert(1)",
			"filter": "invert(1)"
		});*/	
	}
});	
</script>
