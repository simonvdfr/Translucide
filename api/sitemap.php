<?
include_once("../config.php");// Les variables
include_once("fonction.php");// Fonction
include_once("db.php");// Connexion à la db

header('Content-Type:text/xml;');

echo"<?xml version='1.0' encoding='UTF-8'?>\n<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9' xmlns:image='http://www.google.com/schemas/sitemap-image/1.1'>\n";

$sel = $connect->query("SELECT url, date_update FROM ".$GLOBALS['table_content']." WHERE state='active' ORDER BY date_update DESC");
while($res = $sel->fetch_assoc()) 
{
	$date_update=explode(" ", $res['date_update']);

	echo"<url>\n";

	echo"<loc>".make_url($res['url'], true)."</loc><lastmod>".$date_update[0]."</lastmod>\n";

	//if($res[photo]) echo"<image:image><image:loc>".make_url()."</image:loc></image:image>\n";

	echo"</url>\n";
}
echo"</urlset>";
?>