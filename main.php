<?php
include('admin/connexionDB.php');
include('Send_cmd_M2M.php');


$raw_val	=	Read_val("GetAn1");
$an1=($raw_val*0.323)-50;
$time0=date("H:i:s");        
$date=date("Y-m-d");
$parameter='analog0';

echo 'Date : '. $date;
echo '<p>Heure : '. $time0;
echo '<p>Valeur '. $parameter.' : '.$an1;
echo '<p>Valeur brut : '.$raw_val;
if($raw_val>0)
{
mysql_connect($host,$user,$passwd) or die("erreur de connexion au serveur $host");
mysql_select_db($db) or die("erreur de connexion à la base de données");
//

  //Ecriture dans la BDD
  $request="INSERT INTO $ipx800v3 VALUE('','$date','$time0','$parameter','$an1','$raw_val')";
  mysql_query($request) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());

mysql_close();

// envoi thingspeak
$url=$url_thing.$api_temp."&field1=".$an1;
file_get_contents("$url");
}
else
  {
  echo '<br>Probleme lecture temperature RdC';
  }

$retour	=	lire_xml_eco();
$retour	=	Maj_statut_tempsreel();

?>