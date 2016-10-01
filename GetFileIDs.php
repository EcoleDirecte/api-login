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

$agendaDate = $_GET['date'];
$agendaDate = '2016-09-29';

			$content3 = EcoleDirecteGetAgenda($agendaDate);
            
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
 
?>
</html>