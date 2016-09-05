<?
// Si on a posté le formulaire
if($_POST["email"] and !$_POST["champ_vide"])// champ_vide pour éviter les bots qui remplisse tous les champs
{
	include_once("../config.php");// Les variables

	if($_SESSION["nonce_alertmail"] and $_SESSION["nonce_alertmail"] == $_POST["nonce_alertmail"])// Protection CSRF
	{
		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

		if(filter_var($email, FILTER_VALIDATE_EMAIL))// Email valide
		{
			/*$subject = "[".$GLOBALS['sitename']."] ".htmlspecialchars($_POST["email"]);

			$message = nl2br(strip_tags($_POST["message"]));

			$message .= "<br /><br />-------------------------------------------------------<br />";

			if($_POST['referer']) $message .= "Referer : ".htmlspecialchars($_POST['referer'])."<br />";

			$message .= "IP du Visiteur : ".getenv("REMOTE_ADDR")."<br />";
			$message .= "Host : ".gethostbyaddr($_SERVER["REMOTE_ADDR"])."<br />";		
			$message .= "User Agent : ".getenv("HTTP_USER_AGENT")."<br />";
			$message .= "IP du Serveur : ".getenv("SERVER_ADDR")."<br />";

			$header="Content-type:text/html; charset=utf-8\r\nFrom:".($_POST["email"] ? htmlspecialchars($_POST["email"]) : $GLOBALS['email_contact']);

			if(mail($GLOBALS['email_contact'], $subject, stripslashes($message), $header))*/


			//CREATE TABLE IF NOT EXISTS `".$GLOBALS['db_prefix']."email` (`email` varchar(100) NOT NULL, `ip` varchar(16) NOT NULL, `date` datetime NOT NULL, UNIQUE KEY `email` (`email`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			include_once("../api/fonction.php");
			include_once("../api/db.php");

			$email = $GLOBALS['connect']->real_escape_string($email);
			$ip = $GLOBALS['connect']->real_escape_string(ip());

			if($GLOBALS['connect']->query("REPLACE INTO ".$GLOBALS['db_prefix']."email SET email='".addslashes($email)."', ip='".addslashes($ip)."', date=NOW()"))
			{
				?>
				<script>
				light(__("We will keep you informed of progress"));
				//$("#alertmail").fadeOut();
				</script>
				<?
			}
		}
		else 
		{
			?>
			<script>	
			error(__("Invalid email!"));
			$("#email").effect("highlight").effect("highlight");
			</script>
			<?
		}
	}
}
else// Affichage du formulaire
{
	if(!$GLOBALS['microtime']) exit;
	?>

	<div><?txt('txt-attente')?></div>

	<form id="alertmail" class="mas">

		<input type="email" name="email" placeholder="nom.prenom@mail.fr" required maxlength="100" class="w50 mbs">
				
		<input type="hidden" name="champ_vide" value="">

		<input type="hidden" name="nonce_alertmail" value="<?=nonce("nonce_alertmail");?>">

		<button class="mbs"><i class="fa fa-fw fa-envelope"></i> <?_e("M'avertir de l'avancée du projet")?></button>

	</form>


	<script>
	add_translation({
		"Invalid email!" : {"fr" : "Mail invalide !"},
		"We will keep you informed of progress" : {"fr" : "Nous vous tiendrons au courant des avanc\u00e9es"},
	});

	$("#alertmail").submit(function(event)
	{
		event.preventDefault();

		// Désactive le submit
		$("#alertmail button").attr("disabled", true);
		
		// Envoi le mail
		$.ajax(
		{
			type: "POST",
			url: "<?=$GLOBALS['path']."plugin/alertmail.php"?>",				
			data: $(this).serializeArray(),
			success: function(html){ $("body").append(html); }
		});
	});
	</script>
	<?
}
?>