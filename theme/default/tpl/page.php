<section id="hero" class="layout-maxed bg-light">
	<div class="layout-hero bg-section py-8" <?php bg('bg-hero')?>>
		<div class="hero-content">
			<div class="py-16">
				<div class="hero-text text-center m-24 animation fade-in">
					<?php h1('hero-title', 'hero-title color-blue')?>
					<?php txt('hero-subtitle','hero-subtitle pt-20')?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php include('theme/'.$GLOBALS['theme'].'/breadcrumb.php');?>

<section id="page" class="layout-maxed py-36">
	<article class="page-content">
			<?php txt('texte')?>
	</article>
</section>
