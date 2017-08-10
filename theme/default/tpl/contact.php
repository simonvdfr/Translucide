<?
// Si on a posté le formulaire
if($_POST["email"] and $_POST["message"] and isset($_POST["question"]) and !$_POST["champ_vide"])// champ_vide pour éviter les bots qui remplisse tous les champs
{
	include_once("../../../config.php");// Les variables

	if($_SESSION["nonce_contact"] and $_SESSION["nonce_contact"] == $_POST["nonce_contact"])// Protection CSRF
	{
		if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))// Email valide
		{
			if(hash('sha256', $_POST["question"].$GLOBALS['hash']) == $_POST["question_hash"])// Captcha valide
			{		
				$subject = "[".$GLOBALS['sitename']."] ".htmlspecialchars($_POST["email"]);

				$message = nl2br(strip_tags($_POST["message"]));

				$message .= "<br /><br />-------------------------------------------------------<br />";

				if($_POST['referer']) $message .= "Referer : ".htmlspecialchars($_POST['referer'])."<br />";

				$message .= "IP du Visiteur : ".getenv("REMOTE_ADDR")."<br />";
				$message .= "Host : ".gethostbyaddr($_SERVER["REMOTE_ADDR"])."<br />";		
				$message .= "User Agent : ".getenv("HTTP_USER_AGENT")."<br />";
				$message .= "IP du Serveur : ".getenv("SERVER_ADDR")."<br />";

				$header="Content-type:text/html; charset=utf-8\r\nFrom:".($_POST["email"] ? htmlspecialchars($_POST["email"]) : $GLOBALS['email_contact']);

				if(mail($GLOBALS['email_contact'], $subject, stripslashes($message), $header))
				{
					?>
					<script>
						light(__("Message sent"));

						// Icône envoyer
						$("#contact button i").removeClass("fa-spin fa-cog").addClass("fa-ok");
					</script>
					<?
				}
			}
			else 	
			{
				?>
				<script>	
					error(__("Wrong answer to the question!"));
					//$("#question").effect("highlight").effect("highlight");

					// On rétablie le formulaire
					$("#contact button i").removeClass("fa-spin fa-cog").addClass("fa-envelope");
					$("#contact input, #contact textarea, #contact button").attr("readonly", false).removeClass("disabled");
					$("#contact button").attr("disabled", false);
					$("#contact").submit(function(event){ send_contact(event) });
				</script>
				<?
			}
		}
		else 
		{
			?>
			<script>	
				error(__("Invalid email!"));

				//$("#email").effect("highlight").effect("highlight");
				
				// On rétablie le formulaire
				$("#contact button i").removeClass("fa-spin fa-cog").addClass("fa-envelope");
				$("#contact input, #contact textarea, #contact button").attr("readonly", false).removeClass("disabled");
				$("#contact button").attr("disabled", false);
				$("#contact").submit(function(event){ send_contact(event) });
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
	$question_hash = hash('sha256', $question.$GLOBALS['hash']);

	?>
	<script>
	add_translation({
		"Thank you to fill out all fields!" : {"fr" : "Merci de remplir tous les champs !"},
		"Wrong answer to the question!" : {"fr" : "R\u00e9ponse erron\u00e9e \u00e0 la question !"},
		"Invalid email!" : {"fr" : "Mail invalide !"},
		"Message sent" : {"fr" : "Message envoy\u00e9"},
	});
	</script>


	<section class="under-header parallax mod tc ptl" <?bg('bg-header')?>>		
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
				
				<button class="bt fr pat">
					<?_e("Send")?>
					<i class="fa fa-envelope"></i>
				</button>

				<div class="">
					<label class="bold" for="question"><?=($nb1." ".$operator." ".$nb2);?> = </label> <input type="text" name="question" id="question" required placeholder="?" class="w50p vatt">
				</div>
								
				<input type="hidden" name="question_hash" value="<?=$question_hash;?>">

				<input type="hidden" name="champ_vide" value="">

				<input type="hidden" name="nonce_contact" value="<?=nonce("nonce_contact");?>">
				
				<input type="hidden" name="referer" value="<?=htmlspecialchars($_SERVER['HTTP_REFERER']);?>">
				
			</form>

		</article>	

	</section>


	<script>
		function send_contact(event)
		{
			event.preventDefault();

			if($("#question").val()=="" || $("#message").val()=="" || $("#email").val()=="")
				error(__("Thank you to fill out all fields!"));
			else
			{
				// Icone envoi en cours
				$("#contact button i").removeClass("fa-envelope").addClass("fa-spin fa-cog");

				// Désactive le formulaire
				$("#contact input, #contact textarea, #contact button").attr("readonly", true).addClass("disabled");
				$("#contact button").attr("disabled", true);
				$("#contact").off("submit");

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


	<section class="mod">

		<?include("plugin/google-map.php");?>

	</section>
	<?
}
?>