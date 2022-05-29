<?php if(!$GLOBALS['domain']) exit;?>

<section id="hero" class="layout-maxed bg-light">
	<div class="layout-hero bg-section py-8" <?php bg('bg-hero')?>>
		<div class="hero-content">
			<div class="py-16">
					<div class="hero-text text-center m-24 animation fade-in">
						<h1 class="hero-title color-blue"><?=$res['title']?></h1>
						<?php txt('hero-subtitle','hero-subtitle pt-20')?>
					</div>
			</div>
		</div>
	</div>
</section>
