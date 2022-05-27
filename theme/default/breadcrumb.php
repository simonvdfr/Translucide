<?php if(!$GLOBALS['domain']) exit;?>

<section id="breadcrumb" class="inside">
	<div class="breadcrumb-content flex align-items-center ">
		<ol class="breadcrumb-list pt-8">
			<li class="breadcrumb-item">
				<a href="/"><?php _e("Home")?></a>
			</li>
			<?php
				// Supprime le nom du site
				$title = preg_replace('/'.preg_quote(' - '.$GLOBALS['sitename']).'*$/', '', $title);
				if(isset($title)) echo'<li class="breadcrumb-item" aria-current="page">'.$title.'</li>';
			?>
		</ol>
	</div>
</section>
