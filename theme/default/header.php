<?if(!$GLOBALS['domain']) exit;?>


<header>

	<section class="mw960p mod center tc relative">

		<div class="center ptm"><a href="<?=$GLOBALS['home']?>"><?media('logo', '320')?></a></div>


		<nav class="font-ssp mtm mbm">

			<a class="big burger"><span>menu</span></a>

			<ul class="grid up">
				<?
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


	</section>

</header>
