<?if(!$GLOBALS['domain']) exit;?>


<header>

	<div class="mw960p mod center tc relative">

		<div class="center ptm"><a href="<?=$GLOBALS['scheme'].$GLOBALS['domain'].$GLOBALS['path']?>">
				<?media('logo', '110')?></a></div>

		<nav class="mtm mbm">

			<a class="big burger"><span>menu</span></a>

			<ul class="grid up">
				<?
				// Extraction du menu
				foreach($GLOBALS['nav'] as $cle => $val)
				{
					echo"<li><a href=\"".make_url($val['href'])."\"".($val['id']?" id='".$val['id']."'":"")."".($val['target']?" target='".$val['target']."'":"").">".$val['text']."</a></li>";
				}
				?>
			</ul>

		</nav>

	</div>

</header>
