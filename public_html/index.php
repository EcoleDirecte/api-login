<html>
	<head>
		<title>Liste des fichiers</title>
	</head>
	<body>
	<a href="http://www.cronjobonline.com" target="_blank" title="Service Cron Online"><img src="http://www.cronjobonline.com/images/80x15.gif" alt="Service Cron Gratuit" border="0"></a>
		<form method="post">
			<label for="dir">
				<input type="text" name="dir" id="dir" class="dir" value="./">
			</label>
			<input type="submit" name="submit"/>
		</form>
	</body>
<?php
$nb_fichier = 0;
echo '<ul>';
if(isset($_POST['dir'])) { $ds = $_POST['dir']; }
else
	{ $ds = $_GET['d']; }

if(isset($request_url)) {
	$ds = $request_url;
}

if ( stripos($ds, 'u769009388') !== FALSE) exit("INTERDIT ! Vous ne pouvez pas liste le contenu de ce dossier.");
if ( stripos($ds, 'files') !== FALSE) exit("INTERDIT ! Vous ne pouvez pas liste le contenu de ce dossier.");
if ( stripos($ds, '../') !== FALSE) exit("INTERDIT ! Vous ne pouvez pas liste le contenu de ce dossier.");

if($dossier = opendir($ds))
{

	while(false !== ($fichier = readdir($dossier))) {

		if($fichier != '.' && $fichier != '..' && $fichier != 'index.php')
		{

			$nb_fichier++;
			echo '<li><a style="color:green;" target="_blank" href="' . $ds . '/' . $fichier . '">' . $fichier . '</a>                           <a target="_blank" style="color:red;" href="/file.php?file=' . $ds . '/' . $fichier . '">Voir la source de ce fichier</a></li>';
			
		}
	 
	}

	echo '</ul><br />';
	echo 'Il y a <strong>' . $nb_fichier .'</strong> fichier(s) dans le dossier';
	 
	closedir($dossier);
 
}
 
else
     echo "<p class=\"caution\">Le dossier n' a pas pu &ecirc;tre ouvert</p>";
?>
</html>