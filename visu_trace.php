<?php
	
	include('admin/connexionDB.php');
	
	//echo 'Début procedure.';	
	$ipx800v3='trace_cmd_ipx';
			
	try
	{
		$bdd = new PDO('mysql:host='.$host.';dbname='.$db, $user, $passwd);
	}
	catch(Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
	$req="SELECT * FROM ". $ipx800v3."  ORDER BY trace_id DESC";
	$response=$bdd->query($req);
	while ($data=$response->fetch())
	{
		$date=$data['trace_date'];
		$time=$data['trace_time'];
		$value=$data['trace_msg'];
		echo "<br>".$date." ".$time."  : ".$value;
	
	}
	$response->CloseCursor();
	/*mysql_connect($host,$user,$passwd) or die("erreur de connexion au serveur $host");
	mysql_select_db($db) or die("erreur de connexion à la base de données");
                                
	// On prépare la requête 
	$req="SELECT * FROM ". $ipx800v3."  ORDER BY trace_id DESC";
	$query=mysql_query($req) or die ('Erreur SQL ! '.$req.'<br/>'.mysql_error());
	$sautligne=chr(13);
	$i=0;
	while ($lineDB=mysql_fetch_array($query))    
	{
		$date=$lineDB['trace_date'];
		$time=$lineDB['trace_time'];
		$value=$lineDB['trace_msg'];
		echo "<br>".$date." ".$time."  : ".$value;
		
		$i++;
	}
		
                                
	//On ferme sql
	mysql_close ();*/  
?>
