<?if(!$GLOBALS['microtime']) exit;?>


<section class="tc fullpage" <?bg('bg-1')?>>		

	<div class="middle tofadein">

		<div class="logo mod">
			<svg height="80" width="400" xmlns="http://www.w3.org/2000/svg">

				<rect class="shape" height="80" width="400" />

				<div class="shine"></div>

				<h1 class="mega mtn mbs"><?txt('logo')?></h1>

			</svg>
		</div>

		<h2 class="medium under hidden"><?txt('underline')?></h2>

	</div>
	

	<article class="bottom pal">		
		<p><?txt('txt-intro')?></p>
	</article>

</section>


<section class="fullpage" <?bg('bg-2')?>>		
	
	<h2 class="top mts tc"><?txt('titre-cms')?></h2>

	<article class="w50 pal tofadein">		
		<p><?txt('txt-cms')?></p>
	</article>

</section>


<section class="fullpage" <?bg('bg-3')?>>		
	
	<h2 class="top"><?txt('titre-real')?></h2>

	<article class="right pal tofadein w50">		
		<? include("plugin/work.php"); ?>
	</article>

</section>


<section class="fullpage" <?bg('bg-4')?>>		
	
	<h2 class="top"><?txt('titre-contact')?></h2>

	<article class="right pal tofadein">		
		<p><?txt('txt-contact')?></p>
	</article>

</section>