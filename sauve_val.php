<?php
include('Send_cmd_M2M.php');
error_reporting(E_ALL);

$tag=$_REQUEST['tag'];
$rawval=$_REQUEST['rawval'];
$val=$_REQUEST['val'];

$retour=write_val($tag,$rawval,$val);
echo "SUCCESS";
	
?>