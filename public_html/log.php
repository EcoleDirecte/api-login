<!DOCTYPE html>
<html>
	<head>
		<title>Logs PHP</title>
		<meta charset="utf-8"/>
	</head>
</html>
<?php
$fichier = file("../.logs/php_error.log");
$total = count($fichier);
for($i = 0; $i < $total; $i++) 
{
	echo $fichier[$i] . '<br /><br />';
}
?>