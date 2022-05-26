<?php if(!$GLOBALS['domain']) exit;?>

<footer role="contentinfo">

	<section id="footer" class="site-footer py-36">

		<div class="sm:flex justify-evenly animation fade-in">

			<div class="flex align-items-center mx-36 lg:ml-0">
				<?php txt('footer-desc')?>
			</div>

			<div class="flex flex-wrap justify-center align-items-center mx-36 my-36 md:my-0">

				<div class="flex md:align-center">
					<a <?php href("demo-link")?>><?php media('img-footer','350x200')?></a>
				</div>

				<div class="flex md:align-center">
					<div class="m-20">
						<?php h3('footer-title', 'color-white text-uppercase')?>
						<?php txt('footer-content')?>
					</div>
				</div>

			</div>

		</div>

	</section>

	<section id="bottombar" class="text-smaller py-20">

		<div class="flex flex-wrap md:justify-between justify-center content-center">

			<div class="px-20">
				<a href="https://seacms.io"><b>SEA</b>CMS</a> is a fork of <a href="https://www.translucide.net" target="_blank">Translucide</a>.
			</div>

			<div class="px-20">
				Is released under the <a href="http://www.wtfpl.net">WTFPL license</a> and was <i class="icon moon-code"></i> with <i class="icon moon-heart"></i> by <a href="https://thatoo.leprette.fr" target="_blank">Thatoo</a> & <a href="https://marion.bouder.me" target="_blank">MB</a>.
			</div>

			<div class="px-20">
				🄯 2022 - support@seacms.io
			</div>

		</div>

	</section>


</footer>

<noscript><?php _e("Sorry, your browser does not support JavaScript!")?></noscript>
