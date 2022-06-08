<?php if(!$GLOBALS['domain']) exit;?>


<header role="banner">

	<!--<div>
		<nav role="navigation" aria-label="<?php _e("Quick access")?>" class="inline"><a href="#hero" class="acces-rapide"><?php _e("Skip to content")?></a></nav>
		|
		<input type="checkbox" name="high-contrast" id="high-contrast"<?=(@$_COOKIE['high-contrast']?'checked="checked"':'')?>> <label for="high-contrast"><?php _e("Enhanced contrast")?></label>
	</div>-->

<section id="header" class="site-header">

	<div class="navbar sticky shadow inside flex lg:pl-36">

		<div class="site-logo flex align-items-center py-24 mx-36">
			<a href="<?=$GLOBALS['home']?>"><?php media('logo', '200x50')?></a>
		</div>

		<nav role="navigation" class="menu flex" aria-label="<?php _e("Browsing menu")?>">

			<button type="button" class="burger-button float-right" aria-expanded="false" aria-label="Afficher ou masquer la navigation" aria-controls="main-navigation">
				<span class="open"><?php _e("Menu")?></span>
				<span class="close none"><?php _e("Close")?></span>
			</button>

			<ul id="main-navigation" class="nav-menu flex flex-wrap text-bold">
				<?php
				$navigation = [''=>__('Upper menu')];
				// Extraction du menu
				foreach($GLOBALS['nav'] as $cle => $val)
				{
					// Pour le fil d'ariane
					$navigation = array_merge($navigation, [$val['href'] => str_replace('<br>',' ', $val['text'])]);

					// Menu sélectionné si page en cours // @$res['type'] == "article" and $val['href'] == "actualites"  or ()
					if(get_url() == $val['href'] or @array_keys($GLOBALS['filter'])[0] == basename($val['href']))
						$selected = ' selected';
					else
						$selected = '';

					echo'<li class="nav-item '.$selected.'">
						<a href="'.make_url($val['href'], array('domaine' => true)).'"'.
						($val['id']?' id="'.$val['id'].'"':'').
						($val['target']?' target="'.$val['target'].'"':'').
						($selected?' class="selected" title="'.$val['text'].' - '.__("current page").'"':'').
						'>'.$val['text'].'</a>
					</li>';
				}
				?>
			</ul>

			<div class="holder-panel tablet-hidden">
				<div class="holder-contact bg-glaz text-bold"><a class="btn"<?php href('holder-link')?>>Our Github</a><i class="icon moon-github color-white"></i></div>
			</div>

		</nav>

	</div>


</section>

</header>
