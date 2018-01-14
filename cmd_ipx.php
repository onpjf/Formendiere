<?php
include('Send_cmd_M2M.php');
error_reporting(E_ALL);
// permet de recevoir des ordres de google agenda et de les traiter en fonction de la présence absent/present
$relais=$_REQUEST['relais'];
$cmd=$_REQUEST['cmd'];


echo "<p> cmd_ipx.php : Commande relais :".$relais." Commande :". $cmd;
$retour=write_trace("CMD - Commande google calendar -> relais:".$relais." cmd: ".$cmd);
$retour=cmde_ipx($relais,$cmd);	
	
?>