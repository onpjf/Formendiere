<?php
	
	include('admin/connexionDB.php');
	include('graph.php');
	//echo 'Début procedure.';	
	$ipx800v3='data_ix800';
	$date=date("Y-m-d");
			
	mysql_connect($host,$user,$passwd) or die("erreur de connexion au serveur $host");
	mysql_select_db($db) or die("erreur de connexion à la base de données");
                                
	// On prépare la requête 
	$req="SELECT * FROM ". $ipx800v3."  ORDER BY id ASC";
	$query=mysql_query($req) or die ('Erreur SQL ! '.$req.'<br/>'.mysql_error());
	$sautligne=chr(13);
	$i=0;
	//echo 'index;date;time;tag;valeur;raw_value'.$sautligne;
	echo '<table>';
	while ($lineDB=mysql_fetch_array($query))    
	{
		$date=$lineDB['date'];
		$time=$lineDB['time'];
		$value=$lineDB['value'];
		$raw=$lineDB['raw_value'];
		$parameter=$lineDB['parameter'];
		echo '<tr>';
		echo '<td>'.$i.'</td>';
		echo '<td>'.$date.'</td><td>'.$time.'</td><td>'.$parameter.'</td><td>'.$value.'</td><td>'.$raw.'</td>';
		echo'</tr>';
		
		$i++;
	}
	echo '</table>';
                                
	//On ferme sql
	mysql_close ();  
?>
