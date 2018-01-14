<?php
include('admin/connexionDB.php');
include('Send_cmd_M2M.php');

// on vas lire le fichier status.xml pour extraire une valeur
	echo "<br> Debut maj_michamps";
	$retour_write_trace=write_trace("maj_Michamps - Debut");
	
	$xml=simplexml_load_file($fichier_eco);
	echo '<br> fichier xml = '.$fichier_eco;
        echo '<br> xml :'.$xml;
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
	
	if ( ($Tot_HC <> 0) && ($Tot_HP <> 0) )
	{
		
		$mysqli = new mysqli($host,$user,$passwd,$db);
                echo '<br> fin requete Mysql';
		if ($mysqli->connect_errno) 
		{
			$retour=write_trace("Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
		}
		$request = 'select * from statut_tempsreel where nom_appareil="PHOTO"' ; 
		$retour_request=$mysqli->query($request);
                
                
		while($obj = $retour_request->fetch_object())
		{
				$CEP=$obj->val_brute;
				echo '<br> CEP: '.$CEP;
		}
		$mysqli->close();
		
		$client = new SoapClient($url_API_Michamp); 
		$IDCONNECT = $client->Login ($login_michamps,$pw_michamps); 

		$mesindex = '<DATA><CEJ>'. $Tot_HP/1000 . '</CEJ><CEN>' . $Tot_HC/1000 . '</CEN><CEP>'.$CEP.'</CEP></DATA>'; 

		$VALRETOUR = $client->AddData ($IDCONNECT,$mesindex); 
		$retour_xml=simplexml_load_string($VALRETOUR);

		$retour_sms=envoi_SMS(" Envoi Michamp : CEJ=".($Tot_HP/1000)." CEN=".($Tot_HC/1000)." CEP=".$CEP." Transmission=".$retour_xml);
                $retour_write_trace=write_trace("Envoi reussi");
                $retour_write_trace=write_trace("maj_Michamps - Fin");
	}
	else
	{
		$retour_sms=envoi_SMS(" Envoi Michamp - ERREUR sur envoi - un compteur au moins a zéro");
                $retour_write_trace=  write_trace("Erreur sur compteur. Pas d'envoi");
                $retour_write_trace=  write_trace("maj_Michamps - Fin");
	}
echo "<br> Fin maj_michamps";

 

?>
