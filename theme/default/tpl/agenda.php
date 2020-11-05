<?php if(!$GLOBALS['domain']) exit;?>

<style>
.event {
	margin-left: -2.5em;
	border-radius: 0.5em;
}

.event:hover .bt{
	background-color: #fff;
	color: #78cfd6;
	border-color: #78cfd6;
}

.event .date {
	border-radius : 100%;
	margin: 0rem 2rem;
	padding: 1rem 2rem;

	background-color: white;
	border-color: #35747f;
	color: #35747f;
}
</style>

<!-- Event -->
<section>

	<div class="mw960p mod center mtm mbl">

		<?php h2('titre-event', 'tc')?>

		<div class="fl w50 tr no-small-screen">
			<span class="editable-event" id="img-illu-event"><?php media('media-event','425')?></span>
		</div>

		<div class="fl w50 mts">
			<?php 
			$sel_event = $connect->query("SELECT * FROM ".$table_content." WHERE type='event' AND lang='".$lang."' AND state='active' ORDER BY date_insert DESC LIMIT 0, 3");
			while($res_event = $sel_event->fetch_assoc())
			{
				$date = explode("-",explode("\"", explode("aaaa-mm-jj", $res_event['content'])[1])[2]);
				//print_r($date);
				?>
				<div class="event pts pbs mtm mbm animation slide-right">

					<article>

						<!--Picot
						<div class="picto fl">
						<?php
						$res_picto = ('article' == $res_event['type']) ? 'picto-actu.png' : 'picto-evenement.png';
						?>
						<img src="/media/tpl/<?=$res_picto?>" alt="picto <?=$res_event['type']?>">
					</div>-->

					<div class="date bold bt bg-color fl up big tc">
						<div><?=$date[2]?></div>
						<div><?=trim(utf8_encode(strftime("%h", mktime(0, 0, 0, $date[1], 10))),".")?></div>
					</div>

					<div>
						<h2 class="bold mod up bigger man nowrap tdn"><a href="<?=make_url($res_event['url']);?>" class="tdn" target="_blank"><?=$res_event['title']?></a></h2>

						<div class="bold bt bg-color"><?php _e("Lire")?></div>
					</div>

				</article>

			</div>
			<?php 
		}
		?>
	</div>

</div>
</section>

<script>
$(function()
{
	// Met le lien sur zone la box et supprime le lien sur le h2
	$(".event article").wrapInner(function() {
		return "<a href='"+ $("a", this).attr("href") +"'"+ ($(this).attr("class")?" class='"+ $(this).attr("class") +"'":"")+ ($(this).attr("title") ? " title='"+ $(this).attr("title") +"'":"") +" />";
	}).children(0).unwrap();
	$(".event article").contents().unwrap();
});
</script>












<!-- slide event -->

<?php 
// Actu à la une
$sel_alaune = $connect->query("SELECT * FROM ".$table_meta." WHERE type='content' AND cle='alaune' LIMIT 1");
$res_alaune = $sel_alaune->fetch_assoc();
if($res_alaune['cle'])
{
	$sel_alaune = $connect->query("SELECT * FROM ".$table_content." WHERE id='".$res_alaune['val']."' LIMIT 1");
	$res_alaune = $sel_alaune->fetch_assoc();

	$alaune_content = json_decode($res_alaune['content'], true);

	if($res_alaune['type'] == "media") {
		$ext = pathinfo(explode("?", $alaune_content['fichier'])[0], PATHINFO_EXTENSION);
		if($ext == "jpg") $alaune_content['bg-header'] = $GLOBALS['path'].$alaune_content['fichier'];

		$url  = $GLOBALS['path'].$alaune_content['fichier']."\" target=\"_blank";
		$link_txt = __("Voir le document");
	}
	else {
		$url = make_url($res_alaune['url']);
		$link_txt = __("Lire l'article");
	}

	?>
	<section class="mw960p center">
		<div class="alaune overlay cover mal ptl pbl tc tdn" style="background: url('<?=$alaune_content['bg-header']?>');">
			<div class="mod mtm mbl animation slide-up">
				<h2 class="ptl mtl mbn biggest up white">
					<a href="<?=$url?>" class="white tdn"><?=$res_alaune['title']?></a>
				</h2>
				<h3 class="pbm mbl mtn bigger color"><?=$alaune_content['sstitre']?></h3>
				<div class="link-box inbl pat pls prs mbl animation slide-up"><?=$link_txt?></div>
			</div>
		</div>
	</section>

	<script>
		// Met le lien sur toute la zone et supprime le lien sur le h2
		$(".alaune").wrapInner(function() {
			return "<a href='"+ $("a", this).attr("href") +"' class='"+ $(this).attr("class") +"' style=\""+ $(this).attr("style") +"\" "+($("a", this).attr("target")?"target=\""+ $("a", this).attr("target") +"\"":"")+" />";
		}).children(0).unwrap();
		$(".alaune a").contents().unwrap();
	</script>
	<?php 
}
?>
