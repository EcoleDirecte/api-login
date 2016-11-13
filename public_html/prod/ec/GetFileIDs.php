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

// $fdate = "2016-11-07";
            // $EcoleDirecteGetAgenda = EcoleDirecteGetAgenda('date', 'login_ED', 'pass_ED');
            $EcoleDirecteGetAgenda = EcoleDirecteGetAgenda($fdate); // à supprimer après
            $content3 = $EcoleDirecteGetAgenda['content'];
            $content3Decoded = $EcoleDirecteGetAgenda['content'];
			$content3Decoded_data = $content3Decoded['data'];
			
			$content3Decoded_data_matieres = $content3Decoded_data['matieres']; $content3Decoded_data_matieres_0 = $content3Decoded_data_matieres['0'];
			
			$date_agenda = $content3Decoded_data['date'];
			
			// var_dump($content3Decoded_data_matieres);
			
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
				
				$toDisplay = '';
				
				if(!empty($aFaire)) {
					
					if(!empty($aFaire_documents)) {
						
						foreach($aFaire_documents as $ka => &$va) {
							
							$documentActuel = $aFaire_documents[$ka];
			
							$dataFilesFinal = array(
							'fileDate' => $date_agenda,
							'matiereName' => $matiereName,
							'name' => $documentActuel['libelle'],
							'filePlace' => 'aFaire',
							'fileId' => $documentActuel['id'],
							'fileType' => $documentActuel['type']
							);
							
							EcoleDirecteSaveNewFileInDB($EcoleDirecteGetAgenda['id'], $dataFilesFinal);
							
						}
						
					} else $toDisplay .= '<span style="color:#8ABAE2;">Pas de document pour cette matière ! (catégorie devoir)</span>';
					
				}
				
				if(!empty($contenuSeance)) {
					
					if(!empty($ctSeance_documents)) {
						
						foreach($ctSeance_documents as $ka => &$va) {
							
							$documentActuel = $ctSeance_documents[$ka];
			
							$dataFilesFinal = array(
							'fileDate' => $date_agenda,
							'matiereName' => $matiereName,
							'name' => $documentActuel['libelle'],
							'filePlace' => 'contenuSeance',
							'fileId' => $documentActuel['id'],
							'fileType' => $documentActuel['type']
							);
							
							EcoleDirecteSaveNewFileInDB($EcoleDirecteGetAgenda['id'], $dataFilesFinal);
							
						}
						
					} else $toDisplay .= '<span style="color:#8ABAE2;">Pas de document pour cette matière ! (catégorie contenu de seance)</span>';
					
				}
				
				
			}
 
?>
</html>