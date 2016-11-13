<?php

/**
 * 
 * Fonctions définies dans ce fichier :
 *
 *	- AnneeScolaire : Retourne l'année scolaire en cours (exemple : 2016-2017) dans un array avec ['y1'] = 2016 et ['y2'] = 2017.
 *
 *	- FindSchoolYear : Retourne l'année à laquelle appartient un mois, à partir du n° du mois et du couple année scolaire (ex.: entrée : mois 02; année 2016-2017
 * 		et la fonction retourne sous forme de tableau token et id et le fichier de cookie utilisée.
 *
 *	- AnneeScolaire : Retourne l'année scolaire en cours (exemple : 2016-2017) dans un array avec ['y1'] = 2016 et ['y2'] = 2017.
 *		retournera que le mois appartient à l'année 2017)
 *		Prend un array(y1,y2) pour l'année scolaire, et une string ou INT pour le mois
 *
 *	- DateTomorrow : Retourne la date du lendemain, au format AAAA-MM-JJ
 *
 *	- ChangeDate : Paramètres : +/-(x), (month,day,year), date au format AAAA-MM-JJ
 *		Renvoie la date envoyée (par défaut, date du jour) + ou - x jours/mois/année(s)
 *
 *	- MonthLenght : Détermine le nombre de jours du mois renseigné. Si le mois est février, deux options :
 *		- l'année est renseignée en 2nd paramètre : il renvoie alors 28 ou 29, en fonction de si l'anée est bissextile ou non;
 *		- l'année n'est pas renseignée : il renvoie 28 par défaut
 *
**/

function FindSchoolYear($fscyears, $fmonth, $fday='') {
	if(empty($fday)) unset ($fday);
	$fireturn = NULL;
	if($fmonth <= 7) {
		if($fmonth <= 6) $fireturn = $fscyears['y2'];
		elseif( ($fmonth == 7) AND ($fday <= 10) ) $fireturn = $fscyears['y2'];
		else $fireturn = 0;
	} elseif($fmonth >= 9) $fireturn = $fscyears['y1'];
	return $fireturn;
}

function DateTomorrow() {
	return date("Y-m-d", mktime (0,0,0,date("m" ) ,date("d" )+1,date("Y" )));
}

function ChangeDate($nbIncrement, $typeIncrement='days', $date=DATE_TODAY) {

		$dateDepart = '2016-11-10';
		if(!empty($date)) $dateDepart = $date;

		$dateDepartTimestamp = strtotime($dateDepart);

		$dateFin = date('Y-m-d', strtotime($nbIncrement.' '.$typeIncrement, $dateDepartTimestamp ));
		
		return $dateFin;

} DEFINE('ADDDAYTODATE', ChangeDate(+9, 'days')); DEFINE('REMOVEDAYTODATE', ChangeDate(-9, 'days'));

function AnneeScolaire() {
	$fireturn = NULL;
	$fyear = date('Y');
	$fmonth = date('m');
	$fday = date('d');
	if($fmonth <= 7) {
		if($fmonth <= 6) $fireturn = array('y1' => $fyear - 1, 'y2' => intval($fyear));
		elseif( ($fmonth == 7) AND ($fday <= 10) ) $fireturn = array('y1' => $fyear - 1, 'y2' => intval($fyear));
		else $fireturn = "A partir du mois du 10 Juillet, ce n'est plus une annee scolaire.";
	} elseif($fmonth >= 9) $fireturn = array('y1' => intval($fyear), 'y2' => $fyear + 1);
	return $fireturn;
}

function MonthLenght($mlMonth, $mlYear='') {
	
		$months_31 = array(1,3,5,7,8,10,12); $months_30 = array(4,6,9,11);
		if(in_array($mlMonth, $months_31)) $monthLenght = 31;
		elseif(in_array($mlMonth, $months_30)) $monthLenght = 30;
		elseif($mlMonth == 2) {
			if(!empty($mlYear)) {
				
				if (date("L", mktime(0, 0, 0, 1, 1, $mlYear)) == 1)
				{
					$monthLenght = 29;
				}
				else
				{
					$monthLenght = 28;
				}
			} else $monthLenght=28;

		}
	// return array('mois'=>$mlMonth, 'lenght'=>$monthLenght);
	return $monthLenght;
}

?>