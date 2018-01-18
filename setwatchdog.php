<?php

include('admin/connexionDB.php');
include('Send_cmd_M2M.php');
error_reporting(E_ALL);

//   Mise à jour 15-dec-2016
// on vas lire le fichier status.xml pour la date et l'heure
// On la compare à l'heure systeme si ecart de plus de 10s on envoi un email
// La procédure est appelée toutes les 15 minute depuis un script google.
// Mise à jour 17 janvier 2018
// On ajoute un flag pour éviter d'envoyer systématiquement un email 
// 
// 
echo '<br> Debut setwatchdog';

$time0=date("His");        
$date=date("Ymd");
$time_courante=$date.$time0;

$mysqli = new mysqli($host,$user,$passwd,$db);

if ($mysqli->connect_errno) 
{
	$retour=write_trace("Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
	echo '<br> Erreur connection mySQL';
	
}
$request="Select val_brute from ".$statut_tpsreel. " where nom_appareil='IPX800' and type='PING'";
$retour=$mysqli->query($request);

$valeur_precedent = $retour->fetch_assoc();
$val_prec= $valeur_precedent['val_brute'];

echo '<br> Valeur du flag : '.$val_prec;

$today = getdate();
//The JSON data.
$jsonData = array(
    'value1' => $today[hours],
    'value2' => $today[minutes],
    'value3' => $today[yday]
);
 
//Encode the array into JSON.
$jsonDataEncoded = json_encode($jsonData);

if (!$sock = @fsockopen($address, $port_IPX_WEB, $num, $error, 5))
{
    if ($val_prec == 1) 
    {        
        mail('karl.bienfait@onpjf.fr', 'Perte connection Internet Maison', "IPX non joignable " . $commande, $headers);
        $retour = write_trace("Erreur Watchdog" );
        
        $request="UPDATE ".$statut_tpsreel." SET val_brute=0,date_rel=".$time_courante." WHERE nom_appareil='IPX800' and type='PING'";
        $retour=$mysqli->query($request);
        $retour = write_trace("MAJ Flag PING IPX800 = 0");
        echo '<br> Maj Flag PING IPX800 =0';
    }
    //Initiate cURL.
    $ch = curl_init($url_IFTTT . $event_box_out . $IFTTT_api_key);
    echo '<br> Watchdog NOK<br>';
}
else 
{
    if ($val_prec==0)
    {
        $request="UPDATE ".$statut_tpsreel." SET val_brute=1,date_rel=".$time_courante." WHERE nom_appareil='IPX800' and type='PING'";
        $retour=$mysqli->query($request);
        $retour = write_trace("MAJ Flag PING IPX800 = 1");
        mail('karl.bienfait@onpjf.fr', 'Retour connection internet Maison', "IPX de nouveau joignable :" . $commande, $headers);
    }
    $retour = write_trace("Watchdog OK");
    echo '<br> Watchdog OK<br>';
    
    //Initiate cURL.
    $ch = curl_init($url_IFTTT . $event_box_OK . $IFTTT_api_key);

} 

 
//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);
 
//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
 
//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
 
//Execute the request
$result = curl_exec($ch);
 echo'<br> retour curl :'. $result  ; 
  
echo '<br> Fin setwatchdog';
?>