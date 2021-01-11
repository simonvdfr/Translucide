<?php if(!$GLOBALS['domain']) exit;?>

<footer>

	<section class="mw960p center grid">

		<div>

			<?php h3('footer-titre-contact', 'medium up')?>
			<?php txt('footer-texte-contact')?>

		</div>


		<div>

			<?php h3('footer-titre-actu', 'medium up')?>

			<!--Va chercher les dernieres actu-->
			<ul class="unstyled pan">
			<?php
			$sel_actu = $connect->query("SELECT * FROM ".$tc." WHERE (type='article' OR type='event') AND lang='".$lang."' AND state='active' ORDER BY date_insert DESC LIMIT 0, 3");
			while($res_actu = $sel_actu->fetch_assoc())
			{
				?>
				<li class="mbs"><i class="fa-li fa fa-fw fa-<?=($res_actu['type']=='article'?'rss':'calendar-empty')?> fl mrt"></i> <a href="<?=make_url($res_actu['url']);?>" class="tdn" style="color: black;"><?=$res_actu['title']?></a></li>
				<?php
			}
			?>
			</ul>

		</div>


		<div>

			<?php h3('footer-titre-suivez-nous', 'medium up')?>
			<?php txt('footer-texte-suivez-nous', 'color bigger')?>

		</div>

	</section>
	

	<section class="mod w100 tc"><?php txt('webmaster')?></section>


</footer>
