<!DOCTYPE html>
<html>
<head>
	<title>Résultat des données</title>
</head>
<body>
<?php
	
	require "./function/http.php"; //on importe la fonction http qui permet de faire une requete
	require "./function/log.php"; //on importe la foction log qui permet d'ecrire dans un fichier texte des infos utiles au debug

	$tableau = $_GET; //on creer les variables qui servent a stocker les parametres de l'envoie de la requete
	$url = "url du serveur de la base donnée"; //url du serveur
	$methode = "GET"; //methode de resquete

	// ## Partie Traitement du résultat ##
	$retour_json = lancerRequeteHTTP($url, $methode, $tableau);//on lance la requete http avec les bons parametres
	$retour = json_decode($retour_json,TRUE); //on décode le retour de la fonction
	$type_retour = gettype($retour);// on prend le type de retour pour savoir si ca s'est bien passé
	
	if ($type_retour == "string"){// si le type de retour est une string, cest qu'il ya eus une erreur
		echo $retour; //on affiche donc cette erreur
		exit();
	} 
	if ($type_retour == "array"){
		//faire le traitement des données

	}
?>

</body>
</html>