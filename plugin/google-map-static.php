<style>
	#google-map {
		height: 100%;
		min-height: 370px;
	}
</style>

<?
$marker_title = (isset($GLOBALS['content']['marker-title'])?$GLOBALS['content']['marker-title']:"");
$lat = (isset($GLOBALS['content']['lat'])?$GLOBALS['content']['lat']:"");
$lng = (isset($GLOBALS['content']['lng'])?$GLOBALS['content']['lng']:"");
?>

<div id="google-map" class="animation fade-in">
	<a href="http://maps.google.com/maps?q=<?=urlencode($marker_title)?>&ll=<?=$lat?>,<?=$lng?>" target="_blank"><img src="https://maps.googleapis.com/maps/api/staticmap?zoom=13&size=440x370&maptype=roadmap&markers=color:green%7C<?=$lat?>,<?=$lng?>&key=<?=$GLOBALS['google_map']?>&style=feature:water|element:geometry|saturation:-60&style=feature:poi|visibility:off" alt="<?=$GLOBALS['sitename']?>"></a>
</div>


<?hidden_label(__("Information pour la google map : "))?>
<?hidden('marker-title', 'vat mat')?>
<?hidden('lat', 'vat mat')?>
<?hidden('lng', 'vat mat')?>


<script>
$(function() {
	google_map_key = "<?=$GLOBALS['google_map']?>";

	// Si on édit on charge d'edition google map
	edit.push(function() {
		var script = document.createElement('script');
		script.src = path+"plugin/google-map-static.js";
		document.body.appendChild(script);		
	});
});
</script>