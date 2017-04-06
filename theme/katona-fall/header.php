<?if(!$GLOBALS['domain']) exit;?>


<header class="mw960p mod center tc pts pbs none">	
	
	<span class="fl"><a href="<?=$GLOBALS['scheme'].$GLOBALS['domain'].$GLOBALS['path']?>"><?media('header-logo')?></a></span>
	
	<nav>
				
		<a class="burger"><span>menu</span></a>

		<ul>
			<?
			// Extraction du menu
			while(list($cle, $val) = each($GLOBALS['nav']))	
			{
				echo"<li><a href=\"".make_url($val['href'])."\"".($val['id']?" id='".$val['id']."'":"")."".($val['class']?" ".$val['class']:"")."".($val['target']?" target='".$val['target']."'":"").">".$val['text']."</a></li>";
			}
			?>
		</ul>

	</nav>

</header>