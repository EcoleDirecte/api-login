<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/../inc/global.php');
$EcoleDirecteAgenda = EcoleDirecteLogin(EC_CHARLES_LOGIN, EC_CHARLES_PASS);
$postLoginFields = 'leTypeDeFichier=FICHIER_CDT&fichierId=1198&token=' . $EcoleDirecteAgenda['token'];

$ip = $_SERVER["REMOTE_ADDR"];
$ip_srv = $_SERVER["SERVER_ADDR"];
$url1 = $_SERVER["REQUEST_URI"];
$srvname = $_SERVER["SERVER_NAME"];

$url = 'https://vmws09.ecoledirecte.com/v3/telechargement.awp?verbe=get';

// Initialisation : session et flux sur le fichier
$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, $url);
$fileName = 'fichier.pdf';
$fileA = EC_FILES . $fileName;
$fp = fopen($fileA, "w");

// Options
curl_setopt($curl, CURLOPT_FILE, $fp);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($curl, CURLOPT_POSTFIELDS, $postLoginFields);
curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64; rv:47.0) Gecko/20100101 Firefox/47.0)');
     
// Execution
$data = curl_exec($curl);
     
// Retourne le numéro d'erreur de la dernière opération cURL.  
$curl_errno = curl_errno($curl);
$curl_error = curl_error($curl);
     
// Fermeture de la session cURL et du flux sur le fichier
curl_close($curl);
fclose($fp);
     
if ($curl_errno > 0) {
   echo "cURL Error ($curl_errno): $curl_error\n";
}
else {
   echo "Fichier téléchargé = $data\n";
   $urlFichier = 'http://' . $srvname . '/files/EC_files/' . $fileName;
   echo '<br /><br />URL du fichier : <a target="_blank" href="' . $urlFichier . '">' . $urlFichier . '</a>';
}

?>