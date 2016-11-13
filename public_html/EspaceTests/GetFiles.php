<?php

$chaine = 'Ma chaîne';
var_dump(urlencode($chaine));

exit();

require_once($_SERVER['DOCUMENT_ROOT'] . '/../inc/global.php');

var_dump(EcoleDirecteSaveNewFile(array('token' => EcoleDirecteLogin(EC_CHARLES_LOGIN, EC_CHARLES_PASS)['token'],
'fileId' => 1197, 'fileName' => 'monfichier.pdf')));

?>