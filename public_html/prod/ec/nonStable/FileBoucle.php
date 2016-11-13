<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/../inc/global.php');
$AnneeScolaire = AnneeScolaire(); $continue = true;

$value = "2016-11-09";
$AddDayToDate = ChangeDate(+9, 'days');
$RemoveDayToDate = ChangeDate(-9, 'days');
// echo AddDayToDate();
// parseDateForSC($RemoveDayToDate, $AddDayToDate, 'GetFileIDs');
// parseDateForSC('2016-11-05', '2016-11-12', 'GetFileIDs');
parseDateForSC('2016-11-15', '2016-11-16', 'GetFileIDs');
// parseDateForSC('2016-10-12', '2016-10-14', 'GetFileIDs');
exit();

?>