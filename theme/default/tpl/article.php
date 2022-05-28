<?php
if(!$GLOBALS['domain']) exit;
if(!@$GLOBALS['content']['titre']) $GLOBALS['content']['titre'] = $GLOBALS['content']['title'];
?>

<?php include('theme/'.$GLOBALS['theme'].'/mdl/hero-article.php');?>

<section id="breadcrumb" class="sm:flex align-items-center justify-between text-center px-36 py-16">
	<?php include('theme/'.$GLOBALS['theme'].'/mdl/breadcrumb.php');?>
	<?php include('theme/'.$GLOBALS['theme'].'/mdl/form-search.php');?>
</section>

<section id="post" class="p-36">

	<div class="md:flex flex-row flex-no-wrap justify-content ">
		<!-- Contenu de l'article -->
		<article class="post-content py-36 md:pr-36">
			<?php txt('texte')?>
			<?php if($res['tpl'] == "event-form") include 'contact.php';?>
		</article>

		<?php include('theme/'.$GLOBALS['theme'].'/mdl/sidebar.php');?>

	</div>

</section>
