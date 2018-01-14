<?php
include ('Send_cmd_M2M.php');
include ('admin/connexionDB.php');
	
//echo '<br> valeur :'.$_POST['profondeur'];
$valcomp =$_POST['compteur'];


$time0=date("His");        
$date=date("Ymd");
$time_courante=$date.$time0;
$mysqli = new mysqli($host,$user,$passwd,$db);

if ($mysqli->connect_errno) 
{
	$retour=write_trace("Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
	echo '<br> Erreur connection mySQL';
	
}
$request="Select val_brute,date_rel from ".$statut_tpsreel. " where nom_appareil='PHOTO' and type='COMPTEUR'";
$retour=$mysqli->query($request);

$valeur_precedent = $retour->fetch_assoc();
$val_prec= $valeur_precedent['val_brute'];
$date_prec = $valeur_precedent['date_rel'];
if ($val_prec <= $valcomp)
    {
    $request="UPDATE ".$statut_tpsreel." SET val_brute=".$valcomp.",date_rel=".$time_courante." WHERE nom_appareil='PHOTO' and type='COMPTEUR'";
    $retour=$mysqli->query($request);
    }
else 
    {
    echo '<br> Le compteur saisie est inférieur au compteur saisie dernièrement. Pas d enregistrement';
    }
 
$mysqli->close();

echo '<br> valeur precedente = ' .$val_prec. ' à : ' .$date_prec .' - valeur saisie = '.$valcomp . ' à:'.$time_courante;


?>
