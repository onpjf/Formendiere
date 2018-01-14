<?php
include('admin/connexionDB.php');
include ('Send_cmd_M2M.php');
// permet de recevoir des ordres de google agenda et de les traiter en fonction de la présence absent/present
$HC		=$_REQUEST['HCHC'];
$HP		=$_REQUEST['HCHP'];
$PPAP	=$_REQUEST['PPAP'];

echo'<br> HC:'.$HC;  
echo'<hr> HP:'.$HP;

//$retour=envoi_SMS(" HC:".$HC." HP:".$HP." PPAP :".$PPAP);

?>