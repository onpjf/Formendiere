<?php
include('Send_cmd_M2M.php');
error_reporting(E_ALL);
// permet de recevoir des ordres de l'éco device
$relais=$_REQUEST['info'];



echo "<p> commande eco device :".$relais;
$retour=write_trace("CMD - Commande ECO Device -> :".$relais);
	
?>