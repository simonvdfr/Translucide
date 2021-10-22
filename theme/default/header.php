<?php if(!$GLOBALS['domain']) exit;?>


<header>

	<section class="mw960p mod center">

		<div class="center ptm"><a href="<?=$GLOBALS['home']?>"><?php media('logo', '320')?></a></div>


		<nav id="burger-menu">

			Menu

			<ul class="grid up">
				<?php
				// Extraction du menu
				foreach($GLOBALS['nav'] as $cle => $val)
				{
					// Menu sélectionné si page en cours ou article (actu)
					if(get_url() == $val['href'] or (@$res['type'] == "article" and $val['href'] == "actualites"))
						$selected = " selected";
					else
						$selected = "";

					echo"<li><a href=\"".make_url($val['href'], array("domaine" => true))."\"".($val['id']?" id='".$val['id']."'":"")."".($val['target']?" target='".$val['target']."'":"")." class='".$selected."'>".$val['text']."</a></li>";
				}
				?>
			</ul>

		</nav>

		<a href="#burger-menu" id="burger-ouvert" class="burger-bascule" title="Ouvrir le menu de navigation">≡</a>
		<a href="#" id="burger-ferme" class="burger-bascule" title="Fermer le menu de navigation">×</a>

		<a href="#" id="burger-revetement" class="" title="Fermer le menu de navigation"></a>

	</section>

</header>
