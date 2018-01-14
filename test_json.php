<?php

$url='http://www.iris-rfid.com/emulateur_ltr/serveur_rfid_sim.php';
$delivery='20140930';
$order='2014-09';
$truck='10007VF72';
$picklist=array('1111','2222','3333','4444','5555','66666','77777','888888','99999');


$str=array ("delivery"=>$delivery, "order"=>$order,"truck"=>$truck,"list"=>$picklist);
echo'<br> str:'.json_encode($str);
$retour=file_get_contents($url."?".json_encode($str));
echo '<br> retour:'.$retour
/*
{"delivery":12454125, "order":"F5424658","truck":"1245-AZ-17","list":["E52642B6C652481324628462","E52642B6C652481324628463","E52642B6C652481324628464","E52642B6C652481324628465","E52642B6C652481324628466"]} 
*/

?>
