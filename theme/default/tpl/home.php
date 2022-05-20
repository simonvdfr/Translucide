<?php if(!$GLOBALS['domain']) exit;?>

<section id="hero" class="layout-maxed">
	<div class="layout-hero bg-section bg-overlay bg-gradient sm:py-36 py-8" <?php bg('bg-hero')?>>
		<div class="hero-content grid md:grid-cols-2 flex flex-wrap sm:my-36 my-8 sm:py-36 py-8">
			<div class="py-12">
				<div class="hero-text mx-24 animation fade-in">
					<?php h1('hero-title', 'hero-title color-light')?>
					<?php txt('hero-desc','hero-desc color-light py-20 mb-16')?>
					<div class="hero-action grid lg:grid-cols-2">
						<a class="btn hero-btn bg-glaz border-rounded no-decoration" <?php href("hero-link")?>>Download <i class="icon moon-upload ml-8"></i></a>
					</div>
				</div>
			</div>
			<div class="flex mx-24 py-12">
				<div class="hero-panel bg-white border-rounded shadow flex flex-col align-items-center text-center animation delay-1 slide-right-big">
					<?php media('hero-panel-icon','80x80')?>
					<?php h3('hero-panel-title', 'hero-panel-title color-blue my-8')?>
					<?php txt('hero-panel-desc','hero-panel-desc mb-8')?>
				</div>
			</div>
		</div>
	</div>
</section>

<section id="features" class="layout-maxed bg-light text-center py-36">
	<div class="features-content sm:grid md:grid-cols-2 flex flex-wrap py-36 m-auto">
		<div class="features-text md:col-span-2 lg:col-span-full">
			<?php txt('features-subtitle','features-subtitle color-glaz')?>
		</div>
		<div class="features-heading md:col-span-2 lg:col-span-full pt-8">
			<?php h2('features-title', 'features-title color-blue')?>
		</div>
	</div>
	<!-- .module pour bien identifier que ce sont les elements à dupliquer et a sauvegardé -->
	<div id="features-panel" class="module sm:grid md:grid-cols-2 lg:grid-cols-4 gap-36 flex flex-wrap sm:mx-20 mx-8 py-36 animation delay-1 fade-in">
		<?php
		// nom du module "features-panel" = id du module, et au début des id des txt() media() ...
		$module = module("features-panel");
		foreach($module as $key => $val)
		{
			?>
			<div class="features-panel bg-white shadow border-rounded flex flex-col my-16 md:my-24">
				<div class="features-panel-icon">
					<?php media("features-panel-icon-".$key, array('size' => '70x70', "class" => "ml-0", "lazy" => true))?>
				</div>
				<div class="features-panel-content flex flex-col align-items-center text-left">
					<h4 class="features-panel-title color-blue pb-24 mb-24"><?php txt('features-panel-title-'.$key) ?></h4>
					<p class="features-panel-desc mb-0"><?php txt('features-panel-desc-'.$key) ?></p>
				</div>
			</div>
			<?php
		}
		?>
	</div>
</section>
