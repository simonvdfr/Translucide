<?php if(!$GLOBALS['domain']) exit;?>


<header role="banner">

	<!--<div>
		<nav role="navigation" aria-label="<?php _e("Quick access")?>" class="inline"><a href="#hero" class="acces-rapide"><?php _e("Skip to content")?></a></nav>
		|
		<input type="checkbox" name="high-contrast" id="high-contrast"<?=(@$_COOKIE['high-contrast']?'checked="checked"':'')?>> <label for="high-contrast"><?php _e("Enhanced contrast")?></label>
	</div>-->

	<div id="topbar" class="m-0 p-0"></div>

	<section id="header" class="site-header">

		<div class="inside flex lg:pl-36">

			<div class="site-logo flex align-items-center py-24 mx-36">
				<a href="<?=$GLOBALS['home']?>"><?php media('logo', '200x50')?></a>
			</div>

			<nav role="navigation" class="menu flex mobile-hidden" aria-label="<?php _e("Browsing menu")?>">

				<button type="button" class="burger-button float-right" aria-expanded="false" aria-label="Afficher ou masquer la navigation" aria-controls="main-navigation">
					<i></i>
					<span class="close none"><?php _e("Close")?></span>
				</button>

				<ul id="main-navigation" class="nav-menu flex text-uppercase">
					<?php
					// Extraction du menu
					foreach($GLOBALS['nav'] as $cle => $val)
					{
						// Menu sélectionné si page en cours ou article (actu)
						if(get_url() == $val['href'] or (@$res['type'] == "article" and $val['href'] == "actualites"))
							$selected = " selected";
						else
							$selected = "";

						echo"<li class='nav-item'><a href=\"".make_url($val['href'], array("domaine" => true))."\"".($val['id']?" id='".$val['id']."'":"")."".($val['target']?" target='".$val['target']."'":"")." class='".$selected."'".($selected?' title="'.$val['text'].' - '.__("current page").'"':'').">".$val['text']."</a></li>";
					}
					?>
				</ul>

				<div class="holder-panel">
					<div class="holder-contact bg-glaz"><a class="btn"<?php href('holder-link')?>>Our Github</a><i class="icon moon-github color-white"></i></div>
				</div>

			</nav>

		</div>


	</section>

</header>
