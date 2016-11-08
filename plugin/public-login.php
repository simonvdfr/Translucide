<?
if($_GET['ajax']) {
	@include_once("../config.php");// Les variables
	@include_once("../api/function.php");// Fonction

	$lang = get_lang();// Sélectionne la langue
	load_translation('api');// Chargement des traductions du système
}

// @Todo: faire la procedure de récup de mot de passe

?>
<style>
#public-login { max-width: 300px; }

@media (max-width: 480px) {
	#login-dialog { left: 0px !important; }
}
</style>
<?

// SI DÉJÀ CONNECTÉ afficher un message
if($_SESSION['uid'])
{
?>
	<div id="public-login" class="center pas tc">
		
		<?_e("You are already connected");?>
							
		<button id="logout" class="w80 mts bold">
			<?_e("Log out")?>
			<i class="fa fa-sign-out"></i>
		</button>
		
	</form>

	<script>
	$(document).ready(function()
	{
		// Au clique sur le bouton de déconnexion
		$("#logout").on("click", function(event)
		{
			event.preventDefault();
			
			logout();// Déconnexion
		});
	});
	</script>
<?
}
else// Sinon FORMULAIRE DE CONNEXION
{
?>
	<form id="public-login" class="layer center pas">
		
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
				url: path+"api/ajax.php?mode=login",
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
<?
}
?>