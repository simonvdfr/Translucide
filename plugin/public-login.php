<?
@include_once("../config.php");// Les variables
@include_once("../api/fonction.php");// Fonction

$lang = get_lang();// Sélectionne la langue
load_translation('api');// Chargement des traductions du système

// @Todo: faire la procedure de récup de mot de passe

?>
<style>
#public-login { 
	background-color: #ffffff;
	border-top: 1px solid #cccccc;
	border-radius: 4px;
	max-width: 300px;
	box-shadow: 0 2px 5px rgba(0, 0, 0, 0.4);
}
	/* Fleche au dessu de la box de login */
	#login-dialog #public-login:after { 
		border: 10px solid transparent;
		border-bottom-color: rgba(190, 190, 190, 0.6);
		content: ' ';
		position: absolute;
		top: -20px;
		left: 40px;
		height: 0px;
		width: 0px;
	}
</style>


<form id="public-login" class="center pas">
	
	<input type="email" id="email" placeholder="<?_e("My email");?>" required class="w100 mtt mbs"><span class="wrapper big bold">@</span>

	<input type="password" id="password" placeholder="<?_e("My password");?>" required class="w100"><i class="fa fa-lock wrapper bigger"></i>

	<div class="mbt"><input type="checkbox" id="rememberme" class="mts"> <label for="rememberme"><?_e("Remember me");?></label></div>
						
	<button class="w100 mts mbm bold">
		<?_e("Log in")?>
		<i class="fa fa-key"></i>
	</button>

	<input type="hidden" id="champ_vide" value="">

	<input type="hidden" id="nonce" value="<?=nonce("nonce");?>">
	
	<div>
		<?_e("Not a member yet ?");?><br>
		<a href="inscription"><?_e("Sign up");?></a>
		<!-- <div class="tr fr"><a href=""><?_e("Forgot your password");?></a></div> -->
	</div>
	
</form>


<script>
$(document).ready(function()
{
	// Update les nonces dans la page courante pour éviter de perdre le nonce
	$("#nonce").val('<?=$_SESSION['nonce']?>');

	// Login
	$("#public-login").submit(function(event) 
	{
		event.preventDefault();
		
		// Désactive le submit
		$("#public-login input[type='submit']").attr("disabled", true);
		$("#public-login").off("submit");

		$.ajax(
		{ 
			type: "POST",
			url: "api/ajax.php?mode=login",
			data: { 
				email: $("#public-login #email").val(),
				password: $("#public-login #password").val(),
				rememberme: $("#public-login #rememberme").prop("checked"),
				nonce: $("#public-login #nonce").val(),
				callback: callback = "reload"
			}
		})
		.done(function(html) { 
			// On ferme le layer
			$("#login-dialog").fadeOut("fast", function(){ close = true; });			
			
			// On exécute le retour
			$("body").append(html);
		});
	});
});
</script>
