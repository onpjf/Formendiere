<?php

include('admin/connexionDB.php');
include('Send_cmd_M2M.php');
error_reporting(E_ALL);

//   Mise à jour 15-dec-2016
// on vas lire le fichier status.xml pour la date et l'heure
// On la compare à l'heure systeme si ecart de plus de 10s on envoi un email
// La procédure est appelée toutes les 15 minute depuis un script google.
// 
//echo "<br> lire_xml(".$id.")";
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
    mail('karl.bienfait@onpjf.fr', 'Erreur Watchdog', "IPX non joignable " . $commande, $headers);
    $retour = write_trace("Erreur Watchdog" );
    echo '<br> Watchdog NOK';
    
    //Initiate cURL.
    $ch = curl_init($url_IFTTT . $event_box_out . $IFTTT_api_key);

}
else 
{
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
 echo'<p> retour curl :'. $result  ; 
  

?>