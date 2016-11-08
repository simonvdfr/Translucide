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
			include_once("../api/function.php");
			include_once("../api/db.php");

			$nom = $GLOBALS['connect']->real_escape_string($_POST["nom"]);
			$tel = $GLOBALS['connect']->real_escape_string($_POST["tel"]);
			$email = $GLOBALS['connect']->real_escape_string($email);
			$ip = $GLOBALS['connect']->real_escape_string(ip());

			$subject = "[".utf8_encode($GLOBALS['sitename'])."] Inscription alerte mail ".htmlspecialchars($email);

			$message .= htmlspecialchars($nom)."<br>".htmlspecialchars($email)."<br>".htmlspecialchars($tel)."<br>";

			$message .= "<br><br>-------------------------------------------------------<br>";

			$message .= "IP du Visiteur : ".getenv("REMOTE_ADDR")."<br>";
			$message .= "Host : ".gethostbyaddr($_SERVER["REMOTE_ADDR"])."<br>";		
			$message .= "User Agent : ".getenv("HTTP_USER_AGENT")."<br>";
			$message .= "IP du Serveur : ".getenv("SERVER_ADDR")."<br>";

			$header="Content-type:text/html; charset=utf-8\r\nFrom:".($email ? htmlspecialchars($email) : $GLOBALS['email_contact']);

			mail($GLOBALS['email_contact'], $subject, stripslashes($message), $header);


			//CREATE TABLE IF NOT EXISTS `".$GLOBALS['db_prefix']."email` (`email` varchar(100) NOT NULL, `nom` varchar(100) NOT NULL, `tel` varchar(50) NOT NULL, `ip` varchar(16) NOT NULL, `date` datetime NOT NULL, UNIQUE KEY `email` (`email`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;


			if($GLOBALS['connect']->query("REPLACE INTO ".$GLOBALS['db_prefix']."email SET email='".($email)."', nom='".($nom)."', tel='".($tel)."', ip='".($ip)."', date=NOW()"))
			{
				?>
				<script>
				
				</script>
				<script>
				$(document).ready(function()
				{
					//light(__("We will keep you informed of progress"));
					//$("#alertmail").fadeOut();
					$("#alertmail").fadeOut("400", function(){
						$("#alertmail").html("<h2>"+ __("We will keep you informed of progress") +" <i class='fa fa-check checked'></i></h2>").fadeIn();
					});
				});
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

	<form id="alertmail" class="mls mtm">

		<input type="text" name="nom" placeholder="Nom Pr&eacute;nom" required maxlength="100" class="w50 mbs">

		<br>

		<input type="text" name="tel" placeholder="Num&eacute;ro de t&eacute;l&eacute;phone" required maxlength="50" class="w50 mbs">

		<br>

		<input type="email" name="email" placeholder="Mon e-mail" required maxlength="100" class="w50 mbs">

		<br>
				
		<input type="hidden" name="champ_vide" value="">

		<input type="hidden" name="nonce_alertmail" value="<?=nonce("nonce_alertmail");?>">

		<button class="mbs w50"><span><i class="fa fa-fw fa-envelope"></i> <?_e("M'avertir de l'avancée de ".utf8_encode($GLOBALS['sitename']))?></span></button>

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
			url: "<?=$GLOBALS['path']."plugin/alert-mail.php"?>",				
			data: $(this).serializeArray(),
			success: function(html){ $("body").append(html); }
		});
	});
	</script>
	<?
}
?>