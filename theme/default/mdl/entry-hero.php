<!-- First variable to display the subtitle in the hero -->
<?php
if($res['tpl']=='page' or $res['tpl']=='article-list' or $res['tpl']=='event-list' or $res['tpl']=='search' or $res['tpl']=='contact')
txt('subtitle', array('tag'=>'p'));
?>

<!-- Second variable to display the publication information in the hero of the article pages and al -->
<?php
$date_insert = explode("-", explode(" ", $res['date_insert'])[0]);
$date_update = explode("-", explode(" ", $res['date_update'])[0]);
if($res['tpl']=='article' or  $res['tpl']=='event')
{
  if($res['date_insert']){
    echo '<div class="hero-meta flex justify-center"><div class="hero-meta-date-add px-8"><i class="icon moon-calendar mr-8"></i>Publié le '.$date_insert[2].' '.trim(utf8_encode(strftime("%h", mktime(0, 0, 0, $date_insert[1], 10))),".").' '.$date_insert[0].'</div>';
  }
  if($res['date_update']){
    echo '<div class="hero-meta-date-add px-8"><i class="icon moon-calendar mr-8"></i>Modifié le '.$date_update[2].' '.trim(utf8_encode(strftime("%h", mktime(0, 0, 0, $date_update[1], 10))),".").' '.$date_update[0].'</div>';
  }
  echo '</div>';
}
?>
