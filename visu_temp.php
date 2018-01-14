<?php
	
	include('admin/connexionDB.php');
	$tag=$_POST['tag'];
	echo "<br> Tag: :".$tag;	
		try
		{
			$bdd = new PDO('mysql:host='.$host.';dbname='.$db, $user, $passwd);
		}
		catch(Exception $e)
		{
			die('Erreur : ' . $e->getMessage());
		
		}
	
		$req= "SELECT * FROM `". $ipx800v3 . "` WHERE parameter='". $tag . "' order by id desc";
		//echo "<br>".$req;
		$response=$bdd->query($req);
		while ($data=$response->fetch())
		{
				
			$date=$data['date'];
			$time=$data['time'];
			$value=$data['value'];
			echo "<br>".$date." ".$time."  : ".$value/10 . "°C";
		
		}
		$response->CloseCursor();

	
?>
