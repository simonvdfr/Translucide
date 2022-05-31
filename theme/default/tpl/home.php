<?php if(!$GLOBALS['domain']) exit;?>

<section id="hero" class="layout-maxed bg-blue">
	<div class="layout-hero bg-section bg-overlay bg-gradient sm:py-36 py-8" <?php bg('bg-hero')?>>
		<div class="hero-content grid md:grid-cols-2 flex flex-wrap sm:my-36 my-8 sm:py-36 py-8">
			<div class="py-16">
				<div class="hero-text mx-24 animation fade-in">
					<?php h1('hero-title', 'hero-title color-light')?>
					<?php txt('hero-desc','hero-desc color-light py-20 mb-16')?>
					<div class="hero-action grid lg:grid-cols-2">
						<a class="btn hero-btn bg-glaz border-rounded no-decoration" <?php href("hero-link")?>>Download <i class="icon moon-upload ml-8"></i></a>
					</div>
				</div>
			</div>
			<div class="flex mx-24 py-12">
				<div class="hero-card bg-white border-rounded shadow flex flex-col align-items-center text-center animation delay-1 slide-right-big">
					<?php media('hero-card-icon','80x80')?>
					<?php h3('hero-card-title', 'hero-card-title color-blue my-8')?>
					<?php txt('hero-card-desc','hero-card-desc mb-8')?>
				</div>
			</div>
		</div>
	</div>
</section>

<section id="features" class="layout-maxed text-center py-36">
	<div class="features-content sm:grid md:grid-cols-2 flex flex-wrap py-36 m-auto">
		<div class="features-text md:col-span-2 lg:col-span-full">
			<?php txt('features-subtitle','features-subtitle color-glaz')?>
		</div>
		<div class="features-heading md:col-span-2 lg:col-span-full pt-8">
			<?php h2('features-title', 'features-title color-blue')?>
		</div>
	</div>


	<!-- .module pour bien identifier que ce sont les elements à dupliquer et a sauvegardé-->
	<div id="features-card" class="module sm:grid md:grid-cols-2 lg:grid-cols-4 gap-36 sm:mx-20 mx-8 py-36 animation delay-1 fade-in">
		<?php
		// nom du module "features-card" = id du module, et au début des id des txt() media() ...
		$module = module("features-card");
		foreach($module as $key => $val)
		{
			?>
			<div class="features-card bg-white shadow border-rounded flex flex-col my-16 md:my-24">
				<div class="features-card-icon">
					<?php media("features-card-icon-".$key, array('size' => '70x70', "class" => "ml-0", "lazy" => true))?>
				</div>
				<div class="flex flex-col align-items-center text-left">
					<h4 class="color-blue pb-24 mb-24"><?php txt('features-card-title-'.$key) ?></h4>
					<p class="mb-0"><?php txt('features-card-desc-'.$key) ?></p>
				</div>
			</div>
			<?php
		}
		?>
	</div>
	
</section>
