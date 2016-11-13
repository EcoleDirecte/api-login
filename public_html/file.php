<!DOCTYPE html>
<html>
	<head>
		<title>Afficher source fichier</title>
		<meta charset="utf-8"/>
	</head>
</html>
<?php
if ( stripos($_GET['file'], 'global_variables.php') !== FALSE) exit("FORBIDDEN ! Ce fichier contient des variables dont le contenu est privé.");
if ( stripos($_GET['file'], 'fbm.php') !== FALSE) exit("FORBIDDEN ! Ce fichier contient des identifiants dont le contenu est privé.");
$fichier = file("./" . $_GET['file']);
$total = count($fichier);
for($i = 0; $i < $total; $i++) 
{
	echo $fichier[$i] . '<br />';
}
?>