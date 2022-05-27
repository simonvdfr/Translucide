<?php if(!$GLOBALS['domain']) exit;?>

<!-- Formulaire de recherche -->
	<form role="search" id="form-search" action="/search" method="post">

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
