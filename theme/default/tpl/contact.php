<?php

switch(@$_GET['mode'])
{
	// AFFICHAGE DU FORMULAIRE DE CONTACT
	default :

		if(!$GLOBALS['domain']) exit;

		// Encrypte le mail pour permettre des envois de mail que vers des mails ajoutés par l'admin
		$GLOBALS['content']['email-hash'] = hash("sha256", base64_decode(@$GLOBALS['content']['email-to']) . $GLOBALS['pub_hash']);

		// Si pas de sujet on reprend le title de la page
		if(!@$GLOBALS['content']['subject']) $GLOBALS['content']['subject'] = @$GLOBALS['content']['title'];
		?>

		<?php include('theme/'.$GLOBALS['theme'].'/mdl/hero.php');?>

		<script>
		add_translation({
			"Thank you for completing all the required fields!" : {"fr" : "Merci de remplir tous les champs obligatoires !"},
			"Wrong answer to the verification captcha!" : {"fr" : "R\u00e9ponse erron\u00e9e au captcha de vérification !"},
			"Error sending email" : {"fr" : "Erreur lors de l'envoi du mail"},
			"Invalid email!" : {"fr" : "Mail invalide !"},
			"Message sent" : {"fr" : "Message envoy\u00e9"},
		});
		</script>

		<section id="contact">
			<div class="layout-contact layout-maxed">
				<div class="md:flex p-36">

					<article class="contact-card border-rounded flex flex-col justify-between flex-grow bg-blue bg-section bg-overlay bg-glazient color-white sm:p-24 md:m-24" <?php bg('bg-hero')?>>
						<div class="card-content-top">
							<?php h3('title-card', 'color-white')?>
							<?php txt('intro-card', 'text-bold md:mt-24')?>
						</div>
						<div class="card-content-bottom">
							<ul class="card-info is-unstyled text-smaller text-bold mt-24 md:mb-24">
								<li><?php txt('item-1', 'ml-8')?></li>
								<li><?php txt('item-2', 'ml-8')?></li>
								<li><?php txt('item-3', 'ml-8')?></li>
							</ul>
						</div>
					</article>

					<article class="contact-form sm:p-24 md:m-24">

						<form id="form-contact">

							<?php h2('title-form', 'color-blue')?>
							<?php txt('intro-form', 'mb-16')?>

							<div class="editable-hidden">

								<label for="email-to"><?php _e("Email to")?> (your@domain.com)<span class="color-red">*</span></label><br>
								<?input("email-to", array('name' => 'email-to', 'placeholder' => __("Email to"), 'class' => 'w100'));?>
								<?input("email-hash", array('name' => 'email-hash', 'type' => 'hidden', 'class' => 'hidden'));?>

							</div>

							<div class="grid grid-cols-2 gap-36 mt-24">
								<div>
									<input type="text" name="name" id="name" placeholder="<?php _e("Name / Forname")?> *" class="w100" required>
								</div>
								<div>
									<input type="email" name="email-from" id="email-from" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+.[a-z]{2,3}$" autocomplete="email" placeholder="<?php _e("Email")?> *" class="w100" required>
								</div>
								<div class="col-span-full">
									<input type="text" name="subject" id="subject" placeholder="<?php _e("Subject")?> *" class="w100">
								</div>
								<div class="col-span-full">
									<textarea name="message" id="message" placeholder="<?php _e("Message")?> *" rows="5" cols="150" class="mb-8" required></textarea>
								</div>
							</div>


							<div class="">

								<!-- Captcha -->
								<?
								$chiffre = array('zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten');
								$operators = array("+", "-");
								$operator = $operators[array_rand($operators)];
								$nb1 = rand(1, 5);//10
								$nb2 = ($operator === '-') ? mt_rand(1, $nb1) : mt_rand(1, 5);// on évite les résultats négatifs en cas de soustraction
								eval('$captcha = strval('.$nb1.$operator.$nb2.');');
								$captcha_hash = hash('sha256', $captcha.$GLOBALS['pub_hash']);
								// On change le signe "-" moins de calcul en "−" lisible en accessibilité
								?>
								<div>
									<label for="captcha">
										<?php _e("For security reasons, please solve the following calculation")?><span class="color-red">*</span> :
										<?=(__($chiffre[$nb1])." ".($operator=='-'?'−':$operator)." ".__($chiffre[$nb2]));?> =
									</label>
									<input type="text" name="captcha" id="captcha" placeholder="?" class="text-center" autocomplete="off" required>

									<input type="hidden" name="captcha_hash" value="<?=$captcha_hash;?>">
								</div>

								<!-- RGPD -->
								<div class="mt-24">
									<label for="rgpdcheckbox" class="inline"><?php _e("Please verify your entry and check the box to accept the storage and processing of this information.")?><span class="color-red">*</span></label>
									<input type="checkbox" name="rgpdcheckbox" id="rgpdcheckbox" required>
								</div>

							</div>


							<!-- Bouton envoyer -->
							<div class="foat-right mt-36">
								<button type="submit" id="send" class="btn btn--line border-rounded text-bold">
									<?php _e("Send")?>
									<i class="icon moon-arrow-right ml-24" aria-hidden="true"></i>
								</button>
							</div>

							<?php txt('texte-rgpd', 'mt-36')?>

							<input type="hidden" name="rgpd_text" value="<?=htmlspecialchars(@$GLOBALS['content']['rgpd']);?>">

							<input type="hidden" name="nonce_contact" value="<?=nonce("nonce_contact");?>">

							<input type="hidden" name="referer" value="<?=htmlspecialchars((isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:""));?>">

						</form>

					</article>

				</div>
			</div>
		</section>


		<script>
			// Titre de la page en cours
			// title = document.title;

			// Pour rétablir le fonctionnement du formulaire
			function activation_form(){
				desactive = false;

				$("#form-contact #send .moon-settings").removeClass("icon-spin moon-settings").addClass("moon-arrow-right");

				// Activation des champs du formulaire
				$("#form-contact input, #form-contact textarea, #form-contact button").attr("readonly", false).removeClass("disabled");

				// On peut soumettre le formulaire avec la touche entrée
				//$("#form-contact").on("submit", function(event) { send_mail(event) });
				$("#form-contact button").attr("disabled", false);
			}

			desactive = false;
			function send_mail(event)
			{
				event.preventDefault();

				if($("#captcha").val()=="" || $("#name").val()=="" || $("#subject").val()=="" || $("#message").val()=="" || $("#email-from").val()=="" || $("#rgpdcheckbox").prop("checked") == false)
				error(__("Thank you for completing all the required fields!"));
				else if(!desactive)
				{
					desactive = true;

					// Icone envoi en cours
					$("#form-contact #send .moon-settings").removeClass("moon-settings").addClass("icon-spin moon-settings");

					// Désactive le formulaire
					$("#form-contact input, #form-contact textarea, #form-contact button").attr("readonly", true).addClass("disabled");

					// Désactive le bouton submit (pour les soumissions avec la touche entrée)
					//$("#form-contact").off("submit");
					//$("#form-contact button").attr("disabled", true);// => ne permet pas le focus sur le btn une fois envoyer

					$.ajax(
						{
							type: "POST",
							url: path+"theme/"+theme+(theme?"/":"")+"tpl/contact.php?mode=send-mail",
							data: $("#form-contact").serializeArray(),
							success: function(html){ $("body").append(html); }
						});
				}
			}

			$(function()
			{
				// Message d'erreur en cas de mauvaise saisie du mail. Pour l'accessibilité
				var email_from = document.getElementById("email-from");
				email_from.addEventListener("invalid", function() {
					email_from.setCustomValidity("<?_e("Invalid email")?>. <?_e("Expected format")?> : name@example.com")
				}, false);
				email_from.addEventListener("input", function() {
					email_from.setCustomValidity("");
				}, false);

				// Soumettre le formulaire
				$("#form-contact").submit(function(event)
				{
					send_mail(event)
				});
				// Avant la sauvegarde
					before_save.push(function() {
						// Encode
						if(data["content"]["email-to"] != undefined)
							data["content"]["email-to"] = btoa(data["content"]["email-to"]);
					});

					// Edit
					edit.push(function()
					{
						// Décode
						$("#email-to").val(function(index, value) {
							if(value) return atob(value);
						});
					});

				});
			</script>
		<?php

	break;



	// SCRIPT D'ENVOIE DE L'EMAIL
	case 'send-mail':

		//print_r($_REQUEST);

		// Si on a posté le formulaire
		if(isset($_POST["email-from"]) and $_POST["message"] and isset($_POST["captcha"]) and !$_POST["name"])// reponse pour éviter les bots qui remplisse tous les champs
		{
			include_once("../../../config.php");// Les variables

			if($_SESSION["nonce_contact"] and $_SESSION["nonce_contact"] == $_POST["nonce_contact"])// Protection CSRF
			{
				if(filter_var($_POST["email-from"], FILTER_VALIDATE_EMAIL))// Email valide
				{
					if(hash('sha256', $_POST["captcha"].$GLOBALS['pub_hash']) == $_POST["captcha_hash"])// Captcha valide
					{
						// Email pour
						if(@$_POST["email-to"])
						{
							// Vérifie que le mail encrypté envoyer = encryptage
							if($_POST["email-hash"] == hash("sha256", base64_decode($_POST["email-to"]) . $GLOBALS['pub_hash']))
								$to = base64_decode($_POST["email-to"]);
							else
								$to = $GLOBALS['email_contact'];
						}
						else
							$to = $GLOBALS['email_contact'];


						// Email de
						$from = ($_POST["email-from"] ? htmlspecialchars($_POST["email-from"]) : $to);


						// Sujet
						$subject = "[".htmlspecialchars($_SERVER['HTTP_HOST'])."] ".htmlspecialchars($_POST["subject"]);


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
								popin(__("Message sent"), 'nofade', 'popin', $("#send"));
								document.title = title +' - '+ __("Message sent");

								// Icone envoyer
								$("#form-contact #send .icon-spin").removeClass("icon-spin icon-settings").addClass("icon-check");
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
							error(__("Wrong answer to the verification captcha!"), 'nofade', $("#captcha"));
							document.title = title +' - '+ __("Wrong answer to the verification captcha!");

							activation_form();// On rétablie le formulaire
						</script>
						<?php
					}
				}
				else
				{
					?>
					<script>
						error(__("Invalid email!"), 'nofade', $("#email-from"));
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
