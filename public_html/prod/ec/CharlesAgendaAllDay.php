<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
	</head>
<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/../inc/global.php');

/* 
** 
** Se connecte à EcoleDirecte
** 
*/

$cookie_rand = 'ecoledirecte.cookie';
$cookie    = '/tmp/.'.$cookie_rand;
$agendaDate = DateTomorrow();
// $agendaDate = '2016-09-29';

$agendaDateToo = date_create_from_format('Y-m-d', $agendaDate);
$agendaDateToo = date_format($agendaDateToo, 'l');
if( ($agendaDateToo == 'Saturday') OR ($agendaDateToo == 'Sunday') ) exit("DIE: PAs de travail pour le Week-End.");

SendSmsByFree("A faire pour le $agendaDate :");

             $postLoginFields= 'data={
    "identifiant": "' . EC_CHARLES_LOGIN . '",
    "motdepasse": "' . EC_CHARLES_PASS . '"
}';

$url1="https://vm-api.ecoledirecte.com/v3/login.awp";

// while(true) {

            // Tableau contenant les options de téléchargement
            $options=array(
                  CURLOPT_URL            => $url1,
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
			
            $curl1=curl_init();
             
                  curl_setopt_array($curl1,$options);
				  
                  $content1=curl_exec($curl1);
             
			 
            curl_close($curl1);
			
			$content1Decoded = json_decode($content1, true);
			$content1Decoded_data = $content1Decoded['data']; $content1Decoded_data_accounts = $content1Decoded_data['accounts']; $content1Decoded_data_accounts_0 = $content1Decoded_data_accounts['0'];
			$token = $content1Decoded['token'];
			$ec_id = $content1Decoded_data_accounts_0['id'];
			$url3 = "https://vm-api.ecoledirecte.com/v3/Eleves/" . $ec_id . "/cahierdetexte/" . $agendaDate . ".awp?verbe=get&";
			$token = ltrim($token , "\"abcdefhijklmnopqrstuvwxyzABCDEFHIJKLMNOPQRSTUVWXYZ");
			
            $curl1 = null;
			
             $postAgendaFields= 'data={
    "token": "' . $token . '"
}';
			
			/* --------------------------------------------------------------------------------------------------------------------------------------- */
			
            $options[CURLOPT_URL] = $url3;
            $options[CURLOPT_POSTFIELDS] = $postAgendaFields;
            unset($options[CURLOPT_COOKIESESSION]);
             
            $curl3=curl_init();
             
                  curl_setopt_array($curl3,$options);
                  $content3=curl_exec($curl3);
            
			$content3Decoded = json_decode($content3, true);
			$content3Decoded_data = $content3Decoded['data'];
			$content3Decoded_data_matieres = $content3Decoded_data['matieres']; $content3Decoded_data_matieres_0 = $content3Decoded_data_matieres['0'];
			
			$date_agenda = $content3Decoded_data['date'];
			
			$date = date_create_from_format('Y-m-d', $date_agenda);
			echo "Agenda pour le : " . date_format($date, 'l, d F Y') . "<br />";
			echo '<p style="text-align:center;">------------------------------------------------------------------------------------------------------------</p>';
			
			foreach($content3Decoded_data_matieres as $k => &$v) {
				
				$content3Decoded_data_matieres_actuelle = $content3Decoded_data_matieres[$k];
				$matiereName = $content3Decoded_data_matieres_actuelle['matiere'];
				$profName = substr($content3Decoded_data_matieres_actuelle['nomProf'] , 5);
				$interro = $content3Decoded_data_matieres_actuelle['interrogation'];
				$aFaire = $content3Decoded_data_matieres_actuelle['aFaire'];
				$contenuSeance = $content3Decoded_data_matieres_actuelle['contenuDeSeance'];
				$aFaire_ressourceDocuments = $aFaire['ressourceDocuments'];
				$aFaire_documents = $aFaire['documents'];
				$ctSeance_ressourceDocuments = $contenuSeance['ressourceDocuments'];
				$ctSeance_documents = $contenuSeance['documents'];
				echo "Matière : " . $matiereName . ", avec le prof " . $profName . "<br />";
				
				$toDisplay = '';
				
				if(!empty($aFaire)) {
					echo "<span style=\"text-decoration:underline;;\">Travail à faire :</span><br />";
					var_dump($aFaire);
					echo "<br /><br />";
					
					// $ToSendBySms = $matiereName . ' par ' . $profName . ' : ' . base64_decode($aFaire['contenu']);
					$ToSendBySms = $matiereName . ' par ' . $profName . ' : ';
					$ToSendBySms = str_replace('&', 'et', $ToSendBySms);
					SendSmsByFree($ToSendBySms);
					
					$toDisplay .= '<span style="color:brown;">----<br />Travail : ' . base64_decode($aFaire['contenu']) . '<br />----</span>';
					
					if(!empty($aFaire_ressourceDocuments)) {
						$toDisplay .= '<span style="color:#8ABBE2;">Document dans les ressources pour cette matière ! (catégorie devoir)</span>';
					} else $toDisplay .= '<span style="color:#8ABBE2;">Pas de document dans les ressources pour cette matière ! (catégorie devoir)</span>';
					
					if(!empty($aFaire_documents)) {
						
						echo "<span style=\"color:red;\">------Section TRAVAIL A FAIRE------------</span><br />";
						
						foreach($aFaire_documents as $ka => &$va) {
							
							$documentActuel = $aFaire_documents[$ka];
							
							echo "<span style=\"color:red;\">------------</span><br />";
							echo "<span style=\"color:red;\">ID du document : " . $documentActuel['id'] . "</span><br />";
							echo "<span style=\"color:red;\">Nom du document : " . $documentActuel['libelle'] . "</span><br />";
							echo "<span style=\"color:red;\">Type de document : " . $documentActuel['type'] . "</span><br />";
							echo "<span style=\"color:red;\">------------</span><br />";
							
						}
						
					} else $toDisplay .= '<span style="color:#8ABAE2;">Pas de document pour cette matière ! (catégorie devoir)</span>';
					
				}
				
				if(!empty($contenuSeance)) {
					echo "<span style=\"text-decoration:underline;;\">Contenu de la séance :</span><br />";
					var_dump($contenuSeance);
					echo "<br /><br />";
					
					$toDisplay .= '<span style="color:#8A2BE2;">Fait en cours : ' . base64_decode($contenuSeance['contenu']) . '</span>';
					
					if(!empty($ctSeance_ressourceDocuments)) {
						$toDisplay .= '<span style="color:#8ABBE2;">Document dans les ressources pour cette matière ! (catégorie contenu de seance)</span>';
					} else $toDisplay .= '<span style="color:#8ABBE2;">Pas de document dans les ressources pour cette matière ! (catégorie contenu de seance)</span>';
					
					if(!empty($ctSeance_documents)) {
						
						echo "<span style=\"color:red;\">------Section CONTENU DE SEANCE------------</span><br />";
						
						foreach($ctSeance_documents as $ka => &$va) {
							
							$documentActuel = $ctSeance_documents[$ka];
							
							echo "<span style=\"color:red;\">------------</span><br />";
							echo "<span style=\"color:red;\">ID du document : " . $documentActuel['id'] . "</span><br />";
							echo "<span style=\"color:red;\">Nom du document : " . $documentActuel['libelle'] . "</span><br />";
							echo "<span style=\"color:red;\">Type de document : " . $documentActuel['type'] . "</span><br />";
							echo "<span style=\"color:red;\">------------</span><br />";
							
						}
						
					} else $toDisplay .= '<span style="color:#8ABAE2;">Pas de document pour cette matière ! (catégorie contenu de seance)</span>';
					
				}
				
				if($interro) echo "<span style=\"color:red;\">/!\ Interrogation ! /!\</span><br />";
				if(empty($aFaire)) echo "<span style=\"color:green;\">Il n'y avait pas de travail ! :)</span><br />";
				if(!empty($aFaire)) echo "<span style=\"color:blue;\">Il y avait du travail :(</span><br />";
				if(empty($contenuSeance)) echo "<span style=\"color:green;\">Le contenu de séance est vide ! :(</span><br />";
				
				echo $toDisplay;
				
				echo '<p style="text-align:center;">------------------------------------------------------------------------------------------------------------</p>';
				
				
			}
			
			curl_close($curl3);
			
SendSmsByFree("---FIN---");
 
?>
</html>