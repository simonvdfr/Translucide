<?php if(!$GLOBALS['domain']) exit;?>


<header role="banner">

	<section class="mw960p mod center tc relative">


		<nav role="navigation" aria-label="<?php _e("Quick access")?>"><a href="#main" class="acces-rapide">Aller au contenu</a></nav>


		<div class="center ptm"><a href="<?=$GLOBALS['home']?>"><?php media('logo', '320')?></a></div>


		<nav role="navigation" class="mtm mbm" aria-label="<?php _e("Browsing menu")?>">

			<button type="button" class="burger" aria-expanded="false" aria-controls="header-menu">
				<span class="open">Menu</span>
				<span class="close none"><?php _e("Close")?></span>
			</button>
			
			<ul id="header-menu" class="grid up">
				<?php
				// Extraction du menu
				foreach($GLOBALS['nav'] as $cle => $val)
				{
					// Menu sélectionné si page en cours ou article (actu)
					if(get_url() == $val['href'] or (@$res['type'] == "article" and $val['href'] == "actualites"))
						$selected = " selected";
					else
						$selected = "";

					echo"<li><a href=\"".make_url($val['href'], array("domaine" => true))."\"".($val['id']?" id='".$val['id']."'":"")."".($val['target']?" target='".$val['target']."'":"")." class='".$selected."'".($selected?' title="'.$val['text'].' - '.__("current page").'"':'').">".$val['text']."</a></li>";
				}
				?>
			</ul>

		</nav>


	</section>

</header>
