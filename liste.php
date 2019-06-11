<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>Liste de données</title>
</head>
<body>

<?php
	// ## Partie import des fonctions ##

	require "./function/http.php"; //on importe la fonction http qui permet de faire une requete
	require "./function/log.php"; //on importe la foction log qui permet d'ecrire dans un fichier texte des infos utiles au debug
	require "./function/hash.php"; //on importe la fonction hash qui permet de securiser (un peu) le mot de passe et l'utilisateur


	// ## Partie Préparatoire de la requete

	$tableau = $_GET; //on creer les variables qui servent a stocker les parametres de l'envoie de la requete
	$url = "http://147.215.191.33/pr_1101/ws_rasp.php"; //url du serveur
	$methode = "GET"; //methode de resquete


	// ## Partie Sécurité ##

	$fichier_usr = fopen("./.usr", "r"); //on ouvre le fichier contenant un hash de l'utilisateur en lécture seul (plus sécurisé)
	$fichier_mdp = fopen("./.mdp", "r"); //on ouvre le fichier contenant un hash du mot de passe en lécture seul (plus sécurisé)
	if (is_null($fichier_usr) | is_null($fichier_mdp)){ // on regarde si le pointeur renvoyé par fopen est null, ce qui signifie qu'il n'a pas réussi a ouvrir le fichier 
		echo "Authentification impossible"; // on affiche un message d'erreur
		log("Impossible d'ouvrir le fichier du hash","./log/hash.log");//on écrit dans les logs l'erreur qui peut toujours servir
		exit(); // on sort du programme 
	}
	$usr = fgets($fichier_usr); //on stoque dans une variable la ligne correspondant au hash de l'utilisateur
	$mdp = fgets($fichier_mdp);	//on stoque dans une variable la ligne correspondant au hash du mot de passe

	fclose($fichier_usr); // on ferme le fichier, plus sur et evite les fuites mémoires
	fclose($fichier_mdp); // on ferme le fichier, plus sur et evite les fuites mémoires

	$usr_saisis = hash($_GET["usr"]); //On calcul le hash avec une fonction qui choist un hash disponible sur la machine
	$mdp_saisis = hash($_GET["mdp"]); //On calcul le hash avec une fonction qui choist un hash disponible sur la machine



	// ## Partie Authentification ##

	if (!(($usr == $usr_saisis) & ($mdp == $mdp_saisis))){ //on verifie si la hash en mémoire est bien le meme que celui rentré par l'utilisateur, ainsi que la mot de passe 
		echo "L'authentification a échouée"; // si ce n'est pas le cas, on affiche un message d'erreur
		log("Tentative de connexion echoué","./log/data.log"); // on écrit dans les logs qu'il ya eus une tentative de connection qui a echouée
		exit(); // on quite le programme
	}


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
		echo "Session : <select name=\"session\">";

		foreach ($retour as $key => $value) {
			echo "<option value=".$value["id_session"]."\">".$value["nom"]." | ".$value["prenom"]." | ".$value["abrege_contexte_mesures_cliniques"]."</option>";//on ajoute les differentes options
		}
		echo "<input type=\"submit\" value=\"Envoyer\"></form>";//on quite le formulaire
	}
	


?>


</body>
</html>

