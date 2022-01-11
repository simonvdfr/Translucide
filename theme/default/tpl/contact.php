<?php 

if(@$_GET['mode']) 
{
	include_once("../../../config.php");// Les variables
	include_once("../../../api/db.php");// Connexion à la db
	include_once("../../../api/function.php");// Fonction
}

// traductions pour le formulaire
$add_translation = array(
	'fields marked with <span class="red">*</span>  are required' => ['fr' => 'Les champs marqués d\'une <span class="red">*</span> sont obligatoires'],
	'Your email adress' => ['fr' => 'Votre adresse mail'],
	'Your message' => ['fr' => "Votre message"],
	'I agree that this information will be stored and processed to contact me again' =>  ['fr' => 'J\'accepte que ces informations soient stockées et traitées pour me recontacter'],
	'Send' => ['fr' => 'Envoyer'],

	'Error sending email' => ['fr' => 'Erreur lors de l\'envoi du mail'],
	"Wrong answer to the verification question!" => ["fr" => "Réponse erronée à la question de vérification !"],
);

add_translation($add_translation);

switch(@$_GET['mode'])
{
	// affichage du formulaire de contact
	default :

		if(!$GLOBALS['domain']) exit;

	?>

		<script>
			//à supprimer ?
			add_translation({
				"Thank you for completing all the required fields!" : {"fr" : "Merci de remplir tous les champs obligatoires !"},
				"Wrong answer to the verification question!" : {"fr" : "R\u00e9ponse erron\u00e9e \u00e0 la question de vérification !"},
				"Invalid email!" : {"fr" : "Mail invalide !"},
				"Message sent" : {"fr" : "Message envoy\u00e9"},
			});
		</script>

		<style>
			.required::after {content:' *';font-size:1.4em; color:darkred; font-variant:super;}
			#email_contact, #message, #question, #rgpdcheckbox {border:1px solid #666; border-radius: .5rem;}
			#email_contact:focus, #message:focus, #question:focus {border:1px solid #78cfd6; outline:1px solid #78cfd6;}

			[type="checkbox"] {
				cursor: pointer;
				appearance: none;
				width: 2rem;
				height: 2rem;
				vertical-align: text-bottom;
				outline: 0;
				background-color: #fff;
				transition: background-color .15s;
			}
			[type="checkbox"]:checked {
				background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA4IDgiPjxwYXRoIGZpbGw9IiNGRkYiIGQ9Ik02LjQgMWwtLjcuNy0yLjggMi44LS44LS44LS43LS43TDAgNC40bC43LjcgMS41IDEuNS43LjcuNy0uNyAzLjUtMy41LjctLjdMNi40IDF6Ii8+PC9zdmc+");
				background-size: 60% 60%;
				background-position: 50%;
				background-repeat: no-repeat;
				background-color: #35747f;
				border:  1px solid #35747f !important;
			}

			[disabled="true"] {background-color: #eee; color: #828282;}
		</style>

		<section class="mw960p mod center mtm mbm">

			<article class="w80 center">

				<?php h1('titre', 'tc')?>

				<?php txt('texte', 'mbl')?>

				<form id="contact">

					<p class="mbs"><?_e('fields marked with <span class="red">*</span>  are required')?></p>

					<!-- Champs -->
					<div class="mbm">

						<label for="email_contact" class="required block"><?php _e("Your email adress")?></label>
						<input type="email" name="email_contact" id="email_contact" placeholder="<?php _e("Email")?>" class="w100" required aria-required="true">

					<div>

					<div class="mbm">

						<label for="email_contact" class="required none" aria-hidden="true"><?php _e("Email")?></label>
						<input name="reponse" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+.[a-z]{2,3}$" placeholder="nom@domaine.com" aria-hidden="true">

						<label for="message" class="required block"><?php _e("Your message")?></label>
						<textarea name="message" id="message" placeholder="<?php _e("Message")?>" class="w100" style="height: 200px;"  required aria-required="true"></textarea>

					</div>

					<!-- Question -->
					<div class="mbm">
						<?

							$chiffre = array('zéro', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix');
							$operators = array("+", "-");
							$operator = $operators[array_rand($operators)];
							$nb1 = rand(1, 10);
							$nb2 = ($operator === '-') ? mt_rand(1, $nb1) : mt_rand(1, 10); // on évite les résultats négatifs en cas de soustraction
							eval('$question = strval('.$nb1.$operator.$nb2.');');
							$question_hash = hash('sha256', $question.$GLOBALS['pub_hash']);

						?>
						<label for="question" class="vam"><span class="required"><?=($chiffre[$nb1]." ".$operator." ".$chiffre[$nb2]);?></span> = </label> <input type="text" name="question" id="question" placeholder="5 ?" class="w50p tc vam" required aria-required="true">
						
					</div>

					<!-- RGPD -->
					<div class="mbm">
						<input type="checkbox" name="rgpdcheckbox" id="rgpdcheckbox" class="vam" required aria-required="true">
						<label for="rgpdcheckbox" class="required"><?php span('rgpd',array('default' => __('I agree that this information will be stored and processed to contact me again')))?></label>
					</div>

					<!-- Bouton envoyer -->
					<div class="tc">
						<button id="send" type="submit" class="bt bold pat plm prm">
							<?php _e('send')?>
							<i class="fa fa-mail-alt mlt"></i>
						</button>
					</div>

					<input type="hidden" name="rgpd_text" value="<?=htmlspecialchars(@$GLOBALS['content']['rgpd']);?>">

					<input type="hidden" name="question_hash" value="<?=$question_hash;?>">

					<input type="hidden" name="nonce_contact" value="<?=nonce("nonce_contact");?>">

					<input type="hidden" name="referer" value="<?=htmlspecialchars((isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:""));?>">

				</form>

			</article>

		</section>

		<script>
			var formContact;

			window.onload = function () {
				formContact = document.querySelector('#contact');
				formContact.onsubmit = sendMail;
			}

			function sendMail(event) {

				event.preventDefault();

				const formData = new FormData(formContact);
				
				disable_form(true); // désactivation du formulaire

				const xhr = new XMLHttpRequest(); // envoi de la requête
				xhr.open('POST', path+"theme/"+theme+(theme?"/":"")+"tpl/contact.php?mode=send-mail");
				xhr.onload = function() {
					const response = document.createRange().createContextualFragment(this.response);
					document.body.append(response); 
				}
				xhr.send(formData);

			}

			// activation/desactivation du formulaire
			function disable_form(disabled) {
				
				document.querySelectorAll('input, textarea, button[type="submit"]').forEach(function(elem) {
					if(disabled) elem.setAttribute('disabled',true); else elem.removeAttribute('disabled');
				});
				
			}

			function erreur(input, errMsg){

				//s'il n'y a pas déjà un message d'erreur
				if(!document.querySelector('#erreur'+ input)) {

					let err = document.createElement('div');

					err.setAttribute('role','alert');
					err.classList.add('error','pat', 'mbt');
					err.innerText = errMsg;
					err.id='erreur' + input;
					document.querySelector('#'+ input).setAttribute('aria-describedby', err.id);
					document.querySelector('[for="'+ input +'"]').parentNode.insertBefore(err, document.querySelector('[for="'+ input +'"]'));

				}

				disable_form(false);

				document.getElementById(input).focus();

			}

		</script>

	<?
	break;


	// SCRIPT D'ENVOIE DE L'EMAIL
	case 'send-mail' :

		if(isset($_POST["email_contact"]) && $_POST["message"] && isset($_POST["question"]) && !$_POST["reponse"])// reponse pour éviter les bots qui remplisse tous les champs
		{

			if($_SESSION["nonce_contact"] && $_SESSION["nonce_contact"] == $_POST["nonce_contact"])// Protection CSRF
			{
				if(filter_var($_POST["email_contact"], FILTER_VALIDATE_EMAIL))// Email valide
				{
					if(hash('sha256', $_POST["question"].$GLOBALS['pub_hash']) == $_POST["question_hash"])// Captcha valide
					{
						$from = ($_POST["email_contact"] ? htmlspecialchars($_POST["email_contact"]) : $GLOBALS['email_contact']);

						$subject = "[".htmlspecialchars($_SERVER['HTTP_HOST'])."] ".htmlspecialchars($_POST["email_contact"]);

						// Message
						$message = (strip_tags($_POST["message"]));

						$message .= "\n\n-------------------------------------------------------\n";

						if($_POST['referer']) $message .= "Referer : ".htmlspecialchars($_POST['referer'])."\n";

						$message .= "Consentement : ".htmlspecialchars($_POST["rgpd_text"])."\n";
						$message .= "IP du Visiteur : ".getenv("REMOTE_ADDR")."\n";
						$message .= "Host : ".gethostbyaddr($_SERVER["REMOTE_ADDR"])."\n";
						$message .= "IP du Serveur : ".getenv("SERVER_ADDR")."\n";
						$message .= "User Agent : ".getenv("HTTP_USER_AGENT")."\n";


						// header
						$header = "From:".$from."\r\n";
						$header.= "Reply-To: ".$from."\r\n";
						$header.= "Content-Type: text/plain; charset=ISO-8859-1\r\n";// utf-8 ISO-8859-1


						if(mail($GLOBALS['email_contact'], $subject, stripslashes($message), $header))
						{
							?>
							<script>
													
								let log = document.createElement('div');

								log.setAttribute('role','log');
								log.classList.add('pas','mbs','mts');
								log.innerText = __('Message sent');
								log.id='highlight';
								formContact.setAttribute('aria-describedby', log.id);
								document.querySelector('#send').parentNode.insertBefore(log, document.querySelector('#send').nextSibling);

								disable_form(true);
							</script>
							<?php 
						}
						else {
							?>
							<script>						
								let err = document.createElement('div');

								err.setAttribute('role','alert');
								err.classList.add('error','pas','mbs','mts');
								err.innerText = __('Error sending email');
								err.id='erreursend';
								document.querySelector('#send').setAttribute('aria-describedby', err.id);
								document.querySelector('#send').parentNode.insertBefore(err, document.querySelector('#send').nextSibling);
							</script>
							<?php 
						}
					}
					else
					{
						?>
						<script>
							erreur("question",__("Wrong answer to the verification question!"));
						</script>
						<?php 
					}
				}
				else
				{
					?>
					<script>
						erreur("email_contact", __("Invalid email!"));
					</script>
					<?php 
				}
			}

		}

	break;

}







// Si on a posté le formulaire
/*if(isset($_POST["email_contact"]) and $_POST["message"] and isset($_POST["question"]) and !$_POST["reponse"])// reponse pour éviter les bots qui remplisse tous les champs
{
	include_once("../../../config.php");// Les variables

	if($_SESSION["nonce_contact"] and $_SESSION["nonce_contact"] == $_POST["nonce_contact"])// Protection CSRF
	{
		if(filter_var($_POST["email_contact"], FILTER_VALIDATE_EMAIL))// Email valide
		{
			if(hash('sha256', $_POST["question"].$GLOBALS['pub_hash']) == $_POST["question_hash"])// Captcha valide
			{
				$from = ($_POST["email_contact"] ? htmlspecialchars($_POST["email_contact"]) : $GLOBALS['email_contact']);


				$subject = "[".htmlspecialchars($_SERVER['HTTP_HOST'])."] ".htmlspecialchars($_POST["email_contact"]);


				// Message
				$message = (strip_tags($_POST["message"]));

				$message .= "\n\n-------------------------------------------------------\n";

				if($_POST['referer']) $message .= "Referer : ".htmlspecialchars($_POST['referer'])."\n";

				$message .= "Consentement : ".htmlspecialchars($_POST["rgpd_text"])."\n";
				$message .= "IP du Visiteur : ".getenv("REMOTE_ADDR")."\n";
				$message .= "Host : ".gethostbyaddr($_SERVER["REMOTE_ADDR"])."\n";
				$message .= "IP du Serveur : ".getenv("SERVER_ADDR")."\n";
				$message .= "User Agent : ".getenv("HTTP_USER_AGENT")."\n";


				// header
				$header = "From:".$from."\r\n";
				$header.= "Reply-To: ".$from."\r\n";
				$header.= "Content-Type: text/plain; charset=ISO-8859-1\r\n";// utf-8 ISO-8859-1


				if(mail($GLOBALS['email_contact'], $subject, stripslashes($message), $header))
				{
					?>
					<script>
					light(__("Message sent"));

					// Icone envoyer
					$("#contact a .fa-spin").removeClass("fa-spin fa-cog").addClass("fa-ok");
					</script>
					<?php 
				}
				else {
					?><script>error("Erreur lors de l'envoi du mail");</script><?php 
					//echo error_get_last()['message']; print_r(error_get_last());
				}
			}
			else
			{
				?>
				<script>
				error(__("Wrong answer to the verification question!"));

				// On rétablie le formulaire
				disable_form();
				</script>
				<?php 
			}
		}
		else
		{
			?>
			<script>
			error(__("Invalid email!"));

			// On rétablie le formulaire
			disable_form();
			</script>
			<?php 
		}
	}
}
else// Affichage du formulaire
{
	if(!$GLOBALS['domain']) exit;

	$chiffre = array('zéro', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix');
	$operators = array("+", "-");
	$operator = $operators[array_rand($operators)];
	$nb1 = rand(1, 10);
	$nb2 = ($operator === '-') ? mt_rand(1, $nb1) : mt_rand(1, 10); // on évite les résultats négatifs en cas de soustraction
	eval('$question = strval('.$nb1.$operator.$nb2.');');
	$question_hash = hash('sha256', $question.$GLOBALS['pub_hash']);
	?>



	


	<script>
		// Pour rétablir le fonctionnement du formulaire
		function disable_form(){
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

			if($("#question").val()=="" || $("#message").val()=="" || $("#email_contact").val()=="" || $("#rgpdcheckbox").prop("checked") == false)
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
<?php 
}
?>*/