<?
@include_once("../config.php");// Variables
//include_once("../api/fonction.php");// Fonctions
include_once("../api/db.php");// Connexion à la db

$term = $connect->real_escape_string($_GET["term"]);

$sel = $connect->query("SELECT ville_code_commune, ville_nom_reel, ville_code_postal FROM villes_france_free WHERE ".(strstr($_GET['type'], "ville") ? "ville_nom_simple" : "ville_code_postal")." LIKE '".$term."%' LIMIT 50");
while($res = $sel->fetch_assoc())
{
	$data[] = array('id' => $res['ville_code_commune'], 'label' => $res['ville_nom_reel'], 'value' => $res['ville_code_postal']);
}

header("Content-Type: application/json; charset=utf-8");

echo json_encode($data);

$connect->close();
?>