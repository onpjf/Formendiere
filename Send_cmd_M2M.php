<?php


error_reporting(E_ALL);
function envoi_SMS($msg)
{

include('admin/connexionDB.php');
$msg2=str_replace(" ","%20",$msg);
   
    $url=$url_smsapi_free.$msg2;

file_get_contents("$url");

}

function Read_val($M2M)
{
include('admin/connexionDB.php');
	
	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ($socket == false) 
	{
		echo "La création du socket a échoué : " . socket_strerror(socket_last_error())." ";
		$retour=write_trace("M2M - La création du socket a échoué : " . socket_strerror(socket_last_error())." ");
	}

	$text = "Tentative de connexion à ".$address ." par le port ".$port_IPX_M2M."..<br>";
	$result = socket_connect($socket, $address, $port_IPX_M2M);
	if ($result == false) 
	{ $text .= "La connexion a échoué : " . socket_strerror(socket_last_error($socket)) . "<br>"; } 
	else
	{ $text .= "Connexion ok pour $address.<br>"; }

	$text .= "Envoi $M2M à la carte IPX800<br>";
	socket_write($socket, $M2M, strlen($M2M));

	// On lit alors la réponse du serveur
	$input = socket_read($socket, 64);
	socket_close($socket);
	//echo "<p> Input : ".$input;
	if ($input!="Success")
		$input=$input."=0";
	
	$text = explode("=",$input);
//	echo "<p> text :". $text[1];
	$retour=write_trace("M2M - commande".$M2M." : ".trim($text[1]));
	return trim($text[1]);
}

function cmde_ipx($relais,$cmd)
{
	// on regarde l etat absent (count1) pour mettre a jour les relais ou pas
	// absence alors count1=1
	//lecture compteur
	$Count1		=	Read_val("GetCount1");
	
	if ($Count1==0)
	{
		$query="Set0".$relais.$cmd;
		//echo "<p> fonction cmde_ipx :  ".$query;
		$retour=Read_val($query);

		
		// on test la bonne prise en compte de la commande
		// si la valeur lu n'est pas la commande on relance une commande
		// on attend 5 secondes avant d'aller lire la valeur du relais
		// on fait 3 tentatives avant d'abandonner.
		$compteur=0;
		do
		{
			sleep(5);
			
			$retour2=trim(Read_val("GetOut".$relais));
			echo "<br> retour2:".$retour2."-cmd:".$cmd;
			if ($retour2!=$cmd)
			{
				$retour=write_trace("PB  - re-tentative  commande :". $relais.",".$cmd." .");
				$retour=Read_val("Set".$relais.$cmd);
			}
			$compteur++;
			if ($compteur>3)
				break;
		}
		while($retour2!=$cmd);
	}
	else
	{
		$retour=Read_val("Set010");
		$retour=Read_val("Set020");
		$retour=Read_val("Set030");
		$retour=Read_val("Set040");
		$retour=Read_val("Set050");
		$retour=Read_val("Set060");
		$retour=Read_val("Set070");
		$retour=Read_val("Set080");
		echo "<p> Pas de commande de relais sur absence.";
		
	
	}
	return 0;
}

function write_trace($commande)
{
include('admin/connexionDB.php');

	mysql_connect($host,$user,$passwd) or die("erreur de connexion au serveur $host");
	mysql_select_db($db) or die("erreur de connexion à la base de données");
	$trace='trace_cmd_ipx';
	$time0=date("H:i:s");        
	$date=date("Y-m-d");

  //Ecriture dans la BDD
  $request="INSERT INTO $trace VALUE('','$date','$time0','$commande')";
  //echo "<br> request : ".$request;
  mysql_query($request) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());

	mysql_close();
	
	}
function write_val($tag,$rawval,$val)
{
include('admin/connexionDB.php');

	mysql_connect($host,$user,$passwd) or die("erreur de connexion au serveur $host");
	mysql_select_db($db) or die("erreur de connexion à la base de données");
	$trace='trace_cmd_ipx';
	$time0=date("H:i:s");        
	$date=date("Y-m-d");

  //Ecriture dans la BDD

	$request="INSERT INTO $ipx800v3 VALUE('','$date','$time0','$tag','$val','$rawval')";
	mysql_query($request) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	mysql_close();
	
}
	
function purge_trace()
{

	// on purge les message de Plus de 21 jours
	// la fonction est lancé tous les jours via une tache planifié sur manager ovh
	
	include('admin/connexionDB.php');
	mysql_connect($host,$user,$passwd) or die("erreur de connexion au serveur $host");
	mysql_select_db($db) or die("erreur de connexion à la base de données");
	$trace='trace_cmd_ipx';
	$time0=date("H:i:s");        
	$date=date("Y-m-d");
	
	$request="DELETE FROM $trace WHERE trace_date < current_date - 21";
	mysql_query($request) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	
	
	mysql_close();
	$retour=write_trace("ADM - Purge trace message de plus de 21 jours");
}


function lire_xml_eco()
{
include('admin/connexionDB.php');
$retour=write_trace("lire_xml_eco - Lancement");

$time0=date("H:i:s");        
$date=date("Y-m-d");
$parameter1='Tot_HC';
$parameter2='Tot_HP';
$parameter3='PPAP';



mysql_connect($host,$user,$passwd) or die("erreur de connexion au serveur $host");
mysql_select_db($db) or die("erreur de connexion à la base de données");

	
// on vas lire le fichier status.xml pour extraire une valeur
	//echo "<br> lire_xml(".$id.")";
	$xml=simplexml_load_file($fichier_eco);
	$i=0;
	$Tot_HC = $xml->T1_HCHC;
	$Tot_HP = $xml->T1_HCHP;
	$PPAP	= $xml->T1_PPAP;
	$Jour_Nuit = $xml->T1_PTEC;
  echo'<p> Lecture des informations Ecodevice';
  echo'<br> HC      :'.$Tot_HC.'W';
  echo'<br> HP      :'.$Tot_HP.'W';
  echo'<br> PPAP    :'.$PPAP.'W/h';
  echo'<br> T1_PTEC : '.$Jour_Nuit;

	
  $request="INSERT INTO $ipx800v3 VALUE('','$date','$time0','$parameter1','$Tot_HC','$Tot_HC')";
  mysql_query($request) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
  
  $request="INSERT INTO $ipx800v3 VALUE('','$date','$time0','$parameter2','$Tot_HP','$Tot_HP')";
  mysql_query($request) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());

  $request="INSERT INTO $ipx800v3 VALUE('','$date','$time0','$parameter3','$PPAP','$PPAP')";
  mysql_query($request) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());

	$request="SELECT * FROM $ipx800v3 where parameter='Tot_HC' order by id desc limit 0,2" ;
	$query=mysql_query($request) or die ('Erreur SQL ! '.$req.'<br/>'.mysql_error());
	
	$i=0;
	$value=0;
	
	while ($lineDB=mysql_fetch_array($query))    
	{
		$dateDB[$i]=$lineDB['date'];
		$timeDB[$i]=$lineDB['time'];
		list($annee,$mois,$jour)=sscanf($dateDB[$i],"%d-%d-%d"); 
        list($h,$m,$s)=sscanf($timeDB[$i],"%d:%d:%d");
        $timestamp[$i]=mktime($h,$m,0,$mois,$jour,$annee); 
		if ($i ==0)
		{
			$prev_val=$lineDB['raw_value'];
		}
		else
		{
			$conso_HC=($lineDB['raw_value']-$prev_val)/($timestamp[$i]-$timestamp[$i-1]);
			echo'<br> tot : '.($lineDB['raw_value']-$prev_val) . ' temps : '.($timestamp[$i]-$timestamp[$i-1]);
		}
		$i++;
	}
	
	$request="SELECT * FROM $ipx800v3 where parameter='Tot_HP' order by id desc limit 0,2" ;
	$query=mysql_query($request) or die ('Erreur SQL ! '.$req.'<br/>'.mysql_error());
	
	$i=0;
	$value=0;
	$prev_val=0;
	
	while ($lineDB=mysql_fetch_array($query))    
	{
		$dateDB[$i]=$lineDB['date'];
		$timeDB[$i]=$lineDB['time'];
		list($annee,$mois,$jour)=sscanf($dateDB[$i],"%d-%d-%d"); 
        list($h,$m,$s)=sscanf($timeDB[$i],"%d:%d:%d");
        $timestamp[$i]=mktime($h,$m,0,$mois,$jour,$annee); 
		if ($i ==0)
		{
			$prev_val=$lineDB['raw_value'];
		}
		else
		{
			$conso_HP=($lineDB['raw_value']-$prev_val)/($timestamp[$i]-$timestamp[$i-1]);
		}
		$i++;
	}
	
// envoi compteur EDF sur Thingspeak	
	echo'<br> Conso HC   :'. ($conso_HC*36).' 100W/h';
	echo'<br> Conso HP   :'. ($conso_HP*36).' 100W/h';
  
	$url=$url_thing.$api_EDF."&field1=".$PPAP."&field2=".$Tot_HP."&field3=".$Tot_HC."&field4=".$conso_HP."&field5=".$conso_HC;
	echo '<p> envoi Thingspeak des compteurs EDF.';
	file_get_contents("$url");
	
	
mysql_close();	
	$retour=write_trace("lire_xml_eco - valeur lues: ".$Tot_HC." ".$Tot_HP." ".$PPAP);
	$retour=write_trace("lire_xml_eco - conso HC : ". $conso_HC ." / conso HP : " . $conso_HP);
		
}
function visu_conso($profondeur)
{
include('admin/connexionDB.php');
include('graph.php');

	
	//echo 'Début procedure.';	
	echo '<br><H1>Consommation pour la période des '.$profondeur.' jour(s)</H1>';		
	mysql_connect($host,$user,$passwd) or die("erreur de connexion au serveur $host");
	mysql_select_db($db) or die("erreur de connexion à la base de données");
                                
	// On prépare la requête HC 
	$req="SELECT * FROM ". $ipx800v3."  WHERE parameter='Tot_HC' and STR_TO_DATE(concat(date,' ',time), '%Y-%m-%d %H:%i:%s')> DATE_SUB(now(), INTERVAL ".$profondeur." DAY) order by id"; 
	
	$query=mysql_query($req) or die ('Erreur SQL ! '.$req.'<br/>'.mysql_error());
	
	$i=0;
	$value=0;
	
	while ($lineDB=mysql_fetch_array($query))    
	{
		$dateDB[$i]=$lineDB['date'];
		$timeDB[$i]=$lineDB['time'];
		list($annee,$mois,$jour)=sscanf($dateDB[$i],"%d-%d-%d"); 
        list($h,$m,$s)=sscanf($timeDB[$i],"%d:%d:%d");
        $timestamp=mktime($h,$m,0,$mois,$jour,$annee); 
		
		if ($i ==0)
		{
			$prev_val=$lineDB['raw_value'];
			$prem_val=$prev_val;
			$duree=1;
			$value=0;
		}
		else
		{
			// on test si le compteur a évolué si inférieur on ignore cette valeur
			if ($lineDB['raw_value'] >= $prev_val)
			{
				$value=$lineDB['raw_value']-$prev_val;
				$prev_val=$lineDB['raw_value'];
				
			}
			else
			
				$value=0;
			
			$duree=($timestamp-$gtime[$i-1])/3600;
			
			
		}
		
		if ($value<0) 
		{
			$value=0;
		}
		// la quantité de Watt diviés par le temps en h --> W/h
		if ($duree > 0)
		{
			$codeDB[$i]=$value/$duree;
		}
		else
		{
			$codeDB[$i]=$value;
		}
		$tempDB[$i]=sprintf("%.1f",$codeDB[$i]);	

		$gtime[$i]=$timestamp;
	
 
        $i++;
	}
	
	graph('Conso HC',$gtime,$tempDB,'#FF8000','W');
	//Affichage du graphe
	echo "<img src='gConso HC.png'>";
	$conso_HC_H24=$prev_val-$prem_val;
	$cout_HC=$EDF_tarif_HC*($conso_HC_H24/1000);
	echo'<p><br> période du : '.$dateDB[0].' '.$timeDB[0].' au '. $dateDB[$i-1].' '.$timeDB[$i-1].'. Conso HC = '. $conso_HC_H24/1000 . ' kW  soit : '.$cout_HC.' €.';
	
	//on prépapre la requete HP
	$req="SELECT * FROM ". $ipx800v3."  WHERE parameter='Tot_HP' and STR_TO_DATE(concat(date,' ',time), '%Y-%m-%d %H:%i:%s')> DATE_SUB(now(), INTERVAL ".$profondeur." DAY) order by id"; 
	
	$query=mysql_query($req) or die ('Erreur SQL ! '.$req.'<br/>'.mysql_error());
	$sautligne=chr(13);
	$i=0;
	$value=0;
	
	while ($lineDB2=mysql_fetch_array($query))    
	{
		$dateDB2[$i]=$lineDB2['date'];
		$timeDB2[$i]=$lineDB2['time'];
		list($annee,$mois,$jour)=sscanf($dateDB2[$i],"%d-%d-%d"); 
        list($h,$m,$s)=sscanf($timeDB2[$i],"%d:%d:%d");
        $timestamp=mktime($h,$m,0,$mois,$jour,$annee); 
		
		if ($i ==0)
		{
			$prev_val=$lineDB2['raw_value'];
			$prem_val=$prev_val;
			$duree=1;
			$value=0;
		}
		else
		{
			// on test si le compteur a évolué si inférieur on ignore cette valeur
			if ($lineDB2['raw_value'] >= $prev_val)
			{
				$value=$lineDB2['raw_value']-$prev_val;
				$prev_val=$lineDB2['raw_value'];
				
			}
			else
			
				$value=0;
			
			$duree=($timestamp-$gtime2[$i-1])/3600;
			
			
		}
		
		if ($value<0) 
		{
			$value=0;
		}
		// la quantité de W diviés par le temps en h --> W/h
		if ($duree > 0)
		{
			$codeDB2[$i]=$value/$duree;
		}
		else
		{
			$codeDB2[$i]=$value;
		}
		$tempDB2[$i]=sprintf("%.1f",$codeDB2[$i]);	

		$gtime2[$i]=$timestamp;
	
		//echo '<br> date: '.$dateDB2[$i].' '.$timeDB2[$i].' --> '.$tempDB2[$i];
        $i++;
	}
	graph('Conso HP',$gtime2,$tempDB2,'#FF8000','W');
	//Affichage du graphe
	echo'<p>';
	echo "<img src='gConso HP.png'>";
	$conso_HP_H24=$prev_val-$prem_val;
	$cout_HP=$EDF_tarif_HP*($conso_HP_H24/1000);
	echo'<p><br> période du : '.$dateDB[0].' '.$timeDB[0].' au '. $dateDB[$i-1].' '.$timeDB[$i-1].'. Conso HP = '. $conso_HP_H24/1000 . ' kW soit : '.$cout_HP.' €.';
    
	//On ferme sql
	mysql_close ();  
}
function trace_temperature($profondeur)
{
include('admin/connexionDB.php');
	include('graph.php');
	//echo 'Début procedure.';	
	//$ipx800v3='data_ix800';
	echo'<H1>Temperature RdC sur les '.$profondeur.' dernier jour(s)</H1>';
	$date=date("Y-m-d");
			
	mysql_connect($host,$user,$passwd) or die("erreur de connexion au serveur $host");
	mysql_select_db($db) or die("erreur de connexion à la base de données");
                                
	// On prépare la requête 
	$req="SELECT date,time,parameter,value,raw_value FROM ". $ipx800v3." WHERE TO_DAYS(NOW()) - TO_DAYS(date) <= ". $profondeur ." and parameter ='analog0' ORDER BY id ASC";
	$query=mysql_query($req) or die ('Erreur SQL ! '.$req.'<br/>'.mysql_error());

	$i=0;
	while ($lineDB=mysql_fetch_array($query))    
	{
		$dateDB[$i]=$lineDB['date'];
		$timeDB[$i]=$lineDB['time'];
		//echo'<br> raw_val '.$i. ' : '.$lineDB['raw_value'].' val: '.$lineDB['value'];
		if ($lineDB['raw_value']==0)// and $i >0)
		{
			$codeDB[$i]=$codeDB[$i-1];
		}
		else
		{
			$codeDB[$i]=$lineDB['value'];
		}
		
		$tempDB[$i]=sprintf("%.1f",$codeDB[$i]);
		list($annee,$mois,$jour)=sscanf($dateDB[$i],"%d-%d-%d"); 
        	list($h,$m,$s)=sscanf($timeDB[$i],"%d:%d:%d");
        	$timestamp=mktime($h,$m,0,$mois,$jour,$annee); 

		//echo $dateDB[$i]+ $timeDB[$i].'</br>';
		$gtime[$i]=$timestamp;
		//$gtime[$i]=strtotime($timeDB[$i]); //pour le graphe
		$i++;
	}
	graph('Temperature RdC',$gtime,$tempDB,'#FF8000','&#0176;C');
	//Affichage du graphe
echo "<img src='gTemperature RdC.png'>";

	
                                
	//On ferme sql
	mysql_close ();
}

function Maj_statut_tempsreel()
{
include('admin/connexionDB.php');
$retour=write_trace("Maj statut temps reel - Lancement");

$time0=date("His");        
$date=date("Ymd");
$mysqli = new mysqli($host,$user,$passwd,$db);

if ($mysqli->connect_errno) 
{
	$retour=write_trace("Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
}

// lecture ECODEVICE
$xml=simplexml_load_file($fichier_eco);
$time_courante=$date.$time0;
$T1_HC 		= $xml->T1_HCHC;
$T1_HP 		= $xml->T1_HCHP;
$T1_PPAP	= $xml->T1_PPAP;
$T1_PTEC	= $xml->T1_PTEC;
	
$request="UPDATE ".$statut_tpsreel." SET val_brute=".$T1_HC.",date_rel=".$time_courante." WHERE nom_appareil='ECODEVICE' and tag='T1_HCHC'";
$retour=$mysqli->query($request);

$request="UPDATE ".$statut_tpsreel." SET val_brute=".$T1_HP.",date_rel=".$time_courante." WHERE nom_appareil='ECODEVICE' and tag='T1_HCHP'";
$retour=$mysqli->query($request);

$request="UPDATE ".$statut_tpsreel." SET val_brute=".$T1_PPAP.",date_rel=".$time_courante." WHERE nom_appareil='ECODEVICE' and tag='T1_PPAP'";
$retour=$mysqli->query($request);

if ($T1_PTEC =="HC")
	$T1_PTEC=0;
else
	$T1_PTEC=1;
$request="UPDATE ".$statut_tpsreel." SET val_brute=".$T1_PTEC.",date_rel=".$time_courante." WHERE nom_appareil='ECODEVICE' and tag='T1_PTEC'";
$retour=$mysqli->query($request);

//*****lecture IPX800
$xml=simplexml_load_file($fichier_ipx);
foreach ($xml as $tab)
{
	$tag=$tab->getName();
	if ($tag=="analog0")
	{
		$val_cal=($tab*0.323)-50;
		$request="UPDATE ".$statut_tpsreel." SET val_brute=".$tab.",cal_calc=".$val_cal.",date_rel=".$time_courante." WHERE nom_appareil='IPX800' and tag='".$tag."'";
	}
	else
		$request="UPDATE ".$statut_tpsreel." SET val_brute=".$tab.",date_rel=".$time_courante." WHERE nom_appareil='IPX800' and tag='".$tag."'";
	
	echo "<p> ".$request;
	$retour=$mysqli->query($request);
}


//**** lecture Zibase
$mysqli2 = new mysqli($host,$user,$passwd,$db);
// On fait la liste des tag de la zibase que l'on veut suivre ==> il faut ajouter dans statut_temps_reeel les tag
$request = "SELECT * from ".$statut_tpsreel." WHERE nom_appareil='ZIBASE'";
$retour=$mysqli->query($request);
echo'<br>'.$request;
while($donnee = $retour->fetch_assoc())
{
	//$id_zibase=$retour->tag;
	echo'<br><hr>';
	//var_dump($donnee);
	$url=$url_zapi2.$donnee['tag'];
	// A partir de l'id receupere on interroge ZAPI2 pour avoir les info de l'id
	echo'<br> url: '.$url;
	$val_zibase=file_get_contents("$url");
	$obj=json_decode($val_zibase);
	echo'<br> retour:'.$val_zibase;
	$request2="UPDATE ".$statut_tpsreel." SET cal_calc=".$obj->{'body'}->{'val1'}." ,val_brute=".$obj->{'body'}->{'val2'}.",date_rel=".$time_courante.", description='".$obj->{'body'}->{'name'}."' WHERE nom_appareil='ZIBASE' and tag='".$obj->{'body'}->{'id'}."'";
	$ret=$mysqli2->query($request2);
	echo'<br> request2:'.$request2.'<br><hr>';
}

$mysqli2->close();

$retour=write_trace("Maj statut temps reel - Fin");

$mysqli->close();

}

function Aff_statut()
{
include('admin/connexionDB.php');
//header('Content-type: text/html; charset=UTF-8');
$mysqli = new mysqli($host,$user,$passwd,$db);

if ($mysqli->connect_errno) 
{
	$retour=write_trace("Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
}

$request = 'select * from statut_tempsreel where description <>""' ; 
$retour=$mysqli->query($request);
?><table border="1" cellpadding="0" cellspacing="0"><?
while($obj = $retour->fetch_object())
{
//	echo "<p>".$obj->nom_appareil." / ".$obj->description." : ".$obj->val_brute."   -- MAJ:".$obj->date_rel;

	switch($obj->Type)
	{
		case "TEMPERATURE":
			?><tr><?
			?><td><img align="middle" style="width:50px; height:50px;" src="/collect_data_ipx800/images/c_logotype_temperature.png"/></td>
			<td style="text-align:center;vertical-align:top"><?echo "<b>".$obj->description."</b></td>";
			?><td style="text-align:center;"><?echo "<b>Heure du relevé</b><br>".$obj->date_rel."</td>";
			?><td style="text-align:center;"><?echo "<b>Température</b><br>".$obj->cal_calc." <br></td>";
			?></tr><?
		break;
                case "PING":
			?><tr><?
			?><td><img align="middle" style="width:50px; height:50px;" src="/collect_data_ipx800/images/c_logotype_temperature.png"/></td>
			<td style="text-align:center;vertical-align:top"><?echo "<b>".$obj->description."</b></td>";
			?><td style="text-align:center;"><?echo "<b>Heure du relevé</b><br>".$obj->date_rel."</td>";
			?><td style="text-align:center;"><?echo "<b>Valeur</b><br>".$obj->val_brute." <br></td>";
			?></tr><?
		break;
		case "COMPTEUR":
			?><tr><?
			?><td><img align="middle" style="width:50px; height:50px;" src="/collect_data_ipx800/images/c_logotype_power.png"/></td>
			<td style="text-align:center;vertical-align:top"><?echo "<b>".$obj->description."</b></td>";
			?><td style="text-align:center;"><?echo "<b>Heure du relevé</b><br>".$obj->date_rel."</td>";
			?><td style="text-align:center;"><?echo "<b>Index Compteur</b><br>".$obj->val_brute." <br></td>";
			?></tr><?
		break;
		case "CHAUFFAGE":
			if($obj->val_brute==1)
			{			
			
				?><tr><?
				?><td><img align="middle" style="width:50px; height:50px;" src="/collect_data_ipx800/images/chauffage_ON.png"/></td>
				<td style="text-align:center;vertical-align:top"><?echo "<b>".$obj->description."</b></td>";
				?><td style="text-align:center;"><?echo "<b>Heure du relevé</b><br>".$obj->date_rel."</td>";
				?><td style="text-align:center;"><?echo "<b>Marche <br></td>";
				?></tr><?
		
			}
			else
			{
				?><tr><?
				?><td><img align="middle" style="width:50px; height:50px;" src="/collect_data_ipx800/images/chauffage_OFF.png"/></td>
				<td style="text-align:center;vertical-align:top"><?echo "<b>".$obj->description."</b></td>";
				?><td style="text-align:center;"><?echo "<b>Heure du relevé</b><br>".$obj->date_rel."</td>";
				?><td style="text-align:center;"><?echo "<b>Arret <br></td>";
				?></tr><?
		
			}
			
			
	}
}
?></table><?
$mysqli->close();
}

?>