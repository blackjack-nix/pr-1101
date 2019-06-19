<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>Liste de données</title>
	<link rel="stylesheet" type="text/css" href="./style/style-liste.css"/>
	<link rel="icon" type="image/png" href="./image/logo.png" />

</head>
<body>
	<div id="fond">
		<img src="https://ak8.picdn.net/shutterstock/videos/12587588/thumb/1.jpg" alt="fond">
	</div>

<?php

	// ## Partie import des fonctions ##

	include_once "./function/http.php"; //on importe la fonction http qui permet de faire une requete
	include_once "./function/log_redef.php"; //on importe la foction log qui permet d'ecrire dans un fichier texte des infos utiles au debug


	// ## Partie Authentification ##


	if (($_GET["user"] != "jekill") || ($_GET["mdp"]!="congrat_10"))	//on verifie si le hash en mémoire est bien le meme que celui rentré par l'utilisateur, ainsi que le mot de passe
	{
		echo "<h1 id=\"echec\">L'authentification a échouée</h1>"; // si ce n'est pas le cas, on affiche un message d'erreur
		echo "<form>
  				<input id=\"retour\" type=\"button\" value=\"Retour\" onclick=\"history.go(-1)\">
			  </form>";
		log_redef("Tentative de connexion echoué","./log/data.log"); // on écrit dans les logs qu'il ya eus une tentative de connection qui a echouée
		exit(); // on quite le programme
	}
?>

	<div id="text_intro">
		<h1><b>Vous avez reussi a vous identifier</b></h1><br>
		<p><b>Veuiller choisir une session pour connaitre les resultats de l'analyse algorithmique</b></p>
	</div>

<?php
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
		echo"<legend><b><i>Session</i></b></legend>";
		echo "<input type=\"hidden\" name=\"user\" value=\"jekill\">";
		echo "<input type=\"hidden\" name=\"mdp\" value=\"congrat_10\">";
		echo "<select id=\"session\" name=\"session\">";

		foreach ($retour as $key => $value) {
			echo "<option value=".$value["id_session"].">".$value["nom"]." | ".$value["prenom"]." | ".$value["abrege_contexte_mesures_cliniques"]."</option>";//on ajoute les differentes options
		}
	}
?>
<br>
<input id="submit" type="submit" value="VALIDER"> </form>
	

<footer id="footer">
		<p>Fait par : Laurent Delatte, Theo Peresse-Gourbil et Manon Hermann. <br> Dans le cadre du projet de fin de premiere annee à l'ESIEE Paris</p>
		<img src="https://esiee.fr/sites/all/themes/custom/esiee_theme/logo.png"/>
</footer>
</body>
</html>
