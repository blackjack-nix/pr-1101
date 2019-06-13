<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>Liste de données</title>
	<link rel="stylesheet" type="text/css" href="./style/style-liste.css"/>
</head>
<body>
	<div id="fond">
		<img src="https://ak8.picdn.net/shutterstock/videos/12587588/thumb/1.jpg" alt="fond">
	</div>

<?php

	// ## Partie import des fonctions ##

	include_once "./function/http.php"; //on importe la fonction http qui permet de faire une requete
	include_once "./function/log_redef.php"; //on importe la foction log qui permet d'ecrire dans un fichier texte des infos utiles au debug
	include_once "./function/hash_redef.php"; //on importe la fonction hash qui permet de securiser (un peu) le mot de passe et l'utilisateur
	include_once "./function/authentification.php";


	// ## Partie Authentification ##


	if (($_GET["user"] != "jekill") || ($_GET["mdp"]!="congrat_10"))	//on verifie si le hash en mémoire est bien le meme que celui rentré par l'utilisateur, ainsi que le mot de passe
	{
		echo "L'authentification a échouée <br>"; // si ce n'est pas le cas, on affiche un message d'erreur
		log_redef("Tentative de connexion echoué","./log/data.log"); // on écrit dans les logs qu'il ya eus une tentative de connection qui a echouée
		exit(); // on quite le programme
	}


	// ## Partie Préparatoire de la requete

	$tableau = $_GET; //on creer les variables 	qui servent a stocker les parametres de l'envoie de la requete
	$url = "http://147.215.191.35/EDP/ws_rasp.php"; //url du serveur
	$methode = "GET"; //methode de resquete


	// ## Partie Traitement du résultat ##

	$retour_json = lancerRequeteHTTP($url, $methode, $tableau);//on lance la requete http avec les bons parametres
	$retour = json_decode($retour_json,TRUE); //on décode le retour de la fonction
	$type_retour = gettype($retour);// on prend le type de retour pour savoir si ca s'est bien passé

	if ($type_retour == "string"){// si le type de retour est une string, cest qu'il ya eus une erreur
		echo $retour; //on affiche donc cette erreur
		exit();
	}
	if ($type_retour == "array"){ //si le retour est un tableau
		echo "<form action=\"./donnees.php\" methode=".$methode." name=\"Session\">";//on creer le formulaire avec les differents propositions qui nous sont retournées par la requete
		echo "<input type=\"hidden\" name=\"user\" value=\"jekill\">";
		echo "<input type=\"hidden\" name=\"mdp\" value=\"congrat_10\">";
		echo "Session : <select name=\"session\">";

		foreach ($retour as $key => $value) {
			echo "<option value=".$value["id_session"].">".$value["nom"]." | ".$value["prenom"]." | ".$value["abrege_contexte_mesures_cliniques"]."</option>";//on ajoute les differentes options
		}
			echo "<input type=\"submit\" value=\"Envoyer\"></form>";//on quite le formulaire
	}

?>


</body>
</html>
