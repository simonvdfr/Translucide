<?php 

// @todo accessibilité
// changer le titre si le formulaire est bien soumis ou si erreur
// ------Si échape dans la popin ça ferme
// bloquer la tabulation dans la popin => boucle à la fin de la modal pour retour au premier elem ?
// bloquer la nav au clavier dans la popin => forcer le focus dans la modal ?

switch(@$_GET['mode'])
{
	// AFFICHAGE DU FORMULAIRE DE CONTACT
	default :

		if(!$GLOBALS['domain']) exit;		
		?>


		<script>
		add_translation({
			"Thank you for completing all the required fields!" : {"fr" : "Merci de remplir tous les champs obligatoires !"},
			"Wrong answer to the verification question!" : {"fr" : "R\u00e9ponse erron\u00e9e \u00e0 la question de vérification !"},
			"Error sending email" : {"fr" : "Erreur lors de l'envoi du mail"},
			"Invalid email!" : {"fr" : "Mail invalide !"},
			"Message sent" : {"fr" : "Message envoy\u00e9"},
		});
		</script>
		

		<style>
			#email_contact, #message, #question {
				border: 0.1em solid #35747f;
				background-color: #f7f7f7;
			}
		</style>


		<section class="mw960p mod center mtm">

			<article class="w80 center">

				<?php h1('titre', 'tc')?>

				<?php txt('texte', 'mbl')?>

				<form id="contact">

					<?php txt('texte-champs-obligatoires', 'mbm')?>

					<div class="mbm">
						<label for="email_contact"><?php span('texte-label-email')?><span class="red">*</span></label><br>
						<input type="email" name="email_contact" id="email_contact" autocomplete="email" placeholder="<?php _e("Email")?>" class="w40 vatt" required><span class="wrapper big white vam o50">@</span>

						<label for="reponse" class="hidden" aria-hidden="true"><?php _e("Email")?></label>
						<input name="reponse" id="reponse" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+.[a-z]{2,3}$" placeholder="nom@domaine.com" aria-hidden="true">
					</div>

					<div>
						<label for="message"><?php span('texte-label-message')?><span class="red">*</span></label>
						<textarea name="message" id="message" placeholder="<?php _e("Message")?>" class="w100 mbt" style="height: 200px;" required></textarea>
					</div>


					<div class="mod">

						<!-- Question -->
						<?
						$chiffre = array('zéro', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix');
						$operators = array("+", "-");
						$operator = $operators[array_rand($operators)];
						$nb1 = rand(1, 5);//10
						$nb2 = ($operator === '-') ? mt_rand(1, $nb1) : mt_rand(1, 5);// on évite les résultats négatifs en cas de soustraction
						eval('$question = strval('.$nb1.$operator.$nb2.');');
						$question_hash = hash('sha256', $question.$GLOBALS['pub_hash']);
						// On change le signe "-" moins de calcul en "−" lisible en accessibilité
						?>
						<div>
							<label for="question">
								<?php span('texte-label-question')?><span class="red">*</span> :
								<?=($chiffre[$nb1]." ".($operator=='-'?'−':$operator)." ".$chiffre[$nb2]);?> = 
							</label>
							<input type="text" name="question" id="question" placeholder="?" class="w50p tc" autocomplete="off" required>

							<input type="hidden" name="question_hash" value="<?=$question_hash;?>">
						</div>

						<!-- RGPD -->
						<div class="mtm">
							<label for="rgpdcheckbox" class="inline" style="text-transform: none;"><?php span('rgpd')?><span class="red">*</span></label>
							<input type="checkbox" name="rgpdcheckbox" id="rgpdcheckbox" required>
						</div>

					</div>


					<!-- Bouton envoyer -->
					<div class="fr mtm mbl">
						<button type="submit" id="send" class="bt bold">
							<?php _e(array("Send" => array("fr" => "Envoyer")))?>
							<i class="fa fa-mail-alt mlt" aria-hidden="true"></i>
						</button>
					</div>


					<input type="hidden" name="rgpd_text" value="<?=htmlspecialchars(@$GLOBALS['content']['rgpd']);?>">

					<input type="hidden" name="nonce_contact" value="<?=nonce("nonce_contact");?>">

					<input type="hidden" name="referer" value="<?=htmlspecialchars((isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:""));?>">

				</form>

			</article>

		</section>


		<script>
			// Titre de la page en cours
			title = document.title;

			// Pour rétablir le fonctionnement du formulaire
			function activation_form(){
				desactive = false;

				$("#contact #send .fa-cog").removeClass("fa-spin fa-cog").addClass("fa-mail-alt");

				// Activation des champs du formulaire
				$("#contact input, #contact textarea, #contact button").attr("readonly", false).removeClass("disabled");

				// On peut soumettre le formulaire avec la touche entrée
				//$("#contact").on("submit", function(event) { send_mail(event) });
				$("#contact button").attr("disabled", false);
			}

			desactive = false;
			function send_mail(event)
			{
				event.preventDefault();

				if($("#question").val()=="" || $("#message").val()=="" || $("#email_contact").val()=="" || $("#rgpdcheckbox").prop("checked") == false)
				error(__("Thank you for completing all the required fields!"));
				else
				{
					desactive = true;

					// Icone envoi en cours
					$("#contact #send .fa-mail-alt").removeClass("fa-mail-alt").addClass("fa-spin fa-cog");

					// Désactive le formulaire
					$("#contact input, #contact textarea, #contact button").attr("readonly", true).addClass("disabled");

					// Désactive le bouton submit (pour les soumissions avec la touche entrée)
					//$("#contact").off("submit");
					$("#contact button").attr("disabled", true);

					$.ajax(
						{
							type: "POST",
							url: path+"theme/"+theme+(theme?"/":"")+"tpl/contact.php?mode=send-mail",
							data: $("#contact").serializeArray(),
							success: function(html){ $("body").append(html); }
						});
				}
			}

			$("#contact").submit(function(event)
			{
				send_mail(event)
			});
		</script>
		<?php 

	break;



	// SCRIPT D'ENVOIE DE L'EMAIL
	case 'send-mail':

	print_r($_REQUEST);

		// Si on a posté le formulaire
		if(isset($_POST["email_contact"]) and $_POST["message"] and isset($_POST["question"]) and !$_POST["reponse"])// reponse pour éviter les bots qui remplisse tous les champs
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
						$header = "From:".$GLOBALS['email_contact']."\r\n";// Pour une meilleure délivrabilité des mails
						$header.= "Reply-To: ".$from."\r\n";
						$header.= "Content-Type: text/plain; charset=utf-8\r\n";// utf-8 ISO-8859-1


						if(mail($GLOBALS['email_contact'], $subject, stripslashes($message), $header))
						{
							?>
							<script>
								popin(__("Message sent"));
								document.title = title +' - '+ __("Message sent");

								// Icone envoyer
								$("#contact #send .fa-spin").removeClass("fa-spin fa-cog").addClass("fa-ok");
							</script>
							<?php 
						}
						else {
							?>
							<script>
								error(__("Error sending email"), 'nofade', $("#send"));
								document.title = title +' - '+ __("Error sending email");
								
								activation_form();// On rétablie le formulaire
							</script>
							<?php 
							//echo error_get_last()['message']; print_r(error_get_last());
						}
					}
					else
					{
						?>
						<script>
							error(__("Wrong answer to the verification question!"), 'nofade', $("#question"));
							document.title = title +' - '+ __("Wrong answer to the verification question!");
							
							activation_form();// On rétablie le formulaire
						</script>
						<?php 
					}
				}
				else
				{
					?>
					<script>
						error(__("Invalid email!"), 'nofade', $("#email_contact"));
						document.title = title +' - '+ __("Invalid email!");
						
						activation_form();// On rétablie le formulaire
					</script>
					<?php 
				}
			}
		}

	break;
}
?>