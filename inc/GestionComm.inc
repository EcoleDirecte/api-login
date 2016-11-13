<?php

/**
 * 
 * Fonctions définies dans ce fichier :
 *
 *	- SendSmsByFree : Envoie un SMS à mon numéro de TEL. Nécessite un paramètre, le message à envoyer.
 *
 *	- SendMail : Envoie un mail, avec les paramètres suivants :
 *		adresse e-mail du destinataire souhaité du mail
 *		sujet du mail
 *		message contenu dans le mail
 *		[facultatif] nom de l'expéditeur
 *		[facultatif] adresse e-mail de l'expéditeur
 *		--> à ajouter : copie, copie cachée, (envoi de fichier ?)
 *
**/

function SendSmsByFree($CurlSendSMSMsg='') {
	
 	if(empty($CurlSendSMSMsg)) $CurlSendSMSUrl = FREEMB_SMSAPI_URL . "Aucune url n'a ete specifiee";
	else $CurlSendSMSUrl = FREEMB_SMSAPI_URL . $CurlSendSMSMsg;
	
/* 	if(empty($CurlSendSMSMsg)) $CurlSendSMSMsg = "Aucune url n'a ete specifiee";
	else $CurlSendSMSUrl = FREEMB_SMSAPI_URL . dbCleaner($CurlSendSMSMsg, 'mysql'); */
	
	// $postSendSMS = array(
	// 'user' => FREEMB_SMSAPI_USER,
	// 'pass' => FREEMB_SMSAPI_PASS,
	// 'msg' => 
	// )
	
	$CurlSensSMSOptions=array(
			CURLOPT_URL            => $CurlSendSMSUrl, // Url cible (l'url la page que vous voulez télécharger)
			CURLOPT_RETURNTRANSFER => true, // Retourner le contenu téléchargé dans une chaine (au lieu de l'afficher directement)
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_HEADER         => false, // Ne pas inclure l'entête de réponse du serveur dans la chaine retournée
			CURLOPT_FAILONERROR    => false,       // Gestion des codes d'erreur HTTP supérieurs ou égaux à 400
			CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Linux x86_64; rv:47.0) Gecko/20100101 Firefox/47.0)', 
	);
	
	$CurlSendSMS = curl_init();
	curl_setopt_array($CurlSendSMS,$CurlSensSMSOptions);
	$CurlSendSMSContent = curl_exec ($CurlSendSMS);
	curl_close($CurlSendSMS);
	
}

function SendMail($destinataire, $sujet, $message, $expediteurName='', $expediteurMail='') {
	
		if(empty($expediteurName)) $expediteurName = 'Bidouillages - contact';
		if(empty($expediteurMail)) $expediteurMail = 'contact@bidouillages.tk';

		$headers = "From: \"" . $expediteurName . "\"<" . $expediteurMail . "\n";
		$headers .= "Reply-To: " . $expediteurMail . "\n";
		$headers .= "Content-Type: text/html; charset=\"UTF-8\"";

		mail($destinataire,$sujet,$message,$headers);
	
}

?>