<style>
.textecarte {
	text-shadow: 2px 2px 5px white;
}

.color-alt, .color-alt h2, .color-alt h3 {
	color : #1d3a3f;
	font-weight : bold;
}
</style>

<?
// Si on a posté le formulaire
if(isset($_POST["email"]) and $_POST["message"] and isset($_POST["question"]) and !$_POST["mrrobot"])// mrrobot pour éviter les bots qui remplisse tous les champs
{
	include_once("../../../config.php");// Les variables

	if($_SESSION["nonce_contact"] and $_SESSION["nonce_contact"] == $_POST["nonce_contact"])// Protection CSRF
	{
		if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))// Email valide
		{
			if(hash('sha256', $_POST["question"].$GLOBALS['pub_hash']) == $_POST["question_hash"])// Captcha valide
			{
				$subject = "[".$GLOBALS['sitename']."] ".htmlspecialchars($_POST["email"]);

				$message = nl2br(strip_tags($_POST["message"]));

				$message .= "<br /><br />-------------------------------------------------------<br />";

				if($_POST['referer']) $message .= "Referer : ".htmlspecialchars($_POST['referer'])."<br />";

				$message .= "Consentement : ".htmlspecialchars($_POST["rgpd_text"])."<br />";
				$message .= "IP du Visiteur : ".getenv("REMOTE_ADDR")."<br />";
				$message .= "Host : ".gethostbyaddr($_SERVER["REMOTE_ADDR"])."<br />";
				$message .= "IP du Serveur : ".getenv("SERVER_ADDR")."<br />";
				$message .= "User Agent : ".getenv("HTTP_USER_AGENT")."<br />";

				$header="Content-type:text/html; charset=utf-8\r\nFrom:".($_POST["email"] ? htmlspecialchars($_POST["email"]) : $GLOBALS['email_contact']);

				if(mail($GLOBALS['email_contact'], $subject, stripslashes($message), $header))
				{
					?>
					<script>
					light(__("Message sent"));

					// Icone envoyer
					$("#contact a .fa-spin").removeClass("fa-spin fa-cog").addClass("fa-ok");
					</script>
					<?
				}
				else {
					?><script>error("Erreur lors de l'envoi du mail");</script><?
					//echo error_get_last()['message']; print_r(error_get_last());
				}
			}
			else
			{
				?>
				<script>
				error(__("Wrong answer to the verification question!"));

				// On rétablie le formulaire
				activation_form();
				</script>
				<?
			}
		}
		else
		{
			?>
			<script>
			error(__("Invalid email!"));

			// On rétablie le formulaire
			activation_form();
			</script>
			<?
		}
	}
}
else// Affichage du formulaire
{
	if(!$GLOBALS['domain']) exit;

	$operators = array("+", "-");
	$operator = $operators[array_rand($operators)];
	$nb1 = rand(1, 10);
	$nb2 = ($operator === '-') ? mt_rand(1, $nb1) : mt_rand(1, 10); // on évite les résultats négatifs en cas de soustraction
	eval('$question = strval('.$nb1.$operator.$nb2.');');
	$question_hash = hash('sha256', $question.$GLOBALS['pub_hash']);

	?>
	<script>
	add_translation({
		"Thank you for completing all the required fields!" : {"fr" : "Merci de remplir tous les champs obligatoires !"},
		"Wrong answer to the verification question!" : {"fr" : "R\u00e9ponse erron\u00e9e \u00e0 la question de vérification !"},
		"Invalid email!" : {"fr" : "Mail invalide !"},
		"Message sent" : {"fr" : "Message envoy\u00e9"},
	});
	</script>


	<section class="mod tc">
		<h1><?txt('titre')?></h1>
	</section>


	<section class="mw960p mod center mtl">

		<article class="w70 center prl pbl mbl">

			<h2 class="mtn"><?txt('sstitre')?></h2>

			<div><?txt('texte')?></div>

			<form id="contact" class="mat">

				<div>
					<input type="email" name="email" id="email" required placeholder="<?_e("Email")?>" class="w40 vatt"><span class="wrapper big white vam o50">@</span>
				</div>

				<div>
					<textarea name="message" id="message" required placeholder="<?_e("Message")?>" class="w100 mbt" style="height: 200px;"></textarea>
				</div>


				<div class="mod">
					<!-- Question -->
					<div class="fl w20">
						<?=($nb1." ".$operator." ".$nb2);?> = <input type="text" name="question" id="question" required placeholder="?" class="w50p tc">
					</div>

					<!-- RGPD -->
					<div class="fr w80 tr">
						<input type="checkbox" name="rgpdcheckbox" id="rgpdcheckbox" required>
						<label for="rgpdcheckbox" class="inline" style="text-transform: none;"><?txt('rgpd')?></label>
					</div>
				</div>

				<!-- Bouton envoyer -->
				<div class="fr mtm mbl">
					<a href="javascript:$('#contact').submit();void(0)" class="bt bold">
						<?_e(array("Send" => array("fr" => "Envoyer")))?>
						<i class="fa fa-mail-alt mlt"></i>
					</a>
				</div>


				<!-- Pour bien afficher les required -->
				<button class="none"></button>

				<input type="hidden" name="rgpd_text" value="<?=htmlspecialchars($GLOBALS['content']['rgpd']);?>">

				<input type="hidden" name="question_hash" value="<?=$question_hash;?>">

				<input type="hidden" name="mrrobot" value="">

				<input type="hidden" name="nonce_contact" value="<?=nonce("nonce_contact");?>">

				<input type="hidden" name="referer" value="<?=htmlspecialchars((isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:""));?>">

			</form>

		</article>

	</section>


	<script>
	// Pour rétablir le fonctionnement du formulaire
	function activation_form(){
		desactive = false;

		$("#contact a .fa-cog").removeClass("fa-spin fa-cog").addClass("fa-mail-alt");

		// Activation des champs du formulaire
		$("#contact input, #contact textarea, #contact button").attr("readonly", false).removeClass("disabled");

		// On peut soumettre le formulaire avec la touche entrée
		$("#contact").submit(function(event){ send_contact(event) });
		$("#contact button").attr("disabled", false);

		// Active le lien submit
		$("#contact a").on("click", function(event) { send_contact(event) });
	}

	desactive = false;
	function send_contact(event)
	{
		event.preventDefault();

		if($("#question").val()=="" || $("#message").val()=="" || $("#email").val()=="" || $("#rgpdcheckbox").prop("checked") == false)
		error(__("Thank you for completing all the required fields!"));
		else
		{
			desactive = true;

			// Icone envoi en cours
			$("#contact a .fa-mail-alt").removeClass("fa-mail-alt").addClass("fa-spin fa-cog");

			// Désactive le formulaire
			$("#contact input, #contact textarea, #contact button").attr("readonly", true).addClass("disabled");

			// Désactive la soumission du formulaire
			$("#contact").off("submit");

			// Désactive le bouton submit caché (pour les soumissions avec la touche entrée)
			$("#contact button").attr("disabled", true);

			// Désactive le lien submit
			$("#contact a").on("click", function(event) { event.preventDefault(); });

			$.ajax(
				{
					type: "POST",
					url: path+"theme/"+theme+(theme?"/":"")+"tpl/contact.php",
					data: $("#contact").serializeArray(),
					success: function(html){ $("body").append(html); }
				});
			}
		}

		$("#contact").submit(function(event)
		{
			send_contact(event)
		});
		</script>

		<?
	}
	?>

	<div class="cover" <?bg('bg-carte')?>>
		<section class="color-alt textecarte mw960p center tc mod pbl">
			<h2><?txt('titre-2')?></h2>
			<div class="mw960p tl fr prl">
				<article class="mtm animation slide-right">
					<h3 class="mbn"><?txt('sstitre-2')?></h3>
					<div><?txt('txt-2')?></div>
				</article>

				<article class="mtl animation slide-right">
					<h3 class="mbn"><?txt('sstitre-3')?></h3>
					<div><?txt('txt-3')?></div>
				</article>
			</div>
		</section>
	</div>
