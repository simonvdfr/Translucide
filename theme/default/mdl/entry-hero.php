<!-- First variable to display the subtitle in the hero of the static pages -->
<?php
if(stristr($res['tpl'], 'page') or stristr($res['tpl'], 'contact') or stristr($res['tpl'], 'event-list') or stristr($res['tpl'], 'article-list') or stristr($res['tpl'], 'search'))
{
  if(@$GLOBALS["content"]["subtitle"]){
    $subtitle = $GLOBALS["content"]["subtitle"];
    echo '<p class="hero-subtitle pt-20">'.$subtitle.'';
  }
  echo '</p>';
}
input("subtitle", array("type" => "hidden", "autocomplete" => "off", "class" => "w100 text-center"));
?>

<!-- Second variable to display the publication information in the hero of the article pages and al -->
<?php
if(stristr($res['tpl'], 'article') or stristr($res['tpl'], 'event'))
{
  if($res['date_insert']){
    echo '<div class="hero-meta flex justify-center"><div class="hero-meta-date-add px-8"><i class="icon moon-calendar mr-8"></i>Publié le ' .$res['date_insert']. '</div>';
  }
  if($res['date_update']){
    echo '<div class="hero-meta-date-add px-8"><i class="icon moon-calendar mr-8"></i>Modifié le ' .$res['date_update']. '';
  }
  echo '</div></div>';
}
?>
