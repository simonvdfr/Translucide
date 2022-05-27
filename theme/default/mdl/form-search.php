<?php if(!$GLOBALS['domain']) exit;?>

<!-- Formulaire de recherche -->
<?php $sel_search = $connect->query("SELECT * FROM ".$table_content." WHERE tpl='search' LIMIT 1");
if($connect->error) {
	header($_SERVER['SERVER_PROTOCOL']." 503 Service Unavailable");
	exit($connect->error);
}
else $res_search = $sel_search->fetch_assoc();
 ?>

	<form role="search" id="form-search" action="/<?php echo $res_search['url']; ?>" method="post">

		<div id="input-search" class="inline-block">

			<div class="flex">

				<input type="search" placeholder="<?_e("Search")?>" name="search" id="search">

					<button type="submit" class="p-8" value="<?php _e("Search")?>" aria-label="<?php _e("Search")?>">
						<i class="icon moon-search" aria-hidden="true"></i>
					</button>

				</input>

			</div>

		</div>

	</form>
