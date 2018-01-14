<?php
include('Send_cmd_M2M.php');
error_reporting(E_ALL);
// permet de recevoir des ordres de google agenda et de les traiter en fonction de la présence absent/present
$type_event=$_REQUEST['type_event'];
$msg=$_REQUEST['msg'];

$retour=write_trace("Event - ".$type_event." Msg: ".$msg);
echo "SUCCESS";
	
?>