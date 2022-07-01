<?php if(!$GLOBALS['domain']) exit;?>

<?php include('theme/'.$GLOBALS['theme'].'/mdl/hero.php');?>

<section id="event-list" class="p-36">

		<div class="event-list sm:grid md:grid-cols-2 lg:grid-cols-4 gap-36 sm:mx-20 mx-8 py-36 animation fade-in">
			<?php
			$sel_event = $connect->query("SELECT * FROM ".$table_content." WHERE type='event' AND lang='".$lang."' AND state='active' ORDER BY date_insert DESC LIMIT 0, 3");
			while($res_event = $sel_event->fetch_assoc())

			{
				$content_event = json_decode($res_event['content'], true);

				$date = explode("-",explode("\"", explode("aaaa-mm-jj", $res_event['content'])[1])[2]);
				//print_r($date);
				?>

				<div class="event-card shadow border-rounded my-16 md:my-24">

					<article>

						<div class="event-post-img">
							<a href="<?=make_url($res_event['url']);?>" class="no-decoration">
								<img src="<?=(isset(parse_url(@$content_event['img-article'])['scheme'])?'':$GLOBALS['home']).@$content_event['img-article']; ?>">
							</a>
						</div>

						<div class="event-post-content flex align-items-center">
							<div class="event-post-date bg-blue color-light text-uppercase text-center p-16">
								<div class="date-number"><?=$date[2]?></div>
								<div><?=trim(utf8_encode(strftime("%h", mktime(0, 0, 0, $date[1], 10))),".")?></div>
							</div>
							<div class="event-post-title flex flex-no-wrap mx-16">
								<h3 class="mb-0 no-decoration"><a href="<?=make_url($res_event['url']);?>" class="no-decoration"><?=$res_event['title']?></a></h3>
							</div>
						</div>

				</article>

			</div>
			<?php
		}
		?>
		</div>

</section>