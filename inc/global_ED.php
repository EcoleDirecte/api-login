<?php

/**
 * 
 * Fonctions définies dans ce fichier :
 *
 *	- EcoleDirecteLogin : Donner en paramètre (identifiant, mot de passe) de connexion à Ecole Directe,
 *
 *	- EcoleDirecteGetAgenda : Récupère le contenu de l'agenda au format brut. Prend en paramètre date(obligatoire, format AAAA-MM-JJ),
 * 		identifiant E.D (par défaut, mon identifiant), mot de passe E.D. (par défaut le mien également)
 *
 *	- EcoleDirecteSaveNewFileInDB : xxx
 *
 *	- EcoleDirecteSaveNewFile : nécessite :
 *		[EcoleDirecte token], 
 *		[id du fichier], 
 *		[nom du fichier]  et 
 *		[type] (par défaut, FICHIER_CDT)
 *		Télécharge le fichier depuis Ecole Directe et l'enregistre dans FILES (public_html/../files/), puis l'envoie par FTP sur bidoutk.free.fr
 *
 // *	- parseDateForSC : Retourne toutes les dates entre lim1 et lim2, au format AAAA-MM-JJ, dans l'année scolaire
 *
 // *	- doAction : Exécute l'action spécifiée
 *
 // *	- GetFileIDs : Récupère les données des éventuels fichiers présents sur EcoleDirecte au jour spécifié en paramètre
 *
**/

function EcoleDirecteLogin($eclLogin, $eclPass) {
					
					$cookie = '/tmp/.ecl.cookie';
             $postLoginFields= 'data={
    "identifiant": "' . $eclLogin . '",
    "motdepasse": "' . $eclPass . '"
}';
					$eclOptions=array(
						  CURLOPT_URL            => "https://vm-api.ecoledirecte.com/v3/login.awp",
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_FOLLOWLOCATION => false,
						  CURLOPT_HEADER         => false,
						  CURLOPT_FAILONERROR    => false,
						  CURLOPT_POST           => true,
						  CURLOPT_COOKIESESSION  => 1,
						  CURLOPT_COOKIEJAR      => $cookie,
						  CURLOPT_COOKIEFILE     => $cookie,
						  CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Linux x86_64; rv:47.0) Gecko/20100101 Firefox/47.0)', 
						  CURLOPT_POSTFIELDS     => $postLoginFields
					);
					$eclCurl=curl_init();
					curl_setopt_array($eclCurl,$eclOptions); 
					$eclContent=curl_exec($eclCurl);
					 
					curl_close($eclCurl);
					$eclContentDecoded = json_decode($eclContent, true);
					$eclContentDecoded_data = $eclContentDecoded['data']; $eclContentDecoded_data_accounts = $eclContentDecoded_data['accounts']; $eclContentDecoded_data_accounts_0 = $eclContentDecoded_data_accounts['0'];
					$eclCurl = null;
					
					$eclReturn = array(
					'cookie' => $cookie,
					'options' => $eclOptions,
					'token' => $eclContentDecoded['token'],
					'id' => $eclContentDecoded_data_accounts_0['id'],
					);
					
					return $eclReturn;

}

function EcoleDirecteGetAgenda($agendaDate, $eclLogin=EC_CHARLES_LOGIN, $eclPass=EC_CHARLES_PASS) {
					
					$EcoleDirecteLogin = EcoleDirecteLogin($eclLogin, $eclPass);
					$eclaEc_id = $EcoleDirecteLogin['id'];
					$eclaOptions = $EcoleDirecteLogin['options'];
					$eclaCookie = $EcoleDirecteLogin['cookie'];
			
					$postAgendaFields= 'data={
    "token": "' . $EcoleDirecteLogin['token'] . '"
}';
					$eclaUrl = "https://vm-api.ecoledirecte.com/v3/Eleves/" . $eclaEc_id . "/cahierdetexte/" . $agendaDate . ".awp?verbe=get&";
					
					$eclaOptions[CURLOPT_URL] = $eclaUrl;
					$eclaOptions[CURLOPT_POSTFIELDS] = $postAgendaFields;
					unset($eclaOptions[CURLOPT_COOKIESESSION]);
					
						$eclaCurl=curl_init();
             
					curl_setopt_array($eclaCurl,$eclaOptions);
					$eclaContent=array('content' => $content3Decoded = json_decode(curl_exec($eclaCurl), true), 'token' => $EcoleDirecteLogin['token'], 'id' => $eclaEc_id);
					
					return $eclaContent;

}

function EcoleDirecteSaveNewFileInDB($userID, $insertData) {
	
	// $adresse_nouvelle = "SELECT name FROM FILES_" . $userID . " WHERE fileId='".$fileId."'";
	$adresse_nouvelle = "SELECT name FROM FILES_" . $userID . " WHERE fileId='".$insertData['fileId']."'";
	$resultat = db_query($adresse_nouvelle);
	$nombre_adresse = mysqli_num_rows($resultat);
	if(!empty($insertData['fileId'])) {
		
			if($nombre_adresse < 1)
			{
				
				$tmpLink = EcoleDirecteSaveNewFile(array(
					'token' => EcoleDirecteLogin(EC_CHARLES_LOGIN, EC_CHARLES_PASS)['token'],
					'fileId' => $insertData['fileId'],
					'fileName' => $insertData['name']
					)); $tmpLink1 = $tmpLink; $tmpLink = str_replace(' ', '%20', $tmpLink);
					
				$insertData['url'] = $tmpLink1;
					
				
				$SendSMSIfNew = rawurlencode("Nouveau Fichier à la Date : "
					. $insertData['fileDate'] . "\nMatière : " . $insertData['matiereName']
					. "\nURL : " . $tmpLink
					. "\nNom : " . $insertData['name']);
			
				var_dump(dbInsert("FILES_" . $userID, $insertData));
				SendSmsByFree($SendSMSIfNew);
					
				$sujet = utf8_decode("[NOTIFICATION]" . $insertData['matiereName'] . " : Nouveau fichier : " . $insertData['name']);
				$corps = utf8_decode("Un nouveau fichier a été mis en ligne en " . $insertData['matiereName'] . " dans " . $insertData['filePlace'] . ".
				<br /><br />Il est présent à la date du " . $insertData['fileDate'] . " et son nom est \"" . $insertData['name'] . "\".
				<br /><bbr />Le lien pour le télécharger est <a href=\"" . $tmpLink . "\">" . $tmpLink1 . "</a>.
				<br /><bbr />Si besoin, son identifiant est \"" . $insertData['fileId'] . "\".");
				SendMail('charlesdecoux92@gmail.com', $sujet, $corps);
			}
		
		var_dump($insertData);
		echo '<br /><br />';
			
	}
	
}

function EcoleDirecteSaveNewFile($eclData, $fileType='FICHIER_CDT') {

			// eclData => token; fichier_id; [fichier_name]
			
			$srvname = $_SERVER["SERVER_NAME"];
			$postLoginFields = 'leTypeDeFichier=' . $fileType . '&fichierId=' . $eclData['fileId'] . '&token=' . $eclData['token'];
			
			$eclData = array_filter($eclData);
			if(!array_key_exists('fileName', $eclData)) {
						
						exit('DIE: Vous devez sp&eacute;ficier un nom de fichier.');
						
						/* require_once($_SERVER['DOCUMENT_ROOT'] . '/../inc/global.php');

						$url = 'https://vmws09.ecoledirecte.com/v3/telechargement.awp?verbe=get';

						$curl = curl_init();
						curl_setopt($curl, CURLOPT_URL, $url);
						curl_setopt($curl, CURLOPT_HEADERFUNCTION, 'GetFilenameFromHeader');

						curl_setopt($curl, CURLOPT_POST, true);
						curl_setopt($curl, CURLOPT_POSTFIELDS, $postLoginFields);
						curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64; rv:47.0) Gecko/20100101 Firefox/47.0)');
							 
						$data = curl_exec($curl);
						$curl_errno = curl_errno($curl);
						$curl_error = curl_error($curl);
						curl_close($curl);
						
						echo $GetFilenameFromHeaderResult; */
				
			}

			$curl = curl_init();
			$url = 'https://vmws09.ecoledirecte.com/v3/telechargement.awp?verbe=get';
			curl_setopt($curl, CURLOPT_URL, $url);
			// $fileName = 'fichier.pdf';
			$fileName = $eclData['fileName'];
			$fileA = EC_FILES . $fileName;
			$fp = fopen($fileA, "w");

			curl_setopt($curl, CURLOPT_FILE, $fp);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
			curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postLoginFields);
			curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64; rv:47.0) Gecko/20100101 Firefox/47.0)');
				 
			$data = curl_exec($curl);
				  
			$curl_errno = curl_errno($curl);
			$curl_error = curl_error($curl);
				 
			curl_close($curl);
			fclose($fp);
				 
			if ($curl_errno > 0) {
			   echo "cURL Error ($curl_errno): $curl_error\n";
			}
			else {
			   // echo "Fichier téléchargé = $data\n";
			   $urlFichier = 'http://' . $srvname . '/files/EC_files/' . $fileName;
			   // echo '<br /><br />URL du fichier : <a target="_blank" href="' . $urlFichier . '">' . $urlFichier . '</a>';
			   return $urlFichier;
			}
			
}

?>