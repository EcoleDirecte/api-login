<?php

// session_start();

// Variable qui commence par "f_" = variable contenant le chemin exact vers un fichier
// Variable qui commence par "p_" = variable contenant le chemin exact vers un dossier

/**
 * 
 * Fonctions définies dans ce fichier :
 *
 *	- dbCleaner : lui passer en paramètre le mot / expression à nettoyer, ainsi que le type de BDD (à priori, 'mysql') et la fonction retournera
 *		la même expression, avec un encodage des caractères spéciaux
 *
 *  - db_connect : permet se connecter à la BDD directement via db_connect(). Normalement elle n'est utilisée que dans la foncion db_query().
 *
 *  - db_query : on lui passe en paramètre la requête à effectuer (db_query('requete' ou $requete)) et elle se connecte via db_connect puis
 *		effectue la requête
 *
 *  - dbInsert() : insère la valeur retournée par dbCleaner dans la BDD.
 *		Lui passer un array avec 'colonne=>valeur' en 2e paramètre
 *
 *	- GetFilenameFromHeader : à utiliser comme valeur de 'CURLOPT_HEADERFUNCTION'; retourne le "filename" contenu dans le header
 *		dans la variable $GetFilenameFromHeaderResult
 *
 *	- parseDateForSC : Retourne toutes les dates entre lim1 et lim2, au format AAAA-MM-JJ, dans l'année scolaire
 *
 *	- doAction : Exécute l'action spécifiée
 *
 *	- GetFileIDs : Récupère les données des éventuels fichiers présents sur EcoleDirecte au jour spécifié en paramètre
 *
 *	- Mp3_Meta_Data : Retourne les métadonnées contenues dans un fichier mp3
 *
**/

require_once($_SERVER['DOCUMENT_ROOT'] . '/../inc/global_variables.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/../inc/GestionDates.inc');
require_once($_SERVER['DOCUMENT_ROOT'] . '/../inc/GestionComm.inc');
require_once($_SERVER['DOCUMENT_ROOT'] . '/../inc/global_ED.php');
setlocale(LC_TIME, 'fr-FR', 'french', 'fra');
DEFINE('HOMEDIR', '/home/u769009388/');
DEFINE('ROOT', HOMEDIR . 'public_html/');
DEFINE('ROOT_FILES', ROOT . 'files/');
DEFINE('EC_FILES', ROOT_FILES . 'EC_files/');
DEFINE('FILES', HOMEDIR . 'files/');
DEFINE('INC', HOMEDIR . 'inc/');
DEFINE('PROD', ROOT . 'prod/');
DEFINE('PRODEC', PROD . 'ec/');
DEFINE('PCHARTDIR', INC . 'pChart/');

DEFINE('DATE_d', date('d'));
DEFINE('DATE_n', date('n'));
DEFINE('DATE_Y', date('Y'));
DEFINE('DATE_TODAY', date('Y-m-d'));



function dbCleaner($arg, $type = null) {
    $arg = trim($arg);

    switch ($type) {
        case 'mysql':
            $arg = htmlspecialchars($arg, ENT_QUOTES);
            break;
        case 'sqlite':
            $arg = SQLite3::escapeString($arg);
            break;
        case 'pg':
            $arg = pg_escape_string($arg);
            break;
        case 'xml':
            $arg = strtr($arg, array('\\' => '\\\\', "'" => "\'", '"' => '\"', "{" => '\{', "}" => '\}', "<" => '\<', ">" => '\>'));
            break;
        case 'json':
            $arg = strtr($arg, array('\\' => '\\\\', '"' => '\"'));
            break;
        default:
            exit('DIE : LittleSecureLib --> dbCleaner | Bad type.');
            break;
    }

    return ($arg);
}

function db_connect() {
	return mysqli_connect(DB_HOST, DB_USER, DB_PASS , DB_BDD);
}

function db_query($global_db_query_query) {
	$global_db_query_return = mysqli_query(db_connect() , $global_db_query_query)
	or die ('Erreur '.$global_db_query_query);
	mysqli_close(db_connect());
	return $global_db_query_return;
}

function dbInsert($dbInsert_tbl, $dbInsert_arg) {
	$dbInsert_tbl = trim($dbInsert_tbl); $dbInsert_arg_cln = ''; $dbInsert_arg_val = '';
	
	foreach($dbInsert_arg as $k => &$v) {
		$v = dbCleaner($v, 'mysql');
		$dbInsert_arg_cln = $dbInsert_arg_cln . '' . $k . ', ';
		$dbInsert_arg_val = $dbInsert_arg_val . '\'' . $v . '\', ';
	}
	$dbInsert_arg_cln = substr($dbInsert_arg_cln , '0' , '-2'); $dbInsert_arg_val = substr($dbInsert_arg_val , '0' , '-2');
	
	$dbInsert_query = "INSERT INTO " . $dbInsert_tbl . " (" . $dbInsert_arg_cln . ") VALUES (" . $dbInsert_arg_val . ")";
	return db_query($dbInsert_query);
}

/* function curlUse ($cuUrl, $cuType='GET', $cuCookieReset='', ) {
	
						$eclOptions=array(
						  CURLOPT_URL            => "https://vm-api.ecoledirecte.com/v3/login.awp", //
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_FOLLOWLOCATION => false,
						  CURLOPT_HEADER         => false,
						  CURLOPT_FAILONERROR    => false,
						  CURLOPT_POST           => true, //
						  CURLOPT_COOKIESESSION  => 1, //
						  CURLOPT_COOKIEJAR      => $cookie,
						  CURLOPT_COOKIEFILE     => $cookie,
						  CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Linux x86_64; rv:47.0) Gecko/20100101 Firefox/47.0)', 
						  CURLOPT_POSTFIELDS     => $postLoginFields //
					);
	
} */

function GetFilenameFromHeader($curl, $str) {
	global $GetFilenameFromHeaderResult;
	if(strstr($str, 'filename=')) {
		
		$GetFilenameFromHeaderResult = strstr($str, 'filename=');
		$GetFilenameFromHeaderResult = strstr($GetFilenameFromHeaderResult, '"');
		$nbToShrink = strripos($GetFilenameFromHeaderResult, '"');
		$GetFilenameFromHeaderResult = substr($GetFilenameFromHeaderResult, 0, $nbToShrink); $GetFilenameFromHeaderResult = substr($GetFilenameFromHeaderResult, 1);
		
	}
	return strlen($str);
}

function parseDateForSC($lim1=REMOVEDAYTODATE, $lim2=ADDDAYTODATE, $action) {
			
			$AnneeScolaire = AnneeScolaire(); $continue = true; $display=false;

			// $moisActuel = date('m');
			$moisActuel = substr($lim1, 5, 2);
			$anneeActuelle = date('Y');
			
			if($moisActuel == 12) { $moisSuivant = 01; $anneeSuivante=$anneeActuelle+1; }
			else { $moisSuivant = $moisActuel+1; $anneeSuivante = $anneeActuelle; }
			
			$ed_file_date_limit = $lim2;

			// $EcoleDirecteGetID = EcoleDirecteLogin(/*, user_ED, mdp_ED*/);
			$EcoleDirecteGetID = EcoleDirecteLogin(EC_CHARLES_LOGIN, EC_CHARLES_PASS);

			$dateFinale = array();

			$createUserTableQuery = "CREATE TABLE IF NOT EXISTS FILES_" . $EcoleDirecteGetID['id'] . " (id INT(11) NOT NULL AUTO_INCREMENT,
			  fileDate VARCHAR(11) DEFAULT NULL,
			  matiereName VARCHAR(255) DEFAULT NULL,
			  name VARCHAR(255) DEFAULT NULL,
			  url VARCHAR(255) DEFAULT NULL,
			  filePlace VARCHAR(20) DEFAULT NULL,
			  fileId INT(11) DEFAULT NULL,
			  fileType VARCHAR(20) DEFAULT NULL,
			  PRIMARY KEY (id))";
			$createUserTableQueryExec = db_query($createUserTableQuery);

			if($continue)
				for($i = $moisActuel; $i<= 12; $i++){
					
					$FindSchoolYear = FindSchoolYear($AnneeScolaire, $i);
					$fmois = strftime('%B', strtotime(date('Y').'-'.$i.'-'.date('d')));
					$monthLenght = MonthLenght($i);
					
					for($ia = 1; $ia<=$monthLenght; $ia++) {
						if(strlen($ia) == 1) $ia = 0 . $ia;
						if(strlen($i) == 1) $i = 0 . $i;
						if($FindSchoolYear)
							$fdate = $FindSchoolYear . '-' . $i . '-' . $ia;
						$boucleDate = $fdate;
						if($ed_file_date_limit == $fdate) { $continue = false; $i=52; break; }
						
						if($fdate == $lim1) $display=true;
						
						if($display) {
							
							var_dump($fdate); echo '<br />';
							doAction($action, $fdate);
							
						}
					}
					
				}

			if($continue)
				for($i = 1; $i<= 7; $i++){
					
					$FindSchoolYear = FindSchoolYear($AnneeScolaire, $i);
					$fmois = strftime('%B', strtotime(date('Y').'-'.$i.'-'.date('d')));
					$monthLenght = MonthLenght($i);
					
					for($ia = 1; $ia<=$monthLenght; $ia++) {
						if(strlen($ia) == 1) $ia = 0 . $ia;
						if(strlen($i) == 1) $i = 0 . $i;
						if($FindSchoolYear)
							$fdate = $FindSchoolYear . '-' . $i . '-' . $ia;
						if($ed_file_date_limit == $fdate) $continue = false;
						
						if($fdate == $lim1) $display=true;
						
						if($display) {
							
							echo $fdate . '<br />';
							doAction($action, $fdate);
							
						}
						
					}
					
				}
}

function doAction($action, $options='') {
	
	switch($action) {
		
		case 'GetFileIDs':
			// include(PRODEC . 'GetFileIDs.php');
			GetFileIDs($options);
			break;
		
	}
	
}

function GetFileIDs($ffdate) {

            $EcoleDirecteGetAgenda = EcoleDirecteGetAgenda($ffdate); // à supprimer après
            $content3 = $EcoleDirecteGetAgenda['content'];
            $content3Decoded = $EcoleDirecteGetAgenda['content'];
			$content3Decoded_data = $content3Decoded['data'];
			$content3Decoded_data_matieres = $content3Decoded_data['matieres']; $content3Decoded_data_matieres_0 = $content3Decoded_data_matieres['0'];
			$date_agenda = $content3Decoded_data['date'];
			
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

}

function Mp3_Meta_Data($file){
	//Verifie que le fichier existe
	if (! file_exists($file)) return -1;
	
	$metatags_size = array('title'=>30,'artiste'=>30,'album'=>30,
						'year'=>4,'comment'=>30,'genre'=>1);
	$metatags_value=array();
	
	//Positionne sur la partie ou les Tags devraient être
	$id_start=filesize($file)-128;
	$fp=fopen($file,"r");
	fseek($fp,$id_start);
	
	//Verifie qu'il y a un emplacement pour les tags
	if (! fread($fp,3) == "TAG")return -1;
	//Récupère les tags
	foreach($metatags_size as $title => $size)
		echo $metatags_value[$title]=@fread($fp,$size);
	fclose($fp);
	return $metatags_value;
}

?>