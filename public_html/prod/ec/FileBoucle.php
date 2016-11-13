<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/../inc/global.php');
$AddDayToDate = ChangeDate(+9, 'days');
$RemoveDayToDate = ChangeDate(-9, 'days');
parseDateForSC($RemoveDayToDate, $AddDayToDate, 'GetFileIDs');
exit();

?>