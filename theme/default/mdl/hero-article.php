<?php if(!$GLOBALS['domain']) exit;?>

<section id="hero" class="layout-maxed bg-light">
	<div class="layout-hero bg-section py-8" <?php bg('bg-hero')?>>
		<div class="hero-content">
			<div class="py-16">
				<div class="hero-text text-center m-24 animation fade-in">
					<?php h1('hero-title', 'hero-title color-blue')?>
					<p><i class="icon moon-calendar"></i>
						<?php
							echo date("l j \of F Y");
						?>
					</p>
				</div>
			</div>
		</div>
	</div>
</section>
