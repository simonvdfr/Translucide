<?php if(!$GLOBALS['domain']) exit;?>

<?php 
$module = module("slide");/* nom du module "slide" = id du UL, et au début des id des txt() media() ... */
$count = count($module) - 1;/* pour l'animation du slide */
?>
<style>
	.slideshow {
		position: relative;
		width: <?=$count?>00%;
		left: 0%;
		overflow: hidden;
		transition: left 0.8s ease-in;
	}
		.slideshow li {
			position: relative;
			display: inline-block;
			float: left;
            width: <?=str_replace(',', '.', 100/$count)?>%;
            height: 100%;
            overflow: hidden;
		}

	.slidenav { margin-top: 100px; }

	/* En mode edition */
	.lucide	.slideshow {
		/* pour le cas ou il n'y pas de slide (start)*/
		min-width: 300px;
		min-height: 50px;

		width: 100%;
		left: 0% !important;
		overflow: initial;
	}
		.lucide .slideshow li {
	        min-width: 500px;
	        width: 100%;
	        overflow: visible;
		}
</style>
<section class="bgcolor">

	<article class="mw1200p center pam ptn mod">

		<?php h2('titre-slideshow','tc bold up')?>

		<div class="flex mbm tc">

			<a href="javascript:slidenav('prev');void(0);" class="mlm slidenav"><i class="fa fa-up-open r270 biggest"></i></a>

			<div class="mw960p mod auto tl">

				<!-- 
				.module pour bien identifier que ce sont les elements à dupliquer et a sauvegardé
				.slideshow pour lancer gérer les transitions
				#slide id unique du module (pour le cas ou plusieurs modules dans une page)
				-->
				<ul id="slide" class="module slideshow unstyled pan auto">
				<?php 
				foreach($module as $key => $val)
				{
					?>
					<li>
						<div class='img fl mrl'><?php media("slide-img-".$key, array('size' => '250x250'));?></div>
						<div class="pal">« <?php txt("slide-text-".$key, array("tag" => "span"));?> »</div>
					</li>
					<?php 
				}
				?>
				</ul>

			</div>

			<a href="javascript:slidenav('next');void(0);" class="mrm slidenav"><i class="fa fa-up-open r90 biggest"></i></a>

		</div>

		<script>
			slides = $('.slideshow li').length - 2;// -1 pour slide vide et -1 pour init à 0 = -2
			slide = 0;
			autoslidetime = 8000;//12000 2000
			repeat = 0;
			maxrepeat = 5;

			// Position basse du slide
			slide_bottom = $(".slideshow").outerHeight() + $(".slideshow").offset().top;

			function slidenav(action)
			{
				// Si on a pas la souris au dessu du slideshow on arrete l'autoslide
				if(!slide_hover)
				{	
					// Si action manuel on reinit l'autoslide
					if(action != undefined) repeat = 0;

					// On n'a pas dépassé le nombre max d'autoslide
					if(repeat <= maxrepeat)
					{
						// Si le slide est dans l'ecran on joue l'animation (pour eviter de surcharger le nav)
						if($window.scrollTop() < slide_bottom)
						{
							// Sens de navigation
							if(action == 'prev') slide--;
							else slide++;

							// Si on est au extermité du slideshow
							if(slide > slides) slide = 0;
							else if(slide < 0) slide = slides;

							// Change le slide visible avec l'animation
							$(".slideshow").css("left", - (slide * 100)+"%");

							// Re-init l'autoslide
							if(action) {
								clearInterval(timer);
								timer = setInterval(slidenav, autoslidetime);
							}
							else repeat++;
						}
					}
				}
			}

			$(function(){
				slide_hover = false;

				// Si plus de 1 slide : autoslide
				if(slides > 0) timer = setInterval(slidenav, autoslidetime);

				// Mouseover ?
				$(".slideshow").hover(function() { slide_hover = true; }, function() { slide_hover = false; } );
			});
		</script>

	</article>

</section>


<!-- Responsive -->
<style>
	@media (max-width: 1060px)  {
		.module .img img { width: 95%; }
		.slidenav {
			margin: 1rem auto;
			display: block;
		}
	}

	@media (min-width: 480px) and (max-width: 1060px)  {
		.module .img {
			width: 200px;
		    height: 200px;
		    line-height: 200px;
		}
		.slidenav { margin: 1rem auto; }
	}

	@media (max-width: 480px)  {
		.slideshow { height: auto; }
		.slideshow .img {
			margin: auto;
			float: none;
		}
	}
</style>
