<?php
	function log(string $erreur, string $fichier){ //fonction qui prend en parametre une string de l'erreur a ecrire dans le fichier;

	$fichier = fopen($fichier, "a+"); //ouverture du fichier log en lecture et ecriture avec création du fichier si il n'existe pas;
	
	if ($fichier == null){ // on est si le pointeur est null (ce qui signifie qu'il n'a pas d'espace en mémoire donc pas bien ouvert);
		echo "Erreur dans l'ouverture du fichier log"; // on affiche une erreur an html avec echo ;
		return -1; //on arret la fonction si on a pas pu ouvrir le fichier;
	}

	if ($erreur != ""){
		$last_error = $erreur; //on recupere le message a ecrire dans le fichier;
	} else {
	 	$last_error = error_get_last(); //on récupere la derinier erreur générée par php ;
	}

	fwrite($fichier, $last_error); // on ecrit dans le fichier ouvert précédement l'erreur voulue;
	fclose($fichier); //on ferme le fichier (parce que c'est ben de le faire) et ca libere de l'space mémoire;
	return  $last_error; //on revoir la string avec l'erreur générée par php ou le message a ecrire;
}
?>