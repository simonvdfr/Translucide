<?php if(!$GLOBALS['domain']) exit;?>

<section id="hero" class="layout-maxed bg-light">
	<div class="layout-hero bg-section py-8" <?php bg('bg-hero')?>>
		<div class="hero-content">
			<div class="py-16">
				<div class="hero-text text-center m-24 animation fade-in">
					<?php h1('hero-title', 'hero-title color-blue')?>
					<div class="hero-meta flex justify-center">
						<?php if(isset($res['date_insert'])){?><div class="hero-meta-date px-8"><i class="icon moon-calendar"></i> <label class="text-left"><?php _e("Add the")?></label> <?=$res['date_insert']?></div><?php }?>
						<?php if(isset($res['date_update'])){?><div class="hero-meta-date px-8"><i class="icon moon-calendar"></i> <label class="text-left"><?php _e("Updated the")?></label> <?=$res['date_update']?></div><?php }?>
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
