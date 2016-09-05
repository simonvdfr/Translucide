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
					$("#contact").fadeOut();
					</script>
					<?
				}
			}
			else 	
			{
				?>
				<script>	
				error(__("Wrong answer to the question!"));
				$("#question").effect("highlight").effect("highlight");
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


	<section class="under-header parallax mod tc white" <?bg('bg-header')?>>		
		<h1><?txt('titre')?></h1>
	</section>


	<section class="mw960p mod center mtl">
		<article class="fl w70 prl pbm mbm tofadein">			

			<h2 class="mtn"><?txt('titre-2')?></h2>

			<p><?txt('texte')?></p>

			<form id="contact" class="mat">
				
				<div>
					<label class="bold" for="email"><?_e("Email")?> :</label> <input type="text" name="email" id="email" placeholder="exemple@mymail.com" class="w40 vatt">
				</div>
				
				<div>
					<label class="bold" for="message"><?_e("Message")?> :</label> <textarea name="message" id="message" class="w100 mbt" style="height: 200px;"></textarea>
				</div>
				
				<div class="">
					<label class="bold" for="question"><?_e("Question")?> : <?=($nb1." ".$operator." ".$nb2);?> = </label> <input type="text" name="question" id="question" class="w50p vatt">
				</div>
								
				<button class="bt fr">
					<?_e("Send")?>
					<i class="fa fa-envelope"></i>
				</button>

				<input type="hidden" name="question_hash" value="<?=$question_hash;?>">

				<input type="hidden" name="champ_vide" value="">

				<input type="hidden" name="nonce_contact" value="<?=nonce("nonce_contact");?>">
				
				<input type="hidden" name="referer" value="<?=htmlspecialchars($_SERVER['HTTP_REFERER']);?>">
				
			</form>
		</article>	

		<aside class="fl w30 pat tofadein">
			<div class="pam">
				<h2 class="tc medium mtn"><?txt('titre-colonne')?></h2>
				<?txt('texte-colonne')?>
			</div>
		</aside>
	</section>

	<script>
	$("#contact").submit(function(event)
	{
		event.preventDefault();

		if($("#question").val()=="" || $("#message")=="" || $("#email")=="") error(__("Thank you to fill out all fields!"));
		else
		{
			$.ajax(
			{
				type: "POST",
				url: "<?=$GLOBALS['path']."theme/".$GLOBALS['theme']."tpl/contact.php"?>",				
				data: $(this).serializeArray(),
				success: function(html){ $("body").append(html); }
			});
		}		
	});
	</script>


	<section class="mod">

		<?include("plugin/google-map.php")?>

	</section>
	<?
}
?>