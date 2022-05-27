<?php if(!$GLOBALS['domain']) exit;?>

<section id="breadcrumb" class="layout-maxed">
	<div class="breadcrumb-content flex align-items-center text-center justify-between p-16">
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
		<?php include('theme/'.$GLOBALS['theme'].'/mdl/form-search.php');?>
	</div>
</section>
