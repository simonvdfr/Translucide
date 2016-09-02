<?if(!$GLOBALS['microtime']) exit;?>

<!-- @todo menu float onscroll to top display -->
<header class="mod tc pts pbs none">	
	
	<span class="fl"><a href="<?=$GLOBALS['scheme'].$GLOBALS['domain'].$GLOBALS['path']?>"><?img('header-logo')?></a></span>
	
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