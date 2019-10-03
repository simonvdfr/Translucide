<?if(!$GLOBALS['domain']) exit;?>

<footer>

	<section class="grid mw960p center">

		<article>

			<?h3('footer-titre-contact', 'medium up')?>
			<?txt('footer-texte-contact')?>

		</article>


		<article>

			<?h3('footer-titre-actu', 'medium up')?>

			<!--Va chercher les dernieres actu-->
			<ul class="unstyled pan">
			<?
			$sel_actu = $connect->query("SELECT * FROM ".$tc." WHERE (type='article' OR type='event') AND lang='".$lang."' AND state='active' ORDER BY date_insert DESC LIMIT 0, 3");
			while($res_actu = $sel_actu->fetch_assoc())
			{
				?>
				<li class="mbs"><i class="fa-li fa fa-fw fa-<?=($res_actu['type']=='article'?'rss':'calendar-empty')?> fl mrt"></i> <a href="<?=make_url($res_actu['url']);?>" class="tdn" style="color: black;"><?=$res_actu['title']?></a></li>
				<?
			}
			?>
			</ul>

		</article>


		<article>

			<?h3('footer-titre-suivez-nous', 'medium up')?>
			<?txt('footer-texte-suivez-nous', 'color bigger')?>

		</article>

	</section>
	

	<section class="mod w100 tc"><?txt('webmaster')?></section>


</footer>


<script>
$(function()
{
	// BULLE D'INFORMATION SUR L'UTILISATION DES COOKIES
	// Si bandeau pas masqué
	if(typeof google_analytics !== 'undefined' && get_cookie('analytics') == '')
	{
		// Ajout du bandeau en bas
		$("body").append("<div id='cnilcookie'><div class='bt'><i class='fa fa-fw fa-bell'></i> Nous utilisons les cookies pour établir des statistiques sur la fréquentation du site. <a href='javascript:void(0)' id='masquer'><u>Masquer</u></a> / <a href='javascript:void(0)' id='desactiver'><u>Désactiver</u></a></div></div>");

		// Au click sur le bandeau
		$("#cnilcookie a").click(function(event){
			// Desactive Analytics
			if(event.currentTarget.id == "desactiver")
			{
				// Ne plus lancer analytics
				set_cookie("analytics", "desactiver", "365");

				// Supprime les cookies analytics
				var cookieNames = ["__utma","__utmb","__utmc","__utmz","_ga","_gat","_gid"]
				for(var i=0; i < cookieNames.length; i++) set_cookie(cookieNames[i], '', '0');
			}
			else set_cookie("analytics", "hide", "365");// Masque définitivement la barre

			// Masque la barre
			$("#cnilcookie").fadeOut();

			return false;
		});

		// Affichage du message après un délai
		$("#cnilcookie").delay(2000).fadeTo("slow", 0.8);
	}
});
</script>
