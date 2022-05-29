<?php if(!$GLOBALS['domain']) exit;?>

<section id="hero" class="layout-maxed bg-light">
	<div class="layout-hero bg-section bg-overlay py-8" <?php bg('bg-hero')?>>
		<div class="hero-content">
			<div class="py-16">
					<div class="hero-text text-center m-24 animation fade-in">
						<h1 class="hero-title color-blue"><?=$res['title']?></h1>
						<div class="hero-meta flex justify-center">
							<div class="hero-meta-date-add px-8">
								<i class="icon moon-calendar"></i> <?php _e("Add the")?> <?=$res['date_insert']?>
							</div>
							<div class="hero-meta-date-update px-8">
								<i class="icon moon-calendar"></i> <?php _e("Updated the")?> <?=$res['date_update']?>
							</div>
							<div class="hero-meta-author px-8">
								<i class="icon moon-user"></i>
								<?php
									echo get_current_user();
								?>
							</div>
						</div>
					</div>

			</div>
		</div>
	</div>
</section>
